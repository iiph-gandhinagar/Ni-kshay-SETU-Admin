<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AlterColumnInTTrainingTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_training_tag', function (Blueprint $table) {
            $table->dropColumn('response');
            $table->renameColumn('response_json', 'response');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_training_tag', function (Blueprint $table) {
            $table->renameColumn('response', 'response_json');
        });
    }
}