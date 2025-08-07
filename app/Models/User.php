<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $usernm
 * @property string $passwd
 * @property Carbon|null $logged_in
 *
 * @property Collection|Todo[] $todos
 *
 * @package App\Models
 */
class User extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    protected $casts = [
        'logged_in' => 'datetime'
    ];

    protected $fillable = [
        'usernm',
        'passwd',
        'logged_in'
    ];

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function dataUltimoAccesso(): string
    {
        if ($this->logged_in) {
            return $this->logged_in
                ->setTimezone('Europe/Rome')
                ->format('d/m/Y H:i:s');
        }

        return 'N/A';
    }
}
