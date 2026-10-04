<?php

use App\Http\Controllers\AuthController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Segment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// ========================
// HOME / REDIRECT SESUAI ROLE
// ========================

Route::get('/', function () {

    // Jika belum login, arahkan ke login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // Jika sudah login, arahkan sesuai role
    return match (Auth::user()->role?->name) {

        'admin' =>
        redirect()->route('admin.dashboard'),

        'staff_gudang' =>
        redirect()->route('staff.dashboard'),

        'owner' =>
        redirect()->route('owner.dashboard'),

        'customer' =>
        redirect()->route('customer.account'),

        default =>
        abort(403, 'Role akun tidak dikenali.'),
    };
})->name('home');


// ========================
// HOME PREVIEW SAYED
// ========================

Route::get('/home-preview', function () {

    $products = Product::with([
        'segment',
        'category',
        'variants',
        'images'
    ])
        ->where('status', 'active')
        ->latest()
        ->take(4)
        ->get();

    return view('public.home', compact('products'));
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
        ->withMin('variants', 'price')
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
        });


    // ========================
    // STAFF GUDANG
    // ========================

    Route::prefix('staff')
        ->middleware('role:staff_gudang')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('staff.dashboard');
            })->name('staff.dashboard');
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
