<?php

Route::group(["prefix" => "api", "middleware" => ["cors"]], function() {
    Route::group(["prefix" => "v1"], function() {
        // Outlet --- start
        Route::group(["prefix" => "/outlet"], function() {
            Route::get("/", "Api\OutletController@FetchOutlets");
            Route::post("/supervisors", "Api\SupervisorController@FetchSupervisorsByOutlet");
        });
        // Outlet --- end

        // Forms
        include_once(__DIR__ . '/features/forms.php');

        // Testing
        include_once(__DIR__ . '/features/testing.php');

        // Waste --- start
        Route::get("/fetch/{templateId}/waste", "Api\ReportsController@fetchWasteByTemplate");
        // Waste --- end

        // Reports --- start
        Route::group(["prefix" => "/reports"], function() {
            Route::get("/", "Api\ReportsController@Index");
            Route::post("/create", "Api\ReportsController@Store");
        });
        // Reports --- end

        // Superadmin or Admin only
        Route::group(["middleware" => ["route.permission"]], function() {
            Route::get("/master", "Api\MasterDataController@Index")->name("master.index");

            Route::group(["prefix" => "/hierarchy"], function() {
                Route::get("/", "Api\HierarchyController@fetchHierarchy");
            });
        });

        // Form
        include_once(__DIR__ . '/features/form.php');

        // Templates
        include_once(__DIR__ . '/features/templates.php');

        // Template
        include_once(__DIR__ . '/features/template.php');

        // Staffs
        include_once(__DIR__ . '/features/staffs.php');

        Route::group(["prefix" => "staff"], function() {
            Route::group(["middleware" => "guest:staff"], function() {
                Route::get("/test", "Api\TestController@testStaffPage");
            });
        });

        // Managers --- start
        Route::group(["prefix" => "manager"], function() {
            Route::group(["middleware" => "guest:manager"], function() {
                Route::get("/test", "Api\TestController@testManagerPage");
            });
        });
        // Managers --- end

        // Admin --- start
        Route::group(["prefix" => "admin"], function() {
            Route::group(["middleware" => "guest:admin"], function() {
                Route::get("/test", "Api\TestController@testAdminPage");
            });
        });
        // Admin --- end

        Route::post("/logout", "Api\AuthController@Logout");

        Route::get("/manager", "Api\IndexController@testManager");
        Route::get("/supervisor", "Api\IndexController@testSupervisor");

        // Route::get("/hierarchy", "Api\IndexController@testHierarchy");
        Route::get("/template", "Api\IndexController@testTemplate")->name("template");
    });
});

/*
 * Redirect All traffic to index
 */
Route::get("{any?}", function () {
    return redirect("/");
})->where("any", ".*");