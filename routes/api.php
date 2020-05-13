<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("addToken",    "Api\TokenBucketController@add");
Route::post("getToken",    "Api\TokenBucketController@get");
Route::post("resetToken",  "Api\TokenBucketController@reset");
Route::post("getTokenNums","Api\TokenBucketController@getNums");
