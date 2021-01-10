<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\QueryException as QueryException;
use App\Exceptions\Handler;

use Illuminate\Http\Request;
//use Gbrock\Table\Facades\Table;
use Illuminate\Support\Facades\Redirect;

use App\Maintenance_checklist;
use DB;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class MaintenanceController extends Controller {

	public function index()
	{
		//
		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM maintenance_checklists WHERE deleted is NULL ORDER BY sort asc"));
		return view('Maintenance.index', compact('data'));
	}

	public function create()
	{
		//
		return view('Maintenance.create');
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['maintenance'=>'required']);

		$input = $request->all(); // change use (delete or comment user Requestl; )
		
		$sort = $input['sort'];
		$maintenance = $input['maintenance'];
		$maintenance_en = $input['maintenance_en'];
		$maintenance_it = $input['maintenance_it'];
		
				
		try {
			$table = new Maintenance_checklist;

			$table->sort = $sort;
			$table->maintenance = $maintenance;
			$table->maintenance_en = $maintenance_en;
			$table->maintenance_it = $maintenance_it;
			
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('Maintenance.error');
		}
		
		//return view('defectlevel.index');
		return Redirect::to('/maintenance');

	}

	public function edit($id) {

		$maintenance = Maintenance_checklist::findOrFail($id);		
		return view('Maintenance.edit', compact('maintenance'));
	}

	public function update($id, Request $request) {
		//
		$this->validate($request, ['id'=>'required']);

		$table = Maintenance_checklist::findOrFail($id);
		//$machine->update($request->all());

		$input = $request->all(); 
		//dd($input);

		try {
			
			$table->sort = $input['sort'];
			$table->maintenance = $input['maintenance'];
			$table->maintenance_en = $input['maintenance_en'];
			$table->maintenance_it = $input['maintenance_it'];
									
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('Maintenance.error');			
		}
		
		return Redirect::to('/maintenance');
	}

	public function delete($id) {

		$table = Maintenance_checklist::findOrFail($id);
		$table->deleted = date("d.m.Y");
		$table->save();

		return Redirect::to('/maintenance');
	}

}
