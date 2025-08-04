<?php

namespace App\Console\Commands;

use App\Models\Todo;
use Illuminate\Console\Command;

class TodosDeleteCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:delete-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancello le attività completate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Todo::whereNotNull('data_completamento')
            ->delete();

        return Command::SUCCESS;
    }
}
