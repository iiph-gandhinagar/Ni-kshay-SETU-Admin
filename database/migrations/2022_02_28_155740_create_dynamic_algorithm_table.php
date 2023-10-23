<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicAlgorithmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_algorithm', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('algo_key');
            $table->json('title')->nullable();
            $table->string('node_type');
            $table->boolean('is_expandable')->default(0);
            $table->boolean('has_options')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->integer('index')->default(0);
            $table->json('description')->nullable();
            $table->string('redirect_algo_type')->nullable();
            $table->bigInteger('redirect_node_id')->default(0);
            $table->json('header')->nullable();
            $table->json('sub_header')->nullable();
            $table->boolean('activated')->default(0);
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
        Schema::dropIfExists('dynamic_algorithm');
    }
}
