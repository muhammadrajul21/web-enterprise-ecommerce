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
}