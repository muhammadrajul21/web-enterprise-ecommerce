@extends('layouts.store')


@section('title', 'Order Detail')


@section('content')


@php

    $statusLabels = [

        'pending_payment' => 'Pending Payment',

        'waiting_verification' => 'Waiting Verification',

        'processing' => 'Processing',

        'packing' => 'Packing',

        'shipped' => 'Shipped',

        'completed' => 'Completed',

        'cancelled' => 'Cancelled',

    ];


    $statusLabel = $statusLabels[$order->status]

        ?? ucwords(str_replace('_', ' ', $order->status));


    $steps = [

        'pending_payment',

        'waiting_verification',

        'processing',

        'packing',

        'shipped',

        'completed',

    ];


    $currentIndex = array_search(

        $order->status,

        $steps,

        true

    );

@endphp


<section class="order-detail-page">


    <div class="order-detail-container">


        {{-- BACK --}}

        <a

            href="{{ route('orders.index') }}"

            class="order-detail-back"

        >

            <i class="bi bi-arrow-left"></i>

            Back to My Orders

        </a>


        {{-- HEADER --}}

        <div class="order-detail-header">


            <div>


                <span class="order-detail-eyebrow">

                    ORDER DETAIL

                </span>


                <h1>

                    {{ $order->order_code }}

                </h1>


                <p>

                    Placed on

                    {{ $order->created_at->format('d M Y, H:i') }}

                </p>


            </div>


            <span

                class="

                    detail-status

                    detail-status-{{ $order->status }}

                "

            >

                {{ $statusLabel }}

            </span>


        </div>


        {{-- FLASH --}}

        @if(session('success'))


            <div class="detail-alert detail-alert-success">


                <i class="bi bi-check-circle"></i>


                {{ session('success') }}


            </div>


        @endif


        @if(session('error'))


            <div class="detail-alert detail-alert-error">


                <i class="bi bi-exclamation-circle"></i>


                {{ session('error') }}


            </div>


        @endif


        @if($errors->any())

            <div class="detail-alert detail-alert-error">

                <i class="bi bi-exclamation-triangle"></i>

                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>

            </div>

        @endif


        {{-- CANCELLED --}}

        @if($order->status === 'cancelled')


            <div class="cancelled-box">


                <i class="bi bi-x-circle"></i>


                <div>


                    <strong>

                        Order Cancelled

                    </strong>


                    <p>

                        {{ $order->cancel_reason

                            ?? 'This order has been cancelled.' }}

                    </p>


                </div>


            </div>


        @else


            {{-- TIMELINE --}}

            <section class="detail-card">


                <div class="detail-card-heading">


                    <div class="detail-heading-icon">

                        <i class="bi bi-truck"></i>

                    </div>


                    <div>


                        <h2>

                            Order Progress

                        </h2>


                        <p>

                            Track the current progress of your order.

                        </p>


                    </div>


                </div>


                <div class="order-timeline">


                    @foreach($steps as $index => $step)


                        @php

                            $stepLabels = [

                                'pending_payment' => 'Payment',

                                'waiting_verification' => 'Verification',

                                'processing' => 'Processing',

                                'packing' => 'Packing',

                                'shipped' => 'Shipped',

                                'completed' => 'Completed',

                            ];


                            $isActive =

                                $currentIndex !== false

                                && $index <= $currentIndex;


                            $isCurrent =

                                $currentIndex !== false

                                && $index === $currentIndex;

                        @endphp


                        <div

                            class="

                                timeline-step

                                {{ $isActive ? 'active' : '' }}

                                {{ $isCurrent ? 'current' : '' }}

                            "

                        >


                            <div class="timeline-dot">


                                @if($index < $currentIndex)


                                    <i class="bi bi-check"></i>


                                @else


                                    {{ $index + 1 }}


                                @endif


                            </div>


                            <span>

                                {{ $stepLabels[$step] }}

                            </span>


                        </div>


                    @endforeach


                </div>


            </section>


        @endif


        {{-- CONFIRM ORDER RECEIVED --}}
        @if($order->status === 'shipped')

            <section class="order-complete-card">

                <div class="order-complete-content">

                    <div class="order-complete-icon">
                        <i class="bi bi-box2-heart"></i>
                    </div>

                    <div>

                        <span class="order-complete-label">
                            ORDER DELIVERED?
                        </span>

                        <h2>
                            Have you received your order?
                        </h2>

                        <p>
                            Confirm that the order has arrived safely.
                            After confirmation, the order will be marked as completed
                            and you can review the product.
                        </p>

                    </div>

                </div>


                <form
                    action="{{ route('orders.complete', $order) }}"
                    method="POST"
                    onsubmit="return confirm('Confirm that you have received this order?')"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="confirm-received-button"
                    >
                        CONFIRM ORDER RECEIVED

                        <i class="bi bi-check2-circle"></i>
                    </button>

                </form>

            </section>

        @endif


        <div class="order-detail-layout">


            {{-- LEFT --}}

            <div class="order-detail-main">


                {{-- PRODUCTS --}}

                <section class="detail-card">


                    <div class="detail-card-heading">


                        <div class="detail-heading-icon">

                            <i class="bi bi-bag"></i>

                        </div>


                        <div>


                            <h2>

                                Products

                            </h2>


                            <p>

                                Items included in this order.

                            </p>


                        </div>


                    </div>


                    <div class="detail-products">


                        @foreach($order->items as $item)

                        <div class="detail-product-wrapper">

                            <div class="detail-product">

                                <div class="detail-product-icon">
                                    <i class="bi bi-box"></i>
                                </div>

                                <div class="detail-product-info">

                                    <strong>
                                        {{ $item->product_name }}
                                    </strong>

                                    <span>
                                        SKU: {{ $item->sku ?: '-' }}
                                    </span>

                                    <span>
                                        {{ $item->color ?: '-' }}
                                        /
                                        {{ $item->size ?: '-' }}
                                    </span>

                                </div>

                                <div class="detail-product-qty">

                                    <span>QTY</span>

                                    <strong>
                                        {{ $item->quantity }}
                                    </strong>

                                </div>

                                <div class="detail-product-price">

                                    <span>PRICE</span>

                                    <strong>
                                        Rp {{ number_format(
                                            $item->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>

                                </div>

                            </div>


                            {{-- PRODUCT REVIEW --}}
                            @if($order->status === 'completed')

                                <div class="product-review-section">

                                    @if($item->review)

                                        <div class="submitted-review">

                                            <div class="submitted-review-header">

                                                <div>
                                                    <span class="review-label">
                                                        YOUR REVIEW
                                                    </span>

                                                    <div class="review-stars">
                                                        @for($star = 1; $star <= 5; $star++)
                                                            <i class="bi {{ $star <= $item->review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <span class="review-rating-number">
                                                    {{ $item->review->rating }}/5
                                                </span>

                                            </div>

                                            @if($item->review->review)
                                                <p class="submitted-review-text">
                                                    {{ $item->review->review }}
                                                </p>
                                            @endif

                                            <span class="review-submitted-status">
                                                <i class="bi bi-check-circle"></i>
                                                Review submitted
                                            </span>

                                        </div>

                                    @else

                                        <div class="write-review">

                                            <div class="write-review-heading">

                                                <div>
                                                    <span class="review-label">
                                                        PRODUCT REVIEW
                                                    </span>

                                                    <h3>How was this product?</h3>

                                                    <p>
                                                        Share your experience with this product.
                                                    </p>
                                                </div>

                                                <i class="bi bi-chat-square-heart"></i>

                                            </div>

                                            <form
                                                action="{{ route('customer.reviews.store', [
                                                    'order' => $order,
                                                    'orderItem' => $item
                                                ]) }}"
                                                method="POST"
                                                class="review-form"
                                            >
                                                @csrf

                                                <div class="review-form-group">

                                                    <label>Rating</label>

                                                    <div class="review-rating-options">
                                                        @for($rating = 5; $rating >= 1; $rating--)
                                                            <label class="review-rating-option">
                                                                <input
                                                                    type="radio"
                                                                    name="rating"
                                                                    value="{{ $rating }}"
                                                                    {{ old('rating') == $rating ? 'checked' : '' }}
                                                                    required
                                                                >

                                                                <span>
                                                                    {{ $rating }}
                                                                    <i class="bi bi-star-fill"></i>
                                                                </span>
                                                            </label>
                                                        @endfor
                                                    </div>

                                                </div>

                                                <div class="review-form-group">

                                                    <label for="review-{{ $item->id }}">
                                                        Review
                                                        <small>(optional)</small>
                                                    </label>

                                                    <textarea
                                                        id="review-{{ $item->id }}"
                                                        name="review"
                                                        rows="4"
                                                        maxlength="1000"
                                                        placeholder="Tell us what you think about this product..."
                                                    >{{ old('review') }}</textarea>

                                                </div>

                                                <button
                                                    type="submit"
                                                    class="review-submit-button"
                                                >
                                                    SUBMIT REVIEW
                                                    <i class="bi bi-arrow-right"></i>
                                                </button>

                                            </form>

                                        </div>

                                    @endif

                                </div>

                            @endif

                        </div>

                    @endforeach


                    </div>


                </section>


                {{-- SHIPPING ADDRESS --}}

                <section class="detail-card">


                    <div class="detail-card-heading">


                        <div class="detail-heading-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>


                        <div>


                            <h2>

                                Shipping Address

                            </h2>


                            <p>

                                Delivery information for this order.

                            </p>


                        </div>


                    </div>


                    <div class="shipping-detail">


                        <strong>

                            {{ $order->recipient_name }}

                        </strong>


                        <span>

                            {{ $order->recipient_phone }}

                        </span>


                        <p>

                            {{ $order->shipping_address }}<br>

                            {{ $order->shipping_city }},

                            {{ $order->shipping_province }}

                            {{ $order->shipping_postal_code }}

                        </p>


                    </div>


                </section>


                {{-- SHIPPING INFORMATION --}}

                <section class="detail-card">


                    <div class="detail-card-heading">


                        <div class="detail-heading-icon">

                            <i class="bi bi-truck"></i>

                        </div>


                        <div>


                            <h2>

                                Shipping Information

                            </h2>


                            <p>

                                Courier and tracking information.

                            </p>


                        </div>


                    </div>


                    <div class="detail-info-grid">


                        <div>


                            <span>

                                SHIPPING SERVICE

                            </span>


                            <strong>

                                {{ $order->shipping_service ?? '-' }}

                            </strong>


                        </div>


                        <div>


                            <span>

                                COURIER

                            </span>


                            <strong>

                                {{ $order->courier ?? 'Not assigned yet' }}

                            </strong>


                        </div>


                        <div>


                            <span>

                                TRACKING NUMBER

                            </span>


                            <strong>

                                {{ $order->tracking_number

                                    ?? 'Not available yet' }}

                            </strong>


                        </div>


                        <div>


                            <span>

                                SHIPPED AT

                            </span>


                            <strong>

                                {{ $order->shipped_at

                                    ? $order->shipped_at->format(

                                        'd M Y, H:i'

                                    )

                                    : '-'

                                }}

                            </strong>


                        </div>


                    </div>


                </section>


                {{-- PAYMENT --}}

                <section class="detail-card">


                    <div class="detail-card-heading">


                        <div class="detail-heading-icon">

                            <i class="bi bi-credit-card"></i>

                        </div>


                        <div>


                            <h2>

                                Payment

                            </h2>


                            <p>

                                Payment information for this order.

                            </p>


                        </div>


                    </div>


                    @if($latestPayment)


                        <div class="detail-info-grid">


                            <div>


                                <span>

                                    METHOD

                                </span>


                                <strong>

                                    {{ ucwords(

                                        str_replace(

                                            '_',

                                            ' ',

                                            $latestPayment

                                                ->payment_method

                                        )

                                    ) }}

                                </strong>


                            </div>


                            <div>


                                <span>

                                    STATUS

                                </span>


                                <strong>

                                    {{ ucfirst(

                                        $latestPayment->status

                                    ) }}

                                </strong>


                            </div>


                            <div>


                                <span>

                                    AMOUNT

                                </span>


                                <strong>

                                    Rp {{ number_format(

                                        $latestPayment->amount,

                                        0,

                                        ',',

                                        '.'

                                    ) }}

                                </strong>


                            </div>


                            <div>


                                <span>

                                    PAID AT

                                </span>


                                <strong>

                                    {{ $latestPayment->paid_at

                                        ? $latestPayment->paid_at

                                            ->format('d M Y, H:i')

                                        : '-'

                                    }}

                                </strong>


                            </div>


                        </div>


                        @if(

                            $latestPayment->proof_image

                            && file_exists(

                                public_path(

                                    $latestPayment->proof_image

                                )

                            )

                        )


                            <div class="detail-proof">


                                <span>

                                    PAYMENT PROOF

                                </span>


                                <img

                                    src="{{ asset(

                                        $latestPayment->proof_image

                                    ) }}"

                                    alt="Payment proof"

                                >


                            </div>


                        @endif


                        @if(

                            $latestPayment->status === 'rejected'

                            && $latestPayment->rejection_reason

                        )


                            <div class="detail-rejection">


                                <strong>

                                    Rejection Reason

                                </strong>


                                <p>

                                    {{ $latestPayment

                                        ->rejection_reason }}

                                </p>


                            </div>


                        @endif


                    @else


                        <div class="no-payment">


                            <i class="bi bi-credit-card"></i>


                            <div>


                                <strong>

                                    No Payment Submitted

                                </strong>


                                <p>

                                    Complete your payment to

                                    continue processing this order.

                                </p>


                            </div>


                        </div>


                    @endif


                </section>


            </div>


            {{-- RIGHT --}}

            <aside class="order-detail-sidebar">


                <section class="detail-summary-card">


                    <h2>

                        Order Summary

                    </h2>


                    <div class="detail-summary-row">


                        <span>

                            Subtotal

                        </span>


                        <strong>

                            Rp {{ number_format(

                                $order->subtotal,

                                0,

                                ',',

                                '.'

                            ) }}

                        </strong>


                    </div>


                    <div class="detail-summary-row">


                        <span>

                            Discount

                        </span>


                        <strong>

                            - Rp {{ number_format(

                                $order->discount_amount,

                                0,

                                ',',

                                '.'

                            ) }}

                        </strong>


                    </div>


                    <div class="detail-summary-row">


                        <span>

                            Shipping

                        </span>


                        <strong>

                            Rp {{ number_format(

                                $order->shipping_cost,

                                0,

                                ',',

                                '.'

                            ) }}

                        </strong>


                    </div>


                    @if($order->voucher)


                        <div class="voucher-info">


                            <span>

                                VOUCHER

                            </span>


                            <strong>

                                {{ $order->voucher->code }}

                            </strong>


                        </div>


                    @endif


                    <div class="detail-summary-divider"></div>


                    <div class="detail-summary-total">


                        <span>

                            TOTAL

                        </span>


                        <strong>

                            Rp {{ number_format(

                                $order->total_amount,

                                0,

                                ',',

                                '.'

                            ) }}

                        </strong>


                    </div>


                    {{-- ACTIONS --}}


                    @if(

                        $order->status

                        === 'pending_payment'

                    )


                        <a

                            href="{{ route(

                                'payment.show',

                                $order

                            ) }}"

                            class="detail-primary-button"

                        >

                            PAY NOW


                            <i class="bi bi-arrow-right"></i>

                        </a>


                    @elseif(

                        $order->status

                        === 'waiting_verification'

                    )


                        <a

                            href="{{ route(

                                'payment.show',

                                $order

                            ) }}"

                            class="detail-secondary-button"

                        >

                            VIEW PAYMENT


                            <i class="bi bi-arrow-right"></i>

                        </a>


                    @endif


                    <a

                        href="{{ route('orders.index') }}"

                        class="detail-back-button"

                    >

                        <i class="bi bi-arrow-left"></i>


                        MY ORDERS

                    </a>


                </section>


            </aside>


        </div>


    </div>


</section>


@endsection
