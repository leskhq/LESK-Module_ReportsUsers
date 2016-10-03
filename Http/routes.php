<?php

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


// Routes in this group must be authorized.
Route::group(['middleware' => 'authorize'], function () {

    // ReportsUsers routes
    Route::group(['prefix' => 'reports'], function () {
        Route::get( 'users',       ['as' => 'reports.users',       'uses' => 'ReportsUsersController@report_users']);
        Route::post('users-data',  ['as' => 'reports.users-data',  'uses' => 'ReportsUsersController@report_users_data']);
        Route::get( 'perms-and-roles-by-users',       ['as' => 'reports.perms-and-roles-by-users',       'uses' => 'ReportsUsersController@report_perms_and_roles_by_users']);
        Route::post('perms-and-roles-by-users-data',  ['as' => 'reports.perms-and-roles-by-users-data',  'uses' => 'ReportsUsersController@report_perms_and_roles_by_users_data']);
    }); // End of ReportsUsers group

}); // end of AUTHORIZE middleware group
