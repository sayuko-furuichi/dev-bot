<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_store_status', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id');
            $table->string('channel_secret');
            $table->string('channel_access_token');
            $table->string('liff_url');
            $table->integer('member_richmenu_id');
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
        Schema::dropIfExists('line_store_status');
    }
};
