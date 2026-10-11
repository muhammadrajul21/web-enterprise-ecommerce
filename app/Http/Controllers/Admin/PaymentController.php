<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Daftar pembayaran.
     */
    public function index(Request $request)
    {
        $query = Payment::with([
            'order.user',
            'verifier',
        ])->latest();

        // Filter status pembayaran
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Pencarian
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($query) use ($search) {

                $query
                    ->whereHas(
                        'order',
                        function ($orderQuery) use ($search) {

                            $orderQuery->where(
                                'order_code',
                                'like',
                                '%' . $search . '%'
                            );

                        }
                    )
                    ->orWhereHas(
                        'order.user',
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

        $payments = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.payments.index',
            compact('payments')
        );
    }


    /**
     * Verifikasi pembayaran.
     */
    public function verify(Payment $payment)
    {
        try {

            DB::transaction(function () use ($payment) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PAYMENT
                |--------------------------------------------------------------------------
                */

                $lockedPayment = Payment::where(
                    'id',
                    $payment->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | CEK PAYMENT
                |--------------------------------------------------------------------------
                */

                if ($lockedPayment->status !== 'pending') {

                    throw new \RuntimeException(
                        'Pembayaran ini sudah diproses.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $order = $lockedPayment
                    ->order()
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | CEK STATUS ORDER
                |--------------------------------------------------------------------------
                */

                if ($order->status !== 'waiting_verification') {

                    throw new \RuntimeException(
                        'Status order tidak sesuai untuk diverifikasi.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL ORDER ITEMS
                |--------------------------------------------------------------------------
                */

                $orderItems = $order
                    ->items()
                    ->get();


                if ($orderItems->isEmpty()) {

                    throw new \RuntimeException(
                        'Order tidak memiliki item.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | KURANGI STOK SETIAP ITEM
                |--------------------------------------------------------------------------
                */

                foreach ($orderItems as $item) {

                    if (!$item->variant_id) {

                        throw new \RuntimeException(
                            'Variant untuk '
                            . $item->product_name
                            . ' tidak tersedia.'
                        );
                    }


                    /*
                     * Lock variant supaya stok aman dari
                     * proses bersamaan.
                     */
                    $variant = ProductVariant::where(
                        'id',
                        $item->variant_id
                    )
                        ->lockForUpdate()
                        ->first();


                    if (!$variant) {

                        throw new \RuntimeException(
                            'Variant untuk '
                            . $item->product_name
                            . ' tidak ditemukan.'
                        );
                    }


                    /*
                     * Cek stok.
                     */
                    if ($variant->stock < $item->quantity) {

                        throw new \RuntimeException(
                            'Stok '
                            . $item->product_name
                            . ' tidak mencukupi.'
                        );
                    }


                    $stockBefore = $variant->stock;

                    $stockAfter =
                        $stockBefore
                        - $item->quantity;


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STOCK
                    |--------------------------------------------------------------------------
                    */

                    $variant->update([
                        'stock' => $stockAfter,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK LOG
                    |--------------------------------------------------------------------------
                    */

                    StockLog::create([
                        'variant_id' =>
                            $variant->id,

                        'type' =>
                            'OUT',

                        'quantity' =>
                            $item->quantity,

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'reference_type' =>
                            'order',

                        'reference_id' =>
                            $order->id,

                        'note' =>
                            'Stock deducted after payment verification for order '
                            . $order->order_code,

                        'created_by' =>
                            Auth::id(),
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE PAYMENT
                |--------------------------------------------------------------------------
                */

                $lockedPayment->update([
                    'status' =>
                        'verified',

                    'verified_by' =>
                        Auth::id(),

                    'verified_at' =>
                        now(),

                    'rejection_reason' =>
                        null,
                ]);


                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'status' =>
                        'processing',

                    'paid_at' =>
                        $lockedPayment->paid_at
                        ?? now(),
                ]);

            });


            return back()->with(
                'success',
                'Pembayaran berhasil diverifikasi dan stok produk telah diperbarui.'
            );

        } catch (\RuntimeException $exception) {

            return back()->with(
                'error',
                $exception->getMessage()
            );

        }
    }


    /**
     * Tolak pembayaran.
     */
    public function reject(
        Request $request,
        Payment $payment
    ) {
        $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:500',
            ],
        ]);


        if ($payment->status !== 'pending') {

            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }


        DB::transaction(function () use (
            $request,
            $payment
        ) {

            $payment->update([
                'status' =>
                    'rejected',

                'verified_by' =>
                    Auth::id(),

                'verified_at' =>
                    now(),

                'rejection_reason' =>
                    $request->rejection_reason,
            ]);


            $payment->order->update([
                'status' =>
                    'cancelled',

                'cancelled_at' =>
                    now(),

                'cancel_reason' =>
                    $request->rejection_reason,
            ]);

        });


        return back()->with(
            'success',
            'Pembayaran berhasil ditolak.'
        );
    }
}