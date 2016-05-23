<?php
Route::group(['middleware' => ['web']], function () {
    Route::get('category/{slug?}', 'Commerce\Productso\Http\Controllers\PrsoCategoryController@show');
    //Route::get('product/{slug}/{categoryid?}', 'Commerce\Productso\Http\Controllers\PrsoProductController@show');
});
