<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockPositionReportTable extends Migration
{
    private $tableName = "stock_position";

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
                $table->string('date');
                $table->string('product_name');
                $table->string('product_code');
                $table->string('unit');
                $table->string('category');
                $table->string('subcategory');
                $table->string('value');
                $table->timestamps();
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

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
