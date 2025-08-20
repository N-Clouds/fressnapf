<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
        });
    }
};
