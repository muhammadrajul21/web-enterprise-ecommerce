<?php

namespace App\Http\Controllers\Customer;

use App\Models\OrderComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'complaints' => function ($query) {
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
    /**
     * Customer mengonfirmasi bahwa pesanan sudah diterima.
     */
    public function confirmReceived(Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );

        if ($order->status !== 'shipped') {
            return back()->with(
                'error',
                'Hanya pesanan yang sedang dikirim yang dapat dikonfirmasi.'
            );
        }

        $confirmed = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status !== 'shipped') {
                return false;
            }

            $hasOpenComplaint = $lockedOrder->complaints()
                ->whereIn('status', ['open', 'investigating'])
                ->exists();

            if ($hasOpenComplaint) {
                return false;
            }

            $lockedOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_reason' => 'customer_confirmed',
            ]);

            return true;
        });

        if (! $confirmed) {
            return back()->with(
                'error',
                'Pesanan berubah status atau memiliki komplain yang belum selesai.'
            );
        }

        return back()->with(
            'success',
            'Terima kasih! Pesanan telah dikonfirmasi diterima.'
        );
    }

    /**
     * Customer melaporkan masalah pada pesanan.
     */
    public function storeComplaint(Request $request, Order $order)
    {
        // Pastikan pesanan ini milik pelanggan yang sedang login.
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        // Validasi data komplain.
        $validated = $request->validate([
            'category' => [
                'required',
                'in:parcel_not_received,damaged,wrong_item,other',
            ],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Kunci pesanan selama proses agar tidak berbenturan
        // dengan proses penyelesaian otomatis.
        $result = DB::transaction(function () use ($order, $validated) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            // Komplain hanya diperbolehkan ketika pesanan masih dikirim,
            // atau sudah selesai karena batas waktu otomatis.
            $eligible = $lockedOrder->status === 'shipped'
                || (
                    $lockedOrder->status === 'completed'
                    && $lockedOrder->completion_reason === 'auto_timeout'
                );

            if (! $eligible) {
                return 'ineligible';
            }

            // Cegah komplain aktif ganda untuk pesanan yang sama.
            $hasActiveComplaint = $lockedOrder->complaints()
                ->whereIn('status', ['open', 'investigating'])
                ->exists();

            if ($hasActiveComplaint) {
                return 'duplicate';
            }

            OrderComplaint::create([
                'order_id' => $lockedOrder->id,
                'user_id' => auth()->id(),
                'category' => $validated['category'],
                'message' => $validated['message'],
                'status' => 'open',
            ]);

            return 'created';
        });

        // Tampilkan pesan sesuai hasil proses.
        if ($result === 'ineligible') {
            return back()->with(
                'error',
                'Komplain hanya dapat diajukan untuk pesanan yang sedang dikirim atau selesai otomatis.'
            );
        }

        if ($result === 'duplicate') {
            return back()->with(
                'error',
                'Pesanan ini masih memiliki komplain yang sedang diproses.'
            );
        }

        return back()->with(
            'success',
            'Komplain berhasil dikirim. Admin akan meninjau laporan kamu.'
        );
    }
}
