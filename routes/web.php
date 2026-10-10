<?php

use App\Http\Controllers\AuthController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Segment;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\StockController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;

// ========================
// HOME / REDIRECT SESUAI ROLE
// ========================

Route::get('/', function () {

    if (!Auth::check()) {
        return redirect()->route('home.preview');
    }

    $user = Auth::user();

    return match ($user->role?->name) {

        'admin' =>
        redirect()->route('admin.dashboard'),

        'staff_gudang' =>
        redirect()->route('staff.dashboard'),

        'owner' =>
        redirect()->route('owner.dashboard'),

        'customer' =>
        redirect()->route('home.preview'),

        default =>
        redirect()->route('home.preview'),
    };
});


// ========================
// HOME PREVIEW
// ========================

Route::get('/home-preview', function () {

    // Produk terbaru
    $products = Product::with([
        'segment',
        'category',
        'variants',
        'images',
        'collections',
    ])
        ->where('status', 'active')
        ->latest()
        ->take(4)
        ->get();


    // Segment aktif
    $segments = Segment::where('is_active', true)
        ->orderBy('id')
        ->get();


    // Collection aktif + jumlah produk aktif
    $collections = Collection::where('is_active', true)
        ->withCount([
            'products' => function ($query) {
                $query->where(
                    'products.status',
                    'active'
                );
            }
        ])
        ->orderBy('id')
        ->get();


    return view('public.home', compact(
        'products',
        'segments',
        'collections'
    ));
})->name('home.preview');


// ========================
// CATALOG PREVIEW
// ========================

Route::get('/catalog-preview', function () {

    $query = Product::with([
        'segment',
        'category',
        'variants',
        'images',
    ])
        ->withMin([
            'variants' => function ($query) {
                $query->where('status', 'active');
            }
        ], 'price')
        ->where('status', 'active');

    // Filter segment
    if (request('segment')) {
        $query->whereHas('segment', function ($q) {
            $q->where('slug', request('segment'));
        });
    }


    // Filter group
    if (request('group') === 'clothing') {
        $query->whereHas('category', function ($q) {
            $q->whereIn('slug', [
                't-shirts',
                'shirts',
                'pants',
                'outerwear',
            ]);
        });
    }


    // Filter category
    if (request('category')) {
        $query->whereHas('category', function ($q) {
            $q->where('slug', request('category'));
        });
    }

    // Filter collection
    if (request('collection')) {
        $query->whereHas('collections', function ($q) {
            $q->where(
                'collections.slug',
                request('collection')
            );
        });
    }

    // Sorting
    switch (request('sort')) {

        case 'price_low':
            $query->orderBy('variants_min_price');
            break;

        case 'price_high':
            $query->orderByDesc('variants_min_price');
            break;

        case 'name':
            $query->orderBy('name');
            break;

        default:
            $query->latest();
            break;
    }

    $products = $query
        ->paginate(12)
        ->withQueryString();

    $segments = Segment::where('is_active', true)
        ->orderBy('name')
        ->get();

    $categories = Category::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('public.catalog', compact(
        'products',
        'segments',
        'categories'
    ));
})->name('catalog.preview');

Route::get('/search-preview', function () {

    $keyword = request('q');

    $query = Product::with([
        'segment',
        'category',
        'variants',
        'images',
    ])
        ->where('status', 'active');

    if ($keyword) {
        $query->where(function ($q) use ($keyword) {

            $q->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')

                ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                    $categoryQuery->where('name', 'like', '%' . $keyword . '%');
                })

                ->orWhereHas('segment', function ($segmentQuery) use ($keyword) {
                    $segmentQuery->where('name', 'like', '%' . $keyword . '%');
                });
        });
    }

    $products = $query
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view('public.search', compact(
        'products',
        'keyword'
    ));
})->name('search.preview');


// ========================
// PRODUCT DETAIL
// ========================

Route::get('/product/{slug}', function ($slug) {

    $product = Product::with([
        'segment',
        'category',
        'variants',
        'images',
        'collections',
    ])
        ->where('status', 'active')
        ->where('slug', $slug)
        ->firstOrFail();

    $relatedProducts = Product::with([
        'segment',
        'category',
        'variants',
        'images',
    ])
        ->where('status', 'active')
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    return view('public.product-detail', compact(
        'product',
        'relatedProducts'
    ));
})->name('product.detail');

// ========================
// GUEST
// ========================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.process');
});


// ========================
// AUTHENTICATED
// ========================

