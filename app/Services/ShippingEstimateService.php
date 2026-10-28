<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShippingEstimateService
{
    /**
     * Tarif dan estimasi simulasi berdasarkan zona tujuan.
     * Biaya dalam rupiah, estimasi dalam hari kalender.
     */
    private const SHIPPING_RULES = [
        'aceh' => [
            'regular' => [
                'cost' => 0,
                'min_days' => 2,
                'max_days' => 4,
            ],
            'express' => [
                'cost' => 30000,
                'min_days' => 1,
                'max_days' => 2,
            ],
        ],

        'sumatra' => [
            'regular' => [
                'cost' => 15000,
                'min_days' => 3,
                'max_days' => 5,
            ],
            'express' => [
                'cost' => 35000,
                'min_days' => 2,
                'max_days' => 3,
            ],
        ],

        'java' => [
            'regular' => [
                'cost' => 25000,
                'min_days' => 4,
                'max_days' => 6,
            ],
            'express' => [
                'cost' => 50000,
                'min_days' => 2,
                'max_days' => 4,
            ],
        ],

        'bali_nusra' => [
            'regular' => [
                'cost' => 35000,
                'min_days' => 5,
                'max_days' => 7,
            ],
            'express' => [
                'cost' => 60000,
                'min_days' => 3,
                'max_days' => 5,
            ],
        ],

        'kalimantan' => [
            'regular' => [
                'cost' => 35000,
                'min_days' => 5,
                'max_days' => 8,
            ],
            'express' => [
                'cost' => 65000,
                'min_days' => 3,
                'max_days' => 5,
            ],
        ],

        'sulawesi' => [
            'regular' => [
                'cost' => 40000,
                'min_days' => 6,
                'max_days' => 9,
            ],
            'express' => [
                'cost' => 75000,
                'min_days' => 3,
                'max_days' => 6,
            ],
        ],

        'maluku_papua' => [
            'regular' => [
                'cost' => 60000,
                'min_days' => 8,
                'max_days' => 14,
            ],
            'express' => [
                'cost' => 100000,
                'min_days' => 5,
                'max_days' => 9,
            ],
        ],
    ];

    /**
     * Menghitung ongkir dan rentang estimasi pengiriman.
     */
    public function quote(
        string $province,
        string $method,
        ?CarbonImmutable $baseDate = null
    ): array {
        $zone = $this->findZone($province);

        if ($zone === null) {
            throw ValidationException::withMessages([
                'shipping_province' =>
                'Provinsi tujuan belum didukung oleh sistem ongkir.',
            ]);
        }

        if (!isset(self::SHIPPING_RULES[$zone][$method])) {
            throw ValidationException::withMessages([
                'shipping_method' =>
                'Metode pengiriman tidak valid.',
            ]);
        }

        $rule = self::SHIPPING_RULES[$zone][$method];

        $baseDate ??= CarbonImmutable::now(
            config('app.timezone')
        );

        $baseDate = $baseDate->startOfDay();

        return [
            'zone' => $zone,
            'method' => $method,
            'label' => ucfirst($method) . ' Shipping',
            'cost' => $rule['cost'],
            'min_days' => $rule['min_days'],
            'max_days' => $rule['max_days'],
            'estimated_delivery_start' => $baseDate
                ->addDays($rule['min_days'])
                ->toDateString(),
            'estimated_delivery_end' => $baseDate
                ->addDays($rule['max_days'])
                ->toDateString(),
        ];
    }
    public function supportedProvinces(): array
    {
        return [
            'Aceh',
            'Sumatera Utara',
            'Sumatera Barat',
            'Riau',
            'Kepulauan Riau',
            'Jambi',
            'Sumatera Selatan',
            'Bengkulu',
            'Lampung',
            'Kepulauan Bangka Belitung',
            'DKI Jakarta',
            'Banten',
            'Jawa Barat',
            'Jawa Tengah',
            'DI Yogyakarta',
            'Jawa Timur',
            'Bali',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Kalimantan Barat',
            'Kalimantan Tengah',
            'Kalimantan Selatan',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Sulawesi Utara',
            'Sulawesi Tengah',
            'Sulawesi Selatan',
            'Sulawesi Tenggara',
            'Sulawesi Barat',
            'Gorontalo',
            'Maluku',
            'Maluku Utara',
            'Papua',
            'Papua Barat',
            'Papua Barat Daya',
            'Papua Tengah',
            'Papua Pegunungan',
            'Papua Selatan',
        ];
    }


    /**
     * Mengelompokkan provinsi tujuan ke dalam zona.
     */
    private function findZone(string $province): ?string
    {
        $province = Str::of($province)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->trim()
            ->toString();

        $zones = [
            'aceh' => [
                'aceh',
                'nanggroe aceh darussalam',
            ],

            'sumatra' => [
                'sumatera utara',
                'sumatera barat',
                'riau',
                'kepulauan riau',
                'jambi',
                'sumatera selatan',
                'bengkulu',
                'lampung',
                'kepulauan bangka belitung',
                'bangka belitung',
            ],

            'java' => [
                'dki jakarta',
                'jakarta',
                'banten',
                'jawa barat',
                'jawa tengah',
                'di yogyakarta',
                'daerah istimewa yogyakarta',
                'yogyakarta',
                'jawa timur',
            ],

            'bali_nusra' => [
                'bali',
                'nusa tenggara barat',
                'nusa tenggara timur',
            ],

            'kalimantan' => [
                'kalimantan barat',
                'kalimantan tengah',
                'kalimantan selatan',
                'kalimantan timur',
                'kalimantan utara',
            ],

            'sulawesi' => [
                'sulawesi utara',
                'sulawesi tengah',
                'sulawesi selatan',
                'sulawesi tenggara',
                'sulawesi barat',
                'gorontalo',
            ],

            'maluku_papua' => [
                'maluku',
                'maluku utara',
                'papua',
                'papua barat',
                'papua barat daya',
                'papua tengah',
                'papua pegunungan',
                'papua selatan',
            ],
        ];

        foreach ($zones as $zone => $provinces) {
            if (in_array($province, $provinces, true)) {
                return $zone;
            }
        }

        return null;
    }
}
