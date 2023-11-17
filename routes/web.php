<?php

Route::group(["prefix" => "api", "middleware" => ["cors"]], function() {
    Route::group(["prefix" => "v1"], function() {
        // Outlet
        Route::group(["prefix" => "/outlet"], function() {
            Route::get("/", "Api\OutletController@FetchOutlets");
            Route::post("/supervisors", "Api\SupervisorController@FetchSupervisorsByOutlet");
        });

        // Testing
        Route::group(["prefix" => "/testing"], function() {
            Route::group(["prefix" => "/manager/{managerId}"], function() {
                Route::group(["prefix" => "/outlet"], function() {
                    Route::get("/{outletId}/forms", "Api\TestingController@StaffForms");
                });

                Route::group(["prefix" => "/staff"], function() {
                    Route::get("/{staffId}/form", "Api\TestingController@FetchFormByStaffId");
                });
            });

            Route::post("/forms", "Api\TestingController@CreateDailyFormReport");
        });

        // Waste
        Route::get("/fetch/{templateId}/waste", "Api\ReportsController@fetchWasteByTemplate");
        Route::get("/forms", "Api\FormsController@Index");

        Route::group(["prefix" => "/reports"], function() {
            Route::get("/", "Api\ReportsController@Index");
            Route::post("/create", "Api\ReportsController@Store");
        });

        // Superadmin or Admin only
        Route::group(["middleware" => ["route.permission"]], function() {
            Route::get("/master", "Api\MasterDataController@Index")->name("master.index");

            Route::group(["prefix" => "/hierarchy"], function() {
                Route::get("/", "Api\HierarchyController@fetchHierarchy");
            });
        });

        Route::group(["prefix" => "/form"], function() {
            Route::group(["prefix" => "/position"], function() {
                Route::group(["prefix" => "/report"], function() {
                    Route::post("/", "Api\StockPositionController@CreateStockPosition");
                    Route::get("/", "Api\StockPositionController@FetchStockPosition");
                });
            });

            Route::group(["prefix" => "/final"], function() {
                Route::get("/", "Api\FinalFormController@Index");
                Route::post("/", "Api\FinalFormController@Create");
            });

            Route::get("/compare/{templateId}/waste", "Api\ReportsController@FetchWaste");


            Route::group(["prefix" => "/{managerId}"], function() {
                Route::group(["prefix" => "/outlet/{outletId}"], function() {
                    Route::get("/combined", "Api\FormsController@fetchCombinedForm");
                });

                Route::group(["prefix" => "/{staffId}"], function() {
                    Route::get("/details", "Api\FormsController@FetchFormByStaffId");
                    Route::get("/all", "Api\FormsController@FetchAllSelected");

                    Route::post("/create-detail", "Api\FormsController@createFormDetail");
                    Route::post("/remove-detail", "Api\FormsController@removeFormDetail");
                    Route::post("/remove-all-detail", "Api\FormsController@removeAllFormDetail");
                });
            });
        });

        Route::get("/test_templates", "Api\TemplatesController@Test");
        Route::post("/templates", "Api\TemplatesController@Index");

        Route::group(["prefix" => "/template"], function() {
            Route::post("/create", "Api\TemplateController@createTemplateForOutlet");

            Route::group(["prefix" => "/{templateId}"], function() {
                Route::get("/view", "Api\TemplateController@View")->name("template.view");
                Route::get("/all", "Api\TemplateController@FetchAllSelected");

                Route::post("/create-detail", "Api\TemplateController@createTemplateDetail");
                Route::post("/remove-detail", "Api\TemplateController@removeTemplateDetail");
                Route::post("/remove-all-detail", "Api\TemplateController@removeAllTemplateDetail");
            });
        });

        // Staffs
        Route::group([
            "prefix" => "{userType}",
            "where" => ["userType" => "staff|manager|admin"]
        ], function() {
            Route::post("/login", "Api\AuthController@login");
        });

        Route::group(["prefix" => "staff"], function() {
            Route::group(["middleware" => "guest:staff"], function() {
                Route::get("/test", "Api\TestController@testStaffPage");
            });
        });

        // Managers
        Route::group(["prefix" => "manager"], function() {
            Route::group(["middleware" => "guest:manager"], function() {
                Route::get("/test", "Api\TestController@testManagerPage");
            });
        });

        // Admins
        Route::group(["prefix" => "admin"], function() {
            Route::group(["middleware" => "guest:admin"], function() {
                Route::get("/test", "Api\TestController@testAdminPage");
            });
        });

        Route::post("/logout", "Api\AuthController@Logout");

        Route::get("/manager", "Api\IndexController@testManager");
        Route::get("/supervisor", "Api\IndexController@testSupervisor");

        // Route::get("/hierarchy", "Api\IndexController@testHierarchy");
        Route::get("/template", "Api\IndexController@testTemplate")->name("template");
    });
});

Route::get("/", function() {
    return View("welcome");
});

Route::get("{any?}", function () {
    return redirect("/");
})->where("any", ".*");