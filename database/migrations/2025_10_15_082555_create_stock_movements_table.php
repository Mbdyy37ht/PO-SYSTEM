<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->enum('movement_type', ['in', 'out']); // in = masuk, out = keluar
            $table->integer('quantity');
            $table->integer('stock_before'); // stok sebelum transaksi
            $table->integer('stock_after'); // stok setelah transaksi
            $table->string('reference_type'); // PurchaseOrder, GoodReceiptNote, SalesOrder, Delivery
            $table->unsignedBigInteger('reference_id'); // ID dari referensi
            $table->string('reference_number'); // Nomor dokumen (PO/GRN/SO/Delivery)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('movement_date');
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['item_id', 'warehouse_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
