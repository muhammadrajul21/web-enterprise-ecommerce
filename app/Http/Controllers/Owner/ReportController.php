<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
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
        | REVENUE
        |--------------------------------------------------------------------------
        |
        | Revenue hanya dihitung dari payment yang sudah verified.
        |
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
        */

        $orders = $orderQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'owner.reports.index',
            compact(
                'orders',
                'totalOrders',
                'completedOrders',
                'cancelledOrders',
                'totalRevenue',
                'averageOrderValue',
                'startDate',
                'endDate',
                'status'
            )
        );
    }
}