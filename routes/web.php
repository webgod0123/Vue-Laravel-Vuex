<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'AuthController@login');
Route::get('/logout', 'AuthController@logout');

Route::group(['middleware' => 'web'], function(){
    Route::group(['middleware' => 'auth'], function(){
        Route::get('/admins/get','AdminController@show');
        Route::post('/admins','AdminController@store');
        
        // contract
        Route::post('/contract/force-scan','ContractController@forceScan');
        Route::post('/contract/upload-excel','ContractController@uploadExcel');
        Route::post('/contract/get-regular','ContractController@getRegular');
        Route::post('/contract/add-regular','ContractController@addRegular');
        Route::post('/contract/delete-regular','ContractController@deleteRegular');
        Route::post('/contract/ack-change','ContractController@acknowledge');

        // contract api call
        Route::get('/report/contract','RequestController@contract');
        Route::get('/report/obligation','RequestController@obligation');
        
        // exports
        Route::post('/export/contract','PDFController@contract');
        Route::get('/export/fetch-file/{fileName}','PDFController@fetchFile');
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

