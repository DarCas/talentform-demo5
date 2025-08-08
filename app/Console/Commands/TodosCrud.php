<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Models\User;
use App\Traits\CommandsHelperTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodosCrud extends Command
{
    use CommandsHelperTrait;

    protected $fields = [
        'userId' => 'User ID',
        'titolo' => 'Titolo',
        'descrizione' => 'Descrizione',
        'dataInserimento' => 'Data di inserimento',
        'dataScadenza' => 'Data di scadenza',
        'email' => 'Notifica via email',
    ];

    protected $signature = 'todos:crud
                            {action : Operazione da eseguire: create, read, update, delete}
                            {--q= : Filtra attività in read}
                            {--p=1 : Paginazione in read}
                            {--perPage=10 : Attività per pagina in read}
                            {--id= : ID per update e delete}';

    protected $description = 'Comando per eseguire operazioni sulla tabella attività';

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!in_array($action, ['create', 'read', 'update', 'delete'])) {
            $this->error('Operazione non valida');

            return $this::FAILURE;
        }

        return $this->$action();
    }

    protected function create(): int
    {
        $this->clear();

        $usersCollection = $this->getUsers();

        $usernm = $this->anticipate(
            "Indica l'utente a cui associare l'attività",
            array_keys($usersCollection),
        );
        $userId = $usersCollection[$usernm];

        $titolo = $this->ask('Inserisci il titolo');
        $descrizione = $this->ask('Inserisci la descrizione');
        $dataInserimento = $this->ask('Inserisci la data di inizio (formato gg/mm/aaaa)', now()->format('d/m/Y'));
        $dataScadenza = $this->ask('Inserisci la data di scadenza (formato gg/mm/aaaa) [opzionale]');
        $email = $this->confirm('Vuoi ricevere una notifica via email?');

        $dataInserimento = implode('-', array_reverse(explode('/', $dataInserimento)));

        if (!is_null($dataScadenza)) {
            $dataScadenza = implode('-', array_reverse(explode('/', $dataScadenza)));
        }

        $validator = Validator::make([
            'userId' => $userId,
            'titolo' => $titolo,
            'descrizione' => $descrizione,
            'dataInserimento' => $dataInserimento,
            'dataScadenza' => $dataScadenza,
            'email' => $email,
        ], [
            'userId' => 'required|integer',
            'titolo' => 'required|min:10|max:255',
            'descrizione' => 'required',
            'dataInserimento' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'dataScadenza' => [
                'nullable',
                Rule::date()->format('Y-m-d'),
            ],
            'email' => 'boolean',
        ]);

        if ($validator->fails()) {
            $this->error("Errore nell'inserimento dei dati:");

            $errors = array_reduce($validator->errors()->keys(), function ($carry, $key) use ($validator) {
                $carry[$key] = $validator->errors()->get($key)[0];

                return $carry;
            }, []);

            foreach ($errors as $key => $error) {
                $this->error("- {$this->fields[$key]}: {$error}");
            }

            $this->info('');

            return $this::FAILURE;
        }

        try {
            $todo = new Todo();
            $todo->user_id = $validator->getValue('userId');
            $todo->titolo = $validator->getValue('titolo');
            $todo->descrizione = $validator->getValue('descrizione');
            $todo->data_inserimento = $validator->getValue('dataInserimento');
            $todo->data_scadenza = $validator->getValue('dataScadenza');
            $todo->email = $validator->getValue('email') ?? false;
            $todo->save();

            $this->info("L'attività «{$todo->titolo}» è stata aggiunta");

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    protected function read(): int
    {
        $this->clear();

        $usersCollection = $this->getUsers();

        $usernm = $this->anticipate(
            'Le attivita di quale utente vuoi visualizzare?',
            array_keys($usersCollection),
        );
        $userId = $usersCollection[$usernm];

        $page = $this->option('p');

        do {
            $this->clear();

            $builder = Todo::where('user_id', $userId)
                ->orderBy('data_inserimento')
                ->orderBy('data_scadenza');

            /**
             * Pagino i risultati utilizzando Eloquent di Laravel.
             */
            $paginate = $builder->paginate(
                perPage: (int)$this->option('perPage'),
                page: $page,
            );

            $headers = [
                'ID',
                'Attività',
                'Data inizio',
                'Data scadenza',
                'Notifica via email',
            ];

            $rows = array_map(function ($item) {
                return [
                    $item->id,
                    $item->titolo,
                    $item->dataInserimentoHuman(),
                    $item->dataScadenzaHuman(),
                    $item->email ? 'Sì' : 'No',
                ];
            }, $paginate->items());

            $this->table($headers, $rows);

            $this->info('');
            $this->info("\tTotale attività: {$paginate->total()}");
            $this->info("\tPagina $page di {$paginate->lastPage()}");
            $this->info('');
            $continue = $this->confirm('Carico la pagina successiva', true);
            ++$page;
        } while ($continue);

        return $this::SUCCESS;
    }

    protected function update(): int
    {
        try {
            $this->clear();

            $id = $this->option('id');

            if (is_null($id)) {
                $this->error("Il parametro --id= è obbligatorio");

                return $this::FAILURE;
            }

            $todo = Todo::where('id', $id)
                ->first();

            if (!is_null($todo)) {
                $this->info("L'attività è di proprietà di «{$todo->user()->first()->usernm}»");
                $this->info('');

                $titolo = $this->ask('Inserisci il titolo', $todo->titolo);
                $descrizione = $this->ask('Inserisci la descrizione', $todo->descrizione);
                $dataInserimento = $this->ask('Inserisci la data di inizio (formato gg/mm/aaaa)', $todo->data_inserimento->format('d/m/Y'));
                $dataScadenza = $this->ask('Inserisci la data di scadenza (formato gg/mm/aaaa) [opzionale]', $todo->data_scadenza?->format('d/m/Y') ?? null);
                $email = $this->confirm('Vuoi ricevere una notifica via email?', $todo->email);

                $dataInserimento = implode('-', array_reverse(explode('/', $dataInserimento)));

                if (!is_null($dataScadenza)) {
                    $dataScadenza = implode('-', array_reverse(explode('/', $dataScadenza)));
                }

                $validator = Validator::make([
                    'titolo' => $titolo,
                    'descrizione' => $descrizione,
                    'dataInserimento' => $dataInserimento,
                    'dataScadenza' => $dataScadenza,
                    'email' => $email,
                ], [
                    'titolo' => 'required|min:10|max:255',
                    'descrizione' => 'required',
                    'dataInserimento' => [
                        'required',
                        Rule::date()->format('Y-m-d'),
                    ],
                    'dataScadenza' => [
                        'nullable',
                        Rule::date()->format('Y-m-d'),
                    ],
                    'email' => 'boolean',
                ]);

                if ($validator->fails()) {
                    $this->error("Errore nell'inserimento dei dati:");

                    $errors = array_reduce($validator->errors()->keys(), function ($carry, $key) use ($validator) {
                        $carry[$key] = $validator->errors()->get($key)[0];

                        return $carry;
                    }, []);

                    foreach ($errors as $key => $error) {
                        $this->error("- {$this->fields[$key]}: {$error}");
                    }

                    $this->info('');

                    return $this::FAILURE;
                }
            } else {
                $this->error("L'utente non ha l'attività indicata");

                return $this::FAILURE;
            }

            $todo->titolo = $validator->getValue('titolo');
            $todo->descrizione = $validator->getValue('descrizione');
            $todo->data_inserimento = $validator->getValue('dataInserimento');
            $todo->data_scadenza = $validator->getValue('dataScadenza');
            $todo->email = $validator->getValue('email') ?? false;
            $todo->save();

            $this->info("L'attività «{$todo->titolo}» è stata modificata");

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    protected function delete(): int
    {
        try {
            $this->clear();

            $id = $this->option('id');

            if (is_null($id)) {
                $this->error("Il parametro --id= è obbligatorio");

                return $this::FAILURE;
            }

            $todo = Todo::find($id);

            if (is_null($todo)) {
                $this->error("L'attività «{$id}» non esiste");

                return $this::FAILURE;
            }

            if ($this->confirm("Sei sicuro di voler cancellare l'attività «{$todo->titolo}»?")) {
                $todo->delete();

                $this->info("L'attività «{$todo->titolo}» è stata cancellato");
            } else {
                $this->warn("Operazione annullata");
            }

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    private function getUsers(): array
    {
        $users = User::all();
        $usersCollection = [];

        foreach ($users as $user) {
            $usersCollection[$user->usernm] = $user->id;
        }

        return $usersCollection;
    }
}
