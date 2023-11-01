<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyTable extends Migration
{

    private $tableName = "daily";
    private $tableName2 = "daily_forms";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('forms_id');
                $table->integer('items_id');
                $table->string('items_code');
                $table->string('date');
                $table->string('value');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable($this->tableName2)) {
            Schema::create($this->tableName2, function (Blueprint $table) {
                $table->integer('daily_id')->unsigned();
                $table->foreign('daily_id')
                    ->references('id')
                    ->on('daily')
                    ->onDelete('cascade');

                $table->integer('forms_id')->unsigned();
                $table->foreign('forms_id')
                    ->references('id')
                    ->on('forms')
                    ->onDelete('cascade');
                
                $table->primary(['daily_id', 'forms_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::dropIfExists($this->tableName);
        Schema::dropIfExists($this->tableName2);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
