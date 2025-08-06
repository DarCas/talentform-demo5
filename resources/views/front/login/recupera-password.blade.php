<form
    action="/login/invia-password"
    class="mx-auto"
    method="post"
    style="width: 50%"
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

    <div class="row">
        <div class="col-12 text-center mb-3">
            <h2>Recupera la password</h2>
        </div>
        <div class="col-12 col-md-6 offset-md-3">
            <div class="form-floating">
                <input
                    required
                    name="usernm"
                    type="email"
                    class="form-control"
                    id="floatingInput"
                    aria-describedby="emailHelp">
                <label for="floatingInput">Username</label>
            </div>
        </div>

        <div class="col-12 pt-4">
            <a class="btn btn-primary me-2" href="/">Annulla</a>
            <button type="submit" class="btn btn-success">Recupera</button>
        </div>
    </div>
</form>
