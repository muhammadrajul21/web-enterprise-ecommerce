<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    /**
     * Menampilkan daftar stok produk.
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with('product')
            ->orderBy('stock', 'asc')
            ->orderBy('sku', 'asc');


        // =========================
        // SEARCH
        // =========================

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($query) use ($search) {

                $query
                    ->where(
                        'sku',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'color',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'size',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'product',
                        function ($productQuery) use ($search) {

                            $productQuery->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        }
                    );
            });
        }


        // =========================
        // FILTER STATUS VARIANT
        // =========================

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        // =========================
        // FILTER STOCK
        // =========================

        if ($request->stock_filter === 'low') {

            $query->whereBetween(
                'stock',
                [1, 5]
            );
        } elseif ($request->stock_filter === 'out') {

            $query->where(
                'stock',
                0
            );
        } elseif ($request->stock_filter === 'available') {

            $query->where(
                'stock',
                '>',
                5
            );
        }


        $variants = $query
            ->paginate(12)
            ->withQueryString();


        // =========================
        // STOCK LOG TERBARU
        // =========================

        $stockLogs = StockLog::with([
            'variant.product',
            'creator',
        ])
            ->latest()
            ->take(10)
            ->get();


        return view(
            'staff.stock.index',
            compact(
                'variants',
                'stockLogs'
            )
        );
    }


    /**
     * Mengubah stok produk.
     */
    public function update(
        Request $request,
        ProductVariant $variant
    ) {

        // =========================
        // VALIDATION
        // =========================

        $request->validate([

            'type' => [
                'required',
                Rule::in([
                    'IN',
                    'OUT',
                    'RETURN',
                    'ADJUSTMENT',
                ]),
            ],

            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'new_stock' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        // =========================
        // VALIDASI KHUSUS
        // =========================

        if (
            $request->type === 'ADJUSTMENT'
            && $request->new_stock === null
        ) {

            return back()
                ->withErrors([
                    'new_stock' =>
                    'Stok akhir wajib diisi untuk adjustment.',
                ])
                ->withInput();
        }


        if (
            $request->type !== 'ADJUSTMENT'
            && !$request->quantity
        ) {

            return back()
                ->withErrors([
                    'quantity' =>
                    'Quantity wajib diisi.',
                ])
                ->withInput();
        }


        try {

            DB::transaction(function () use (
                $request,
                $variant
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK VARIANT
                |--------------------------------------------------------------------------
                |
                | Menghindari dua perubahan stok terjadi bersamaan.
                |
                */

                $lockedVariant = ProductVariant::where(
                    'id',
                    $variant->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();


                $stockBefore =
                    (int) $lockedVariant->stock;

                $stockAfter =
                    $stockBefore;

                $logQuantity = 0;


                // =========================
                // STOCK IN
                // =========================

                if ($request->type === 'IN') {

                    $quantity =
                        (int) $request->quantity;

                    $stockAfter =
                        $stockBefore + $quantity;

                    $logQuantity =
                        $quantity;
                }


                // =========================
                // STOCK OUT
                // =========================

                elseif ($request->type === 'OUT') {

                    $quantity =
                        (int) $request->quantity;


                    if ($quantity > $stockBefore) {

                        throw new \RuntimeException(
                            'Jumlah stock out melebihi stok yang tersedia.'
                        );
                    }


                    $stockAfter =
                        $stockBefore - $quantity;

                    $logQuantity =
                        $quantity;
                }


                // =========================
                // RETURN
                // =========================

                elseif ($request->type === 'RETURN') {

                    $quantity =
                        (int) $request->quantity;

                    $stockAfter =
                        $stockBefore + $quantity;

                    $logQuantity =
                        $quantity;
                }


                // =========================
                // ADJUSTMENT
                // =========================

                elseif ($request->type === 'ADJUSTMENT') {

                    $stockAfter =
                        (int) $request->new_stock;

                    $logQuantity =
                        abs(
                            $stockAfter
                                - $stockBefore
                        );
                }


                // =========================
                // UPDATE VARIANT
                // =========================

                $lockedVariant->update([
                    'stock' => $stockAfter,
                ]);


                // =========================
                // STOCK LOG
                // =========================

                StockLog::create([

                    'variant_id' =>
                    $lockedVariant->id,

                    'type' =>
                    $request->type,

                    'quantity' =>
                    $logQuantity,

                    'stock_before' =>
                    $stockBefore,

                    'stock_after' =>
                    $stockAfter,

                    'reference_type' =>
                    'manual',

                    'reference_id' =>
                    null,

                    'note' =>
                    $request->note,

                    'created_by' =>
                    Auth::id(),

                ]);
            });


            return back()->with(
                'success',
                'Stok berhasil diperbarui.'
            );
        } catch (\RuntimeException $exception) {

            return back()
                ->withErrors([
                    'stock' =>
                    $exception->getMessage(),
                ])
                ->withInput();
        }
    }
}
