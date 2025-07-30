<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'passwd' => 'required',
            'usernm' => [
                'required',
                'email:rfc',
            ],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            /**
             * Salvo con un algoritmo standard di riduzione una matrice di errori in formato key-value.
             */
            Session::put('errors', array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                $carry[$key] = $errors->get($key)[0];

                return $carry;
            }, []));

            /**
             * Reindirizzo alla pagina di login.
             */
            return redirect('/');
        }

        $user = User::where('usernm', $validator->getValue('usernm'))
            ->where('passwd', sha1($validator->getValue('passwd')))
            ->first();

        if (is_null($user)) {
            /**
             * Se l'utente non esiste le credenziali sono errate.
             */
            Session::put('errors', ['usernm' => 'Credenziali non valide']);
        } else {
            /**
             * Se l'utente esiste, salvo i suoi dati in una sessione.
             */
            Session::put('logged_in', $user);
        }

        return redirect('/');
    }

    function logout()
    {
        /**
         * Cancello i dati dell'utente dalla sessione.
         */
        Session::forget('logged_in');

        return redirect('/');
    }
}
