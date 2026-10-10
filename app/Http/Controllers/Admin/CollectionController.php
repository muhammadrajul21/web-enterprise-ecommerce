<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = Collection::withCount('products');

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

        $collections = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.collections.index',
            compact(
                'collections',
                'search',
                'status'
            )
        );
    }


    public function create()
    {
        $collection = null;

        return view(
            'admin.collections.form',
            compact('collection')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:collections,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Collection::create([
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
            ->route('admin.collections.index')
            ->with(
                'success',
                'Collection berhasil ditambahkan.'
            );
    }


    public function edit(Collection $collection)
    {
        return view(
            'admin.collections.form',
            compact('collection')
        );
    }


    public function update(
        Request $request,
        Collection $collection
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'collections',
                    'name'
                )->ignore($collection->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $collection->update([
            'name' => $validated['name'],

            'slug' => $this->generateUniqueSlug(
                $validated['name'],
                $collection->id
            ),

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.collections.index')
            ->with(
                'success',
                'Collection berhasil diperbarui.'
            );
    }


    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with(
                'success',
                'Collection berhasil dihapus.'
            );
    }


    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'collection';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Collection::where(
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