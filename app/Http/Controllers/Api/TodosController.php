<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodosController extends Controller
{
    function create(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'userId' => 'required|integer',
            'titolo' => 'required|min:10|max:255',
            'descrizione' => 'required',
            'dataInserimento' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'dataScadenza' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'email' => 'boolean',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()
                ->json([
                    'errors' => array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                        $carry[$key] = $errors->get($key)[0];

                        return $carry;
                    }, []),
                ]);
        }

        $todo = new Todo();
        $todo->user_id = $validator->getValue('userId');
        $todo->titolo = $validator->getValue('titolo');
        $todo->descrizione = $validator->getValue('descrizione');
        $todo->data_inserimento = $validator->getValue('dataInserimento');
        $todo->data_scadenza = $validator->getValue('dataScadenza');
        $todo->email = $validator->getValue('email') ?? false;
        $todo->save();

        return response()
            ->json([
                'id' => $todo->id,
                'success' => true,
            ]);
    }

    public function read(Request $request)
    {
        $builder = Todo::orderBy('data_inserimento')
            ->orderBy('data_scadenza');

        if ($request->get('id')) {
            $builder->where('id', $request->get('id'));
        }

        $paginate = $builder->paginate((int)$request->get('perPage', 10));

        return response()
            ->json([
                'count' => $paginate->total(),
                'items' => $paginate->items(),
                'page' => $paginate->currentPage(),
            ]);
    }

    function update(Request $request, int $id)
    {
        $validator = Validator::make($request->post(), [
            'userId' => 'required|integer',
            'titolo' => 'required|min:10|max:255',
            'descrizione' => 'required',
            'dataInserimento' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'dataScadenza' => [
                'required',
                Rule::date()->format('Y-m-d'),
            ],
            'email' => 'boolean',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()
                ->json([
                    'errors' => array_reduce($errors->keys(), function ($carry, $key) use ($errors) {
                        $carry[$key] = $errors->get($key)[0];

                        return $carry;
                    }, []),
                ]);
        }

        $todo = Todo::find($id);

        if (!is_null($todo)) {
            $todo->user_id = $validator->getValue('userId');
            $todo->titolo = $validator->getValue('titolo');
            $todo->descrizione = $validator->getValue('descrizione');
            $todo->data_inserimento = $validator->getValue('dataInserimento');
            $todo->data_scadenza = $validator->getValue('dataScadenza');
            $todo->email = $validator->getValue('email') ?? false;
            $todo->save();

            return response()
                ->json([
                    'success' => true,
                ]);
        } else {
            return response()
                ->json([
                    'success' => false,
                ]);
        }
    }

    function delete(int $id)
    {
        $todo = Todo::find($id);

        if (!is_null($todo)) {
            $todo->delete();

            return response()
                ->json([
                    'success' => true,
                ]);
        } else {
            return response()
                ->json([
                    'success' => false,
                ]);
        }
    }
}
