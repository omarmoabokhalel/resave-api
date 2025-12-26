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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->decimal('estimated_quantity', 8, 2); // وزن تقديري من المستخدم
            $table->decimal('actual_quantity', 8, 2)->nullable(); // الوزن الحقيقي من الرايدر
            $table->decimal('price', 8, 2); // السعر وقت الطلب
            $table->integer('points_earned')->default(0); // النقاط تحسب بعد الوزن الفعلي
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('order_items', function (Blueprint $table) {
        $table->dropColumn('image');
    });
}

};
