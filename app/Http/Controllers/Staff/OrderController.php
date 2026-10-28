<?php

namespace App\Http\Controllers\Staff;

use App\Services\ShippingEstimateService;
use Carbon\CarbonImmutable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShipmentUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
     * Menampilkan order Packing, Shipped,
     * dan Completed.
     */
    public function shipping(Request $request)
    {
        $query = Order::with([
            'user',
            'items',
            'shipmentUpdates',
        ])
            ->whereIn('status', [
                'packing',
                'shipped',
                'completed',
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


        // Filter Packing / Shipped / Completed
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
     * Input informasi pengiriman dan nomor resi.
     * Mengubah Packing menjadi Shipped.
     */
    /**
     * Mengirim pesanan dan memperbarui estimasi kedatangan.
     */
    public function ship(
        Request $request,
        Order $order,
        ShippingEstimateService $shippingEstimateService
    ) {
        if ($order->status !== 'packing') {
            return back()->with(
                'error',
                'Hanya order berstatus packing yang dapat dikirim.'
            );
        }

        $validated = $request->validate([
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

        // Gunakan waktu pengiriman aktual sebagai dasar estimasi.
        $shippedAt = now();

        // Pertahankan pilihan awal Customer.
        // Pesanan lama tanpa pilihan akan menggunakan Regular.
        $method = in_array(
            $order->shipping_method,
            ['regular', 'express'],
            true
        )
            ? $order->shipping_method
            : 'regular';

        $estimate = $shippingEstimateService->quote(
            $order->shipping_province,
            $method,
            CarbonImmutable::instance($shippedAt)
        );

        DB::transaction(function () use (
            $validated,
            $order,
            $shippedAt,
            $estimate,
            $method
        ) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status !== 'packing') {
                throw new \RuntimeException(
                    'Status pesanan berubah. Silakan muat ulang halaman.'
                );
            }

            $lockedOrder->update([
                'courier' => $validated['courier'],

                // Nama layanan aktual yang diisi Staff.
                'shipping_service' => $validated['shipping_service'],
                'shipping_method' => $method,

                'tracking_number' => $validated['tracking_number'],

                'status' => 'shipped',

                'shipped_at' => $shippedAt,

                'estimated_delivery_start' =>
                $estimate['estimated_delivery_start'],

                'estimated_delivery_end' =>
                $estimate['estimated_delivery_end'],
            ]);
        });

        return back()->with(
            'success',
            'Order berhasil dikirim dan estimasi kedatangan telah diperbarui.'
        );
    }




    /**
     * Mengkonfirmasi barang sudah diterima customer.
     * Order otomatis berubah dari Shipped
     * menjadi Completed.
     */
    /**
     * Endpoint lama POD dinonaktifkan.
     * Pesanan dikonfirmasi oleh pelanggan atau
     * diselesaikan otomatis setelah masa tenggang.
     */
    public function markDelivered(Request $request, Order $order)
    {
        return back()->with(
            'error',
            'Verifikasi POD oleh Staff sudah dinonaktifkan. ' .
                'Pesanan dikonfirmasi oleh pelanggan atau diselesaikan otomatis setelah masa tenggang.'
        );
    }
}
