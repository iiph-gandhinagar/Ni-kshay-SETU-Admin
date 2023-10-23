<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLbSubscriberRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_subscriber_rankings', function (Blueprint $table) {
            $table->id();
            $table->integer('subscriber_id');
            $table->integer('level_id');
            $table->integer('badge_id');
            $table->string('mins_spent_count');
            $table->string('sub_module_usage_count');
            $table->integer('App_opended_count');
            $table->integer('chatbot_usage_count');
            $table->integer('resource_material_accessed_count');
            $table->integer('total_task_count');
            $table->softDeletes();
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
        Schema::dropIfExists('lb_subscriber_rankings');
    }
}
