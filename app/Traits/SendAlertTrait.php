<?php

namespace App\Traits;

use App\Models\Todo;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Mail;

trait SendAlertTrait
{
    protected function sendAlert(Todo $todo): ?SentMessage
    {
        /**
         * Tramite il Model, recupero l'utente proprietario dell'attività per avere il suo indirizzo e-mail.
         */
        $user = $todo->user()->first();

        /**
         * Genero il corpo del messaggio da inviare utilizzando il templating Blade.
         */
        $template = new \App\Mail\TodosAlert([
            'attivita' => $todo->titolo,
            'dataInizio' => $todo->dataInserimentoHuman(),
            'dataScadenza' => $todo->dataScadenzaHuman(),
        ]);

        /**
         * Configuro un'istanza di invio e-mail dandogli come parametro l'indirizzo e-mail a cui inviare l'alert.
         */
        $mail = Mail::to($user->usernm);

        /**
         * Invio l'e-mail
         */
        return $mail->send($template);
    }
}
