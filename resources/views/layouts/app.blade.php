<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Портал звернень')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('services.index') }}">Портал звернень</a>
        <div class="d-flex gap-2 flex-wrap">
            @auth
                @if(auth()->user()->isClient())
                    <a href="{{ route('client-requests.index') }}" class="btn btn-outline-light btn-sm">Мої звернення</a>
                    <a href="{{ route('client-requests.create') }}" class="btn btn-primary btn-sm">Нове звернення</a>
                @endif
                @if(auth()->user()->isManager())
                    <a href="{{ route('manager.index') }}" class="btn btn-outline-light btn-sm">Керування</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Вийти ({{ auth()->user()->name }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Вхід</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Реєстрація</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
