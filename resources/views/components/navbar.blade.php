<header class="site-header">

    <div class="topbar">
        FREE SHIPPING FOR SELECTED ORDERS
    </div>

    <nav class="navbar">

        <div class="navbar-brand">
            <a href="{{ route('home.preview') }}">
                LIFESTYLE
            </a>
        </div>

        <div class="navbar-menu">

            <a href="{{ route('catalog.preview', ['sort' => 'latest']) }}">
                NEW ARRIVALS
            </a>

            <a href="{{ route('catalog.preview', ['segment' => 'men']) }}">
                MEN
            </a>

            <a href="{{ route('catalog.preview', ['segment' => 'women']) }}">
                WOMEN
            </a>

            <a href="{{ route('catalog.preview', ['segment' => 'unisex']) }}">
                UNISEX
            </a>

            <a href="{{ route('catalog.preview', ['group' => 'clothing']) }}">
                CLOTHING
            </a>

            <a href="{{ route('catalog.preview', ['category' => 'footwear']) }}">
                FOOTWEAR
            </a>

            <a href="{{ route('catalog.preview', ['category' => 'accessories']) }}">
                ACCESSORIES
            </a>

        </div>

        <div class="navbar-actions">

            <a
                href="{{ route('search.preview') }}"
                class="nav-icon"
                aria-label="Search">
                <i class="bi bi-search"></i>
            </a>

            <a href="#" class="nav-icon">
                <i class="bi bi-person"></i>
            </a>

            <a href="#" class="nav-icon cart-icon">
                <i class="bi bi-bag"></i>
                <span class="cart-count">0</span>
            </a>

        </div>

    </nav>

</header>