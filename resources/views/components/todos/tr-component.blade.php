@props([
    'editing',
    'id',
    'titolo',
    'dataInserimento',
    'dataScadenza',
    'dataCompletamento',
    'descrizione'
])
<tr class="{{ $editing ? 'table-primary' : '' }}">
    <th rowspan="2" scope="row" class="text-end">{{ $id }}</th>
    <td>{{ $titolo }}</td>
    <td>{{ $dataInserimento }}</td>
    <td>{{ $dataScadenza }}</td>
    <td>{{ $dataCompletamento }}</td>
    <td rowspan="2">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                @if($dataCompletamento === 'In corso...')
                    <li>
                        <a href="/todos/{{ $id }}/completed" class="dropdown-item">Completata</a>
                    </li>
                @endif

                <li>
                    <a href="/?edit={{ $id }}" class="dropdown-item">Modifica</a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a href="/todos/{{ $id }}/delete" class="dropdown-item">Cancella</a>
                </li>
            </ul>
        </div>
    </td>
</tr>
<tr class="{{ $editing ? 'table-primary' : '' }}">
    <td colspan="4">{!! nl2br($descrizione) !!}</td>
</tr>
