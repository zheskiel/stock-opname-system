<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupervisorTable extends Migration
{
    private $tableName = 'supervisor';
    private $tableName2 = 'supervisor_type';
    private $relations = [
        'outlet', 'manager', 'supervisor_type'
    ];

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
                $table->string('name');
                $table->string('slug');
                $table->timestamps();
            });

            Schema::create($this->tableName2, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->timestamps();
            });

            foreach ($this->relations as $relation) {
                Schema::table($this->tableName, function (Blueprint $table) use ($relation) {
                    $table->integer($relation.'_id')->unsigned()->nullable()->after('slug');
                    $table->foreign($relation.'_id')->references('id')->on($relation)
                        ->onDelete('set null')->onUpdate('cascade');
                });
            }
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
