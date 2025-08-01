<div class="card shadow p-2">
    <form
        action="/todos{{ $todo ? "/$todo->id" : '' }}"
        method="post"
        class="card-body"
    >
        @csrf

        @if($errors)
            <div
                class="alert alert-danger mx-auto shadow-lg"
                style="width: 100%"
                role="alert"
            >
                <h3>Si sono verificati errori:</h3>
                <ul>
                    @foreach ($errors as $key => $value)
                        <li>{{ $key }}: {{ $value }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="card-title">{{ $todo ? 'Modifica' : 'Aggiungi' }} un todo</h2>
        <div class="card-text">
            <div class="row">
                <div class="col-12 py-3">
                    <input
                        value="{{ $todo?->titolo ?? '' }}"
                        name="titolo"
                        type="text"
                        class="form-control"
                        placeholder="Titolo"
                        required>
                </div>
                <div class="col-12">
                    <textarea
                        name="descrizione"
                        class="form-control"
                        placeholder="Descrizione"
                        required>{{ $todo?->descrizione ?? '' }}</textarea>
                </div>
                <div class="col-12 py-3">
                    <label for="dataInserimento">Data inizio</label>
                    <input
                        value="{{ $todo?->dataInserimentoHTML() }}"
                        name="dataInserimento"
                        type="date"
                        class="form-control"
                        required>
                </div>
                <div class="col-12">
                    <label for="dataScadenza">Data scadenza</label>
                    <input
                        value="{{ $todo?->dataScadenzaHTML() }}"
                        name="dataScadenza"
                        type="date"
                        class="form-control"
                    >
                </div>
                <div class="col-12 py-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="email"
                            value="1"
                            {{ $todo?->email ? 'checked' : '' }}
                            id="checkDefault">
                        <label class="form-check-label" for="checkDefault">
                            Ricevi avviso
                        </label>
                    </div>
                </div>
                @if($todo)
                    <div class="col-6">
                        <a href="/" class="btn btn-secondary">Chiudi</a>
                    </div>
                @endif
                <div class="col-{{ $todo ? 6 : 12 }} text-end">
                    <button type="submit" class="btn btn-primary">{{ $todo ? 'Modifica' : 'Aggiungi' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
