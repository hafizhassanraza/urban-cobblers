<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('carrier')->nullable()->after('tracking_number');
            $table->text('admin_notes')->nullable()->after('notes');
            $table->timestamp('shipped_at')->nullable()->after('admin_notes');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['carrier', 'admin_notes', 'shipped_at', 'delivered_at']);
        });
    }
};
