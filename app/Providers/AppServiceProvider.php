<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Segment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Data navigasi dibagikan ke navbar & footer.
        // Di-memo per request supaya query tidak berjalan dua kali.
        $nav = null;

        View::composer(
            ['components.navbar', 'components.footer'],
            function ($view) use (&$nav) {

                $nav ??= [
                    'navSegments' => Segment::where('is_active', true)
                        ->orderBy('id')
                        ->get(['id', 'name', 'slug']),

                    'navCategories' => Category::where('is_active', true)
                        ->orderBy('name')
                        ->get(['id', 'name', 'slug']),
                ];

                $view->with($nav);
            }
        );

        // Jumlah item di ikon keranjang (hanya untuk user yang login).
        View::composer('components.navbar', function ($view) {

            $count = 0;

            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())
                    ->withSum('items', 'quantity')
                    ->first();

                $count = (int) ($cart?->items_sum_quantity ?? 0);
            }

            $view->with('cartCount', $count);
        });
    }
}
