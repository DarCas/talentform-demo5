<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Traits\SendAlertTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodosController extends Controller
{
    use SendAlertTrait;

    public function index(Request $request)
    {
        /**
         * Interrogo la tabella del database tramite il Model (Todo).
         * Recupero solo i record dell'utente loggato.
         * Ordino i records per «data_inserimento» e «data_scadenza».
         */
        $builder = Todo::where('user_id', Session::get('logged_in')->id)
            ->orderBy('data_inserimento')
            ->orderBy('data_scadenza');

        if ($request->get('q')) {
            /**
             * Se effettuo una ricerca, filtro i valori della tabella del database (Todo) per
             * «titolo» e per «descrizione». Utilizzo il LIKE di SQL, che mi permette di cercare
             * una stringa all'interno di una parola.
             *
             * Il record viene selezionato se la stringa è presente in «titolo» oppure in «descrizione» o in
             * entrambe le colonne.
             */
            $builder->where('titolo', 'LIKE', "%{$request->get('q')}%")
                ->orWhere('descrizione', 'LIKE', "%{$request->get('q')}%");
        }

        /**
         * Pagino i risultati utilizzando Eloquent di Laravel.
         */
        $paginate = $builder->paginate((int)$request->get('perPage', 10));

        $todo = null;

        if ($request->get('edit')) {
            /**
             * Se esiste il parametro GET «edit», provo a recuperare il record dalla tabella del database
             * corrispondente all'ID indicato.
             */
            $todo = Todo::find($request->get('edit'));
        }

        $content = view('front.todos', [
            // Passo gli eventuali errori al form di creazione di un Todo
            'errors' => Session::get('errors'),
            // Passo la paginazione dei risultati
            'pagination' => $paginate->links()->toHtml(),
            // Passo tutti i risultati
            'todos' => $paginate->items(),
            // Passo il record che eventualmente è in modifica
            'todo' => $todo,
        ]);

        /**
         * Una volta visualizzati, cancello gli eventuali errori.
         */
        Session::forget('errors');

        return view('front.default', [
            'centered' => false,
            'content' => $content,
            'title' => 'To-Do',
            'q' => $request->get('q'),
            'user' => Session::get('logged_in'),
        ]);
    }

    public function create(Request $request)
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

    public function update(Request $request, Todo $todo)
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

            return redirect("/?edit={$todo->id}");
        }

        /**
         * Recupero il record dalla tabella del database sfruttando il Model (Todo::find).
         */
        $todo->titolo = $validator->getValue('titolo');
        $todo->descrizione = $validator->getValue('descrizione');
        $todo->data_inserimento = $validator->getValue('dataInserimento');
        $todo->data_scadenza = $validator->getValue('dataScadenza');
        $todo->email = $validator->getValue('email') ?? false;
        $todo->save();

        return redirect('/');
    }

    public function delete(Todo $todo)
    {
        $todo->delete();

        return redirect('/');
    }

    public function alert(Todo $todo)
    {
        $this->sendAlert($todo);

        return redirect('/');
    }

    public function completed(Todo $todo)
    {
        $todo->data_completamento = Carbon::now();
        $todo->save();

        return redirect('/');
    }
}
