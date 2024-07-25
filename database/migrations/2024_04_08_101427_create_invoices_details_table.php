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
        Schema::create('invoices_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_Invoice')->references("id")->on("invoices")->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("invoice_number", 100);
            $table->string("product", 50);
            $table->string("section", 50);
            $table->string("Status", 50);
            $table->integer("Value_Status");
            $table->date("Payment_Date")->nullable();
            $table->text("note")->nullable();
            $table->string("user", 300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_details');
    }
};
