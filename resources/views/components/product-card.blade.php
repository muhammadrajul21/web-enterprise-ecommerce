@php
    /*
    |--------------------------------------------------------------------------
    | PRODUCT CARD DATA
    |--------------------------------------------------------------------------
    |
    | $product:
    | Product dengan relasi:
    | - segment
    | - category
    | - variants
    | - images
    |
    | $wishlist:
    | Opsional.
    | Default false karena fitur wishlist belum memiliki tabel/backend.
    |
    */

    $variants = $product->variants;


    /*
    |--------------------------------------------------------------------------
    | ACTIVE VARIANTS
    |--------------------------------------------------------------------------
    |
    | Hanya varian berstatus active yang boleh ditampilkan
    | kepada customer.
    |
    */

    $activeVariants = $variants
        ->where('status', 'active')
        ->values();


    /*
    |--------------------------------------------------------------------------
    | PRICE
    |--------------------------------------------------------------------------
    |
    | Harga hanya dihitung dari varian aktif.
    | Varian inactive tidak digunakan sebagai fallback.
    |
    */

    $minPrice = $activeVariants->isNotEmpty()
        ? (float) $activeVariants->min('price')
        : null;

    $maxPrice = $activeVariants->isNotEmpty()
        ? (float) $activeVariants->max('price')
        : null;


    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS
    |--------------------------------------------------------------------------
    |
    | Produk dianggap sold out apabila:
    |
    | 1. Tidak mempunyai varian aktif
    | 2. Semua varian aktif mempunyai stok 0
    |
    */

    $hasAvailableStock = $activeVariants
        ->where('stock', '>', 0)
        ->isNotEmpty();

    $soldOut = $activeVariants->isEmpty()
        || ! $hasAvailableStock;


    /*
    |--------------------------------------------------------------------------
    | COLOR COUNT
    |--------------------------------------------------------------------------
    |
    | Satu product variant merupakan kombinasi warna dan ukuran.
    | Di kartu produk lebih berguna menampilkan jumlah warna.
    |
    */

    $colorCount = $activeVariants
        ->pluck('color')
        ->filter()
        ->unique()
        ->count();


    /*
    |--------------------------------------------------------------------------
    | PRODUCT IMAGES
    |--------------------------------------------------------------------------
    |
    | Prioritas:
    |
    | 1. is_primary
    | 2. sort_order
    |
    | Untuk saat ini image_path memang menggunakan file lokal
    | di public/images/products.
    |
    */

    $images = $product->images
        ->sortBy([
            ['is_primary', 'desc'],
            ['sort_order', 'asc'],
        ])
        ->filter(function ($image) {
            return $image->image_path
                && file_exists(public_path($image->image_path));
        })
        ->values();


    $image = $images->get(0);

    $hoverImage = $images->get(1);


    /*
    |--------------------------------------------------------------------------
    | PRODUCT LABEL
    |--------------------------------------------------------------------------
    |
    | Hanya satu label ditampilkan.
    |
    | Prioritas:
    | Sold Out > Featured > New
    |
    */

    $label = null;


    if ($soldOut) {

        $label = [
            'Sold out',
            'is-soldout',
        ];

    } elseif ($product->is_featured) {

        $label = [
            'Featured',
            '',
        ];

    } elseif (
        $product->created_at
        && $product->created_at->gt(
            now()->subDays(30)
        )
    ) {

        $label = [
            'New',
            '',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAIL URL
    |--------------------------------------------------------------------------
    */

    $url = route(
        'product.detail',
        $product->slug
    );

@endphp


<article
    class="
        product-card
        {{ $soldOut ? 'is-soldout' : '' }}
        {{ $hoverImage ? 'has-hover-image' : '' }}
    "
>


    {{-- ========================
        PRODUCT IMAGE
    ======================== --}}

    <div class="product-image-wrapper">


        <div class="product-image">


            @if ($image)


                {{-- MAIN IMAGE --}}

                <img
                    class="product-image-main"

                    src="{{ asset(
                        $image->image_path
                    ) }}"

                    alt="{{ $product->name }}"

                    loading="lazy"

                    decoding="async"
                >


                {{-- HOVER IMAGE --}}

                @if ($hoverImage)

                    <img
                        class="product-image-hover"

                        src="{{ asset(
                            $hoverImage->image_path
                        ) }}"

                        alt=""

                        loading="lazy"

                        decoding="async"
                    >

                @endif


            @else


                {{-- IMAGE PLACEHOLDER --}}

                <div class="product-image-placeholder">

                    <i class="bi bi-image"></i>

                    <span>
                        {{ $product->name }}
                    </span>

                </div>


            @endif


        </div>



        {{-- ========================
            PRODUCT LABEL
        ======================== --}}

        @if ($label)

            <span
                class="
                    product-label
                    {{ $label[1] }}
                "
            >

                {{ $label[0] }}

            </span>

        @endif



        {{-- ========================
            WISHLIST
        ======================== --}}

        @if ($wishlist ?? false)

            <button
                type="button"

                class="wishlist-button"

                aria-label="
                    Add {{ $product->name }}
                    to wishlist
                "
            >

                <i class="bi bi-heart"></i>

            </button>

        @endif


    </div>



    {{-- ========================
        PRODUCT INFORMATION
    ======================== --}}

    <div class="product-info">


        {{-- PRODUCT NAME --}}

        <h3 class="product-title">

            <a href="{{ $url }}">

                {{ $product->name }}

            </a>

        </h3>



        {{-- SEGMENT & CATEGORY --}}

        @if(
            $product->segment
            || $product->category
        )

            <p class="product-category">

                {{ $product->segment?->name }}

                @if(
                    $product->segment
                    && $product->category
                )

                    &middot;

                @endif

                {{ $product->category?->name }}

            </p>

        @endif



        {{-- COLOR COUNT --}}

        @if ($colorCount > 0)

            <p class="product-colors">

                {{ $colorCount }}

                {{ $colorCount === 1
                    ? 'color'
                    : 'colors'
                }}

            </p>

        @endif



        {{-- PRICE --}}

        @if ($minPrice !== null)

            <p class="product-price">


                @if (
                    $maxPrice !== null
                    && $maxPrice > $minPrice
                )

                    <span class="product-price-from">

                        From

                    </span>

                @endif


                Rp {{ number_format(
                    $minPrice,
                    0,
                    ',',
                    '.'
                ) }}


            </p>

        @else

            <p class="product-price product-price-unavailable">

                Currently unavailable

            </p>

        @endif


    </div>


</article>