<?php

namespace App\Http\Controllers\Todos;

use App\Http\Controllers\AbstractBackupController;

class BackupController extends AbstractBackupController
{
    protected string $context = 'todos';
    protected string $title = 'To-Do » Backup';
}
