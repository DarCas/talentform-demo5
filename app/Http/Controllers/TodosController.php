<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodosController extends Controller
{
    function create(Request $request)
    {
        /**
         * Validazione dei campi che ricevo dal form HTML
         */
        $validator = Validator::make($request->post(), [
            'titolo' => 'required|min:10|max:255',
            'descrizione' => 'required',
            'dataInserimento' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'dataScadenza' => [
                Rule::date()->format('Y-m-d'),
            ],
            'email' => 'boolean',
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

            return redirect('/');
        }

        /**
         * Creo un nuovo record per la tabella del database istanziando un nuovo oggetto Model (Todo).
         */
        $todo = new Todo();
        $todo->user_id = Session::get('logged_in')->id;
        $todo->titolo = $validator->getValue('titolo');
        $todo->descrizione = $validator->getValue('descrizione');
        $todo->data_inserimento = $validator->getValue('dataInserimento');
        $todo->data_scadenza = $validator->getValue('dataScadenza');
        $todo->email = $validator->getValue('email') ?? false;
        $todo->save();

        return redirect('/');
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->post(), [
            'titolo' => 'required|min:10|max:255',
            'descrizione' => 'required',
            'dataInserimento' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'dataScadenza' => [
                Rule::date()->format('Y-m-d'),
            ],
            'email' => 'boolean',
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

            return redirect("/?edit=$id");
        }

        /**
         * Recupero il record dalla tabella del database sfruttando il Model (Todo::find).
         */
        $todo = Todo::find($id);

        if (!is_null($todo)) {
            $todo->titolo = $validator->getValue('titolo');
            $todo->descrizione = $validator->getValue('descrizione');
            $todo->data_inserimento = $validator->getValue('dataInserimento');
            $todo->data_scadenza = $validator->getValue('dataScadenza');
            $todo->email = $validator->getValue('email') ?? false;
            $todo->save();
        }

        return redirect('/');
    }

    public function completed(int $id)
    {
        $todo = Todo::find($id);

        if (!is_null($todo)) {
            $todo->data_completamento = Carbon::now();
            $todo->save();
        }

        return redirect('/');
    }

    public function delete(int $id)
    {
        $todo = Todo::find($id);

        if (!is_null($todo)) {
            $todo->delete();
        }

        return redirect('/');
    }
}
