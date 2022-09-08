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
        Schema::create('user_profs', function (Blueprint $table) {
            $table->id();
            $table->string('line_user_id');
            $table->string('line_user_name');
            $table->string('prof_img_url');
            $table->string('prof_msg');
            $table->string('user_os');
            $table->string('user_trans');
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
        Schema::dropIfExists('user_prof');
    }
};
