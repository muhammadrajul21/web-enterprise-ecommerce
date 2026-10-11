<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\StockLog;

class OperationalSummaryController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | INVENTORY SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalVariants = ProductVariant::where(
            'status',
            'active'
        )->count();


        $totalStock = ProductVariant::where(
            'status',
            'active'
        )->sum('stock');


        $lowStockCount = ProductVariant::where(
            'status',
            'active'
        )
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count();


        $outOfStockCount = ProductVariant::where(
            'status',
            'active'
        )
            ->where('stock', '<=', 0)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | RECENT STOCK ACTIVITY
        |--------------------------------------------------------------------------
        */

        $recentStockLogs = StockLog::with([
            'variant.product',
            'creator',
        ])
            ->latest()
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | REVIEW SUMMARY
        |--------------------------------------------------------------------------
        */

        $reviewQuery = Review::where(
            'is_visible',
            true
        );


        $totalReviews = (clone $reviewQuery)
            ->count();


        $averageRating = (clone $reviewQuery)
            ->avg('rating');


        $averageRating = $averageRating
            ? round($averageRating, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | RATING DISTRIBUTION
        |--------------------------------------------------------------------------
        */

        $ratingCounts = [];

        for ($rating = 5; $rating >= 1; $rating--) {

            $ratingCounts[$rating] = Review::where(
                'is_visible',
                true
            )
                ->where(
                    'rating',
                    $rating
                )
                ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | RECENT REVIEWS
        |--------------------------------------------------------------------------
        */

        $recentReviews = Review::with([
            'user',
            'orderItem.variant.product',
        ])
            ->where(
                'is_visible',
                true
            )
            ->latest()
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'owner.operations.index',
            compact(
                'totalVariants',
                'totalStock',
                'lowStockCount',
                'outOfStockCount',
                'recentStockLogs',
                'totalReviews',
                'averageRating',
                'ratingCounts',
                'recentReviews'
            )
        );
    }
}