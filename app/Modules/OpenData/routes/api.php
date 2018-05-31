<?php

// Route::group(['module' => 'OpenData', 'middleware' => ['authOne'], 'namespace' => 'App\Modules\OpenData\Controllers','prefix'=>'openData'], function() {
Route::group(['module' => 'OpenData', 'middleware' => [], 'namespace' => 'App\Modules\OpenData\Controllers','prefix'=>'openData'], function() {
    Route::post("/export/{token}","OpenDataController@export");
    Route::get("/exportToDb","OpenDataController@exportToDb");
    Route::put("/{entityKey}","OpenDataController@update");
    Route::get("/{entityKey}","OpenDataController@show");
    Route::get("/","OpenDataController@index");
});
