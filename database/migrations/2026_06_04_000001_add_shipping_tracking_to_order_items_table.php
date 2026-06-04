<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'shipping_carrier')) {
                $table->string('shipping_carrier', 120)->nullable()->after('line_total');
            }

            if (!Schema::hasColumn('order_items', 'tracking_number')) {
                $table->string('tracking_number', 120)->nullable()->after('shipping_carrier');
            }

            if (!Schema::hasColumn('order_items', 'tracking_events')) {
                $table->json('tracking_events')->nullable()->after('tracking_number');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'tracking_events')) {
                $table->dropColumn('tracking_events');
            }

            if (Schema::hasColumn('order_items', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }

            if (Schema::hasColumn('order_items', 'shipping_carrier')) {
                $table->dropColumn('shipping_carrier');
            }
        });
    }
};
