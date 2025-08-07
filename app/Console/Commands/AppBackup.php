<?php

namespace App\Console\Commands;

use App\Traits\CommandsHelperTrait;
use Illuminate\Console\Command;

class AppBackup extends Command
{
    use CommandsHelperTrait;

    protected $signature = 'app:backup';

    protected $description = "Esegue il backup dell'applicazione";

    public function handle()
    {
        $this->clear();

        $this->call('todos:backup', ['--noClear' => true]);
        $this->call('users:backup', ['--noClear' => true]);
    }
}
