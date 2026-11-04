<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan riwayat order customer.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with([
                'items',
                'payments' => function ($query) {
                    $query->latest();
                },
            ])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Menormalisasi nama kota dan provinsi
     * agar mudah dicocokkan dengan dataset wilayah.
     */
    private static function normalizeRegionName(?string $value): string
    {
        $value = strtolower(trim($value ?? ''));

        $value = preg_replace(
            '/^(kabupaten|kab\.?|kota|kotamadya)\s*/u',
            '',
            $value
        ) ?? $value;

        return preg_replace('/[^a-z0-9]/', '', $value) ?? '';
    }

    /**
     * Menampilkan detail order customer.
     */
    public function show(Order $order)
    {
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
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

        $order->load([
            'items.review',
            'voucher',

            'payments' => function ($query) {
                $query->latest();
            },

            'complaints' => function ($query) {
                $query->latest();
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | PAYMENT TERBARU
        |--------------------------------------------------------------------------
        */

        $latestPayment = $order->payments->first();

        /*
        |--------------------------------------------------------------------------
        | SIMULATED SHIPMENT TRACKING
        |--------------------------------------------------------------------------
        |
        | Peta customer tidak lagi bergantung pada Proof of Delivery.
        | Titik awal menggunakan pusat distribusi LIFESTYLE di Banda Aceh.
        | Titik tujuan menggunakan koordinat pusat kabupaten/kota pelanggan.
        |
        */

        $origin = [
            'latitude' => 5.550,
            'longitude' => 95.318,
            'label' => 'Pusat Distribusi LIFESTYLE - Banda Aceh',
        ];

        /*
        |--------------------------------------------------------------------------
        | LOAD INDONESIA REGENCY COORDINATES
        |--------------------------------------------------------------------------
        |
        | Dataset lokal:
        | public/data/indonesia-regencies.json
        |
        | Data disimpan dalam cache agar JSON tidak dibaca
        | dan diproses ulang pada setiap permintaan.
        |
        */

        $regions = Cache::rememberForever(
            'lifestyle.regency-coordinates.v1',
            function () {

                $path = public_path(
                    'data/indonesia-regencies.json'
                );

                if (!is_file($path)) {
                    return [];
                }

                $content = file_get_contents($path);

                $rows = json_decode(
                    $content ?: '',
                    true
                );

                if (!is_array($rows)) {
                    return [];
                }

                $result = [];

                foreach ($rows as $row) {

                    $latitude = $row['geo']['lat'] ?? null;
                    $longitude = $row['geo']['lon'] ?? null;

                    if (
                        !is_numeric($latitude) ||
                        !is_numeric($longitude)
                    ) {
                        continue;
                    }

                    $localCity = $row['name']['local'] ?? null;
                    $englishCity = $row['name']['en'] ?? null;

                    $localProvince =
                        $row['parent']['name']['local'] ?? null;

                    $englishProvince =
                        $row['parent']['name']['en'] ?? null;

                    $cityKeys = array_values(array_unique(array_filter([
                        self::normalizeRegionName($localCity),
                        self::normalizeRegionName($englishCity),
                    ])));

                    $provinceKeys = array_values(array_unique(array_filter([
                        self::normalizeRegionName($localProvince),
                        self::normalizeRegionName($englishProvince),
                    ])));

                    if (empty($cityKeys)) {
                        continue;
                    }

                    $result[] = [
                        'city_keys' => $cityKeys,
                        'province_keys' => $provinceKeys,

                        'city_label' => $localCity ?? $englishCity,
                        'province_label' => $localProvince ?? $englishProvince,

                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude,
                    ];
                }

                return $result;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | MATCH CUSTOMER DESTINATION
        |--------------------------------------------------------------------------
        */

        $cityKey = self::normalizeRegionName(
            $order->shipping_city
        );

        $provinceKey = self::normalizeRegionName(
            $order->shipping_province
        );

        /*
         * Toleransi variasi penulisan Bireuen.
         */
        if ($cityKey === 'bireun') {
            $cityKey = 'bireuen';
        }

        $matchingRegions = array_values(array_filter(
            $regions,
            function ($region) use ($cityKey, $provinceKey) {

                $cityMatches = in_array(
                    $cityKey,
                    $region['city_keys'],
                    true
                );

                $provinceMatches = $provinceKey === ''
                    || in_array(
                        $provinceKey,
                        $region['province_keys'],
                        true
                    );

                return $cityMatches && $provinceMatches;
            }
        ));

        $destination = count($matchingRegions) === 1
            ? $matchingRegions[0]
            : null;

        /*
         * Jika nama provinsi tidak cocok,
         * lakukan pencocokan kota saja.
         * Hanya digunakan apabila hasilnya unik.
         */

        if (!$destination && $cityKey !== '') {

            $cityMatches = array_values(array_filter(
                $regions,
                fn ($region) => in_array(
                    $cityKey,
                    $region['city_keys'],
                    true
                )
            ));

            if (count($cityMatches) === 1) {
                $destination = $cityMatches[0];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRACKING PROGRESS
        |--------------------------------------------------------------------------
        |
        | Posisi marker merupakan simulasi.
        |
        | 15% = awal perjalanan.
        | 90% = mendekati tujuan.
        | 100% = pesanan selesai.
        |
        */

        $trackingProgress = 15;

        $trackingStageLabel = 'Paket baru diberangkatkan';

        if ($order->status === 'completed') {

            $trackingProgress = 100;

            $trackingStageLabel = 'Pesanan telah selesai';

        } elseif ($order->shipped_at) {

            if ($order->estimated_delivery_end) {

                $startTimestamp = $order->shipped_at->timestamp;

                $endTimestamp = $order->estimated_delivery_end
                    ->copy()
                    ->endOfDay()
                    ->timestamp;

                $duration = $endTimestamp - $startTimestamp;

                if ($duration > 0) {

                    $elapsed = now()->timestamp - $startTimestamp;

                    $ratio = max(
                        0,
                        min(1, $elapsed / $duration)
                    );

                    $trackingProgress = (int) round(
                        15 + (75 * $ratio)
                    );

                } else {

                    $trackingProgress = 85;
                }

            } else {

                $trackingProgress = 55;
            }

            if ($trackingProgress >= 75) {

                $trackingStageLabel = 'Mendekati area tujuan';

            } elseif ($trackingProgress >= 40) {

                $trackingStageLabel = 'Dalam perjalanan';

            } else {

                $trackingStageLabel = 'Paket baru diberangkatkan';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRACKING MAP DATA
        |--------------------------------------------------------------------------
        */

        $trackingMap = [
            'origin' => $origin,

            'destination' => $destination ? [
                'latitude' => $destination['latitude'],
                'longitude' => $destination['longitude'],

                'label' => trim(
                    ($order->shipping_city ?: $destination['city_label'])
                    . ', '
                    . ($order->shipping_province ?: $destination['province_label'])
                ),
            ] : null,

            'progress' => $trackingProgress,

            'stage_label' => $trackingStageLabel,

            'courier' => $order->courier ?: 'Kurir',

            'tracking_number' => $order->tracking_number,

            'estimated_start' => $order->estimated_delivery_start
                ?->format('d M Y'),

            'estimated_end' => $order->estimated_delivery_end
                ?->format('d M Y'),

            'order_status' => $order->status,
        ];


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'customer.orders.show',
            compact(
                'order',
                'latestPayment',
                'trackingMap'
            )
        );
    }

    /**
     * Customer mengonfirmasi bahwa pesanan sudah diterima.
     */
    public function confirmReceived(Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );

        if ($order->status !== 'shipped') {
            return back()->with(
                'error',
                'Hanya pesanan yang sedang dikirim yang dapat dikonfirmasi.'
            );
        }

        $confirmed = DB::transaction(function () use ($order) {

            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status !== 'shipped') {
                return false;
            }

            $hasOpenComplaint = $lockedOrder->complaints()
                ->whereIn('status', ['open', 'investigating'])
                ->exists();

            if ($hasOpenComplaint) {
                return false;
            }

            $lockedOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_reason' => 'customer_confirmed',
            ]);

            return true;
        });

        if (!$confirmed) {
            return back()->with(
                'error',
                'Pesanan berubah status atau memiliki komplain yang belum selesai.'
            );
        }

        return back()->with(
            'success',
            'Terima kasih! Pesanan telah dikonfirmasi diterima.'
        );
    }

    /**
     * Customer melaporkan masalah pada pesanan.
     */
    public function storeComplaint(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => [
                'required',
                'in:parcel_not_received,damaged,wrong_item,other',
            ],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $result = DB::transaction(function () use ($order, $validated) {

            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $eligible = $lockedOrder->status === 'shipped'
                || (
                    $lockedOrder->status === 'completed'
                    && $lockedOrder->completion_reason === 'auto_timeout'
                );

            if (!$eligible) {
                return 'ineligible';
            }

            $hasActiveComplaint = $lockedOrder->complaints()
                ->whereIn('status', ['open', 'investigating'])
                ->exists();

            if ($hasActiveComplaint) {
                return 'duplicate';
            }

            OrderComplaint::create([
                'order_id' => $lockedOrder->id,
                'user_id' => auth()->id(),
                'category' => $validated['category'],
                'message' => $validated['message'],
                'status' => 'open',
            ]);

            return 'created';
        });

        if ($result === 'ineligible') {
            return back()->with(
                'error',
                'Komplain hanya dapat diajukan untuk pesanan yang sedang dikirim atau selesai otomatis.'
            );
        }

        if ($result === 'duplicate') {
            return back()->with(
                'error',
                'Pesanan ini masih memiliki komplain yang sedang diproses.'
            );
        }

        return back()->with(
            'success',
            'Komplain berhasil dikirim. Admin akan meninjau laporan kamu.'
        );
    }
}
