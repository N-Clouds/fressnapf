<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('firstname')->change();
            $table->text('lastname')->change();
        });

        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->text('firstname')->nullable()->change();
            $table->text('lastname')->nullable()->change();
            $table->text('company')->nullable()->change();
            $table->text('street_1')->nullable()->change();
            $table->text('street_2')->nullable()->change();
            $table->text('city')->nullable()->change();
            $table->text('zip_code')->nullable()->change();
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->text('firstname')->nullable()->change();
            $table->text('lastname')->nullable()->change();
            $table->text('company')->nullable()->change();
            $table->text('street_1')->nullable()->change();
            $table->text('street_2')->nullable()->change();
            $table->text('city')->nullable()->change();
            $table->text('zip_code')->nullable()->change();
        });

    }

    public function down(): void
    {
    }
};
