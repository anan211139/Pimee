<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSendGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('examgroup_id');
            $table->Integer('room_id');
            $table->dateTimeTz('exp_date');
            $table->boolean('noti_status')->default(false);
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
        Schema::dropIfExists('send_groups');
    }
}
