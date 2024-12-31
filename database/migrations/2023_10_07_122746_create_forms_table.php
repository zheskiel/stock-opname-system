<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    private $tableName  = "forms";
    private $tableName2 = "items";
    private $tableName3 = "forms_items";

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
                $table->integer('template_id');
                $table->integer('manager_id');
                $table->integer('outlet_id');
                $table->integer('supervisor_id');
                $table->integer('staff_id');
                $table->timestamps();
            });
        };

        if (!Schema::hasTable($this->tableName2)) {
            Schema::create($this->tableName2, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('forms_id');
                $table->integer('product_id');
                $table->string('product_code');
                $table->string('product_name');
                $table->string('unit');
                $table->string('unit_value');
                $table->string('unit_sku');
                $table->timestamps();
            });
        };

        if (!Schema::hasTable($this->tableName3)) {
            Schema::create($this->tableName3, function (Blueprint $table) {
                $table->integer('forms_id')->unsigned();
                $table->foreign('forms_id')
                    ->references('id')
                    ->on('forms')
                    ->onDelete('cascade');
                
                $table->integer('items_id')->unsigned();
                $table->foreign('items_id')
                    ->references('id')
                    ->on('items')
                    ->onDelete('cascade');

                $table->primary(['forms_id', 'items_id']);
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
        Schema::dropIfExists($this->tableName3);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
