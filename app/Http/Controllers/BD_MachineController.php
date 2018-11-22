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
use App\DB_Category;
use App\Machine_type;
use App\BD_Machine_link;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class BD_MachineController extends Controller {


	public function index()
	{
		//
		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT link.id,link.machine_code,mas.machine_desc,link.bd_id ,cat.bd_rs
		  FROM b_d__machine_links as link
		  LEFT JOIN d_b__categories as cat ON link.bd_id = cat.bd_id
		  LEFT JOIN machine_types as mas ON link.machine_code = mas.machine_code
		  ORDER BY link.machine_code asc"));

		// dd($data);
		return view('BD_Machine_link.index', compact('data'));
	}

	public function create()
	{
		//
		$category_data = DB_Category::orderBy('bd_id')->lists('bd_rs','bd_id'); //pluck
		$machine_data = Machine_type::orderBy('machine_code')->lists('machine_code','id'); //pluck

		return view('BD_Machine_link.create', compact('category_data','machine_data'));
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['machine_code'=>'required', 'bd_id'=>'required']);

		$input = $request->all(); // change use (delete or comment user Requestl; )
		
		$machine_code = $input['machine_code'];
		$bd_id = $input['bd_id'];
		// dd($bd_id);

		$machine = Machine_type::findOrFail($input['machine_code']);
		// dd($machine->machine_code);
			
		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM [downtime].[dbo].[b_d__machine_links] WHERE bd_id = '".$bd_id."' and machine_code = '".$machine->machine_code."' "));
		// dd($data);

		if (empty($data)) {
			// dd("not exist");
			try {
				$table = new BD_Machine_link;

				$table->machine_code = $machine->machine_code;
				$table->bd_id = $bd_id;
				
				$table->save();
			}
			catch (\Illuminate\Database\QueryException $e) {
				return view('BD_Machine_link.error');
			}

		} else {
			// dd("already exist");
			$msg = "Link aready exist!";
			return view('BD_Machine_link.error',compact('msg'));
		}

		return Redirect::to('/bd_machine');

	}

	public function delete($id) {

		$table = BD_Machine_link::findOrFail($id);
		$table->delete();

		// $machine_code = $table->machine_code;
		// $bd_id = $table->bd_id;
		// $data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM [downtime].[dbo].[b_d__machine_links] WHERE bd_id = '".$bd_id."' and machine_code = '".$machine->machine_code."' "));

		return Redirect::to('/bd_machine');
	}


	
}
