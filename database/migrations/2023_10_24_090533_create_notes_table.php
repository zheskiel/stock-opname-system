<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    private $tableName = "notes";
    private $tableName2 = "forms_notes";

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
                $table->integer('staff_id');
                $table->string('date');
                $table->string('notes');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable($this->tableName2)) {
            Schema::create($this->tableName2, function (Blueprint $table) {
                $table->integer('forms_id')->unsigned();
                $table->foreign('forms_id')
                    ->references('id')
                    ->on('forms')
                    ->onDelete('cascade');

                $table->integer('notes_id')->unsigned();
                $table->foreign('notes_id')
                    ->references('id')
                    ->on('notes')
                    ->onDelete('cascade');
                
                $table->primary(['forms_id', 'notes_id']);
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
