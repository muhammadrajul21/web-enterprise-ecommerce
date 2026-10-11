<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Operational Summary</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/dashboard.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/operations.css') }}"
    >
</head>

<body>

<div class="owner-layout">

    {{-- SIDEBAR --}}
    <aside class="owner-sidebar">

        <div class="sidebar-brand">

            <h2>
                LIFESTYLE
            </h2>

            <span>
                OWNER PANEL
            </span>

        </div>


        <nav class="sidebar-menu">

            <a href="{{ route('owner.dashboard') }}">
                <i class="bi bi-grid"></i>
                Dashboard
            </a>

            <a href="{{ route('owner.reports.index') }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                Reports
            </a>

            <a
                href="{{ route('owner.operations.index') }}"
                class="active"
            >
                <i class="bi bi-activity"></i>
                Operations
            </a>

        </nav>


        <div class="sidebar-footer">

            <div class="owner-user">

                <div class="owner-avatar">
                    {{ strtoupper(
                        substr(
                            auth()->user()->name,
                            0,
                            1
                        )
                    ) }}
                </div>

                <div>

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        Owner
                    </span>

                </div>

            </div>


            <form
                action="{{ route('logout') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </button>

            </form>

        </div>

    </aside>


    {{-- MAIN --}}
    <main class="owner-main">

        {{-- HEADER --}}
        <div class="page-header">

            <div>

                <span class="page-label">
                    OPERATION MONITORING
                </span>

                <h1>
                    Operational Summary
                </h1>

                <p>
                    Monitor inventory activity and customer feedback.
                </p>

            </div>

            <div class="header-icon">
                <i class="bi bi-activity"></i>
            </div>

        </div>


        {{-- INVENTORY SUMMARY --}}
        <section class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-boxes"></i>
                </div>

                <div class="stat-content">

                    <span>
                        TOTAL VARIANTS
                    </span>

                    <strong>
                        {{ $totalVariants }}
                    </strong>

                    <p>
                        Active product variants
                    </p>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div class="stat-content">

                    <span>
                        TOTAL STOCK
                    </span>

                    <strong>
                        {{ $totalStock }}
                    </strong>

                    <p>
                        Current inventory units
                    </p>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon warning-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>

                <div class="stat-content">

                    <span>
                        LOW STOCK
                    </span>

                    <strong>
                        {{ $lowStockCount }}
                    </strong>

                    <p>
                        Variants with stock 1–5
                    </p>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon danger-icon">
                    <i class="bi bi-x-octagon"></i>
                </div>

                <div class="stat-content">

                    <span>
                        OUT OF STOCK
                    </span>

                    <strong>
                        {{ $outOfStockCount }}
                    </strong>

                    <p>
                        Variants with no stock
                    </p>

                </div>

            </div>

        </section>


        {{-- STOCK ACTIVITY --}}
        <section class="dashboard-card">

            <div class="card-header">

                <div>

                    <h2>
                        Recent Stock Activity
                    </h2>

                    <p>
                        Latest inventory movements recorded by the system.
                    </p>

                </div>

                <i class="bi bi-clock-history"></i>

            </div>


            <div class="operations-table-wrapper">

                <table class="operations-table">

                    <thead>

                    <tr>
                        <th>PRODUCT</th>
                        <th>SKU</th>
                        <th>TYPE</th>
                        <th>QTY</th>
                        <th>STOCK CHANGE</th>
                        <th>CREATED BY</th>
                        <th>DATE</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse($recentStockLogs as $log)

                        <tr>

                            <td>
                                <strong>
                                    {{ $log->variant?->product?->name
                                        ?? 'Product' }}
                                </strong>

                                <span class="table-subtext">
                                    {{ $log->variant?->color ?? '-' }}
                                    /
                                    {{ $log->variant?->size ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $log->variant?->sku ?? '-' }}
                            </td>

                            <td>

                                <span
                                    class="
                                        stock-type
                                        stock-type-{{ strtolower($log->type) }}
                                    "
                                >
                                    {{ strtoupper($log->type) }}
                                </span>

                            </td>

                            <td>
                                <strong>
                                    {{ $log->quantity }}
                                </strong>
                            </td>

                            <td>

                                <span class="stock-change">
                                    {{ $log->stock_before }}
                                    <i class="bi bi-arrow-right"></i>
                                    {{ $log->stock_after }}
                                </span>

                            </td>

                            <td>
                                {{ $log->creator?->name ?? 'System' }}
                            </td>

                            <td>

                                {{ $log->created_at->format('d M Y') }}

                                <span class="table-subtext">
                                    {{ $log->created_at->format('H:i') }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="operations-empty"
                            >
                                <i class="bi bi-clock-history"></i>

                                <strong>
                                    No stock activity yet
                                </strong>

                                <span>
                                    Stock movements will appear here.
                                </span>
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </section>


        {{-- REVIEW SUMMARY --}}
        <div class="operations-grid">

            <section class="dashboard-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Review Summary
                        </h2>

                        <p>
                            Customer rating overview.
                        </p>

                    </div>

                    <i class="bi bi-star"></i>

                </div>


                <div class="review-overview">

                    <div class="rating-score">

                        <strong>
                            {{ number_format($averageRating, 1) }}
                        </strong>

                        <span>
                            / 5
                        </span>

                        <div class="rating-stars">

                            @for($star = 1; $star <= 5; $star++)

                                <i class="
                                    bi
                                    {{ $star <= round($averageRating)
                                        ? 'bi-star-fill'
                                        : 'bi-star'
                                    }}
                                "></i>

                            @endfor

                        </div>

                        <small>
                            {{ $totalReviews }}
                            {{ $totalReviews === 1 ? 'review' : 'reviews' }}
                        </small>

                    </div>


                    <div class="rating-distribution">

                        @for($rating = 5; $rating >= 1; $rating--)

                            @php
                                $ratingTotal =
                                    $ratingCounts[$rating] ?? 0;

                                $percentage =
                                    $totalReviews > 0
                                        ? ($ratingTotal / $totalReviews) * 100
                                        : 0;
                            @endphp

                            <div class="rating-row">

                                <span>
                                    {{ $rating }}
                                    <i class="bi bi-star-fill"></i>
                                </span>

                                <div class="rating-bar">

                                    <div
                                        class="rating-bar-value"
                                        style="width: {{ $percentage }}%"
                                    ></div>

                                </div>

                                <strong>
                                    {{ $ratingTotal }}
                                </strong>

                            </div>

                        @endfor

                    </div>

                </div>

            </section>


            {{-- RECENT REVIEWS --}}
            <section class="dashboard-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Recent Reviews
                        </h2>

                        <p>
                            Latest feedback from customers.
                        </p>

                    </div>

                    <i class="bi bi-chat-square-text"></i>

                </div>


                <div class="recent-review-list">

                    @forelse($recentReviews as $review)

                        <div class="recent-review-item">

                            <div class="review-item-header">

                                <div>

                                    <strong>
                                        {{ $review->user?->name
                                            ?? 'Customer' }}
                                    </strong>

                                    <span>
                                        {{ $review
                                            ->orderItem
                                            ?->variant
                                            ?->product
                                            ?->name
                                            ?? $review
                                                ->orderItem
                                                ?->product_name
                                            ?? 'Product'
                                        }}
                                    </span>

                                </div>


                                <div class="review-item-rating">

                                    <i class="bi bi-star-fill"></i>

                                    {{ $review->rating }}/5

                                </div>

                            </div>


                            @if($review->review)

                                <p>
                                    {{ $review->review }}
                                </p>

                            @else

                                <p class="review-no-comment">
                                    No written review.
                                </p>

                            @endif


                            <small>
                                {{ $review->created_at
                                    ->format('d M Y, H:i') }}
                            </small>

                        </div>

                    @empty

                        <div class="review-empty">

                            <i class="bi bi-chat-square"></i>

                            <strong>
                                No reviews yet
                            </strong>

                            <span>
                                Customer reviews will appear here.
                            </span>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </main>

</div>

</body>

</html>