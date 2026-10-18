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

$statusLabel =
$statusLabels[$order->status]
?? ucwords(
str_replace(
'_',
' ',
$order->status
)
);

$steps = [
'pending_payment',
'waiting_verification',
'processing',
'packing',
'shipped',
'completed',
];

$stepLabels = [
'pending_payment' => 'Payment',
'waiting_verification' => 'Verification',
'processing' => 'Processing',
'packing' => 'Packing',
'shipped' => 'Shipped',
'completed' => 'Completed',
];

$currentIndex = array_search(
$order->status,
$steps,
true
);

$shipmentUpdates =
$shipmentUpdates ?? collect();

$lastKnownLocation =
$lastKnownLocation ?? null;
@endphp


<section class="order-detail-page">

    <div class="order-detail-container">


        {{-- ================================================= --}}
        {{-- BACK --}}
        {{-- ================================================= --}}

        <a
            href="{{ route('orders.index') }}"
            class="order-detail-back">
            <i class="bi bi-arrow-left"></i>

            Back to My Orders
        </a>


        {{-- ================================================= --}}
        {{-- HEADER --}}
        {{-- ================================================= --}}

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
                ">
                {{ $statusLabel }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ================================================= --}}

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

                <div>
                    {{ $error }}
                </div>

                @endforeach

            </div>

        </div>

        @endif


        {{-- ================================================= --}}
        {{-- CANCELLED --}}
        {{-- ================================================= --}}

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


        {{-- ================================================= --}}
        {{-- ORDER PROGRESS --}}
        {{-- ================================================= --}}

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
                $isActive =
                $currentIndex !== false
                && $index <= $currentIndex;

                    $isCurrent=$currentIndex !==false
                    && $index===$currentIndex;
                    @endphp


                    <div
                    class="
                                timeline-step
                                {{ $isActive ? 'active' : '' }}
                                {{ $isCurrent ? 'current' : '' }}
                            ">

                    <div class="timeline-dot">

                        @if(
                        $currentIndex !== false
                        && $index < $currentIndex
                            )

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


{{-- ================================================= --}}
{{-- POD INFORMATION --}}
{{-- ================================================= --}}

@if($order->status === 'shipped')

<section class="detail-card">

    <div class="detail-card-heading">

        <div class="detail-heading-icon">
            <i class="bi bi-box-seam"></i>
        </div>

        <div>

            <h2>
                Delivery in Progress
            </h2>

            <p>
                Your package is being handled by the courier.
                The order will be completed automatically after
                Proof of Delivery is verified.
            </p>

        </div>

    </div>

</section>

@endif


