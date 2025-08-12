<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
            ],
        ]);

        /**
         * Verifico se si sono verificati errori di compilazione del form.
         */
        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()
                ->json([
                    'errors' => array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                        $carry[$key] = $errors->get($key)[0];

                        return $carry;
                    }, []),
                ], 412);
        }

        $user = new User();
        $user->usernm = $validator->getValue('usernm');
        $user->passwd = sha1($validator->getValue('passwd'));
        $user->save();

        return response()
            ->json([
                'id' => $user->id,
                'success' => true,
            ]);
    }

    public function read(Request $request)
    {
        $builder = User::orderBy('usernm');

        if ($request->get('q')) {
            /**
             * Se effettuo una ricerca, filtro i valori della tabella del database (Users) per
             * «usernm». Utilizzo il LIKE di SQL, che mi permette di cercare una stringa all'interno di una parola.
             */
            $builder->where('usernm', 'LIKE', "{$request->get('q')}%");
        }

        /**
         * Pagino i risultati utilizzando Eloquent di Laravel.
         */
        $paginate = $builder->paginate(
            perPage: (int)$request->get('perPage', 100),
            page: $request->get('page', 1),
        );

        return response()
            ->json([
                'count' => $paginate->total(),
                'items' => $paginate->items(),
                'page' => $paginate->currentPage(),
            ]);
    }

    public function update(Request $request, User $user)
    {
        /**
         * Validazione dei campi che ricevo dal form HTML
         */
        $validator = Validator::make($request->post(), [
            'usernm' => [
                'required',
                'email:rfc',
                Rule::unique('users', 'usernm')
                    ->ignore($user->id),
            ],
            'passwd' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        /**
         * Verifico se si sono verificati errori di compilazione del form.
         */
        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()
                ->json([
                    'errors' => array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                        $carry[$key] = $errors->get($key)[0];

                        return $carry;
                    }, []),
                ], 412);
        }

        $user->passwd = sha1($validator->getValue('passwd'));
        $user->save();

        return response()
            ->json([
                'id' => $user->id,
                'success' => true,
            ]);
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()
            ->json([
                'success' => true,
            ]);
    }
}
