<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\QueryException as QueryException;
use App\Exceptions\Handler;

use Illuminate\Http\Request;
//use Gbrock\Table\Facades\Table;
use Illuminate\Support\Facades\Redirect;

use App\Downtime;
use DB;
use App\Activity;
use App\Machines;
use App\Maintenance_checklist;
use App\Machine_maintanence;

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;


use Session;
use Validator;

class MechineController extends Controller {

	public function index() // ne koristi se
	{
		//
		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');

    	$id = Session::get('id_ses');

    	// dd($mechanic);

    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') and activity_type = 'MAINTENANCE' ORDER BY status asc "));
    	// dd($activities);

    	if (!empty($activities)) {
    		// dd("activity exist");
    		$activity_id = $activities[0]->id;		
    		$activity_type = $activities[0]->activity_type;
    		$activity_status = $activities[0]->status;
    	} else {
    		// dd("activity not exist");

    		$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE id = '".$id."' "));
    		$activity_id = $activities[0]->id;		
    		$activity_type = $activities[0]->activity_type;
    		$activity_status = $activities[0]->status;
    	}

    	// $machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT m.* , COUNT(mm.id) as c FROM [downtime].[dbo].machines as m
		  LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
		  WHERE m.[activity_id] = '".$activity_id."' 
		  GROUP BY m.[id]
		      ,m.[machine]
		      ,m.[start_time]
		      ,m.[machine_brand]
		      ,m.[machine_type]
		      ,m.[machine_code]
		      ,m.[mechanic]
		      ,m.[mechanicid]
		      ,m.[activity_type]
		      ,m.[activity_id]
		      ,m.[created_at]
		      ,m.[updated_at] "));
    	// dd($machines);

    	return view('Activity.add_machine', compact('mechanicid','mechanic','mechanic_plant','activity_id','activity_type','activity_status','machines'));
	}

	public function index_id($id)
	{
		// dd($id);
		//
		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');

    	$id_ses = Session::set('id_ses', $id);

    	// dd($mechanic);

    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE id = '".$id."' "));
    	// dd($activities);

    	if (!empty($activities)) {
    		// dd("activity exist");
    		$activity_id = $activities[0]->id;		
    		$activity_type = $activities[0]->activity_type;
    		$activity_status = $activities[0]->status;
    	}  else {
    		dd("activity not exist");

    	}

    	// $machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT m.* , COUNT(mm.id) as c FROM [downtime].[dbo].machines as m
		  LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
		  WHERE m.[activity_id] = '".$activity_id."' 
		  GROUP BY m.[id]
		      ,m.[machine]
		      ,m.[start_time]
		      ,m.[machine_brand]
		      ,m.[machine_type]
		      ,m.[machine_code]
		      ,m.[mechanic]
		      ,m.[mechanicid]
		      ,m.[activity_type]
		      ,m.[activity_id]
		      ,m.[created_at]
		      ,m.[updated_at] "));
    	// dd($machines);

    	return view('Activity.add_machine', compact('mechanicid','mechanic','mechanic_plant','activity_id','activity_type','activity_status','machines'));
	}


