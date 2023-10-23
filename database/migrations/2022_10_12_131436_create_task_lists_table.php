<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_task_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->integer('badges');
            $table->string('mins_spent')->nullable();
            $table->string('sub_module_usage_count');
            $table->integer('App_opended_count');
            $table->integer('chatbot_usage_count')->nullable();
            $table->integer('resource_material_accessed_count')->nullable();
            $table->integer('total_task');
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
        Schema::dropIfExists('lb_task_lists');
    }
}
