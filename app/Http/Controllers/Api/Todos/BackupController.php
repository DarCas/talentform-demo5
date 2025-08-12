<?php

namespace App\Http\Controllers\Api\Todos;

use App\Http\Controllers\Api\AbstractBackupController;

class BackupController extends AbstractBackupController
{
    protected string $context = 'todos';
}
