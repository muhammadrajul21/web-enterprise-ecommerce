<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IndonesiaRegionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProvinces();
        $this->seedRegencies();

        $this->command->info(
            'Data provinsi dan kabupaten/kota berhasil dimasukkan.'
        );
    }

    /**
     * Memasukkan data provinsi.
     */
    private function seedProvinces(): void
    {
        $path = database_path('data/wilayah/provinces.csv');

        if (!file_exists($path)) {
            throw new RuntimeException(
                'File provinces.csv tidak ditemukan.'
            );
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException(
                'File provinces.csv tidak dapat dibuka.'
            );
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);

            throw new RuntimeException(
                'Header provinces.csv tidak valid.'
            );
        }

        $header[0] = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            trim($header[0])
        );

        $columns = array_flip(
            array_map('trim', $header)
        );

        if (
            !isset($columns['id'])
            || !isset($columns['name'])
        ) {
            fclose($handle);

            throw new RuntimeException(
                'Kolom provinces.csv harus memiliki id dan name.'
            );
        }

        $rows = [];
        $now = now();

        while (($data = fgetcsv($handle)) !== false) {
            $id = trim((string) ($data[$columns['id']] ?? ''));
            $name = trim((string) ($data[$columns['name']] ?? ''));

            if ($id === '' || $name === '') {
                continue;
            }

            // Menyesuaikan nama dengan sistem ongkir LIFESTYLE.
            $name = match ($name) {
                'Daerah Khusus Ibukota Jakarta' => 'DKI Jakarta',
                'Daerah Istimewa Yogyakarta' => 'DI Yogyakarta',
                default => $name,
            };

            $rows[] = [
                'id' => $id,
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            throw new RuntimeException(
                'Tidak ada data provinsi yang berhasil dibaca.'
            );
        }

        DB::table('provinces')->upsert(
            $rows,
            ['id'],
            ['name', 'updated_at']
        );

        $this->command->info(
            count($rows) . ' provinsi berhasil diproses.'
        );
    }

    /**
     * Memasukkan data kabupaten/kota.
     */
    private function seedRegencies(): void
    {
        $path = database_path('data/wilayah/regencies.csv');

        if (!file_exists($path)) {
            throw new RuntimeException(
                'File regencies.csv tidak ditemukan.'
            );
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException(
                'File regencies.csv tidak dapat dibuka.'
            );
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);

            throw new RuntimeException(
                'Header regencies.csv tidak valid.'
            );
        }

        $header[0] = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            trim($header[0])
        );

        $columns = array_flip(
            array_map('trim', $header)
        );

        foreach (['id', 'province_id', 'name'] as $column) {
            if (!isset($columns[$column])) {
                fclose($handle);

                throw new RuntimeException(
                    "Kolom {$column} tidak ditemukan di regencies.csv."
                );
            }
        }

        $rows = [];
        $now = now();

        while (($data = fgetcsv($handle)) !== false) {
            $id = trim((string) ($data[$columns['id']] ?? ''));

            $provinceId = trim(
                (string) ($data[$columns['province_id']] ?? '')
            );

            $name = trim(
                (string) ($data[$columns['name']] ?? '')
            );
            // Hilangkan awalan jenis wilayah agar konsisten
            // dengan format kota pada data alamat pelanggan.
            $name = preg_replace(
                '/^(Kabupaten Administrasi|Kabupaten|Kota Administrasi|Kota)\s+/i',
                '',
                $name
            );

            if (
                $id === ''
                || $provinceId === ''
                || $name === ''
            ) {
                continue;
            }

            $rows[] = [
                'id' => $id,
                'province_id' => $provinceId,
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            throw new RuntimeException(
                'Tidak ada data kabupaten/kota yang berhasil dibaca.'
            );
        }

        DB::table('regencies')->upsert(
            $rows,
            ['id'],
            ['province_id', 'name', 'updated_at']
        );

        $this->command->info(
            count($rows) . ' kabupaten/kota berhasil diproses.'
        );
    }
}
