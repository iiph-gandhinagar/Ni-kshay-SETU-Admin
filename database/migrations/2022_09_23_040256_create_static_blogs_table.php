<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_blogs', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('short_description')->nullable();
            $table->json('description');
            $table->string('author');
            $table->string('source');
            $table->integer('order_index');
            $table->boolean('active');
            $table->string('keywords');
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
        Schema::dropIfExists('static_blogs');
    }
}
