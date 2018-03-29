<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\QueryException as QueryException;
use App\Exceptions\Handler;

use Illuminate\Http\Request;
//use Gbrock\Table\Facades\Table;
use Illuminate\Support\Facades\Redirect;

use App\DB_Category;
use App\Downtime;
use DB;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		$user = User::find(Auth::id());

		// if ($user->is('admin')) { 
		//     // if user has at least one role
		//     $msg = "Hi admin";
		// }
		// if ($user->is('preparacija')) { 
		//     // if user has at least one role
		//     $msg = "Pa gde ste preparacija?";
		//     //return redirect('/maintable');
		// }

		// dd($user);

		if (!is_null($user)) {

			if ($user->is('modul')) { 
			    // if user has at least one role
			    $msg = "Hi modul";
			  	return redirect('/inteoslogin2');
		 	}
		}
		


		return view('home');
	}

}
