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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // الطلب تابع لمستخدم
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete(); // رايدر ممكن يكون فاضي
            $table->enum('status', ['draft', 'pending', 'assigned', 'collected', 'delivered', 'cancelled'])->default('draft');
            $table->decimal('total_quantity', 8, 2)->default(0); // مجموع الكميات بعد الاستلام
            $table->integer('total_points')->default(0); // مجموع النقاط بعد الاستلام
            $table->timestamp('scheduled_at')->nullable(); // ميعاد الجمع
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['address', 'latitude', 'longitude']);
    });
}

};
