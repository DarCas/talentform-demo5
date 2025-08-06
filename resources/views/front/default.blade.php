<!doctype html>
<html lang="en" class="{{ $centered ? 'h-100' : '' }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="d-flex {{ $centered ? 'h-100 text-center' : '' }} text-bg-dark">
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <strong>DEMO 5</strong>
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                @if($user)
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    To-Do
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/">Lista</a></li>
                                    <li><a class="dropdown-item" href="/todos/backup">Backup</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Utenti
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/users">Lista</a></li>
                                    <li><a class="dropdown-item" href="/users/backup">Backup</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <x-user-component
                                    :user="$user"
                                />
                            </li>
                        </ul>
                        <form action="{{ url()->current() }}" method="get" class="d-flex" role="search">
                            <input
                                name="q"
                                value="{{ $q ?? '' }}"
                                type="search"
                                class="form-control me-2"
                                placeholder="Cerca {{ \Illuminate\Support\Str::startsWith(request()->path(), 'users') ? 'utente' : 'attività' }}"
                                onchange="this.form.submit();"
                            >
                            <button class="btn btn-success" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                @endif
            </div>
        </nav>
    </header>

    <main class="p-3">
        <hr class="pb-3">

        {!! $content !!}
    </main>

    <div class="container-fluid">
        <hr>

        <footer class="row">
            <div class="col-4 d-flex align-items-center">
                <div class="mb-3 text-body-secondary">
                    <a class="text-body-secondary" href="https://getbootstrap.com/"
                       target="_blank" title="Bootstrap" aria-label="Bootstrap"><i class="bi bi-bootstrap"></i></a>
                    &middot;&middot;&middot;
                    <strong>DarCas Software &copy; {{ date('Y') }}</strong>
                </div>
            </div>

            <ul class="nav col-8 justify-content-end list-unstyled d-flex">
                <li class="ms-3">
                    <a class="text-body-secondary" href="https://www.instagram.com/darcas"
                       target="_blank" aria-label="Instagram" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                </li>
                <li class="ms-3">
                    <a class="text-body-secondary" href="https://github.com/DarCas"
                       aria-label="GitHub" title="GitHub" target="_blank">
                        <i class="bi bi-github"></i>
                    </a>
                </li>
            </ul>
        </footer>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
</html>
