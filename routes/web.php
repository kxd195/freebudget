<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/settings', 'HomeController@settings')->name('settings');

Route::resource('global_settings', 'GlobalSettingController');
Route::resource('categories', 'CategoryController');
Route::resource('units', 'UnitController');
Route::resource('rate_classes', 'RateClassController');
Route::resource('shows', 'ShowController');
Route::resource('budgets', 'BudgetController');
Route::resource('days', 'DayController');
Route::resource('people', 'PersonController');
Route::post('scene/edit/{budget_id}', 'PersonController@updateWholeScene')->name('scene.edit');
Route::post('budgets/{id}/tag', 'BudgetController@tagVersion')->name('budgets.tag');
Route::get('budgets/{id}/version/{version}', 'BudgetController@showVersion')->name('budgets.version');
Route::resource('shares', 'ShareController');
Route::get('$h@r-{id}', 'ShareController@show')->name('shares.external');
Route::resource('line_items', 'LineItemController');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');    