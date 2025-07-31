@props([
    'id',
    'titolo',
    'dataInserimento',
    'dataScadenza',
    'dataCompletamento',
    'descrizione'
])
<tr>
    <th rowspan="2" scope="row" class="text-end">{{ $id }}</th>
    <td>{{ $titolo }}</td>
    <td>{{ $dataInserimento }}</td>
    <td>{{ $dataScadenza }}</td>
    <td>{{ $dataCompletamento }}</td>
</tr>
<tr>
    <td colspan="4">{!! nl2br($descrizione) !!}</td>
</tr>
