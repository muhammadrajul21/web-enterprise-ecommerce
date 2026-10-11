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
        |
        | Customer hanya boleh melihat order miliknya sendiri.
        |
        */

        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

   $order->load([
    'items.review',

    'voucher',

    'payments' => function ($query) {
        $query->latest();
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
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'customer.orders.show',
            compact(
                'order',
                'latestPayment'
            )
        );
    }
}