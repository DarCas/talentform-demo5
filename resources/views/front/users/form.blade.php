<div class="card shadow p-2">
    <form
        action="/users{{ $user ? "/$user->id" : '' }}"
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

        <h2 class="card-title">{{ $user ? 'Modifica' : 'Aggiungi' }} un utente</h2>
        <div class="card-text">
            <div class="row">
                <div class="col-12 py-3">
                    <input
                        value="{{ $user?->usernm ?? '' }}"
                        name="usernm"
                        type="email"
                        {{ $user ? 'readonly' : '' }}
                        class="form-control"
                        placeholder="Username"
                        required>
                </div>
                <div class="col-12 py-3">
                    <input
                        name="passwd"
                        type="password"
                        class="form-control"
                        placeholder="Password"
                        required>
                </div>
                <div class="col-12 py-3">
                    <input
                        name="passwd_confirmation"
                        type="password"
                        class="form-control"
                        placeholder="Conferma password"
                        required>
                </div>

                @if($user)
                    <div class="col-6">
                        <a href="/users" class="btn btn-secondary">Chiudi</a>
                    </div>
                @endif
                <div class="col-{{ $user ? 6 : 12 }} text-end">
                    <button type="submit" class="btn btn-primary">{{ $user ? 'Modifica' : 'Aggiungi' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
