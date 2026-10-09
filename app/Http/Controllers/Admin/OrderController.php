<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar order.
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'payments',
        ])->latest();

        // Filter status order
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Search berdasarkan order code / nama customer
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'order_code',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhereHas(
                    'user',
                    function ($userQuery) use ($search) {

                        $userQuery->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );

                    }
                );

            });
        }

        $orders = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }


    /**
     * Menampilkan detail order.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'voucher',
            'items.variant.product',
            'payments.verifier',
        ]);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }
}