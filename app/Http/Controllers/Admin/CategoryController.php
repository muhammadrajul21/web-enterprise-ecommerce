<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = Category::withCount('products');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        }

        if ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.categories.index',
            compact(
                'categories',
                'search',
                'status'
            )
        );
    }


    public function create()
    {
        $category = null;

        return view(
            'admin.categories.form',
            compact('category')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Category::create([
            'name' => $validated['name'],

            'slug' => $this->generateUniqueSlug(
                $validated['name']
            ),

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category berhasil ditambahkan.'
            );
    }


    public function edit(Category $category)
    {
        return view(
            'admin.categories.form',
            compact('category')
        );
    }


    public function update(
        Request $request,
        Category $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'categories',
                    'name'
                )->ignore($category->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $category->update([
            'name' => $validated['name'],

            'slug' => $this->generateUniqueSlug(
                $validated['name'],
                $category->id
            ),

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category berhasil diperbarui.'
            );
    }


    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    'Category tidak dapat dihapus karena masih digunakan oleh product.'
                );
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category berhasil dihapus.'
            );
    }


    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Category::where(
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

            $slug = $baseSlug . '-' . $counter;

            $counter++;
        }

        return $slug;
    }
}