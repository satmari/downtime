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

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;


use Session;
use Validator;

class ActivityController extends Controller {

	public function index()
	{
		//
		$mechanicid = Session::get('mechanicid');
		if (isset($mechanicid) OR ($mechanicid != '')) {
			// $msg = 'Mechanic is not autenticated';
			// return view('InteosLogin.error',compact('msg'));
			return Redirect::to('afterlogin3'); //afterlogin
		}

		return view('Activity.index'); //InteosLogin
	}

	public function logincheck(Request $request)
	{
		$this->validate($request, ['pin'=>'required|min:4|max:5']);
		$forminput = $request->all(); 

		$pin = $forminput['pin'];
		// dd($pin);

		$inteosmech = DB::connection('sqlsrv2')->select(DB::raw("SELECT	Cod, Name,
		(SELECT e.[Subdepartment] FROM [172.27.161.221\GPD].[Gordon_LIVE].[dbo].[GORDON\$Employee] as e where e.[No_] COLLATE Latin1_General_CI_AS = BadgeNum ) as plant
		FROM BdkCLZG.dbo.WEA_PersData WHERE Func = 2 AND FlgAct = 1 AND PinCode = '".$pin."'"));
		// dd($inteosmech);
		
		/*
		$inteosleaders = DB::connection('sqlsrv2')->select(DB::raw("SELECT 
			Name 
		FROM [BdkCLZG].[dbo].[WEA_PersData] 
		WHERE (Func = 2) and (FlgAct = 1) and (PinCode = ".$pin.")
		UNION ALL
		SELECT 
			Name 
		FROM [SBT-SQLDB01P\\INTEOSKKA].[BdkCLZKKA].[dbo].[WEA_PersData]
		WHERE (Func = 2) and (FlgAct = 1) and (PinCode = ".$pin.")"));
		*/

		if (empty($inteosmech)) {
			$msg = 'Mechanic with this PIN is not active';
		    return view('Activity.error',compact('msg'));
		
		} else {
			foreach ($inteosmech as $row) {
				$mechanicid = $row->Cod;
    			$mechanic = $row->Name;

    			if ($row->plant == 'Mechanics') {
    				$mechanic_plant = 'Subotica';
    			} else if  ($row->plant == 'Mechanics KIKINDA') {
    				$mechanic_plant = 'Kikinda';
    			} else {
    				$mechanic_plant = 'missing';
    			}

    			// dd($mechanic_plant);

    			Session::set('mechanicid', $mechanicid);
    			Session::set('mechanic', $mechanic);
    			Session::set('mechanic_plant', $mechanic_plant);
    		}

   			//if (Auth::check())
			// {
			//     $userId = Auth::user()->id;
			//     $module = Auth::user()->name;
			// } else {
			// 	$msg = 'Modul is not autenticated';
			// 	return view('InteosLogin.error',compact('msg'));
			// }
			// $module_line = substr($module, 0, 1);
    		// $module_name = substr($module, 1, 3);
    	}
    	return Redirect::to('afterlogin3');
	}

	public function afterlogin()
	{

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');
    	// dd($mechanic);

    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') ORDER BY status asc "));
    	// dd($activities);


    	return view('Activity.menu', compact('mechanicid','mechanic', 'mechanic_plant', 'activities'));
    }

    public function clear_session_mechanic()
	{

		Session::set('mechanicid', NULL);
		Session::set('mechanic', NULL);
		Session::set('mechanic_plant', NULL);

		return view('Activity.index');
	}

	public function add_activity() 
	{
		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');

    	// dd($mechanic);

    	
    	return view('Activity.add_activity', compact('mechanicid','mechanic','mechanic_plant'));

	}

	public function stop_activity() 
	{
		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');

    	// dd($mechanic);
    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND status = 'OPEN' "));
    	

    	if (!empty($activities)) {
    	// dd($activities);

    		if ($activities[0]->activity_type == "SETTING") {

    			$check_machine = DB::connection('sqlsrv')->select(DB::raw("SELECT id FROM machines WHERE activity_id = '".$activities[0]->id."' "));
    			// dd($check_machine[0]);

				if (isset($check_machine[0])) { 

	    			$t = DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON;
	    				UPDATE activities SET status = 'CLOSED', end_time = '".date("H:i:s")."' WHERE id = '".$activities[0]->id."';
	    				SELECT TOP 1 * FROM activities"));

	    			// dd("done");
	    		} else {

	    			$t = DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON;
	    				UPDATE activities SET status = 'PENDING', end_time = '".date("H:i:s")."' WHERE id = '".$activities[0]->id."';
	    				SELECT TOP 1 * FROM activities"));


	    		}

    		} else {

    			// dd("MAINTENANCE activiti, check if contain mashines");
    			$check_machine = DB::connection('sqlsrv')->select(DB::raw("SELECT id FROM machines WHERE activity_id = '".$activities[0]->id."' "));
    			// dd($check_machine);

    			if (isset($check_machine[0])) {


    				$check_tasks = DB::connection('sqlsrv')->select(DB::raw("SELECT
 						a.*, m.*,
							 COUNT(mm.id) as number_of_tasks
							 
							 FROM [downtime].[dbo].[activities] as a
							 LEFT JOIN [downtime].[dbo].[machines] as m ON a.id = m.activity_id
							 LEFT JOIN [downtime].[dbo].[machine_maintanences] as mm on m.id = mm.machine_id
							 WHERE  mm.id is NULL AND a.id = '".$activities[0]->id."'
							 GROUP BY a.[id]
							      ,a.[mechanic]
							      ,a.[mechanicid]
							      ,a.[activity_type]
							      ,a.[status]
							      ,a.[date]
							      ,a.[start_time]
							      ,a.[end_time]
							      ,a.[plant]
							      ,a.[created_at]
							      ,a.[updated_at]
							      ,m.[id]
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
							      ,m.[updated_at]
							      ,mm.[id]
    					"));

    				// dd($check_tasks);

    				if(empty($check_tasks)) {
    					// dd('all mechines has tasks -> yes');

	    				$t = DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON;
	    				UPDATE activities SET status = 'CLOSED', end_time = '".date("H:i:s")."' WHERE id = '".$activities[0]->id."';
	    				SELECT TOP 1 * FROM activities"));

    				} else {
    					// dd('all mechines has tasks -> no');

    					
	    				$t = DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON;
	    				UPDATE activities SET status = 'PENDING', end_time = '".date("H:i:s")."' WHERE id = '".$activities[0]->id."';
	    				SELECT TOP 1 * FROM activities"));
    				}

    			} else {

    				$msg = 'You can not close MAINTENANCE ACTIVITY without declared machine. Nije moguce zatvoriti aktivnost ORDZAVANJA ukoliko nije deklarisana masina.';
    				return view('Activity.error', compact('msg'));

    			}

    			

    		}
    		
    	}

    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
    	return view('Activity.menu', compact('mechanicid','mechanic','mechanic_plant','activities'));
    	// return view('Activity.index');

	}

	public function stop_activity_id ($id) 
	{
		dd('test');

	}

	public function maintenance(Request $request) 
	{	
		$forminput = $request->all(); 
		$mechanic = $forminput['mechanic'];
		$mechanicid = $forminput['mechanicid'];
		$mechanic_plant = $forminput['mechanic_plant'];

		if ($mechanicid == '3217') {
			$mechanic_plant = 'Kikinda';
		}

		// $mechanicid = Session::get('mechanicid');
    	// $mechanic = Session::get('mechanic');

    	// dd('mechenic in maintenance: '.$mechanic);

		$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND status = 'OPEN'  "));
		// dd($activities);


		if (!isset($activities[0]->id)) {
					
	    	$activity_type = 'MAINTENANCE';
	    	$status = 'OPEN';

	    	$date = date("Y-m-d");
	    	$start_time = date("H:i:s");
	    	// $end_time;

	    	try {
				$table = new Activity;

				$table->mechanic = $mechanic;
				$table->mechanicid = $mechanicid;

				$table->activity_type = $activity_type;
				$table->status = $status;

				$table->date = $date;
				$table->start_time = $start_time;
				// $table->end_time = $end_time;

				$table->plant = $mechanic_plant;

				$table->save();
			}
			catch (\Illuminate\Database\QueryException $e) {
				$msg = 'Problem to save';
				return view('Activity.error', compact('msg'));
			}

		} else {
			// dd("some activity for user is already open");

			// $activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
	    	// return view('Activity.menu', compact('mechanicid','mechanic', 'activities'));

			$msg = 'This user already have some activity with status OPEN';
			return view('Activity.error', compact('msg'));
		}

		$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
    	return view('Activity.menu', compact('mechanicid','mechanic', 'mechanic_plant','activities'));
		// return view('Activity.index');
    	
	}

	public function setting(Request $request) 
	{	
		$forminput = $request->all(); 
		$mechanic = $forminput['mechanic'];
		$mechanicid = $forminput['mechanicid'];
		$mechanic_plant = $forminput['mechanic_plant'];

		if ($mechanicid == '3217') {
			$mechanic_plant = 'Kikinda';
		}

		// $mechanicid = Session::get('mechanicid');
    	// $mechanic = Session::get('mechanic');

    	// dd('mechenic in setting: '.$mechanic);

		$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND status = 'OPEN'  "));
		// dd($activities);

		if (!isset($activities[0]->id)) {

	    	$activity_type = 'SETTING';
	    	$status = 'OPEN';

	    	$date = date("Y-m-d");
	    	$start_time = date("H:i:s");
	    	// $end_time;

	    	try {
				$table = new Activity;

				$table->mechanic = $mechanic;
				$table->mechanicid = $mechanicid;

				$table->activity_type = $activity_type;
				$table->status = $status;

				$table->date = $date;
				$table->start_time = $start_time;
				// $table->end_time = $end_time;
				
				$table->plant = $mechanic_plant;

				$table->save();
			}
			catch (\Illuminate\Database\QueryException $e) {
				$msg = 'Problem to save';
				return view('Activity.error', compact('msg'));
			}

		} else {
			// dd("some activity for user is already open");

			// $activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
	    	// return view('Activity.menu', compact('mechanicid','mechanic', 'activities'));

			$msg = 'This user already have some activity with status OPEN';
			return view('Activity.error', compact('msg'));
		}

		$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE mechanicid = '".$mechanicid."' AND (status = 'OPEN' OR status = 'PENDING') "));
    	return view('Activity.menu', compact('mechanicid','mechanic','mechanic_plant','activities'));
		// return view('Activity.index');

   	}

   	public function activity_table() {

   		$data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE (status = 'OPEN' OR status = 'PENDING') "));
    	return view('Activity.activity_table', compact('data'));

   	}

}
