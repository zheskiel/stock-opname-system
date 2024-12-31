<?php

// Forms --- start
Route::group(["prefix" => "/forms"], function() {
    Route::post("/", "Api\FormsController@Index");

    Route::post('/managers', 'Api\FormsController@fetchManager');
    Route::post('/outlets', 'Api\FormsController@fetchOutletsByManager');
    Route::post('/templates', 'Api\FormsController@fetchTemplatesByManager');
    Route::post('/supervisors', 'Api\FormsController@fetchSupervisorByManager');
    Route::post('/staffs', 'Api\FormsController@fetchStaffBySupervisor');
});
// Forms --- End