<div class="order-detail-layout">


    {{-- ================================================= --}}
    {{-- LEFT CONTENT --}}
    {{-- ================================================= --}}

    <div class="order-detail-main">


        {{-- ================================================= --}}
        {{-- PRODUCTS --}}
        {{-- ================================================= --}}

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
                                SKU:
                                {{ $item->sku ?: '-' }}
                            </span>

                            <span>
                                {{ $item->color ?: '-' }}
                                /
                                {{ $item->size ?: '-' }}
                            </span>

                        </div>


                        <div class="detail-product-qty">

                            <span>
                                QTY
                            </span>

                            <strong>
                                {{ $item->quantity }}
                            </strong>

                        </div>


                        <div class="detail-product-price">

                            <span>
                                PRICE
                            </span>

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

                                        @for(
                                        $star = 1;
                                        $star <= 5;
                                            $star++
                                            )

                                            <i
                                            class="
                                                                            bi
                                                                            {{
                                                                                $star <= $item->review->rating
                                                                                    ? 'bi-star-fill'
                                                                                    : 'bi-star'
                                                                            }}
                                                                        "></i>

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

                                    <h3>
                                        How was this product?
                                    </h3>

                                    <p>
                                        Share your experience
                                        with this product.
                                    </p>

                                </div>

                                <i class="bi bi-chat-square-heart"></i>

                            </div>


                            <form
                                action="{{ route(
                                                            'customer.reviews.store',
                                                            [
                                                                'order' => $order,
                                                                'orderItem' => $item,
                                                            ]
                                                        ) }}"
                                method="POST"
                                class="review-form">

                                @csrf


                                <div class="review-form-group">

                                    <label>
                                        Rating
                                    </label>


                                    <div class="review-rating-options">

                                        @for(
                                        $rating = 5;
                                        $rating >= 1;
                                        $rating--
                                        )

                                        <label class="review-rating-option">

                                            <input
                                                type="radio"
                                                name="rating"
                                                value="{{ $rating }}"
                                                {{ old('rating') == $rating
                                                                                ? 'checked'
                                                                                : '' }}
                                                required>

                                            <span>

                                                {{ $rating }}

                                                <i class="bi bi-star-fill"></i>

                                            </span>

                                        </label>

                                        @endfor

                                    </div>

                                </div>


                                <div class="review-form-group">

                                    <label
                                        for="review-{{ $item->id }}">
                                        Review

                                        <small>
                                            (optional)
                                        </small>
                                    </label>

                                    <textarea
                                        id="review-{{ $item->id }}"
                                        name="review"
                                        rows="4"
                                        maxlength="1000"
                                        placeholder="Tell us what you think about this product...">{{ old('review') }}</textarea>

                                </div>


                                <button
                                    type="submit"
                                    class="review-submit-button">
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


        {{-- ================================================= --}}
        {{-- SHIPPING ADDRESS --}}
        {{-- ================================================= --}}

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


        {{-- ================================================= --}}
        {{-- SHIPPING INFORMATION --}}
        {{-- ================================================= --}}

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
                        {{ $order->courier
                                        ?? 'Not assigned yet' }}
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
                                        ?->format('d M Y, H:i')
                                        ?? '-' }}
                    </strong>

                </div>

            </div>

        </section>
        {{-- ================================================= --}}
        {{-- SHIPMENT TRACKING --}}
        {{-- ================================================= --}}

        @if($order->shipped_at)

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-truck"></i>
                </div>

                <div>

                    <h2>
                        Shipment Tracking
                    </h2>

                    <p>
                        Shipping and delivery information for your order.
                    </p>

                </div>

            </div>


            {{-- PACKAGE SHIPPED --}}

            <div
                style="
                position: relative;
                padding-left: 32px;
            ">

                <div
                    style="
                    position: relative;
                    padding-bottom: 30px;
                ">

                    <span
                        style="
                        position: absolute;
                        left: -32px;
                        top: 2px;
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        background: #111;
                    "></span>


                    @if($delivery)

                    <div
                        style="
                            position: absolute;
                            left: -25px;
                            top: 18px;
                            bottom: 0;
                            width: 2px;
                            background: #e0e0e0;
                        "></div>

                    @endif


                    <strong
                        style="
                        display: block;
                        margin-bottom: 6px;
                    ">
                        Package Shipped
                    </strong>


                    <span
                        style="
                        display: block;
                        font-size: 13px;
                        color: #555;
                    ">
                        {{ $order->courier ?? 'Courier' }}

                        ·

                        {{ $order->tracking_number ?? '-' }}
                    </span>


                    <span
                        style="
                        display: block;
                        margin-top: 5px;
                        font-size: 12px;
                        color: #999;
                    ">
                        {{ $order->shipped_at
                        ?->format('d M Y, H:i')
                        ?? '-' }}
                    </span>

                </div>


                {{-- DELIVERED --}}

                @if($delivery)

                <div
                    style="
                        position: relative;
                        padding-bottom: 10px;
                    ">

                    <span
                        style="
                            position: absolute;
                            left: -32px;
                            top: 2px;
                            width: 16px;
                            height: 16px;
                            border-radius: 50%;
                            background: #198754;
                        "></span>


                    <strong
                        style="
                            display: block;
                            margin-bottom: 6px;
                        ">
                        Delivered
                    </strong>


                    @if($delivery->location_name)

                    <span
                        style="
                                display: block;
                                font-size: 13px;
                                color: #555;
                            ">

                        <i class="bi bi-geo-alt"></i>

                        {{ $delivery->location_name }}

                    </span>

                    @endif


                    @if($delivery->receiver_name)

                    <span
                        style="
                                display: block;
                                margin-top: 6px;
                                font-size: 13px;
                                color: #555;
                            ">
                        Received by:

                        <strong>
                            {{ $delivery->receiver_name }}
                        </strong>

                    </span>

                    @endif


                    @if($delivery->note)

                    <p
                        style="
                                margin: 8px 0 0;
                                font-size: 13px;
                                color: #777;
                                line-height: 1.6;
                            ">
                        {{ $delivery->note }}
                    </p>

                    @endif


                    <span
                        style="
                            display: block;
                            margin-top: 7px;
                            font-size: 12px;
                            color: #999;
                        ">
                        {{ $delivery->occurred_at
                            ?->format('d M Y, H:i')
                            ?? '-' }}
                    </span>


                    @if($delivery->proof_image)

                    <a
                        href="{{ asset(
                                'storage/'
                                . $delivery->proof_image
                            ) }}"
                        target="_blank"
                        rel="noopener"
                        style="
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                margin-top: 12px;
                                font-size: 12px;
                                font-weight: 700;
                                text-decoration: none;
                            ">

                        <i class="bi bi-image"></i>

                        VIEW PROOF OF DELIVERY

                    </a>

                    @endif

                </div>

                @else

                <div
                    style="
                        margin-bottom: 10px;
                        padding: 14px 16px;
                        background: #f7f7f7;
                        border: 1px solid #e5e5e5;
                        font-size: 13px;
                        line-height: 1.6;
                    ">

                    <i class="bi bi-info-circle"></i>

                    Your package has been handed over to
                    {{ $order->courier ?? 'the courier' }}.

                    Use tracking number

                    <strong>
                        {{ $order->tracking_number ?? '-' }}
                    </strong>

                    for detailed courier tracking.

                </div>

                @endif

            </div>


            {{-- LAST DELIVERY LOCATION --}}

            @if($lastKnownLocation)

            <div
                style="
                    margin-top: 28px;
                    padding-top: 28px;
                    border-top: 1px solid #e5e5e5;
                ">

                <span
                    style="
                        display: block;
                        margin-bottom: 6px;
                        font-size: 11px;
                        font-weight: 700;
                        letter-spacing: 1px;
                        color: #777;
                    ">
                    LAST RECORDED DELIVERY LOCATION
                </span>


                <strong>
                    {{ $lastKnownLocation->location_name }}
                </strong>


                <div
                    id="shipment-map"

                    data-latitude="{{
                        $lastKnownLocation->latitude
                    }}"

                    data-longitude="{{
                        $lastKnownLocation->longitude
                    }}"

                    data-location="{{
                        $lastKnownLocation->location_name
                    }}"

                    style="
                        width: 100%;
                        height: 360px;
                        margin-top: 16px;
                        border: 1px solid #ddd;
                        z-index: 1;
                    "></div>


                <p
                    style="
                        margin-top: 10px;
                        font-size: 12px;
                        color: #777;
                    ">

                    <i class="bi bi-info-circle"></i>

                    This map shows the recorded delivery
                    location from Proof of Delivery.
                    It is not live courier GPS.

                </p>

            </div>

            @endif

        </section>

        @endif

        {{-- ================================================= --}}
        {{-- PAYMENT --}}
        {{-- ================================================= --}}

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
                                            ?->format(
                                                'd M Y, H:i'
                                            )
                                            ?? '-' }}
                    </strong>

                </div>

            </div>


            @if(
            $latestPayment->proof_image
            && file_exists(
            public_path(
            $latestPayment
            ->proof_image
            )
            )
            )

            <div class="detail-proof">

                <span>
                    PAYMENT PROOF
                </span>

                <img
                    src="{{ asset(
                                            $latestPayment
                                                ->proof_image
                                        ) }}"
                    alt="Payment proof">

            </div>

            @endif


            @if(
            $latestPayment->status
            === 'rejected'
            && $latestPayment
            ->rejection_reason
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


    {{-- ================================================= --}}
    {{-- RIGHT SIDEBAR --}}
    {{-- ================================================= --}}

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


            {{-- ACTION --}}

            @if(
            $order->status
            === 'pending_payment'
            )

            <a
                href="{{ route(
                                    'payment.show',
                                    $order
                                ) }}"
                class="detail-primary-button">
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
                class="detail-secondary-button">
                VIEW PAYMENT

                <i class="bi bi-arrow-right"></i>
            </a>

            @endif


            <a
                href="{{ route('orders.index') }}"
                class="detail-back-button">
                <i class="bi bi-arrow-left"></i>

                MY ORDERS
            </a>

        </section>

    </aside>

