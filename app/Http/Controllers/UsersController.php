<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        /**
         * Validazione dei campi che ricevo dal form HTML
         */
        $validator = Validator::make($request->post(), [
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

        /**
         * Verifico se si sono verificati errori di compilazione del form.
         */
        if ($validator->fails()) {
            $errors = $validator->errors();

            /**
             * Salvo con un algoritmo standard di riduzione una matrice di errori in formato key-value.
             */
            Session::put('errors', array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                $carry[$key] = $errors->get($key)[0];

                return $carry;
            }, []));

            return redirect('/users');
        }

        $user = new User();
        $user->usernm = $validator->getValue('usernm');
        $user->passwd = sha1($validator->getValue('passwd'));
        $user->save();

        return redirect('/users');
    }

    public function read(Request $request)
    {
        $builder = User::orderBy('usernm');

        /**
         * Pagino i risultati utilizzando Eloquent di Laravel.
         */
        $paginate = $builder->paginate((int)$request->get('perPage', 10));

        $user = null;

        if ($request->get('edit')) {
            /**
             * Se esiste il parametro GET «edit», provo a recuperare il record dalla tabella del database
             * corrispondente all'ID indicato.
             */
            $user = User::find($request->get('edit'));
        }

        $content = view('front.users', [
            // Passo gli eventuali errori al form di creazione di un Todo
            'errors' => Session::get('errors'),
            // Passo l'id dell'utente autenticato
            'me' => Session::get('logged_in')->id,
            // Passo la paginazione dei risultati
            'pagination' => $paginate->links()->toHtml(),
            // Passo tutti i risultati
            'users' => $paginate->items(),
            // Passo il record che eventualmente è in modifica
            'user' => $user,

        ]);

        /**
         * Una volta visualizzati, cancello gli eventuali errori.
         */
        Session::forget('errors');

        return view('front.default', [
            'centered' => !Session::has('logged_in'),
            'content' => $content,
            'q' => $request->get('q'),
            'title' => 'Home',
            'user' => Session::get('logged_in'),
        ]);
    }

    public function update(Request $request, int $id)
    {
        /**
         * Validazione dei campi che ricevo dal form HTML
         */
        $validator = Validator::make($request->post(), [
            'usernm' => [
                'required',
                'email:rfc',
                Rule::unique('users', 'usernm')
                    ->ignore($id),
            ],
            'passwd' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        /**
         * Verifico se si sono verificati errori di compilazione del form.
         */
        if ($validator->fails()) {
            $errors = $validator->errors();

            /**
             * Salvo con un algoritmo standard di riduzione una matrice di errori in formato key-value.
             */
            Session::put('errors', array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                $carry[$key] = $errors->get($key)[0];

                return $carry;
            }, []));

            return redirect("/users?edit=$id");
        }

        $user = User::find($id);

        if (!is_null($user)) {
            $user->passwd = sha1($validator->getValue('passwd'));
            $user->save();
        }

        return redirect('/users');
    }

    public function delete(int $id)
    {
        $user = User::find($id);

        if (!is_null($user)) {
            $user->delete();
        }

        return redirect('/users');
    }

    public function backup()
    {
        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        if ($disk->exists('users.csv')) {
            return response()
                ->download($disk->path('users.csv'));
        } else {
            return response()
                ->noContent(404);
        }
    }
}
