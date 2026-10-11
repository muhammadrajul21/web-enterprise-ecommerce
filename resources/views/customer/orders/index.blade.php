@extends('layouts.store')

@section('title', 'My Orders')

@section('content')

<section class="orders-page">

    <div class="orders-container">

        {{-- HEADER --}}
        <div class="orders-header">

            <div>
                <span class="orders-eyebrow">
                    MY ACCOUNT
                </span>

                <h1>
                    My Orders
                </h1>

                <p>
                    Track and review your recent purchases.
                </p>
            </div>

            <div class="orders-header-icon">
                <i class="bi bi-bag-check"></i>
            </div>

        </div>


        {{-- ALERT --}}
        @if(session('success'))

            <div class="orders-alert orders-alert-success">

                <i class="bi bi-check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- EMPTY STATE --}}
        @if($orders->isEmpty())

            <div class="orders-empty">

                <div class="orders-empty-icon">
                    <i class="bi bi-bag"></i>
                </div>

                <h2>
                    No Orders Yet
                </h2>

                <p>
                    You haven't placed any orders yet.
                    Start exploring our collection.
                </p>

                <a
                    href="{{ route('catalog.preview') }}"
                    class="orders-shop-button"
                >
                    SHOP NOW

                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        @else

            {{-- ORDER LIST --}}
            <div class="orders-list">

                @foreach($orders as $order)

                    @php

                        $statusLabels = [
                            'pending_payment'
                                => 'Pending Payment',

                            'waiting_verification'
                                => 'Waiting Verification',

                            'processing'
                                => 'Processing',

                            'packing'
                                => 'Packing',

                            'shipped'
                                => 'Shipped',

                            'completed'
                                => 'Completed',

                            'cancelled'
                                => 'Cancelled',
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

                        $latestPayment =
                            $order->payments->first();

                    @endphp


                    <article class="order-card">


                        {{-- ORDER CARD HEADER --}}
                        <div class="order-card-header">

                            <div>

                                <span class="order-label">
                                    ORDER NUMBER
                                </span>

                                <strong class="order-code">
                                    {{ $order->order_code }}
                                </strong>

                            </div>


                            <span
                                class="
                                    order-status
                                    order-status-{{ $order->status }}
                                "
                            >
                                {{ $statusLabel }}
                            </span>

                        </div>


                        {{-- ORDER INFO --}}
                        <div class="order-info-grid">

                            <div>

                                <span>
                                    ORDER DATE
                                </span>

                                <strong>
                                    {{ $order->created_at
                                        ->format('d M Y') }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    ITEMS
                                </span>

                                <strong>
                                    {{ $order->items
                                        ->sum('quantity') }}
                                    Item
                                </strong>

                            </div>


                            <div>

                                <span>
                                    SHIPPING
                                </span>

                                <strong>
                                    {{ $order->shipping_service
                                        ?? '-' }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    TOTAL
                                </span>

                                <strong class="order-total">
                                    Rp {{ number_format(
                                        $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </div>

                        </div>


                        {{-- PRODUCTS --}}
                        <div class="order-products">

                            @foreach(
                                $order->items->take(3)
                                as $item
                            )

                                <div class="order-product">

                                    <div class="order-product-icon">
                                        <i class="bi bi-box"></i>
                                    </div>


                                    <div class="order-product-info">

                                        <strong>
                                            {{ $item->product_name }}
                                        </strong>

                                        <span>

                                            {{ $item->color ?: '-' }}

                                            /

                                            {{ $item->size ?: '-' }}

                                            · Qty
                                            {{ $item->quantity }}

                                        </span>

                                    </div>


                                    <strong class="order-product-price">

                                        Rp {{ number_format(
                                            $item->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>

                                </div>

                            @endforeach


                            @if($order->items->count() > 3)

                                <div class="order-more-items">

                                    + {{ $order->items->count() - 3 }}
                                    more product(s)

                                </div>

                            @endif

                        </div>


                        {{-- FOOTER --}}
                        <div class="order-card-footer">

                            <div class="order-payment-info">

                                @if($latestPayment)

                                    <span>
                                        Payment
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
                                        ·
                                        {{ ucfirst(
                                            $latestPayment->status
                                        ) }}
                                    </strong>

                                @elseif(
                                    $order->status
                                    === 'pending_payment'
                                )

                                    <span>
                                        Payment
                                    </span>

                                    <strong>
                                        Not submitted
                                    </strong>

                                @endif

                            </div>


                            <div class="order-actions">

                                @if(
                                    $order->status
                                    === 'pending_payment'
                                )

                                    <a
                                        href="{{ route(
                                            'payment.show',
                                            $order
                                        ) }}"
                                        class="
                                            order-action
                                            order-action-primary
                                        "
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
                                        class="order-action"
                                    >
                                        VIEW PAYMENT

                                        <i class="bi bi-arrow-right"></i>
                                    </a>

                                @else

                                    <span
                                        class="
                                            order-action
                                            order-action-disabled
                                        "
                                    >
                                        VIEW DETAILS

                                        <i class="bi bi-arrow-right"></i>
                                    </span>

                                @endif

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


            {{-- PAGINATION --}}
            @if($orders->hasPages())

                <div class="orders-pagination">

                    {{ $orders->links() }}

                </div>

            @endif

        @endif

    </div>

</section>

@endsection