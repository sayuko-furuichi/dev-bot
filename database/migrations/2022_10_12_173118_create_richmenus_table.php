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
        Schema::create('richmenus', function (Blueprint $table) {
            $table->id();
            $table->string('richmenu_id');
            $table->string('richmenu_name');
            $table->string('menu_bar_title');
            $table->string('image');
            $table->string('richmenu_alias_id');
            $table->boolean('is_default');
            $table->string('store_id');
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
        Schema::dropIfExists('richmenus');
    }
};
