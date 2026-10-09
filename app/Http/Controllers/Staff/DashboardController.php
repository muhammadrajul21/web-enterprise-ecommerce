<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockLog;

class DashboardController extends Controller
{
    public function index()
    {
        $processingOrders = Order::where(
            'status',
            'processing'
        )->count();

        $packingOrders = Order::where(
            'status',
            'packing'
        )->count();

        $shippedOrders = Order::where(
            'status',
            'shipped'
        )->count();

        $lowStockVariants = ProductVariant::where(
            'stock',
            '<=',
            5
        )->count();

        $recentOrders = Order::with('user')
            ->whereIn(
                'status',
                [
                    'processing',
                    'packing',
                    'shipped',
                ]
            )
            ->latest()
            ->take(5)
            ->get();

        $recentStockLogs = StockLog::with([
            'variant.product',
        ])
            ->latest()
            ->take(5)
            ->get();

        return view(
            'staff.dashboard',
            compact(
                'processingOrders',
                'packingOrders',
                'shippedOrders',
                'lowStockVariants',
                'recentOrders',
                'recentStockLogs'
            )
        );
    }
}