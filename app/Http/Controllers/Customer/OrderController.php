<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Menampilkan riwayat order customer.
     */
    public function index()
    {
        $orders = Order::where(
            'user_id',
            auth()->id()
        )
            ->with([
                'items',

                'payments' => function ($query) {
                    $query->latest();
                },
            ])
            ->latest()
            ->paginate(10);


        return view(
            'customer.orders.index',
            compact('orders')
        );
    }


    /**
     * Menampilkan detail order customer.
     */
    public function show(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        |
        | Shipment update yang dibutuhkan customer sekarang
        | hanya data final "delivered" dari Proof of Delivery.
        |
        */

        $order->load([

            'items.review',

            'voucher',

            'payments' => function ($query) {
                $query->latest();
            },

            'shipmentUpdates' => function ($query) {

                $query
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->orderBy('occurred_at')
                    ->orderBy('id');

            },

        ]);


        /*
        |--------------------------------------------------------------------------
        | PAYMENT TERBARU
        |--------------------------------------------------------------------------
        */

        $latestPayment =
            $order->payments->first();


        /*
        |--------------------------------------------------------------------------
        | DELIVERY DATA
        |--------------------------------------------------------------------------
        */

        $delivery =
            $order->shipmentUpdates
                ->last();


        /*
         * Map hanya ditampilkan jika POD memiliki
         * latitude dan longitude.
         *
         * Lokasi ini adalah lokasi penerimaan terakhir,
         * bukan live GPS kurir.
         */

        $lastKnownLocation =
            $delivery
            && $delivery->latitude !== null
            && $delivery->longitude !== null
                ? $delivery
                : null;


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'customer.orders.show',
            compact(
                'order',
                'latestPayment',
                'delivery',
                'lastKnownLocation'
            )
        );
    }
}