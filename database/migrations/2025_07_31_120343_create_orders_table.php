<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_id')->unique();
            $table->string('commercial_id');
            $table->string('customer_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->nullable();
            $table->string('channel_code');
            $table->string('channel_label');
            $table->string('order_state');
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_commission', 10, 2);
            $table->string('currency_iso_code', 3);
            $table->string('shipping_type_label');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
