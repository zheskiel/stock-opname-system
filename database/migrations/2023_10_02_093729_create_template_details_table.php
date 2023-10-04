<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("details_templates")) {
            Schema::create("details_templates", function (Blueprint $table) {
                $table->integer('details_id')->unsigned();
                $table->foreign('details_id')
                    ->references('id')
                    ->on('details')
                    ->onDelete('cascade');
                
                $table->integer('templates_id')->unsigned();
                $table->foreign('templates_id')
                    ->references('id')
                    ->on('templates')
                    ->onDelete('cascade');
                
                $table->primary(['details_id', 'templates_id']);
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
        Schema::dropIfExists('details_templates');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
