<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORT PAGE
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | PRINT REPORT
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | BUILD REPORT DATA
    |--------------------------------------------------------------------------
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


        $startDate =
            $validated['start_date'] ?? null;

        $endDate =
            $validated['end_date'] ?? null;

        $status =
            $validated['status'] ?? null;


        /*
        |--------------------------------------------------------------------------
        | BASE ORDER QUERY
        |--------------------------------------------------------------------------
        */

        $orderQuery = Order::query()
            ->with([
                'user',

                'payments' => function ($query) {
                    $query->latest();
                },
            ]);


        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($status) {
            $orderQuery->where(
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REPORT SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalOrders = (clone $orderQuery)
            ->count();


        $completedOrders = (clone $orderQuery)
            ->where(
                'status',
                'completed'
            )
            ->count();


        $cancelledOrders = (clone $orderQuery)
            ->where(
                'status',
                'cancelled'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | VERIFIED REVENUE
        |--------------------------------------------------------------------------
        */

        $paymentQuery = Payment::query()
            ->where(
                'status',
                'verified'
            )
            ->whereHas(
                'order',
                function ($query) use (
                    $startDate,
                    $endDate,
                    $status
                ) {
                    if ($startDate) {
                        $query->whereDate(
                            'created_at',
                            '>=',
                            $startDate
                        );
                    }


                    if ($endDate) {
                        $query->whereDate(
                            'created_at',
                            '<=',
                            $endDate
                        );
                    }


                    if ($status) {
                        $query->where(
                            'status',
                            $status
                        );
                    }
                }
            );


        $totalRevenue = $paymentQuery
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | AVERAGE ORDER VALUE
        |--------------------------------------------------------------------------
        */

        $averageOrderValue =
            $totalOrders > 0
                ? $totalRevenue / $totalOrders
                : 0;


        /*
        |--------------------------------------------------------------------------
        | ORDER LIST
        |--------------------------------------------------------------------------
        |
        | Halaman report biasa menggunakan pagination.
        | Halaman print mengambil semua data sesuai filter.
        |
        */

        if ($paginate) {
            $orders = $orderQuery
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $orders = $orderQuery
                ->latest()
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN REPORT DATA
        |--------------------------------------------------------------------------
        */

        return compact(
            'orders',
            'totalOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'averageOrderValue',
            'startDate',
            'endDate',
            'status'
        );
    }
}