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

//Mechanics Activity
Route::get('activity', 'ActivityController@index');
Route::get('afterlogin3', 'ActivityController@afterlogin');
Route::post('logincheck3', 'ActivityController@logincheck');
Route::get('activity_table', 'ActivityController@activity_table');

Route::get('add_activity', 'ActivityController@add_activity');
Route::get('stop_activity', 'ActivityController@stop_activity');
// Route::get('stop_activity/{id}', 'ActivityController@stop_activity_id');
Route::post('maintenance', 'ActivityController@maintenance');
Route::post('setting', 'ActivityController@setting');

Route::get('clear_session_mechanic2', 'ActivityController@clear_session_mechanic');

// Machines
Route::get('add_machine1', 'MechineController@index');
Route::get('add_machine_id/{id}', 'MechineController@index_id');
Route::post('add_machine', 'MechineController@add_machine');
Route::get('/add_maintenance/{id}', 'MechineController@add_maintenance');
Route::post('add_maintenance_confirm', 'MechineController@add_maintenance_confirm');

// Maintenance
Route::get('/maintenance', 'MaintenanceController@index');
Route::get('/maintenance_new', 'MaintenanceController@create');
Route::post('/maintenance_insert', 'MaintenanceController@insert');
Route::get('/maintenance/edit/{id}', 'MaintenanceController@edit');
Route::post('/maintenance/{id}', 'MaintenanceController@update');
Route::get('/maintenance/delete/{id}', 'MaintenanceController@delete');
Route::post('/maintenance/delete/{id}', 'MaintenanceController@delete');


// Lineleader
Route::get('inteoslogin2', 'InteosLogin2Controller@index');
Route::get('afterlogin2', 'InteosLogin2Controller@afterlogin');
Route::get('afterlogin2all', 'InteosLogin2Controller@afterloginall');
Route::post('logincheck2', 'InteosLogin2Controller@logincheck');
Route::get('new_bd_info/{value}', 'InteosLogin2Controller@new_bd_info');
Route::get('new_bd_info_test/{value}', 'InteosLogin2Controller@new_bd_info_test'); // for testing
Route::post('downtime_insert2', 'InteosLogin2Controller@downtime_insert');
Route::post('downtime_insert2_cs', 'InteosLogin2Controller@downtime_insert_cs');
Route::post('downtime_insert2_test', 'InteosLogin2Controller@downtime_insert');
Route::get('clear_session_lineleader', 'InteosLogin2Controller@clear_session_lineleader');

// BD Category
Route::get('/bd_category', 'BDCategoryController@index');
Route::get('/bd_category_new', 'BDCategoryController@create');
Route::post('/bd_category_insert', 'BDCategoryController@insert');
Route::get('/bd_category/edit/{id}', 'BDCategoryController@edit');
Route::post('/bd_category/{id}', 'BDCategoryController@update');
Route::get('/bd_category/delete/{id}', 'BDCategoryController@delete');
Route::post('/bd_category/delete/{id}', 'BDCategoryController@delete');

// Machine Type
Route::get('/machine_type', 'Machine_typeController@index');
Route::get('/machine_type_new', 'Machine_typeController@create');
Route::post('/machine_type_insert', 'Machine_typeController@insert');
Route::get('/machine_type/edit/{id}', 'Machine_typeController@edit');
Route::post('/machine_type/{id}', 'Machine_typeController@update');
Route::get('/machine_type/delete/{id}', 'Machine_typeController@delete');
Route::post('/machine_type/delete/{id}', 'Machine_typeController@delete');

// BD Machine Link
Route::get('/bd_machine', 'BD_MachineController@index');
Route::get('/bd_machine_new', 'BD_MachineController@create');
Route::post('/bd_machine_insert', 'BD_MachineController@insert');
// Route::get('/bd_machine/edit/{id}', 'BD_MachineController@edit');
// Route::post('/bd_machine/{id}', 'BD_MachineController@update');
Route::get('/bd_machine/delete/{id}', 'BD_MachineController@delete');
Route::post('/bd_machine/delete/{id}', 'BD_MachineController@delete');

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