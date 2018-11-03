<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeworkResultNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_result_news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('line_code');
            $table->Integer('examgroup_id');
            $table->Integer('total');
            // $table->boolean('status')->default(false);
            $table->timestamps();
        });
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
        Schema::dropIfExists('homework_result_news');
    }
}
