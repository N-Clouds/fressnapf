<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->text('phone')->nullable();
            $table->text('mobile')->nullable();
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->text('phone')->nullable();
            $table->text('mobile')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
        });
    }
};
