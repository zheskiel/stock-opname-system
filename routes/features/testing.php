<?php

// Testing --- start
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

Route::get("/test_templates", "Api\TemplatesController@Test");

// Testing --- end