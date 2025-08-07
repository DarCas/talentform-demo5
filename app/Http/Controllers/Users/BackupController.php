<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\AbstractBackupController;

class BackupController extends AbstractBackupController
{
    protected string $context = 'users';
    protected string $title = 'Utenti » Backup';
}