	public function add_machine(Request $request)
	{
		$this->validate($request, ['machine'=>'required']);
		$forminput = $request->all(); 

		$machine = $forminput['machine'];
		$activity_id = $forminput['activity_id'];
		$activity_type = $forminput['activity_type'];
		$activity_status = $forminput['activity_status'];
		$mechanicid = $forminput['mechanicid'];
		$mechanic = $forminput['mechanic'];
		$mechanic_plant = $forminput['mechanic_plant'];

		$start_time = date("H:i:s");

		$check_machine = DB::connection('sqlsrv2')->select(DB::raw("  SELECT m.[MachNum], t.[Brand], t.[MaTyp], t.[MaCod]
		  FROM [BdkCLZG].[dbo].[CNF_MachPool] as m
		  JOIN [BdkCLZG].[dbo].[CNF_MaTypes] as t ON t.[IntKey] = m.[MaTyCod]
		  WHERE m.[MachNum] = '".$machine."' "));

		if (empty($check_machine)) {
			// dd('Machine does not exist in Inteos, MASINA NE POSTOJI U INTEOSU');

			$msg = 'Machine does not exist in Inteos, MASINA NE POSTOJI U INTEOSU';
			return view('Activity.error', compact('msg'));
		}
		// dd($check_machine);

		$machine_brand = $check_machine[0]->Brand;
		$machine_type = $check_machine[0]->MaTyp;
		$machine_code = $check_machine[0]->MaCod;

		$check_ifexist = DB::connection('sqlsrv')->select(DB::raw("SELECT id FROM machines WHERE activity_id = '".$activity_id."' AND machine = '".$machine."' "));
		// dd($check_ifexist);

		if (!empty($check_ifexist)) {
			// dd('Machine does not exist in Inteos, MASINA NE POSTOJI U INTEOSU');

			$msg = 'Machine alredy linked with this activity';
			return view('Activity.error', compact('msg'));
		}

		try {
			$table = new Machines;

			$table->machine = strtoupper($machine);
			$table->start_time = $start_time;

			$table->machine_brand = $machine_brand;
			$table->machine_type = $machine_type;
			$table->machine_code = $machine_code;

			$table->mechanic = $mechanic;
			$table->mechanicid = $mechanicid;
			
			$table->activity_type = $activity_type;
			$table->activity_id = $activity_id;
			
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('Activity.error');
		}


		// $activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
    	// return view('Activity.menu', compact('mechanicid','mechanic', 'activities'));

		// $machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT m.* , COUNT(mm.id) as c FROM [downtime].[dbo].machines as m
		  LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
		  WHERE m.[activity_id] = '".$activity_id."' 
		  GROUP BY m.[id]
		      ,m.[machine]
		      ,m.[start_time]
		      ,m.[machine_brand]
		      ,m.[machine_type]
		      ,m.[machine_code]
		      ,m.[mechanic]
		      ,m.[mechanicid]
		      ,m.[activity_type]
		      ,m.[activity_id]
		      ,m.[created_at]
		      ,m.[updated_at] "));

    	return view('Activity.add_machine', compact('mechanicid','mechanic','mechanic_plant','activity_id','activity_type','activity_status','machines'));

	}
	
	public function add_maintenance($id) 
	{
		// dd($id);
		$machine_id = $id;

		$activity_id_check = DB::connection('sqlsrv')->select(DB::raw("SELECT activity_id FROM machines WHERE id = '".$machine_id."' "));
		// dd($activity_id_check[0]->activity_id);
		$activity_id = $activity_id_check[0]->activity_id;

		$maintenance_checklist = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM maintenance_checklists WHERE deleted is NULL ORDER BY sort asc"));
		// dd($maintenance_checklist);

		$maintenance_machine_check = DB::connection('sqlsrv')->select(DB::raw("SELECT maintenance_id FROM machine_maintanences WHERE machine_id = '".$machine_id."' "));
		// dd($maintenance_machine_check);

    	return view('Activity.add_maintenance', compact('machine_id', 'activity_id','maintenance_checklist','maintenance_machine_check'));
	}

	public function add_maintenance_confirm(Request $request)
	{
		$this->validate($request, ['machine_id'=>'required']);
		$forminput = $request->all(); 
		// dd($forminput);

		$machine_id = $forminput['machine_id'];
		$activity_id = $forminput['activity_id'];
		
		$activity_status = DB::connection('sqlsrv')->select(DB::raw("SELECT status FROM activities WHERE id = '".$activity_id."' "));
		$activity_status = $activity_status[0]->status;
		// dd($activity_status);

		//$maintenance_machine_check = DB::connection('sqlsrv')->select(DB::raw("SELECT maintenance_id FROM machine_maintanences WHERE machine_id = '".$machine_id."' "));
		// dd($maintenance_machine_check);
		// var_dump($maintenance_machine_check);

		DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON ;DELETE FROM machine_maintanences WHERE machine_id = '".$machine_id."' ;
			SELECT TOP 1 * FROM machine_maintanences"));


		if (isset($forminput['maintenance_code'])) {

				$maintenance_code = $forminput['maintenance_code'];

				for ($i=0; $i < count($maintenance_code); $i++) { 
					// var_dump($maintenance_code[$i]);

					list($maintenance_id, $maintenance) = explode('#', $maintenance_code[$i]);
					// var_dump($maintenance_id);

					try {
						$table = new Machine_maintanence;

						$table->machine_id = $machine_id;
						$table->maintenance_id = $maintenance_id;
			
						$table->save();
					}
					catch (\Illuminate\Database\QueryException $e) {
						return view('Activity.error');
					}

				}

		} /*else {

			$maintenance_checklist = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM maintenance_checklists WHERE deleted is NULL ORDER BY sort asc"));
			// dd($maintenance_checklist);

	    	return view('Activity.add_maintenance', compact('machine_id', 'activity_id','maintenance_checklist'));

		}*/

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');

    	$activity_type = "MAINTENANCE";

		// dd($forminput);
		
		// $machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT m.* , COUNT(mm.id) as c FROM [downtime].[dbo].machines as m
		  LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
		  WHERE m.[activity_id] = '".$activity_id."' 
		  GROUP BY m.[id]
		      ,m.[machine]
		      ,m.[start_time]
		      ,m.[machine_brand]
		      ,m.[machine_type]
		      ,m.[machine_code]
		      ,m.[mechanic]
		      ,m.[mechanicid]
		      ,m.[activity_type]
		      ,m.[activity_id]
		      ,m.[created_at]
		      ,m.[updated_at] "));

    	return view('Activity.add_machine', compact('mechanicid','mechanic','mechanic_plant','activity_id','activity_type','activity_status','machines'));

		

	}

	public function add_maintenance_all($id) 
	{
		// dd($id);
		// $machine_id = $id;

		// $activity_id_check = DB::connection('sqlsrv')->select(DB::raw("SELECT activity_id FROM machines WHERE id = '".$machine_id."' "));
		// dd($activity_id_check[0]->activity_id);
		// $activity_id = $activity_id_check[0]->activity_id;

		$activity_id = $id;
		$machine_id = '';

		$maintenance_checklist = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM maintenance_checklists WHERE deleted is NULL ORDER BY sort asc"));
		// dd($maintenance_checklist);

		//$maintenance_machine_check = DB::connection('sqlsrv')->select(DB::raw("SELECT maintenance_id FROM machine_maintanences WHERE machine_id = '".$machine_id."' "));
		// dd($maintenance_machine_check);
		$maintenance_machine_check = NULL;

    	return view('Activity.add_maintenance_all', compact('machine_id','activity_id','maintenance_checklist','maintenance_machine_check'));
	}

	public function add_maintenance_confirm_all(Request $request)
	{
		// $this->validate($request, ['machine_id'=>'required']);
		$forminput = $request->all(); 
		// dd($forminput);

		// $machine_id = $forminput['machine_id'];
		$activity_id = $forminput['activity_id'];
		
		$activity_status = DB::connection('sqlsrv')->select(DB::raw("SELECT status FROM activities WHERE id = '".$activity_id."' "));
		$activity_status = $activity_status[0]->status;
		// dd($activity_status);

		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
		// dd($machines);

		foreach ($machines as $line) {
			// dd($line->id);

			$maintenance_machine_check = DB::connection('sqlsrv')->select(DB::raw("SELECT maintenance_id FROM machine_maintanences WHERE machine_id = '".$line->id."' "));
			// dd($maintenance_machine_check);
			// var_dump($maintenance_machine_check);

			// DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON ;DELETE FROM machine_maintanences WHERE machine_id = '".$line->id."' ;
			// 	SELECT TOP 1 * FROM machine_maintanences"));

			if (isset($forminput['maintenance_code'])) {
				// dd($forminput['maintenance_code']);

				$maintenance_code = $forminput['maintenance_code'];

				for ($i=0; $i < count($maintenance_code); $i++) { 
					// var_dump($maintenance_code[$i]);

					list($maintenance_id, $maintenance) = explode('#', $maintenance_code[$i]);
					// var_dump($maintenance_id);

					$exist = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machine_maintanences WHERE machine_id = '".$line->id."' AND maintenance_id = '".$maintenance_id."' "));
					// dd($exist);

					if (isset($exist[0]->id)) {
						
					} else {

						try {
						$table = new Machine_maintanence;

						$table->machine_id = $line->id;
						$table->maintenance_id = $maintenance_id;
			
						$table->save();
						}
						catch (\Illuminate\Database\QueryException $e) {
							return view('Activity.error');
						}
					}
				}
			}
		}

		// $machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines WHERE activity_id = '".$activity_id."' "));
			$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT m.* , COUNT(mm.id) as c FROM [downtime].[dbo].machines as m
			  LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
			  WHERE m.[activity_id] = '".$activity_id."' 
			  GROUP BY m.[id]
			      ,m.[machine]
			      ,m.[start_time]
			      ,m.[machine_brand]
			      ,m.[machine_type]
			      ,m.[machine_code]
			      ,m.[mechanic]
			      ,m.[mechanicid]
			      ,m.[activity_type]
			      ,m.[activity_id]
			      ,m.[created_at]
			      ,m.[updated_at] "));

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');
    	$activity_type = "MAINTENANCE";

    	return view('Activity.add_machine', compact('mechanicid','mechanic','mechanic_plant','activity_id','activity_type','activity_status','machines'));
	}

	public function add_maachine_all($id) {

		// dd($id);
		$activity_id = $id;
		$ses = Session::set('activity_id', $activity_id);
		return view('Import.index', compact('activity_id'));

	}





	
}
