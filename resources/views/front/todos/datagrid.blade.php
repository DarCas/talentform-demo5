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
                    <td colspan="6" class="px-4 pt-4">
                        <div class="row">
                            <div class="col-3">
                                <!--div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ request()->query('perPage', 10) }}
                                </button>
                                <ul class="dropdown-menu shadow">
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ request()->fullUrlWithQuery(['perPage' => 10]) }}">10</a>
                                            <a class="dropdown-item"
                                               href="{{ request()->fullUrlWithQuery(['perPage' => 25]) }}">25</a>
                                            <a class="dropdown-item"
                                               href="{{ request()->fullUrlWithQuery(['perPage' => 50]) }}">50</a>
                                        </li>
                                    </ul>
                                </div-->
                            </div>
                            <div class="col-9">
                                {!! $pagination !!}
                            </div>
                        </div>
                    </td>
                </tr>
                </tfoot>

                <tbody>
                @forelse($todos as $todo)
                    <x-todos.tr-component
                        :dataInserimento="$todo->dataInserimentoHuman()"
                        :dataScadenza="$todo->dataScadenzaHuman()"
                        :dataCompletamento="$todo->dataCompletamentoHuman() ?? 'In corso...'"
                        :descrizione="$todo->descrizione"
                        :editing="$todo->id === $id"
                        :email="$todo->email"
                        :id="$todo->id"
                        :titolo="$todo->titolo"
                    />
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            Non ci sono ancora attività
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
