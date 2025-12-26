<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('name');                 // اسم المخلفات
            $table->text('description')->nullable(); // وصف
            $table->string('image')->nullable();    // صورة (path)

            // Pricing
            $table->enum('pricing_type', ['kg', 'piece']);
            $table->decimal('price', 8, 2);         // سعر الكيلو أو القطعة

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
