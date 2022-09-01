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
            $table->string('name');
            $table->string('chat_bar');
            $table->string('img');
            $table->string('richmenu_alias_id');
            $table->boolean('is_default');
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
