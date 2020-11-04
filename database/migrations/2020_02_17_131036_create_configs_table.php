<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedInteger('time_limit')->nullable();
            $table->unsignedSmallInteger('time_mode');
            $table->unsignedSmallInteger('question_order');
            $table->unsignedSmallInteger('answer_order');
            $table->unsignedSmallInteger('result_status');
            $table->unsignedSmallInteger('ranking_status');
            $table->unsignedSmallInteger('passing_grade_status');
            $table->unsignedSmallInteger('score_status');
            $table->unsignedInteger('default_score');
            $table->unsignedInteger('default_passing_grade');
            $table->timestamps();

            $table->foreign('exam_id')
                ->references('id')->on('exams')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configs');
    }
}
