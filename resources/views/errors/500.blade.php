<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Errore del server</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="//cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="text-center">
    <h1 class="display-4 text-danger">500</h1>
    <p class="lead">Si è verificato un errore imprevisto.</p>
    <a href="{{ url('/') }}" class="btn btn-dark mt-3">Torna alla Home</a>

    @if (config('app.debug'))
        <div class="container text-start mt-5">
            <h5>Dettagli:</h5>
            <div class="alert alert-warning">
                <pre class="mb-0">{{ $exception->getMessage() ?? 'Nessun dettaglio' }}</pre>
            </div>
        </div>
    @endif
</div>
</body>
</html>
