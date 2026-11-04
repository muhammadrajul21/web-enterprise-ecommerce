<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman pengaturan laporan Owner.
     */
    public function index(Request $request)
    {
        $report = $this->buildReport(
            $request,
            true
        );

        return view(
            'owner.reports.index',
            $report
        );
    }


    /**
     * Menampilkan dokumen laporan untuk dicetak atau disimpan PDF.
     */
    public function print(Request $request)
    {
        $report = $this->buildReport(
            $request,
            false
        );

        return view(
            'owner.reports.print',
            $report
        );
    }


    /**
     * Mengumpulkan data aktivitas sistem untuk laporan.
     */
    private function buildReport(
        Request $request,
        bool $paginate = true
    ): array {
        /*
        |--------------------------------------------------------------------------
        | FILTER VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'pending_payment',
                    'waiting_verification',
                    'processing',
                    'packing',
                    'shipped',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $status = $validated['status'] ?? null;


        /*
        |--------------------------------------------------------------------------
        | ORDER QUERY
        |--------------------------------------------------------------------------
        |
        | Periode pesanan berdasarkan tanggal pesanan dibuat.
        |
        */

        $orderQuery = Order::query()
            ->with([
                'user',
                'payments' => function ($query) {
                    $query->latest();
                },
            ]);

        if ($startDate) {
            $orderQuery->whereDate(
                'created_at',
                '>=',
                $startDate
            );
        }

        if ($endDate) {
            $orderQuery->whereDate(
                'created_at',
                '<=',
                $endDate
            );
        }

        if ($status) {
            $orderQuery->where(
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ORDER SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalOrders = (clone $orderQuery)
            ->count();

        $completedOrders = (clone $orderQuery)
            ->where('status', 'completed')
            ->count();

        $cancelledOrders = (clone $orderQuery)
            ->where('status', 'cancelled')
            ->count();

        $orderStatusSummary = (clone $orderQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');


        /*
        |--------------------------------------------------------------------------
        | PAYMENT QUERY
        |--------------------------------------------------------------------------
        |
        | Aktivitas pembayaran menggunakan tanggal aktivitas:
        | verified_at, paid_at, atau created_at.
        |
        | Jika filter status pesanan digunakan, pembayaran
        | dibatasi pada pesanan dengan status tersebut.
        |
        */

        $paymentQuery = Payment::query()
            ->with([
                'order.user',
                'verifier',
            ]);

        if ($startDate) {
            $paymentQuery->whereRaw(
                'DATE(COALESCE(
                    payments.verified_at,
                    payments.paid_at,
                    payments.created_at
                )) >= ?',
                [$startDate]
            );
        }

        if ($endDate) {
            $paymentQuery->whereRaw(
                'DATE(COALESCE(
                    payments.verified_at,
                    payments.paid_at,
                    payments.created_at
                )) <= ?',
                [$endDate]
            );
        }

        if ($status) {
            $paymentQuery->whereHas(
                'order',
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalPaymentRecords = (clone $paymentQuery)
            ->count();

        $paymentStatusSummary = (clone $paymentQuery)
            ->selectRaw(
                'status, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount'
            )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $verifiedPaymentCount = (int) (
            $paymentStatusSummary->get('verified')->total ?? 0
        );

        $pendingPaymentCount = (int) (
            $paymentStatusSummary->get('pending')->total ?? 0
        );

        $rejectedPaymentCount = (int) (
            $paymentStatusSummary->get('rejected')->total ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | VERIFIED PAYMENT ACTIVITY
        |--------------------------------------------------------------------------
        |
        | Nominal pembayaran terverifikasi ditampilkan sebagai
        | catatan pemasukan pembayaran, bukan laba bersih.
        |
        */

        $totalRevenue = (clone $paymentQuery)
            ->where('status', 'verified')
            ->sum('amount');

        $averageOrderValue = $totalOrders > 0
            ? $totalRevenue / $totalOrders
            : 0;


        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHOD SUMMARY
        |--------------------------------------------------------------------------
        |
        | Rekap metode pembayaran hanya menggunakan pembayaran
        | yang sudah diverifikasi.
        |
        */

        $paymentMethodSummary = (clone $paymentQuery)
            ->where('status', 'verified')
            ->selectRaw(
                'payment_method, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount'
            )
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ORDER LIST
        |--------------------------------------------------------------------------
        |
        | Halaman Reports menggunakan pagination.
        | Dokumen cetak mengambil semua pesanan sesuai filter.
        |
        */

        if ($paginate) {
            $orders = $orderQuery
                ->latest()
                ->paginate(10)
                ->withQueryString();

            $payments = collect();

        } else {
            $orders = (clone $orderQuery)
                ->latest()
                ->get();

            $payments = (clone $paymentQuery)
                ->orderByRaw(
                    'COALESCE(
                        payments.verified_at,
                        payments.paid_at,
                        payments.created_at
                    ) DESC'
                )
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | REPORT INFORMATION
        |--------------------------------------------------------------------------
        */

        $generatedAt = now();

        $reportPreparedBy = auth()->user()?->name
            ?? 'Owner';


        /*
        |--------------------------------------------------------------------------
        | RETURN REPORT DATA
        |--------------------------------------------------------------------------
        */

        return compact(
            'orders',
            'payments',
            'totalOrders',
            'completedOrders',
            'cancelledOrders',
            'orderStatusSummary',
            'totalPaymentRecords',
            'paymentStatusSummary',
            'verifiedPaymentCount',
            'pendingPaymentCount',
            'rejectedPaymentCount',
            'totalRevenue',
            'averageOrderValue',
            'paymentMethodSummary',
            'startDate',
            'endDate',
            'status',
            'generatedAt',
            'reportPreparedBy'
        );
    }
}
