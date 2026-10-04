@php
    $image = $product->images->first();
    $price = $product->variants->min('price');
@endphp

<article class="product-card">

    <div class="product-image-wrapper">

        <a href="#">

            <div class="product-image">

                @if($image && file_exists(public_path($image->image_path)))

                    <img
                        src="{{ asset($image->image_path) }}"
                        alt="{{ $product->name }}"
                    >

                @else

                    <div class="product-image-placeholder">

                        <i class="bi bi-image"></i>

                        <span>
                            {{ $product->name }}
                        </span>

                    </div>

                @endif

            </div>

        </a>

        <button
            type="button"
            class="wishlist-button"
            aria-label="Wishlist"
        >
            <i class="bi bi-heart"></i>
        </button>

        @if($product->is_featured)
            <span class="product-label">
                FEATURED
            </span>
        @endif

    </div>

    <div class="product-info">

        <h3>
            <a href="#">
                {{ $product->name }}
            </a>
        </h3>

        <p class="product-category">

            {{ $product->segment?->name }}

            @if($product->category)
                · {{ $product->category->name }}
            @endif

        </p>

        @if($product->variants->count() > 0)

            <p class="product-variants">
                {{ $product->variants->count() }}
                variants
            </p>

        @endif

        @if($price)

            <strong class="product-price">
                Rp {{ number_format($price, 0, ',', '.') }}
            </strong>

        @endif

    </div>

</article>