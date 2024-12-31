<?php

// Form --- start
Route::group(["prefix" => "/form"], function() {
    Route::group(["prefix" => "/template"], function() {
       Route::group(["prefix" => "/{templateId}"], function() {    
            Route::group(["prefix" => "/outlet"], function() {
                Route::group(["prefix" => "/{outletId}"], function() {
                    Route::group(["prefix" => "/manager"], function() {
                        Route::group(["prefix" => "/{managerId}"], function() {
                            Route::group(["prefix" => "/supervisor"], function() {
                                Route::group(["prefix" => "/{supervisorId}"], function() {
                                    Route::group(["prefix" => "/staff"], function() {
                                        Route::group(["prefix" => "/{staffId}"], function() {
                                            Route::post("/create", "Api\FormsController@CreateNewFormDetail");
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        }); 
    });

    // Update form detail
    Route::group(["prefix" => "/{form_id}"], function() {
        Route::group(["prefix" => "outlet"], function() {
            Route::post("/{outlet_id}", "Api\FormsController@UpdateFormDetail");
        });
    });

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

        Route::group(["prefix" => "/staff"], function() {
            Route::group(["prefix" => "/{staffId}"], function() {
                Route::get("/details", "Api\FormsController@FetchFormByStaffId");
                Route::get("/all", "Api\FormsController@FetchAllSelected");
            });
        });

        Route::group(["prefix" => "/{staffId}"], function() {
            Route::post("/create-detail", "Api\FormsController@createFormDetail");
        });
    });
});
// Form --- end