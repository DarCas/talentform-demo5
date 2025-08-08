<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Todo
 *
 * @property int $id
 * @property int $user_id
 * @property string $titolo
 * @property string $descrizione
 * @property Carbon $data_inserimento
 * @property Carbon $data_scadenza
 * @property Carbon|null $data_completamento
 * @property bool $email
 *
 * @property User $user
 *
 * @package App\Models
 */
class Todo extends Model
{
    protected $table = 'todos';
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'int',
        'data_inserimento' => 'datetime',
        'data_scadenza' => 'datetime',
        'data_completamento' => 'datetime',
        'email' => 'bool'
    ];

    protected $fillable = [
        'user_id',
        'titolo',
        'descrizione',
        'data_inserimento',
        'data_scadenza',
        'data_completamento',
        'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function dataInserimentoHTML(): string
    {
        return $this->data_inserimento
            ->format('Y-m-d');
    }

    function dataScadenzaHTML(): string
    {
        return $this->data_scadenza
            ->format('Y-m-d');
    }

    function dataInserimentoHuman(): string
    {
        return $this->data_inserimento
            ->format('d/m/Y');
    }

    function dataScadenzaHuman($silent = false, $raw = false): ?string
    {
        if (!$this->data_scadenza) {
            return $silent ? null : 'N/A';
        }

        $data = $this->data_scadenza
            ->format('d/m/Y');

        $days = $this->data_scadenza
            ->diffInDays(Carbon::now());

        $days = round($days);

        return $raw ? $data : "{$data} ({$days} giorni)";
    }

    function dataCompletamentoHuman(): ?string
    {
        return $this->data_completamento?->format('d/m/Y') ?? null;
    }
}
