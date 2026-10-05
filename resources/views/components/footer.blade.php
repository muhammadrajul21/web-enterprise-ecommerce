<footer class="site-footer">

    <div class="footer-container">

        <div class="footer-brand">
            <a href="{{ route('home.preview') }}" class="footer-logo">Lifestyle</a>

            <p class="footer-tagline">
                Everyday pieces for the way you live: clothing, footwear, and accessories made to be worn on repeat.
            </p>

            <ul class="footer-social">
                <li><a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a></li>
                <li><a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a></li>
                <li><a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a></li>
                <li><a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a></li>
            </ul>
        </div>

        <details class="footer-group" open>
            <summary>
                <h3>Shop</h3>
                <i class="bi bi-plus-lg"></i>
            </summary>
            <ul>
                <li><a href="{{ route('catalog.preview', ['sort' => 'latest']) }}">New arrivals</a></li>
                @foreach ($navSegments as $segment)
                    <li>
                        <a href="{{ route('catalog.preview', ['segment' => $segment->slug]) }}">
                            {{ $segment->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </details>

        <details class="footer-group" open>
            <summary>
                <h3>Categories</h3>
                <i class="bi bi-plus-lg"></i>
            </summary>
            <ul>
                @foreach ($navCategories as $category)
                    <li>
                        <a href="{{ route('catalog.preview', ['category' => $category->slug]) }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </details>

        <details class="footer-group" open>
            <summary>
                <h3>Support</h3>
                <i class="bi bi-plus-lg"></i>
            </summary>
            <ul>
                <li><a href="#">Help</a></li>
                <li><a href="#">Shipping</a></li>
                <li><a href="#">Returns</a></li>
                <li><a href="#">Contact us</a></li>
            </ul>
        </details>

        <details class="footer-group" open>
            <summary>
                <h3>About</h3>
                <i class="bi bi-plus-lg"></i>
            </summary>
            <ul>
                <li><a href="#">Our story</a></li>
                <li><a href="{{ route('catalog.preview') }}">All products</a></li>
                <li><a href="{{ route('search.preview') }}">Search</a></li>
            </ul>
        </details>

    </div>

    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} Lifestyle Store. All rights reserved.</span>

        <ul class="footer-legal">
            <li><a href="#">Privacy policy</a></li>
            <li><a href="#">Terms of use</a></li>
        </ul>
    </div>

</footer>
