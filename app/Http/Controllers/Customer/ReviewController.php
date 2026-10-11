<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Menyimpan review produk dari order yang sudah selesai.
     */
    public function store(
        Request $request,
        Order $order,
        OrderItem $orderItem
    ) {
        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );


        /*
         * Pastikan item benar-benar milik order tersebut.
         */
        abort_unless(
            (int) $orderItem->order_id === (int) $order->id,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'review' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        try {

            DB::transaction(function () use (
                $validated,
                $order,
                $orderItem
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder = Order::where(
                    'id',
                    $order->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                 * Review hanya boleh setelah order selesai.
                 */
                if ($lockedOrder->status !== 'completed') {

                    throw new \RuntimeException(
                        'Review hanya dapat diberikan setelah pesanan selesai.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER ITEM
                |--------------------------------------------------------------------------
                */

                $lockedItem = OrderItem::where(
                    'id',
                    $orderItem->id
                )
                    ->where(
                        'order_id',
                        $lockedOrder->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                 * Database memang sudah unique,
                 * tapi kita tetap cek agar error lebih ramah.
                 */
                $alreadyReviewed = Review::where(
                    'order_item_id',
                    $lockedItem->id
                )->exists();


                if ($alreadyReviewed) {

                    throw new \RuntimeException(
                        'Produk pada pesanan ini sudah direview.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CREATE REVIEW
                |--------------------------------------------------------------------------
                */

                Review::create([
                    'user_id' =>
                        auth()->id(),

                    'order_item_id' =>
                        $lockedItem->id,

                    'rating' =>
                        $validated['rating'],

                    'review' =>
                        $validated['review']
                        ?: null,

                    'is_visible' =>
                        true,
                ]);

            });


            return back()->with(
                'success',
                'Review berhasil dikirim.'
            );

        } catch (\RuntimeException $exception) {

            return back()->with(
                'error',
                $exception->getMessage()
            );

        }
    }
}