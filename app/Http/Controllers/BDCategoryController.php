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
use DB;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class BDCategoryController extends Controller {


	public function index()
	{
		//
		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM d_b__categories ORDER BY id asc"));
		return view('BDCategory.index', compact('data'));
	}

	public function create()
	{
		//
		return view('BDCategory.create');
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['bd_id'=>'required']);

		$input = $request->all(); // change use (delete or comment user Requestl; )
		
		$bd_id = $input['bd_id'];
		$bd_rs = $input['bd_rs'];
		$bd_en = $input['bd_en'];
		$bd_it = $input['bd_it'];
		
				
		try {
			$table = new DB_Category;

			$table->bd_id = $bd_id;
			$table->bd_rs = $bd_rs;
			$table->bd_en = $bd_en;
			$table->bd_it = $bd_it;
			
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('BDCategory.error');
		}
		
		//return view('defectlevel.index');
		return Redirect::to('/logincheck');

	}

	public function edit($id) {

		$bd_category = DB_Category::findOrFail($id);		
		return view('BDCategory.edit', compact('bd_category'));
	}

	public function update($id, Request $request) {
		//
		$this->validate($request, ['bd_id'=>'required']);

		$table = DB_Category::findOrFail($id);		
		//$machine->update($request->all());

		$input = $request->all(); 
		//dd($input);

		try {
			
			$table->bd_id = $input['bd_id'];
			$table->bd_rs = $input['bd_rs'];
			$table->bd_en = $input['bd_en'];
			$table->bd_it = $input['bd_it'];
			
						
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('BDCategory.error');			
		}
		
		return Redirect::to('/bd_category');
	}

	public function delete($id) {

		$table = DB_Category::findOrFail($id);
		$table->delete();

		return Redirect::to('/bd_category');
	}

	
}
