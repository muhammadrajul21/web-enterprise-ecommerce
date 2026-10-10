<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

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

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'category',
                        function ($categoryQuery) use ($search) {
                            $categoryQuery->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        }
                    )
                    ->orWhereHas(
                        'segment',
                        function ($segmentQuery) use ($search) {
                            $segmentQuery->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        }
                    );
            });
        }

        if (
            in_array(
                $status,
                ['draft', 'active', 'inactive'],
                true
            )
        ) {
            $query->where('status', $status);
        }

        $products = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.products.index',
            compact(
                'products',
                'search',
                'status'
            )
        );
    }


    public function create()
    {
        $segments = Segment::where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $collections = Collection::where('is_active', true)
            ->orderBy('name')
            ->get();

        $product = null;

        return view(
            'admin.products.form',
            compact(
                'product',
                'segments',
                'categories',
                'collections'
            )
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'segment_id' => [
                'nullable',
                'exists:segments,id',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,active,inactive',
            ],

            'collections' => [
                'nullable',
                'array',
            ],

            'collections.*' => [
                'distinct',
                'exists:collections,id',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $storedImage = null;

        try {

            $product = DB::transaction(
                function () use (
                    $request,
                    $validated,
                    &$storedImage
                ) {

                    $product = Product::create([
                        'segment_id' =>
                            $validated['segment_id'] ?? null,

                        'category_id' =>
                            $validated['category_id'],

                        'name' =>
                            $validated['name'],

                        'slug' =>
                            $this->generateUniqueSlug(
                                $validated['name']
                            ),

                        'description' =>
                            $validated['description'] ?? null,

                        'status' =>
                            $validated['status'],

                        'is_featured' =>
                            $request->boolean('is_featured'),
                    ]);


                    $product
                        ->collections()
                        ->sync(
                            $validated['collections']
                            ?? []
                        );


                    if ($request->hasFile('image')) {

                        $storedImage =
                            $request
                                ->file('image')
                                ->store(
                                    'products',
                                    'public'
                                );

                        $product
                            ->images()
                            ->create([
                                'variant_id' => null,

                                'image_path' =>
                                    'storage/'
                                    . $storedImage,

                                'is_primary' => true,

                                'sort_order' => 0,
                            ]);
                    }

                    return $product;
                }
            );

        } catch (Throwable $exception) {

            if ($storedImage) {
                Storage::disk('public')
                    ->delete($storedImage);
            }

            throw $exception;
        }


        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product berhasil ditambahkan.'
            );
    }


    public function edit(Product $product)
    {
        $product->load([
            'collections',
            'images',
        ]);

        $segments = Segment::where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $collections = Collection::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.form',
            compact(
                'product',
                'segments',
                'categories',
                'collections'
            )
        );
    }


    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'segment_id' => [
                'nullable',
                'exists:segments,id',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,active,inactive',
            ],

            'collections' => [
                'nullable',
                'array',
            ],

            'collections.*' => [
                'distinct',
                'exists:collections,id',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        $newImage = null;
        $oldImage = null;


        try {

            DB::transaction(
                function () use (
                    $request,
                    $validated,
                    $product,
                    &$newImage,
                    &$oldImage
                ) {

                    $product->update([
                        'segment_id' =>
                            $validated['segment_id'] ?? null,

                        'category_id' =>
                            $validated['category_id'],

                        'name' =>
                            $validated['name'],

                        'slug' =>
                            $this->generateUniqueSlug(
                                $validated['name'],
                                $product->id
                            ),

                        'description' =>
                            $validated['description'] ?? null,

                        'status' =>
                            $validated['status'],

                        'is_featured' =>
                            $request->boolean('is_featured'),
                    ]);


                    $product
                        ->collections()
                        ->sync(
                            $validated['collections']
                            ?? []
                        );


                    if ($request->hasFile('image')) {

                        $primaryImage =
                            $product
                                ->images()
                                ->where(
                                    'is_primary',
                                    true
                                )
                                ->first();


                        if ($primaryImage) {

                            if (
                                Str::startsWith(
                                    $primaryImage->image_path,
                                    'storage/'
                                )
                            ) {
                                $oldImage =
                                    Str::after(
                                        $primaryImage->image_path,
                                        'storage/'
                                    );
                            }

                            $primaryImage->delete();
                        }


                        $newImage =
                            $request
                                ->file('image')
                                ->store(
                                    'products',
                                    'public'
                                );


                        $product
                            ->images()
                            ->create([
                                'variant_id' => null,

                                'image_path' =>
                                    'storage/'
                                    . $newImage,

                                'is_primary' => true,

                                'sort_order' => 0,
                            ]);
                    }
                }
            );

        } catch (Throwable $exception) {

            if ($newImage) {
                Storage::disk('public')
                    ->delete($newImage);
            }

            throw $exception;
        }


        if ($oldImage) {
            Storage::disk('public')
                ->delete($oldImage);
        }


        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product berhasil diperbarui.'
            );
    }


    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;

        $counter = 1;


        while (true) {

            $query = Product::where(
                'slug',
                $slug
            );

            if ($ignoreId !== null) {
                $query->where(
                    'id',
                    '!=',
                    $ignoreId
                );
            }

            if (!$query->exists()) {
                break;
            }

            $slug =
                $baseSlug
                . '-'
                . $counter;

            $counter++;
        }


        return $slug;
    }
}