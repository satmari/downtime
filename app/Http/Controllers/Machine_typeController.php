<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\QueryException as QueryException;
use App\Exceptions\Handler;

use Illuminate\Http\Request;
//use Gbrock\Table\Facades\Table;
use Illuminate\Support\Facades\Redirect;

// use App\trans_color;

use DB;
use App\Machine_type;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class Machine_typeController extends Controller {


	public function index()
	{
		//
		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machine_types ORDER BY id asc"));
		return view('Machine_type.index', compact('data'));
	}

	public function create()
	{
		//
		return view('Machine_type.create');
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['machine_code'=>'required']);

		$input = $request->all(); // change use (delete or comment user Requestl; )
		
		$machine_code = $input['machine_code'];
		$machine_desc = $input['machine_desc'];
		$machine_group = $input['machine_group'];
			
		try {
			$table = new Machine_type;

			$table->machine_code = $machine_code;
			$table->machine_desc = $machine_desc;
			$table->machine_group = $machine_group;
			
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('Machine_type.error');
		}
		
		//return view('defectlevel.index');
		return Redirect::to('/machine_type');

	}

	public function edit($id) {

		$machine_type = Machine_type::findOrFail($id);		
		return view('Machine_type.edit', compact('machine_type'));
	}

	public function update($id, Request $request) {
		//
		$this->validate($request, ['machine_code'=>'required']);

		$table = Machine_type::findOrFail($id);		
		
		$input = $request->all(); 
		//dd($input);

		try {
			
			// $table->machine_code = $input['machine_code'];
			$table->machine_desc = $input['machine_desc'];
			$table->machine_group = $input['machine_group'];
									
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('Machine_type.error');			
		}
		
		return Redirect::to('/machine_type');
	}

	public function delete($id) {

		$table = Machine_type::findOrFail($id);
		$table->delete();

		return Redirect::to('/machine_type');
	}



}
