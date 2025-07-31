<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando per aggiungere un utente al database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usernm = $this->ask('Inserisci lo username (e-mail valida)');
        $passwd = $this->secret('Inserisci la password (min 6 caratteri)');
        $passwd_confirmation = $this->secret('Conferma la password inserita');

        $fields = [
            'usernm' => 'Username',
            'passwd' => 'Password',
        ];

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
                $this->error("- {$fields[$key]}: {$error}");
            }

            $this->info('');

            return Command::FAILURE;
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

            return Command::FAILURE;
        }
    }
}
