<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Lifestyle Store')</title>

    <link rel="stylesheet" href="{{ asset('css/store.css') }}">
</head>
<body>

    @include('components.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>