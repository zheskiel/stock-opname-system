<?php

Route::group(["prefix" => "/template"], function() {
    Route::post("/create", "Api\TemplateController@createTemplateForOutlet");

    Route::group(["prefix" => "/{templateId}"], function() {
        Route::post("/update", "Api\TemplateController@updateTemplateForOutlet");

        Route::get("/view", "Api\TemplateController@View")->name("template.view");
        Route::get("/all", "Api\TemplateController@FetchAllSelected");

        Route::post("/create-detail", "Api\TemplateController@createTemplateDetail");
        Route::post("/remove-detail", "Api\TemplateController@removeTemplateDetail");
        Route::post("/remove-all-detail", "Api\TemplateController@removeAllTemplateDetail");
    });
});