Route::middleware('auth')->group(function () {

    // ========================
    // LOGOUT
    // ========================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // ========================
    // CUSTOMER
    // ========================

    Route::middleware('role:customer')->group(function () {

        Route::get('/account', function () {
            return view('customer.account');
        })->name('customer.account');
    });


    // ========================
    // ADMIN
    // ========================

    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');

            Route::get(
                '/products',
                [ProductController::class, 'index']
            )->name('admin.products.index');

            Route::get(
                '/products/create',
                [ProductController::class, 'create']
            )->name('admin.products.create');


            Route::post(
                '/products',
                [ProductController::class, 'store']
            )->name('admin.products.store');


            Route::get(
                '/products/{product}/edit',
                [ProductController::class, 'edit']
            )->name('admin.products.edit');


            Route::put(
                '/products/{product}',
                [ProductController::class, 'update']
            )->name('admin.products.update');

            Route::get(
                '/orders',
                [OrderController::class, 'index']
            )->name('admin.orders.index');


            Route::get(
                '/orders/{order}',
                [OrderController::class, 'show']
            )->name('admin.orders.show');

            Route::get(
                '/payments',
                [PaymentController::class, 'index']
            )->name('admin.payments.index');


            Route::post(
                '/payments/{payment}/verify',
                [PaymentController::class, 'verify']
            )->name('admin.payments.verify');


            Route::post(
                '/payments/{payment}/reject',
                [PaymentController::class, 'reject']
            )->name('admin.payments.reject');

            Route::get(
                '/variants',
                [ProductVariantController::class, 'index']
            )->name('admin.variants.index');

            Route::get(
                '/variants/create',
                [ProductVariantController::class, 'create']
            )->name('admin.variants.create');

            Route::post(
                '/variants',
                [ProductVariantController::class, 'store']
            )->name('admin.variants.store');

            Route::get(
                '/variants/{variant}/edit',
                [ProductVariantController::class, 'edit']
            )->name('admin.variants.edit');

            Route::put(
                '/variants/{variant}',
                [ProductVariantController::class, 'update']
            )->name('admin.variants.update');

            // ========================
            // CATEGORIES
            // ========================

            Route::get(
                '/categories',
                [CategoryController::class, 'index']
            )->name('admin.categories.index');

            Route::get(
                '/categories/create',
                [CategoryController::class, 'create']
            )->name('admin.categories.create');

            Route::post(
                '/categories',
                [CategoryController::class, 'store']
            )->name('admin.categories.store');

            Route::get(
                '/categories/{category}/edit',
                [CategoryController::class, 'edit']
            )->name('admin.categories.edit');

            Route::put(
                '/categories/{category}',
                [CategoryController::class, 'update']
            )->name('admin.categories.update');

            Route::delete(
                '/categories/{category}',
                [CategoryController::class, 'destroy']
            )->name('admin.categories.destroy');

            // ========================
            // COLLECTIONS
            // ========================

            Route::get(
                '/collections',
                [CollectionController::class, 'index']
            )->name('admin.collections.index');

            Route::get(
                '/collections/create',
                [CollectionController::class, 'create']
            )->name('admin.collections.create');

            Route::post(
                '/collections',
                [CollectionController::class, 'store']
            )->name('admin.collections.store');

            Route::get(
                '/collections/{collection}/edit',
                [CollectionController::class, 'edit']
            )->name('admin.collections.edit');

            Route::put(
                '/collections/{collection}',
                [CollectionController::class, 'update']
            )->name('admin.collections.update');

            Route::delete(
                '/collections/{collection}',
                [CollectionController::class, 'destroy']
            )->name('admin.collections.destroy');
        });


    // ========================
    // STAFF GUDANG
    // ========================

    Route::prefix('staff')
        ->middleware('role:staff_gudang')
        ->group(function () {

            Route::get(
                '/dashboard',
                [StaffDashboardController::class, 'index']
            )->name('staff.dashboard');

            Route::get(
                '/stock',
                [StockController::class, 'index']
            )->name('staff.stock.index');


            Route::post(
                '/stock/{variant}',
                [StockController::class, 'update']
            )->name('staff.stock.update');

            Route::get(
                '/orders/processing',
                [StaffOrderController::class, 'processing']
            )->name('staff.orders.processing');


            Route::post(
                '/orders/{order}/packing',
                [StaffOrderController::class, 'markPacking']
            )->name('staff.orders.packing');

            Route::get(
                '/orders/shipping',
                [StaffOrderController::class, 'shipping']
            )->name('staff.orders.shipping');


            Route::post(
                '/orders/{order}/ship',
                [StaffOrderController::class, 'ship']
            )->name('staff.orders.ship');
        });


    // ========================
    // OWNER
    // ========================

    Route::prefix('owner')
        ->middleware('role:owner')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('owner.dashboard');
            })->name('owner.dashboard');
        });
});
