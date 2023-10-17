<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    private $tableNames = [
        'admin'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->tableNames as $tableName) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                    $table->increments('id');
                    $table->string('name');
                    $table->string('slug');
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->integer('brand_id')->nullable();
                    $table->rememberToken();
                    $table->timestamps();
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

        foreach($this->tableNames as $tableName) {
            Schema::dropIfExists($tableName);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
