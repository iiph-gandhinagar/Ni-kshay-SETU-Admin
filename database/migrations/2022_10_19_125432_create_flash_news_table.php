<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlashNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flash_news', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('source');
            $table->string('href')->nullable();
            $table->string('publish_date')->nullable();
            $table->integer('order_index')->default(1000);
            $table->boolean('active')->default(0);
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
        Schema::dropIfExists('flash_news');
    }
}
