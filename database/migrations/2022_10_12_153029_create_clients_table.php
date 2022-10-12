<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('sei');
            $table->string('mei');
            $table->string('phone_number');
            $table->tinyInteger('prefecture_id');
            $table->string('municipality');
            $table->string('house_number');
            $table->string('building_name');
            $table->string('email');
            $table->text('line_user_id');
            $table->bigInteger('store_id');
            $table->integer('c_corporate_number');
            $table->string('c_corporate_name');
            $table->integer('referral_number');
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
        Schema::dropIfExists('clients');
    }
};
