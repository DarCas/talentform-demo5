<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\CommandsHelperTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersCrud extends Command
{
    protected $fields = [
        'usernm' => 'Username',
        'passwd' => 'Password',
    ];

    use CommandsHelperTrait;

    protected $signature = 'users:crud
                            {action : Operazione da eseguire: create, read, update, delete}
                            {--usernm= : Filtra per username in read}
                            {--id= : ID per update e delete}';

    protected $description = 'Comando per eseguire operazioni sulla tabella utenti';

    public function handle()
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
        $usernm = $this->ask('Inserisci lo username (e-mail valida)');
        $passwd = $this->secret('Inserisci la password (min 6 caratteri)');
        $passwd_confirmation = $this->secret('Conferma la password inserita');

        $validator = Validator::make([
            'usernm' => $usernm,
            'passwd' => $passwd,
            'passwd_confirmation' => $passwd_confirmation,
        ], [
            'usernm' => [
                'required',
                'email:rfc',
                Rule::unique('users', 'usernm')
            ],
            'passwd' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
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
            $user = new User();
            $user->usernm = $usernm;
            $user->passwd = sha1($passwd);
            $user->save();

            $this->info("Utente «{$usernm}» aggiunto");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    protected function read(): int
    {
        $builder = User::orderBy('usernm');

        $usernm = $this->option('usernm');
        if ($usernm) {
            $builder->where('usernm', 'LIKE', "{$usernm}%");
        }

        $users = $builder->get();

        $headers = [
            'ID',
            'Username',
            'Ultimo accesso'
        ];

        $row = $users->map(function ($user) {
            return [
                $user->id,
                $user->usernm,
                $user->dataUltimoAccesso(),
            ];
        });

        $this->clear();
        $this->table($headers, $row);

        return $this::SUCCESS;
    }

    protected function update(): int
    {
        $id = $this->option('id');

        if (!$id) {
            $usernm = $this->ask('Inserisci lo username che vuoi modificare');

            $validator = Validator::make([
                'usernm' => $usernm,
            ], [
                'usernm' => [
                    'required',
                    'email:rfc',
                ],
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

            $builder = User::where('usernm', $usernm);
        } else {
            $builder = User::where('id', $id);
        }

        $user = $builder->first();

        if (is_null($user)) {
            $this->error("L'utente «{($usernm ?? $id)}» non esiste");

            return $this::FAILURE;
        }

        try {
            $passwd = $this->secret('Inserisci la nuova password (min 6 caratteri)');
            $passwd_confirmation = $this->secret('Conferma la password inserita');

            $validator = Validator::make([
                'passwd' => $passwd,
                'passwd_confirmation' => $passwd_confirmation,
            ], [
                'passwd' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                ],
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

            $user->passwd = sha1($passwd);
            $user->save();

            $this->info("La password dell'utente «{$user->usernm}» è stata modificata");

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }

    protected function delete(): int
    {
        $id = $this->option('id');

        if (!$id) {
            $usernm = $this->ask('Inserisci lo username che vuoi cancellare');

            $validator = Validator::make([
                'usernm' => $usernm,
            ], [
                'usernm' => [
                    'required',
                    'email:rfc',
                ],
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

            $builder = User::where('usernm', $usernm);
        } else {
            $builder = User::where('id', $id);
        }

        $user = $builder->first();

        if (is_null($user)) {
            $this->error("L'utente «{($usernm ?? $id)}» non esiste");

            return $this::FAILURE;
        }

        try {
            if ($this->confirm("Sei sicuro di voler cancellare l'utente «{$user->usernm}»?")) {
                $user->delete();

                $this->info("L'utente «{$user->usernm}» è stata cancellato");
            } else {
                $this->warn("Operazione annullata");
            }

            return $this::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $this::FAILURE;
        }
    }
}
