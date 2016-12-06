<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->unsigned();
            $table->string('voucher_no');
            $table->timestamp('purchased_date');
            $table->integer('total_amount')->default(0);
            $table->integer('received_amount')->default(0);
            $table->integer('net_amount')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->text('remark')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_cancel')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
