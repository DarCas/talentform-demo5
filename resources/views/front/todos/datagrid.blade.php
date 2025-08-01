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
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="6">
                        {!! $pagination !!}
                    </td>
                </tr>
                </tfoot>

                <tbody>
                @foreach($todos as $todo)
                    <x-todos.tr-component
                        :editing="$todo->id === $id"
                        :id="$todo->id"
                        :titolo="$todo->titolo"
                        :dataInserimento="$todo->dataInserimentoHuman()"
                        :dataScadenza="$todo->dataScadenzaHuman()"
                        :dataCompletamento="$todo->dataCompletamentoHuman() ?? 'In corso...'"
                        :descrizione="$todo->descrizione"
                    />
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
