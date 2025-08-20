<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Basis-Felder
            $table->string('order_line_id')->unique();
            $table->unsignedBigInteger('offer_id');
            $table->string('offer_sku');
            $table->string('offer_state_code')->nullable();
            $table->string('product_shop_sku')->nullable();
            $table->string('product_sku');
            $table->string('product_title');
            $table->unsignedInteger('order_line_index')->nullable();
            $table->string('order_line_state')->nullable();
            $table->string('order_line_state_reason_code')->nullable();
            $table->string('order_line_state_reason_label')->nullable();

            // Mengen und Preise
            $table->unsignedInteger('quantity');
            $table->decimal('origin_unit_price', 12, 2)->nullable();
            $table->decimal('price_unit', 12, 2)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2);
            $table->decimal('commission_fee', 12, 2)->nullable();
            $table->decimal('total_commission', 12, 2)->nullable();
            $table->decimal('shipping_price', 12, 2)->nullable();

            // Flags
            $table->boolean('can_open_incident')->default(false);
            $table->boolean('can_refund')->default(false);

            // Zeitstempel aus API
            $table->timestamp('created_date')->nullable();
            $table->timestamp('debited_date')->nullable();
            $table->timestamp('received_date')->nullable();
            $table->timestamp('shipped_date')->nullable();
            $table->timestamp('last_updated_date')->nullable();

            // Labels, Hinweise
            $table->string('category_code')->nullable();
            $table->string('category_label')->nullable();
            $table->string('tax_legal_notice')->nullable();
            $table->text('description')->nullable();

            // Komplexe Strukturen als JSON
            $table->json('cancelations')->nullable();
            $table->json('commission_taxes')->nullable();
            $table->json('eco_contributions')->nullable();
            $table->json('fees')->nullable();
            $table->json('funding')->nullable();
            $table->json('order_line_additional_fields')->nullable();
            $table->json('product_medias')->nullable();
            $table->json('promotions')->nullable();
            $table->json('purchase_information')->nullable();
            $table->json('refunds')->nullable();
            $table->json('shipping_from')->nullable();
            $table->json('shipping_taxes')->nullable();
            $table->json('taxes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_lines');
    }
};
