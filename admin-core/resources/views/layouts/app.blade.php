<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Panel de inventario')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('productos.index') }}">Inventario - Admin</a>
        @auth
            <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center gap-3">
                @csrf
                <span class="text-white small">{{ auth()->user()->nombre }}</span>
                <button class="btn btn-sm btn-outline-light">Salir</button>
            </form>
        @endauth
    </div>
</nav>

<div class="container">
    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif
    @yield('contenido')
</div>
</body>
</html>
