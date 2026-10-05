@php

    $onCatalog = request()->route()?->getName() === 'catalog.preview';

    $filtered = request()->hasAny([
        'segment',
        'group',
        'category'
    ]);


    /*
    |--------------------------------------------------------------------------
    | NAVIGATION LINKS
    |--------------------------------------------------------------------------
    |
    | Satu sumber data digunakan untuk navbar desktop
    | dan drawer mobile.
    |
    */

    $navLinks = [];


    // NEW ARRIVALS

    $navLinks[] = [
        'label' => 'New arrivals',

        'url' => route(
            'catalog.preview',
            ['sort' => 'latest']
        ),

        'active' =>
            $onCatalog
            && ! $filtered
            && request('sort') === 'latest',
    ];


    // SEGMENTS

    foreach ($navSegments as $segment) {

        $navLinks[] = [

            'label' => $segment->name,

            'url' => route(
                'catalog.preview',
                ['segment' => $segment->slug]
            ),

            'active' =>
                $onCatalog
                && request('segment') === $segment->slug,
        ];
    }


    // CLOTHING GROUP

    $navLinks[] = [

        'label' => 'Clothing',

        'url' => route(
            'catalog.preview',
            ['group' => 'clothing']
        ),

        'active' =>
            $onCatalog
            && request('group') === 'clothing',
    ];


    // FOOTWEAR & ACCESSORIES

    foreach (
        [
            'footwear' => 'Footwear',
            'accessories' => 'Accessories'
        ]
        as $slug => $label
    ) {

        $navLinks[] = [

            'label' => $label,

            'url' => route(
                'catalog.preview',
                ['category' => $slug]
            ),

            'active' =>
                $onCatalog
                && request('category') === $slug,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ACCOUNT URL
    |--------------------------------------------------------------------------
    */

    $role = auth()->user()?->role?->name;

    $accountUrl = match ($role) {

        'customer' =>
            route('customer.account'),

        'admin' =>
            route('admin.dashboard'),

        'staff_gudang' =>
            route('staff.dashboard'),

        'owner' =>
            route('owner.dashboard'),

        default =>
            route('login'),
    };

@endphp



{{-- ========================
    TOP BAR
======================== --}}

<div class="topbar">

    Free shipping on selected orders

</div>



{{-- ========================
    HEADER
======================== --}}

<header class="site-header">


    <nav
        class="navbar"
        aria-label="Main"
    >


        {{-- MOBILE MENU BUTTON --}}

        <button
            type="button"
            class="nav-icon navbar-toggle"
            data-drawer-open
            aria-label="Open menu"
            aria-controls="nav-drawer"
            aria-expanded="false"
        >

            <i class="bi bi-list"></i>

        </button>



        {{-- BRAND --}}

        <div class="navbar-brand">

            <a href="{{ route('home.preview') }}">

                Lifestyle

            </a>

        </div>



        {{-- DESKTOP MENU --}}

        <ul class="navbar-menu">

            @foreach ($navLinks as $link)

                <li>

                    <a
                        href="{{ $link['url'] }}"
                        class="
                            navbar-link
                            {{ $link['active'] ? 'is-active' : '' }}
                        "
                        @if ($link['active'])
                            aria-current="page"
                        @endif
                    >

                        {{ $link['label'] }}

                    </a>

                </li>

            @endforeach

        </ul>



        {{-- NAVBAR ACTIONS --}}

        <div class="navbar-actions">


            {{-- SEARCH --}}

            <button
                type="button"
                class="nav-icon"
                data-search-toggle
                aria-label="Search"
                aria-controls="search-panel"
                aria-expanded="false"
            >

                <i class="bi bi-search"></i>

            </button>


            {{-- ACCOUNT --}}

            <a
                href="{{ $accountUrl }}"
                class="nav-icon"
                aria-label="
                    {{ auth()->check()
                        ? 'My account'
                        : 'Sign in'
                    }}
                "
            >

                <i class="bi bi-person"></i>

            </a>


            {{-- CART --}}

            <a
                href="#"
                class="nav-icon"
                aria-label="
                    Cart,
                    {{ $cartCount }}
                    items
                "
            >

                <i class="bi bi-bag"></i>


                @if ($cartCount > 0)

                    <span class="cart-count">

                        {{ $cartCount > 99
                            ? '99+'
                            : $cartCount
                        }}

                    </span>

                @endif

            </a>


        </div>

    </nav>



    {{-- ========================
        SEARCH PANEL
    ======================== --}}

    <div
        class="search-panel"
        id="search-panel"
        hidden
    >

        <form
            action="{{ route('search.preview') }}"
            method="GET"
            role="search"
        >

            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search products"
                aria-label="Search products"
                autocomplete="off"
            >

            <button
                type="submit"
                aria-label="Submit search"
            >

                <i class="bi bi-arrow-right"></i>

            </button>

        </form>

    </div>


</header>



{{-- ========================
    MOBILE DRAWER
======================== --}}

<div
    class="drawer"
    id="nav-drawer"
    aria-hidden="true"
>


    <div
        class="drawer-backdrop"
        data-drawer-close
    ></div>


    <aside
        class="drawer-panel"
        role="dialog"
        aria-modal="true"
        aria-label="Menu"
    >


        {{-- DRAWER HEADER --}}

        <div class="drawer-head">


            <a
                href="{{ route('home.preview') }}"
                class="drawer-brand"
            >

                Lifestyle

            </a>


            <button
                type="button"
                class="nav-icon"
                data-drawer-close
                aria-label="Close menu"
            >

                <i class="bi bi-x-lg"></i>

            </button>


        </div>



        {{-- DRAWER LINKS --}}

        <ul class="drawer-links">

            @foreach ($navLinks as $link)

                <li>

                    <a
                        href="{{ $link['url'] }}"
                        class="
                            {{ $link['active']
                                ? 'is-active'
                                : ''
                            }}
                        "
                        @if ($link['active'])
                            aria-current="page"
                        @endif
                    >

                        {{ $link['label'] }}

                        <i class="bi bi-chevron-right"></i>

                    </a>

                </li>

            @endforeach

        </ul>



        {{-- DRAWER ACCOUNT --}}

        <div class="drawer-account">


            @auth

                <a
                    href="{{ $accountUrl }}"
                    class="btn-primary-store"
                >

                    My account

                </a>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                >

                    @csrf


                    <button
                        type="submit"
                        class="btn-outline-store"
                    >

                        Sign out

                    </button>

                </form>


            @else


                <a
                    href="{{ route('login') }}"
                    class="btn-primary-store"
                >

                    Sign in

                </a>


                <a
                    href="{{ route('register') }}"
                    class="btn-outline-store"
                >

                    Create account

                </a>


            @endauth


        </div>


    </aside>

</div>