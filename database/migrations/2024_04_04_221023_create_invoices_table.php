<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         */

        public function up(): void
        {
            Schema::create('invoices' , function ( Blueprint $table ) {
                $table->id();
                $table->string("invoice_number" , 50);
                $table->date('invoice_Date')->nullable();
                $table->date('Due_date')->nullable();
                $table->string("product" , 100);
                $table->foreignId("section_id")->references("id")->on("sections")->cascadeOnDelete()->cascadeOnUpdate();
                $table->decimal("Amount_collection" , 15 , 2)->nullable();
                $table->decimal("Amount_Commission" , 15 , 2)->nullable();
                $table->decimal("Value_VAT" , 15 , 2);
                $table->decimal("Discount" , 15 , 2);
                $table->string("Rate_VAT" , 999);
                $table->decimal("Total" , 15 , 2);
                $table->string("Status" , 50);
                $table->integer("Value_Status");
                $table->text("note")->nullable();
                $table->date('Payment_Date')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('invoices');
        }
    };
