<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan order Processing dan Packing.
     */
    public function processing(Request $request)
    {
        $query = Order::with([
            'user',
            'items',
        ])
            ->whereIn('status', [
                'processing',
                'packing',
            ])
            ->latest();


        // Search order / customer
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($query) use ($search) {

                $query
                    ->where(
                        'order_code',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'user',
                        function ($userQuery) use ($search) {

                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
            });
        }


        // Filter Processing / Packing
        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        $orders = $query
            ->paginate(10)
            ->withQueryString();


        return view(
            'staff.orders.processing',
            compact('orders')
        );
    }


    /**
     * Mengubah Processing menjadi Packing.
     */
    public function markPacking(Order $order)
    {
        if ($order->status !== 'processing') {

            return back()->with(
                'error',
                'Hanya order berstatus processing yang dapat masuk tahap packing.'
            );
        }


        DB::transaction(function () use ($order) {

            $order->update([
                'status' => 'packing',
            ]);
        });


        return back()->with(
            'success',
            'Order berhasil dipindahkan ke tahap packing.'
        );
    }
    /**
     * Menampilkan order yang siap dikirim
     * dan order yang sudah dikirim.
     */
    public function shipping(Request $request)
    {
        $query = Order::with([
            'user',
            'items',
        ])
            ->whereIn('status', [
                'packing',
                'shipped',
            ])
            ->latest();


        // Search order / customer / resi
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($query) use ($search) {

                $query
                    ->where(
                        'order_code',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'tracking_number',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'user',
                        function ($userQuery) use ($search) {

                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
            });
        }


        // Filter packing / shipped
        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        $orders = $query
            ->paginate(10)
            ->withQueryString();


        return view(
            'staff.orders.shipping',
            compact('orders')
        );
    }


    /**
     * Input data pengiriman dan nomor resi.
     */
    public function ship(
        Request $request,
        Order $order
    ) {
        if ($order->status !== 'packing') {

            return back()->with(
                'error',
                'Hanya order berstatus packing yang dapat dikirim.'
            );
        }


        $request->validate([

            'courier' => [
                'required',
                'string',
                'max:100',
            ],

            'shipping_service' => [
                'required',
                'string',
                'max:100',
            ],

            'tracking_number' => [
                'required',
                'string',
                'max:255',
            ],

        ]);


        DB::transaction(function () use (
            $request,
            $order
        ) {

            $order->update([

                'courier' =>
                $request->courier,

                'shipping_service' =>
                $request->shipping_service,

                'tracking_number' =>
                $request->tracking_number,

                'status' =>
                'shipped',

                'shipped_at' =>
                now(),

            ]);
        });


        return back()->with(
            'success',
            'Order berhasil dikirim dan nomor resi telah disimpan.'
        );
    }
}
