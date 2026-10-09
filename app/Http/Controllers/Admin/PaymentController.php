<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        if ($payment->status !== 'pending') {

            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }

        DB::transaction(function () use ($payment) {

            $payment->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);


            $payment->order->update([
                'status' => 'processing',
                'paid_at' => $payment->paid_at ?? now(),
            ]);

        });


        return back()->with(
            'success',
            'Pembayaran berhasil diverifikasi.'
        );
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
                'status' => 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_reason' =>
                    $request->rejection_reason,
            ]);


            $payment->order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
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