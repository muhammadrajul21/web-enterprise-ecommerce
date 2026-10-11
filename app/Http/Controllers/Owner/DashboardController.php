<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SALES SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalRevenue = Payment::where(
            'status',
            'verified'
        )->sum('amount');

        $totalOrders = Order::count();

        $completedOrders = Order::where(
            'status',
            'completed'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalCustomers = User::whereHas(
            'role',
            function ($query) {
                $query->where(
                    'name',
                    'customer'
                );
            }
        )->count();


        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS SUMMARY
        |--------------------------------------------------------------------------
        */

        $orderStatusCounts = [
            'pending_payment' => Order::where(
                'status',
                'pending_payment'
            )->count(),

            'waiting_verification' => Order::where(
                'status',
                'waiting_verification'
            )->count(),

            'processing' => Order::where(
                'status',
                'processing'
            )->count(),

            'packing' => Order::where(
                'status',
                'packing'
            )->count(),

            'shipped' => Order::where(
                'status',
                'shipped'
            )->count(),

            'completed' => Order::where(
                'status',
                'completed'
            )->count(),

            'cancelled' => Order::where(
                'status',
                'cancelled'
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | STOCK SUMMARY
        |--------------------------------------------------------------------------
        */

        $lowStockCount = ProductVariant::where(
            'status',
            'active'
        )
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count();

        $outOfStockCount = ProductVariant::where(
            'stock',
            '<=',
            0
        )->count();

        $lowStockVariants = ProductVariant::with(
            'product'
        )
            ->where(
                'status',
                'active'
            )
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | REVIEW SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalReviews = Review::where(
            'is_visible',
            true
        )->count();

        $averageRating = Review::where(
            'is_visible',
            true
        )->avg('rating');

        $averageRating = $averageRating
            ? round($averageRating, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | RECENT ORDERS
        |--------------------------------------------------------------------------
        */

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'owner.dashboard',
            compact(
                'totalRevenue',
                'totalOrders',
                'completedOrders',
                'totalCustomers',
                'orderStatusCounts',
                'lowStockCount',
                'outOfStockCount',
                'lowStockVariants',
                'totalReviews',
                'averageRating',
                'recentOrders'
            )
        );
    }
}