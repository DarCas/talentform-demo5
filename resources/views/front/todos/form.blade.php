<div class="card shadow p-2">
    <form
        action="/todos/add"
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

        <h2 class="card-title">Aggiungi un todo</h2>
        <div class="card-text">
            <div class="row">
                <div class="col-12 py-3">
                    <input
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
                        required></textarea>
                </div>
                <div class="col-12 py-3">
                    <label for="dataInserimento">Data inserimento</label>
                    <input
                        name="dataInserimento"
                        type="date"
                        class="form-control"
                        required>
                </div>
                <div class="col-12">
                    <label for="dataScadenza">Data scadenza</label>
                    <input
                        name="dataScadenza"
                        type="date"
                        class="form-control"
                    >
                </div>
                <div class="col-12 py-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="email" value="1" id="checkDefault">
                        <label class="form-check-label" for="checkDefault">
                            Ricevi avviso
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </div>
        </div>
    </form>
</div>
