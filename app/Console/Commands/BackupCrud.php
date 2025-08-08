<?php

namespace App\Console\Commands;

use App\Traits\CommandsHelperTrait;
use App\Traits\FilesystemTrait;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Common\Version;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupCrud extends Command
{
    use CommandsHelperTrait;
    use FilesystemTrait;

    protected $signature = 'backup:crud
                            {action : Operazione da eseguire: create, read, delete, download}
                            {context : Contesto dell\'operazione: todos, users}';

    protected $description = 'Comando per eseguire operazioni sui file di backup';

    public function handle()
    {
        $action = $this->argument('action');

        if (!in_array($action, ['create', 'read', 'delete', 'download'])) {
            $this->alert('Operazione non valida');

            return $this::FAILURE;
        }

        return $this->$action();
    }

    protected function create(): int
    {
        return $this->call('app:backup', [
            'context' => $this->argument('context'),
        ]);
    }

    protected function read(): int
    {
        $this->clear();

        $this->table([
            'Nome file',
            'Dimensione',
            'Tipologia',
            'Data creazione',
            '#',
        ], $this->items());

        return $this::SUCCESS;
    }

    protected function delete(): int
    {
        $this->clear();

        $filename = $this->askWithCompletion('Inserisci il nome del file da eliminare',
            $this->items()->pluck('filename')->toArray()
        );

        if (is_null($filename)) {
            $this->alert('Il nome del file è obbligatorio');

            return $this::FAILURE;
        }

        $disk = Storage::disk('backup');

        if (!$disk->exists("/{$this->argument('context')}/{$filename}")) {
            $this->alert("Il file «{$filename}» non esiste");

            return $this::FAILURE;
        }

        if ($this->confirm("Se sicuro di voler cancellare il file «{$filename}» di «{$this->argument('context')}»?")) {
            $disk->delete("/{$this->argument('context')}/{$filename}");

            $this->info("Il file «{$filename}» è stato cancellato!");
        } else {
            $this->warn("Operazione annullata");
        }

        return $this::SUCCESS;
    }

    protected function download(): int
    {
        $this->clear();

        $filename = $this->askWithCompletion('Inserisci il nome del file da eliminare',
            $this->items()->pluck('filename')->toArray()
        );

        $disk = Storage::disk('backup');

        if (!$disk->exists("/{$this->argument('context')}/{$filename}")) {
            $this->alert("Il file «{$filename}» non esiste");

            return $this::FAILURE;
        }

        $url = "http://demo5.loc/{$this->argument('context')}/backup/$filename/download";

        $qrCode = Encoder::encode(
            content: $url,
            ecLevel: ErrorCorrectionLevel::L(),
            forcedVersion: Version::getVersionForNumber(4),
        );
        $matrix = $qrCode->getMatrix()
            ->getArray();

        $this->info("Scansiona il QR Code per scaricare il file");
        $this->info('');

        $blocco = str_repeat(' ', 2);

        foreach ($matrix as $row) {
            foreach ($row as $col) {
                echo $col ? "\033[40m$blocco\033[0m" : "\033[47m$blocco\033[0m";
            }

            echo PHP_EOL;
        }

        $this->info('');
        $this->info("\tURL: $url");
        $this->info('');

        return $this::SUCCESS;
    }

    private function items(): Collection
    {
        $disk = Storage::disk('backup');
        $index = 1;

        $items = collect($disk->files("/{$this->argument('context')}"))
            ->filter(fn($file) => Str::endsWith($file, '.csv'))
            ->sortBy(fn($file) => $disk->lastModified($file), SORT_DESC, true)
            ->map(function ($file) use ($disk, &$index) {
                $oDateTime = \DateTime::createFromFormat('U', $disk->lastModified($file));

                return [
                    'filename' => collect(explode('/', $file))->last(),
                    'filesize' => $this->filesizeVerbose($disk->size($file), 1),
                    'filetype' => $disk->mimeType($file),
                    'lastModified' => $oDateTime->format('d/m/Y H:i:s'),
                    'idx' => $index++,
                ];
            });

        return $items->values();
    }
}
