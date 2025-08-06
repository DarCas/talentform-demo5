<?php

namespace App\Http\Controllers;

use App\Mail\UsersRecuperaPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        /**
         * Verifico se le credenziali fornite sono associate a un utente.
         * Interrogo la tabella del database degli utenti con il Model (User).
         */
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

    public function recuperaPassword()
    {
        /**
         * Se non è stato effettuato l'accesso, visualizzo il form di login.
         */
        $content = view('front.login.recupera-password', [
            // Passo gli eventuali errori in fase di login
            'errors' => Session::get('errors'),
        ]);

        /**
         * Una volta visualizzati, cancello gli eventuali errori.
         */
        Session::forget('errors');

        return view('front.default', [
            'centered' => true,
            'content' => $content,
            'title' => 'Home',
            'q' => '',
            'user' => null,
        ]);
    }

    public function inviaPassword(Request $request)
    {
        $validator = Validator::make($request->post(), [
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
            return redirect('/recupera-password');
        }

        /**
         * Recupero l'utente dalla tabella del database
         */
        $user = User::where('usernm', $validator->getValue('usernm'))
            ->first();

        if (!is_null($user)) {
            $password = Str::password(
                length: 6,
                symbols: false,
            );

            $user->passwd = sha1($password);
            $user->save();

            $template = new UsersRecuperaPassword([
                'usernm' => $user->usernm,
                'passwd' => $password,
            ]);

            $mail = Mail::to($user->usernm);
            $mail->send($template);

            return redirect('/');
        } else {
            Session::put('errors', ['usernm' => 'Utente inesistente']);

            return redirect('/recupera-password');
        }
    }
}
