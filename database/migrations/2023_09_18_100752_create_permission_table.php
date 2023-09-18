<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        $permissionPivot = config('permission.column_names.permission_pivot_key') ?: 'permission_id';
        $pivotRole = config('permission.column_names.role_pivot_key') ?: 'role_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        if (!Schema::hasTable($tableNames['permissions'])) {
            Schema::create($tableNames['permissions'], function (Blueprint $table) {
                $table->increments('id');

                $table->string('name');
                $table->string('guard_name');
                $table->longText('description')->nullable();
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        };

        if (!Schema::hasTable($tableNames['roles'])) {
            Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
                $table->increments('id');

                if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                    $table->unsignedInteger($columnNames['team_foreign_key'])->nullable();
                    $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
                }

                $table->string('name');       // For MySQL 8.0 use string('name', 125);
                $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
                $table->longText('description')->nullable();
                $table->timestamps();

                if ($teams || config('permission.testing')) {
                    $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                } else {
                    $table->unique(['name', 'guard_name']);
                }
            });
        }

        if (!Schema::hasTable($tableNames['model_has_permissions'])) {
            if (Schema::hasTable($tableNames['permissions'])) {
                Schema::create($tableNames['model_has_permissions'],
                    function (Blueprint $table) use ($tableNames, $columnNames, $teams, $permissionPivot) {
                    $table->unsignedInteger($permissionPivot);
                    // $table->uuid($permissionPivot);

                    $table->string('model_type');
                    // $table->unsignedInteger($columnNames['model_morph_key']);
                    $table->uuid($columnNames['model_morph_key']);
                    $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

                    $table->foreign($permissionPivot)
                        ->references('id')
                        ->on($tableNames['permissions'])
                        ->onDelete('cascade');

                    if ($teams) {
                        $table->unsignedInteger($columnNames['team_foreign_key']);
                        $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                        $table->primary(
                            [$columnNames['team_foreign_key'], $permissionPivot, $columnNames['model_morph_key'], 'model_type'],
                            'model_has_permissions_permission_model_type_primary'
                        );
                    } else {
                        $table->primary(
                            [$permissionPivot, $columnNames['model_morph_key'], 'model_type'],
                            'model_has_permissions_permission_model_type_primary'
                        );
                    }
                });
            }
        }

        if (!Schema::hasTable($tableNames['model_has_roles'])) {
            if (Schema::hasTable($tableNames['roles'])) {
                Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams, $pivotRole) {
                    $table->unsignedInteger($pivotRole);
                    // $table->uuid($pivotRole);

                    $table->string('model_type');
                    // $table->unsignedInteger($columnNames['model_morph_key']);
                    $table->uuid($columnNames['model_morph_key']);
                    $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

                    $table->foreign($pivotRole)
                        ->references('id')
                        ->on($tableNames['roles'])
                        ->onDelete('cascade');
                    if ($teams) {
                        $table->unsignedInteger($columnNames['team_foreign_key']);
                        $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                        $table->primary(
                            [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                            'model_has_roles_role_model_type_primary'
                        );
                    } else {
                        $table->primary(
                            [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                            'model_has_roles_role_model_type_primary'
                        );
                    }
                });
            }
        }

        if (!Schema::hasTable($tableNames['role_has_permissions'])) {
            if (Schema::hasTable($tableNames['permissions']) && Schema::hasTable($tableNames['roles'])) {
                Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $permissionPivot, $pivotRole) {
                    $table->unsignedInteger($permissionPivot);
                    $table->unsignedInteger($pivotRole);
                    // $table->uuid($permissionPivot);
                    // $table->uuid($pivotRole);

                    $table->foreign($permissionPivot)
                        ->references('id')
                        ->on($tableNames['permissions'])
                        ->onDelete('cascade');

                    $table->foreign($pivotRole)
                        ->references('id')
                        ->on($tableNames['roles'])
                        ->onDelete('cascade');

                    $table->primary([$permissionPivot, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
                });
            }
        }

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);

        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
