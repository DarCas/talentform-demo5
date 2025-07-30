<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodosController extends Controller
{
    function add(Request $request)
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

            return redirect('/');
        }

        $todo = new Todo();
        $todo->user_id = Session::get('logged_in')->id;
        $todo->titolo = $validator->getValue('titolo');
        $todo->descrizione = $validator->getValue('descrizione');
        $todo->data_inserimento = $validator->getValue('dataInserimento');
        $todo->data_scadenza = $validator->getValue('dataScadenza');
        $todo->email = $validator->getValue('email');
        $todo->save();

        return redirect('/');
    }
}
