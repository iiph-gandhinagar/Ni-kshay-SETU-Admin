<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLbSubModuleUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_sub_module_usages', function (Blueprint $table) {
            $table->id();
            $table->integer('subscriber_id');
            $table->string('module_id');
            $table->string('sub_module');
            $table->string('total_time');
            $table->string('mins_spent');
            $table->boolean('completed_flag');
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
        Schema::dropIfExists('lb_sub_module_usages');
    }
}
