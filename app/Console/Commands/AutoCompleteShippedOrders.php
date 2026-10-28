<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCompleteShippedOrders extends Command
{
    protected $signature = 'orders:auto-complete
                            {--dry-run : Periksa pesanan tanpa mengubah data}';

    protected $description =
        'Menutup otomatis pesanan shipped yang melewati masa tenggang tanpa komplain aktif.';

    public function handle(): int
    {
        $now = CarbonImmutable::now(config('app.timezone'));

        /*
         * Tanggal akhir estimasi harus sudah terlewati,
         * kemudian tiga hari kalender penuh diberikan
         * sebagai masa tenggang.
         *
         * Contoh:
         * Estimasi berakhir 30 Oktober.
         * Masa tenggang: 31 Oktober, 1 November, 2 November.
         * Paling awal otomatis ditutup: 3 November pukul 00.00.
         */
        $cutoffDate = $now
            ->startOfDay()
            ->subDays(4)
            ->toDateString();

        $candidates = Order::query()
            ->where('status', 'shipped')
            ->whereNotNull('estimated_delivery_end')
            ->whereDate('estimated_delivery_end', '<=', $cutoffDate)
            ->whereDoesntHave('complaints', function ($query) {
                $query->whereIn('status', [
                    'open',
                    'investigating',
                ]);
            })
            ->orderBy('id');

        // Mode pemeriksaan aman: tidak mengubah database.
        if ($this->option('dry-run')) {
            $orders = $candidates->get([
                'id',
                'order_code',
                'estimated_delivery_end',
            ]);

            if ($orders->isEmpty()) {
                $this->info(
                    'Tidak ada pesanan yang memenuhi syarat otomatisasi.'
                );

                return self::SUCCESS;
            }

            foreach ($orders as $order) {
                $this->line(sprintf(
                    'Kandidat: %s | Estimasi akhir: %s',
                    $order->order_code,
                    $order->estimated_delivery_end->format('d-m-Y')
                ));
            }

            $this->info(sprintf(
                'Total kandidat: %d. Tidak ada data yang diubah.',
                $orders->count()
            ));

            return self::SUCCESS;
        }

        $completedCount = 0;

        $candidates
            ->select('id')
            ->chunkById(100, function ($orders) use (&$completedCount) {
                foreach ($orders as $candidate) {
                    $completed = DB::transaction(function () use ($candidate) {
                        $order = Order::query()
                            ->lockForUpdate()
                            ->find($candidate->id);

                        if (
                            ! $order
                            || $order->status !== 'shipped'
                            || ! $order->estimated_delivery_end
                        ) {
                            return false;
                        }

                        $now = CarbonImmutable::now(
                            config('app.timezone')
                        );

                        $eligibleAt = CarbonImmutable::parse(
                            $order->estimated_delivery_end->toDateString(),
                            config('app.timezone')
                        )->startOfDay()->addDays(4);

                        if ($now->lt($eligibleAt)) {
                            return false;
                        }

                        // Komplain aktif selalu menghalangi
                        // penyelesaian otomatis.
                        $hasActiveComplaint = $order->complaints()
                            ->whereIn('status', [
                                'open',
                                'investigating',
                            ])
                            ->exists();

                        if ($hasActiveComplaint) {
                            return false;
                        }

                        $order->update([
                            'status' => 'completed',
                            'completed_at' => $now,
                            'completion_reason' => 'auto_timeout',
                        ]);

                        return true;
                    });

                    if ($completed) {
                        $completedCount++;
                    }
                }
            });

        $this->info(sprintf(
            'Otomatisasi selesai. Pesanan ditutup otomatis: %d.',
            $completedCount
        ));

        return self::SUCCESS;
    }
}
