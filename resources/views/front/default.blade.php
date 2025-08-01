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
    <header class="mb-auto">
        <div class="row">
            <div class="col-4">
                <h3 class="mb-0">Todo List</h3>
            </div>
            <div class="col-4">
                <form action="/" method="get">
                    <div class="input-group">
                        <input
                            name="q"
                            value="{{ $q ?? '' }}"
                            type="search"
                            class="form-control"
                            placeholder="Cerca attività"
                            onchange="this.form.submit();"
                        >
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-4 text-end">
                @if($user)


                    <x-user-component
                        :user="$user"
                    />
                @endif
            </div>
        </div>
    </header>

    <main class="p-3">
        {!! $content !!}
    </main>

    <footer class="mt-auto text-white-50 text-end">
        <a href="https://getbootstrap.com/" class="text-white">Bootstrap</a>
    </footer>
</div>

<script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
</html>
