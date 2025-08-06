<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Traits\SendAlertTrait;
use Illuminate\Console\Command;

class TodosAlert extends Command
{
    use SendAlertTrait;

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
            $this->sendAlert($todo);

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
