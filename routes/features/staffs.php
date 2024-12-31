<?php

// Staffs --- start
Route::group([
    "prefix" => "{userType}",
    "where" => ["userType" => "staff|manager|admin"]
], function() {
    Route::post("/login", "Api\AuthController@login");
});
// Staffs --- end