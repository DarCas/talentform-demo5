<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        if (Session::has('logged_in')) {
            $builder = Todo::orderBy('data_inserimento')
                ->orderBy('data_scadenza');

            $paginate = $builder->paginate((int)$request->get('perPage', 10));

            $content = view('front.todos', [
                'errors' => Session::get('errors'),
                'pagination' => $paginate->links()->toHtml(),
                'todos' => $paginate->items(),
            ]);
        } else {
            $content = view('front.login', [
                'errors' => Session::get('errors'),
            ]);
        }

        Session::forget('errors');

        return view('front.default', [
            'centered' => !Session::has('logged_in'),
            'content' => $content,
            'title' => 'Home',
            'user' => Session::get('logged_in'),
        ]);
    }
}
