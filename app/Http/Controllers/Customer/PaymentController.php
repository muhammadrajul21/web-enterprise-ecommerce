<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman pembayaran customer.
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
            'items',
            'payments' => function ($query) {
                $query->latest();
            },
        ]);


        $latestPayment =
            $order->payments->first();


        return view(
            'customer.payment.show',
            compact(
                'order',
                'latestPayment'
            )
        );
    }


    /**
     * Upload bukti pembayaran.
     */
    public function store(
        Request $request,
        Order $order
    ) {
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
        | ORDER STATUS
        |--------------------------------------------------------------------------
        |
        | Upload pembayaran hanya diperbolehkan saat order
        | masih menunggu pembayaran.
        |
        */

        if ($order->status !== 'pending_payment') {

            return redirect()
                ->route(
                    'payment.show',
                    $order
                )
                ->with(
                    'error',
                    'Order ini tidak dapat menerima pembayaran baru.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'payment_method' => [
                'required',
                'string',
                'in:bank_transfer,qris,e_wallet',
            ],

            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE PENDING PAYMENT
        |--------------------------------------------------------------------------
        */

        $hasPendingPayment =
            $order->payments()
                ->where(
                    'status',
                    'pending'
                )
                ->exists();


        if ($hasPendingPayment) {

            return redirect()
                ->route(
                    'payment.show',
                    $order
                )
                ->with(
                    'error',
                    'Bukti pembayaran untuk order ini sudah dikirim.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | PREPARE UPLOAD
        |--------------------------------------------------------------------------
        */

        $file =
            $request->file('proof_image');


        $uploadDirectory =
            public_path(
                'uploads/payments'
            );


        File::ensureDirectoryExists(
            $uploadDirectory
        );


        $extension =
            strtolower(
                $file->extension()
            );


        $fileName =
            'payment-'
            . $order->id
            . '-'
            . Str::uuid()
            . '.'
            . $extension;


        $relativePath =
            'uploads/payments/'
            . $fileName;


        /*
        |--------------------------------------------------------------------------
        | MOVE FILE
        |--------------------------------------------------------------------------
        */

        $file->move(
            $uploadDirectory,
            $fileName
        );


        try {

            DB::transaction(
                function () use (
                    $validated,
                    $order,
                    $relativePath
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK ORDER
                    |--------------------------------------------------------------------------
                    */

                    $lockedOrder =
                        Order::where(
                            'id',
                            $order->id
                        )
                            ->lockForUpdate()
                            ->firstOrFail();


                    /*
                     * Cek lagi setelah lock untuk
                     * mencegah request ganda.
                     */
                    if (
                        $lockedOrder->status
                        !== 'pending_payment'
                    ) {

                        throw new \RuntimeException(
                            'Order sudah diproses sebelumnya.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    Payment::create([
                        'order_id' =>
                            $lockedOrder->id,

                        'payment_method' =>
                            $validated[
                                'payment_method'
                            ],

                        'amount' =>
                            $lockedOrder->total_amount,

                        'proof_image' =>
                            $relativePath,

                        'status' =>
                            'pending',

                        'verified_by' =>
                            null,

                        'paid_at' =>
                            now(),

                        'verified_at' =>
                            null,

                        'rejection_reason' =>
                            null,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $lockedOrder->update([
                        'status' =>
                            'waiting_verification',
                    ]);

                }
            );


            return redirect()
                ->route(
                    'payment.show',
                    $order
                )
                ->with(
                    'success',
                    'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin.'
                );

        } catch (\Throwable $exception) {

            /*
             * Jika transaksi database gagal,
             * hapus file yang sudah ter-upload.
             */
            if (
                File::exists(
                    public_path(
                        $relativePath
                    )
                )
            ) {

                File::delete(
                    public_path(
                        $relativePath
                    )
                );

            }


            return redirect()
                ->route(
                    'payment.show',
                    $order
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );

        }
    }
}