<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Support\Facades\Session;

class FrontController extends Controller
{
    function index()
    {
        if (Session::has('logged_in')) {
            $todos = Todo::orderBy('data_inserimento')
                ->orderBy('data_scadenza')
                ->get();

            $content = view('front.todos', [
                'errors' => Session::get('errors'),
                'todos' => $todos,
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
