<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Verifico che l'utente di default non sia presente.
         */
        $user = User::where('usernm', 'dario@casertano.name')
            ->first();

        if (is_null($user)) {
            /**
             * Se non è presente, creo il nuovo utente.
             */

            $user = new User();
            $user->usernm = 'dario@casertano.name';
        }

        /**
         * Imposto (o reimposto, nel caso di utente già presente) la password.
         */
        $user->passwd = sha1('password1');

        /**
         * Salvo i dati nella tabella del database.
         */
        $user->save();
    }
}
