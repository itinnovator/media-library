<?php
Route::get('media/library/{type}', 'AdminControllers\DashboardController@initContentMediaLibrary');
Route::post('media', 'AdminControllers\DashboardController@initProcessMedia');
