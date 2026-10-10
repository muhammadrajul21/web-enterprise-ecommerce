<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $productId = $request->input('product');
        $status = $request->input('status');

        $query = ProductVariant::with('product');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('sku', 'like', '%' . $search . '%')
                    ->orWhere('color', 'like', '%' . $search . '%')
                    ->orWhere('size', 'like', '%' . $search . '%')
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $variants = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view(
            'admin.variants.index',
            compact(
                'variants',
                'products',
                'search',
                'productId',
                'status'
            )
        );
    }


    public function create()
    {
        $products = Product::orderBy('name')->get();

        $variant = null;

        return view(
            'admin.variants.form',
            compact(
                'products',
                'variant'
            )
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:255',
                'unique:product_variants,sku',
            ],

            'color' => [
                'nullable',
                'string',
                'max:100',
            ],

            'size' => [
                'nullable',
                'string',
                'max:50',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        ProductVariant::create([
            'product_id' => $validated['product_id'],

            'sku' => strtoupper(
                trim($validated['sku'])
            ),

            'color' =>
                ($validated['color'] ?? null)
                    ?: null,

            'size' =>
                ($validated['size'] ?? null)
                    ?: null,

            'price' => $validated['price'],

            'stock' => 0,

            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.variants.index')
            ->with(
                'success',
                'Variant berhasil ditambahkan.'
            );
    }


    public function edit(ProductVariant $variant)
    {
        $products = Product::orderBy('name')->get();

        return view(
            'admin.variants.form',
            compact(
                'variant',
                'products'
            )
        );
    }


    public function update(
        Request $request,
        ProductVariant $variant
    ) {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'product_variants',
                    'sku'
                )->ignore($variant->id),
            ],

            'color' => [
                'nullable',
                'string',
                'max:100',
            ],

            'size' => [
                'nullable',
                'string',
                'max:50',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $variant->update([
            'product_id' => $validated['product_id'],

            'sku' => strtoupper(
                trim($validated['sku'])
            ),

            'color' =>
                ($validated['color'] ?? null)
                    ?: null,

            'size' =>
                ($validated['size'] ?? null)
                    ?: null,

            'price' => $validated['price'],

            // Stock tidak diubah Admin.
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.variants.index')
            ->with(
                'success',
                'Variant berhasil diperbarui.'
            );
    }
}