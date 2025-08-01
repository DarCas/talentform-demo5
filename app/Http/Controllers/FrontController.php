<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Verifico se è stato effettuato l'accesso
         */
        if (Session::has('logged_in')) {
            /**
             * Interrogo la tabella del database tramite il Model (Todo).
             * Ordino i dati per «data_inserimento» e «data_scadenza».
             */
            $builder = Todo::orderBy('data_inserimento')
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
                $builder->where('titolo', 'LIKE', "%{$request->get('q')}%");
                $builder->orWhere('descrizione', 'LIKE', "%{$request->get('q')}%");
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
        } else {
            /**
             * Se non è stato effettuato l'accesso, visualizzo il form di login.
             */
            $content = view('front.login', [
                // Passo gli eventuali errori in fase di login
                'errors' => Session::get('errors'),
            ]);
        }

        /**
         * Una volta visualizzati, cancello gli eventuali errori.
         */
        Session::forget('errors');

        return view('front.default', [
            'centered' => !Session::has('logged_in'),
            'content' => $content,
            'title' => 'Home',
            'q' => $request->get('q'),
            'user' => Session::get('logged_in'),
        ]);
    }
}
