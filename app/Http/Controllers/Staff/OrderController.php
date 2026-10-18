<?php

namespace App\Http\Controllers\Staff;

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


    /**
     * Mengkonfirmasi barang sudah diterima customer.
     * Order otomatis berubah dari Shipped
     * menjadi Completed.
     */
    public function markDelivered(
        Request $request,
        Order $order
    ) {
        if ($order->status !== 'shipped') {

            return back()->with(
                'error',
                'Hanya order berstatus shipped yang dapat dikonfirmasi sebagai delivered.'
            );
        }


        $request->validate([

            'receiver_name' => [
                'required',
                'string',
                'max:255',
            ],

            'location_name' => [
                'required',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

        ]);


        $proofPath = $request
            ->file('proof_image')
            ->store(
                'delivery-proofs',
                'public'
            );


        try {

            DB::transaction(function () use (
                $request,
                $order,
                $proofPath
            ) {

                /*
                 * Mengunci order agar tidak dapat
                 * dikonfirmasi dua kali bersamaan.
                 */
                $lockedOrder = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);


                if ($lockedOrder->status !== 'shipped') {

                    throw new \RuntimeException(
                        'Status order sudah berubah.'
                    );
                }


                /*
                 * Menyimpan bukti barang diterima.
                 */
                ShipmentUpdate::create([

                    'order_id' =>
                    $lockedOrder->id,

                    'updated_by' =>
                    auth()->id(),

                    'status' =>
                    'delivered',

                    'location_name' =>
                    $request->location_name,

                    'latitude' =>
                    $request->latitude,

                    'longitude' =>
                    $request->longitude,

                    'receiver_name' =>
                    $request->receiver_name,

                    'note' =>
                    $request->note,

                    'proof_image' =>
                    $proofPath,

                    'occurred_at' =>
                    now(),

                ]);


                /*
                 * Setelah barang diterima,
                 * status order otomatis selesai.
                 */
                $lockedOrder->update([

                    'status' =>
                    'completed',

                    'completed_at' =>
                    now(),

                ]);
            });
        } catch (\Throwable $exception) {

            /*
             * Jika penyimpanan database gagal,
             * hapus kembali foto yang sudah
             * ter-upload.
             */
            Storage::disk('public')
                ->delete($proofPath);


            report($exception);


            return back()->with(
                'error',
                'Verifikasi Proof of Delivery gagal. Silakan coba kembali.'
            );
        }


        return back()->with(
            'success',
            'Proof of Delivery berhasil diverifikasi dan order otomatis diselesaikan.'
        );
    }
}
