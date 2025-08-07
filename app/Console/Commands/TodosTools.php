<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Traits\SendAlertTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TodosTools extends Command
{
    use SendAlertTrait;

    protected $signature = 'todos:tools
                            {tool : Operazione da eseguire: delete-completed, expiration-alert}
                            {--days=7 : Numero di giorni prima della scadenza per «expiration-alert»}';

    protected $description = 'Tools per le attività';

    public function handle()
    {
        $tool = $this->argument('tool');

        if (!in_array($tool, ['delete-completed', 'expiration-alert'])) {
            $this->error('Operazione non valida');

            return $this::FAILURE;
        }

        $tool = Str::camel($tool);

        return $this->$tool();
    }

    protected function expirationAlert()
    {
        try {
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

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    protected function deleteCompleted()
    {
        try {
            Todo::whereNotNull('data_completamento')
                ->delete();

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }
}
