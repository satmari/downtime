<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', 'WelcomeController@index');
Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index');

// Mechanics 
Route::get('inteoslogin', 'InteosLoginController@index');
Route::get('afterlogin', 'InteosLoginController@afterlogin');
Route::get('afterloginall', 'InteosLoginController@afterloginall');
Route::post('logincheck', 'InteosLoginController@logincheck');
Route::get('new_bd_comment/{value}', 'InteosLoginController@new_bd_comment');
Route::post('downtime_insert', 'InteosLoginController@downtime_insert');
Route::get('clear_session_mechanic', 'InteosLoginController@clear_session_mechanic');

// Lineleader
Route::get('inteoslogin2', 'InteosLogin2Controller@index');
Route::get('afterlogin2', 'InteosLogin2Controller@afterlogin');
Route::get('afterlogin2all', 'InteosLogin2Controller@afterloginall');
Route::post('logincheck2', 'InteosLogin2Controller@logincheck');
Route::get('new_bd_info/{value}', 'InteosLogin2Controller@new_bd_info');
Route::post('downtime_insert2', 'InteosLogin2Controller@downtime_insert');
Route::get('clear_session_lineleader', 'InteosLogin2Controller@clear_session_lineleader');

// BD Category
Route::get('/bd_category', 'BDCategoryController@index');
Route::get('/bd_category_new', 'BDCategoryController@create');
Route::post('/bd_category_insert', 'BDCategoryController@insert');
Route::get('/bd_category/edit/{id}', 'BDCategoryController@edit');
Route::post('/bd_category/{id}', 'BDCategoryController@update');
Route::get('/bd_category/delete/{id}', 'BDCategoryController@delete');
Route::post('/bd_category/delete/{id}', 'BDCategoryController@delete');



Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


Route::any('getstyledata', function() {
	$term = Input::get('term');

	// $data = DB::connection('sqlsrv3')->table('styles')->select('style')->where('style','LIKE', $term.'%')->take(10)->get();
	$data = DB::connection('sqlsrv')->select(DB::raw("SELECT style FROM [settings].[dbo].[styles] where LEN(style) < 9 and style like '%".$term."%' order by style asc"));
	foreach ($data as $v) {
		$retun_array[] = array('value' => $v->style);
	}
return Response::json($retun_array);
});