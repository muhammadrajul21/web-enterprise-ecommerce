<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = Product::with([
            'segment',
            'category',
            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },
        ])
            ->withCount('variants')
            ->withMin('variants', 'price')
            ->withMax('variants', 'price');

        // Pencarian produk
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );
                    })
                    ->orWhereHas('segment', function ($segmentQuery) use ($search) {
                        $segmentQuery->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        // Filter status
        if (in_array($status, ['draft', 'active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $products = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact(
            'products',
            'search',
            'status'
        ));
    }
}