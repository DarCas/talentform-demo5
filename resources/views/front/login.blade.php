<form
    action="/login/login"
    method="post"
    style="width: 50%"
    class="mx-auto"
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
        <div class="col-12 col-md-6">
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
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input
                    required
                    name="passwd"
                    type="password"
                    class="form-control"
                    id="floatingPassword">
                <label for="floatingPassword">Password</label>
            </div>
        </div>

        <div class="col-12 pt-4">
            <button type="submit" class="btn btn-primary">Accedi</button>
        </div>
    </div>
</form>
