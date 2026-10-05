@php
    // CSS khusus halaman dimuat otomatis berdasarkan nama route,
    // jadi view lama tidak perlu diubah.
    $pageStyles = [
        'home.preview'    => 'home',
        'catalog.preview' => 'catalog',
        'search.preview'  => 'search',
        'product.detail'  => 'product-detail',
    ];

    $pageCss = $pageStyles[request()->route()?->getName()] ?? null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">

    <title>@yield('title', 'Lifestyle Store')</title>

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    {{-- Base --}}
    <link rel="stylesheet" href="{{ asset('css/base/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/tokens.css') }}">

    {{-- Layout --}}
    <link rel="stylesheet" href="{{ asset('css/layout/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/footer.css') }}">

    {{-- Components --}}
    <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/section.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/product-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/motion.css') }}">

    {{-- Page --}}
    @if ($pageCss)
        <link rel="stylesheet" href="{{ asset('css/pages/' . $pageCss . '.css') }}">
    @endif

    @stack('styles')
</head>

<body>

    <a href="#main" class="skip-link">Skip to content</a>

    @include('components.navbar')

    <main id="main">
        @yield('content')
    </main>

    @include('components.footer')

    <script src="{{ asset('js/store.js') }}" defer></script>
    <script src="{{ asset('js/motion.js') }}" defer></script>
    @stack('scripts')

</body>
</html>
