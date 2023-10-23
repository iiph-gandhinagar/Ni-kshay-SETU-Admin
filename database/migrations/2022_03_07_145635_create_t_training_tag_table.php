<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTrainingTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_training_tag', function (Blueprint $table) {
            $table->id();
            $table->string('tag');
            $table->text('pattern')->nullable();
            $table->boolean('is_fix_response')->default(0);
            $table->text('response')->nullable();
            $table->string('questions')->nullable();
            $table->string('modules')->nullable();
            $table->string('sub_modules')->nullable();
            $table->string('resource_material')->nullable();
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
        Schema::dropIfExists('t_training_tag');
    }
}
