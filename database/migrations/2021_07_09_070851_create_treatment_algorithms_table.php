<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentAlgorithmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatment_algorithms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('node_type');
            $table->boolean('is_expandable')->default(0);
            $table->boolean('has_options')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->text('description')->nullable();
            $table->integer('index')->default(0);
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
        Schema::dropIfExists('treatment_algorithms');
    }
}
