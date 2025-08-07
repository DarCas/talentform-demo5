<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Traits\CommandsHelperTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TodosBackup extends Command
{
    use CommandsHelperTrait;

    protected $signature = 'todos:backup
                            {--orderBy=id : Ordina la lista in base alla colonna indicata}
                            {--orderDesc : Ordina la lista in ordine decrescente}
                            {--separator=, : Carattere utilizzato per separare i campi}';

    protected $description = 'Eseguo il backup della tabella delle attività in formato CSV';

    public function handle()
    {
        /**
         * Cancello tutta la finestra di comando
         */
        $this->clear();

        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        $filename = now()->format('YmdHis') . '.csv';

        if (!$disk->exists('todos')) {
            $disk->makeDirectory('todos');
        }

        /**
         * Creo il percorso completo dove andrò a salvare il file
         */
        $filepath = $disk->path("todos/$filename");

        try {
            /**
             * Recupero tutti i dati dalla tabella del database
             */
            $todos = Todo::orderBy($this->option('orderBy'), $this->option('orderDesc') ? 'desc' : 'asc')
                ->get();

            /**
             * Creo la risorsa per la scrittura del mio file CSV
             */
            $file = fopen($filepath, 'w');

            /**
             * Recupero dinamicamente gli header della mia tabella
             */
            $header = array_keys($todos[0]->getAttributes());

            /**
             * Salvo come prima riga gli header della tabella
             */
            fputcsv($file, $header, $this->option('separator'));

            /**
             * Aggiungo tutti gli elementi della tabella al file CSV
             */
            $todos->map(function ($todo) use ($file) {
                fputcsv($file, $todo->toArray(), $this->option('separator'));
            });

            /**
             * Salvo il file e rilascio la risorsa
             */
            fclose($file);

            $this->info("La tabella delle attività è stata esportata in «{$filepath}»");

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->error($th->getMessage());

            return Command::FAILURE;
        }
    }
}
