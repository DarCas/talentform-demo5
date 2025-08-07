<?php

namespace App\Console\Commands;

use App\Models;
use App\Traits\CommandsHelperTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class AppBackup extends Command
{
    use CommandsHelperTrait;

    protected $signature = 'app:backup
                            {context? : Operazione da eseguire: todos, users}
                            {--orderBy=id : Ordina la lista in base alla colonna indicata}
                            {--orderDesc : Ordina la lista in ordine decrescente}
                            {--separator=, : Carattere utilizzato per separare i campi}
                            {--noClear : Non cancellare la finestra di comando}';

    protected $description = "Esegue il backup dell'applicazione";

    public function handle(): int
    {
        $this->clear();

        $context = $this->argument('context');

        if (!$context) {
            $this->backup(Models\Todo::class, 'todos');
            $this->backup(Models\User::class, 'users');

            return $this::SUCCESS;
        } else if (in_array($context, ['todos', 'users'])) {
            $model = "App\\Models\\" . ucfirst(substr($context, 0, -1));

            if (!class_exists($model)) {
                $this->error("Il paramentro context indicato «{$context}» non è valido");

                return $this::FAILURE;
            }

            return $this->backup($model, $context);
        } else {
            $this->error("Il paramentro context indicato «{$context}» non è valido");

            return $this::FAILURE;
        }
    }

    protected function backup(string $model, string $context): int
    {
        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        $filename = now()->format('YmdHis') . '.csv';

        if (!$disk->exists($context)) {
            $disk->makeDirectory($context);
        }

        /**
         * Creo il percorso completo dove andrò a salvare il file
         */
        $filepath = $disk->path("$context/$filename");

        try {
            /**
             * Recupero tutti i dati dalla tabella del database
             */
            /** @var Collection $items */
            $items = $model::orderBy($this->option('orderBy'), $this->option('orderDesc') ? 'desc' : 'asc')
                ->get();

            /**
             * Creo la risorsa per la scrittura del mio file CSV
             */
            $file = fopen($filepath, 'w');

            /**
             * Recupero dinamicamente gli header della mia tabella
             */
            $header = array_keys($items[0]->getAttributes());

            /**
             * Salvo come prima riga gli header della tabella
             */
            fputcsv($file, $header, $this->option('separator'));

            /**
             * Aggiungo tutti gli elementi della tabella al file CSV
             */
            $items->map(fn($todo) => fputcsv($file, $todo->toArray(), $this->option('separator')));

            /**
             * Salvo il file e rilascio la risorsa
             */
            fclose($file);

            $this->info("La tabella «{$context}» è stata esportata in «{$filepath}»");

            return $this::SUCCESS;
        } catch (\Throwable $th) {
            $this->error($th->getMessage());

            return $this::FAILURE;
        }
    }
}
