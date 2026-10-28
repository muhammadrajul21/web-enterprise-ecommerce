
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('estimated_delivery_start')
                ->nullable()
                ->after('shipping_service');

            $table->date('estimated_delivery_end')
                ->nullable()
                ->after('estimated_delivery_start');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_delivery_start',
                'estimated_delivery_end',
            ]);
        });
    }
};
