<div class="card shadow py-2">
    <div class="card-body p-0">
        <div class="card-text">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" class="text-end">#</th>
                    <th scope="col">Attività</th>
                    <th scope="col">Data inizio</th>
                    <th scope="col">Data scadenza</th>
                    <th scope="col">Data completamento</th>
                </tr>
                </thead>

                <tbody>
                @foreach($todos as $todo)
                    <tr>
                        <th rowspan="2" scope="row" class="text-end">{{ $todo->id }}</th>
                        <td>{{ $todo->titolo }}</td>
                        <td>{{ $todo->dataInserimentoHuman() }}</td>
                        <td>{{ $todo->dataScadenzaHuman() }}</td>
                        <td>{{ $todo->dataCompletamentoHuman() ?? 'In corso...' }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">{!! nl2br($todo->descrizione) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
