<?php

namespace App\Console\Commands;

use App\Models\Todo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TodosAlert extends Command
{
    protected $signature = 'todos:alert
                            {--days=7 : Numero di giorni prima della scadenza}';

    protected $description = 'Invio e-mail alert di scadenza attività';

    public function handle()
    {
        /**
         * Recupero tutte le attività per le quali voglio inviare un promemoria di scadenza.
         */
        $builder = Todo::where('email', true);
        /**
         * Escludo le attività già completate.
         */
        $builder->whereNull('data_completamento');
        /**
         * Seleziono solo le attività che stanno per scadere entro l'intervallo di tempo scelto.
         * Quello di default è 7 giorni.
         */
        $builder->whereRaw("DATE_ADD(data_scadenza, INTERVAL -{$this->option('days')} DAY) <= NOW()");

        /**
         * Recupero i risultati
         */
        $todos = $builder->get();

        foreach ($todos as $todo) {
            /**
             * Tramite il Model, recupero l'utente proprietario dell'attività per avere il suo indirizzo e-mail.
             */
            $user = $todo->user()->first();

            /**
             * Genero il corpo del messaggio da inviare utilizzando il templating Blade.
             */
            $template = new \App\Mail\TodosAlert([
                'attivita' => $todo->titolo,
                'dataInizio' => $todo->dataInserimentoHuman(),
                'dataScadenza' => $todo->dataScadenzaHuman(),
            ]);

            /**
             * Configuro un'istanza di invio e-mail dandogli come parametro l'indirizzo e-mail a cui inviare l'alert.
             */
            $mail = Mail::to($user->usernm);

            /**
             * Invio l'e-mail
             */
            $mail->send($template);

            /**
             * Imposto a «false» il campo «email» della tabella del database, così da non inviare continuamento la
             * stessa e-mail di alert.
             */
            $todo->email = false;

            /**
             * Salvo il record nella tabella del database.
             */
            $todo->save();
        }

        return Command::SUCCESS;
    }
}
