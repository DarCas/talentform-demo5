<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Authenticator
{
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Verifico se è stato effettuato l'accesso
         */
        if (Session::has('logged_in')) {
            Session::get('logged_in')->logged_in = now();
            Session::get('logged_in')->save();

            return $next($request);
        } else {
            return redirect('/login');
        }
    }
}