</div>

@endif

</div>

</section>


{{-- ================================================= --}}
{{-- LEAFLET --}}
{{-- ================================================= --}}

@if($lastKnownLocation)

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">


<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const mapElement =
                document.getElementById(
                    'shipment-map'
                );


            if (
                !mapElement ||
                typeof L === 'undefined'
            ) {
                return;
            }


            const latitude =
                Number(
                    mapElement.dataset.latitude
                );


            const longitude =
                Number(
                    mapElement.dataset.longitude
                );


            const locationName =
                mapElement.dataset.location ||
                'Shipment Location';


            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude)
            ) {
                return;
            }


            const map = L.map(
                mapElement
            ).setView(
                [
                    latitude,
                    longitude
                ],
                13
            );


            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,

                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            const popupContent =
                document.createElement(
                    'div'
                );


            const popupTitle =
                document.createElement(
                    'strong'
                );


            popupTitle.textContent =
                'Last Known Location';


            const popupLocation =
                document.createElement(
                    'span'
                );


            popupLocation.textContent =
                locationName;


            popupContent.appendChild(
                popupTitle
            );


            popupContent.appendChild(
                document.createElement(
                    'br'
                )
            );


            popupContent.appendChild(
                popupLocation
            );


            L.marker([
                    latitude,
                    longitude
                ])
                .addTo(map)
                .bindPopup(
                    popupContent
                )
                .openPopup();

        }
    );
</script>

@endif

@endsection