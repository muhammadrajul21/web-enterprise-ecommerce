<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $totalVariants = ProductVariant::count();

        $totalOrders = Order::count();

        $totalCustomers = User::whereHas(
            'role',
            function ($query) {
                $query->where('name', 'customer');
            }
        )->count();


        $pendingPayments = Payment::where(
            'status',
            'pending'
        )->count();


        $totalRevenue = Payment::where(
            'status',
            'verified'
        )->sum('amount');


        $statuses = [
            'pending_payment',
            'waiting_verification',
            'processing',
            'packing',
            'shipped',
            'completed',
            'cancelled',
        ];


        $orderStatusCounts = [];

        foreach ($statuses as $status) {
            $orderStatusCounts[$status] = Order::where(
                'status',
                $status
            )->count();
        }


        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();


        $recentPendingPayments = Payment::with([
            'order.user',
        ])
            ->where(
                'status',
                'pending'
            )
            ->latest()
            ->take(5)
            ->get();


        return view(
            'admin.dashboard',
            compact(
                'totalProducts',
                'totalVariants',
                'totalOrders',
                'totalCustomers',
                'pendingPayments',
                'totalRevenue',
                'orderStatusCounts',
                'recentOrders',
                'recentPendingPayments'
            )
        );
    }
}