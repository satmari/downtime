<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\QueryException as QueryException;
use App\Exceptions\Handler;

// use Illuminate\Http\Request;
use Request;
//use Gbrock\Table\Facades\Table;
use Illuminate\Support\Facades\Redirect;

use Maatwebsite\Excel\Facades\Excel;

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

class importController extends Controller {

	public function index()
	{
		//

	}

	public function postImportMachines(Request $request) {

		// dd("Test");
		// $forminput = $request->all(); 
		// dd($forminput);

		// $machine_id = $forminput['machine_id'];
		// $activity_id = $forminput['activity_id'];

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
    	$mechanic_plant = Session::get('mechanic_plant');
    	$activity_id = Session::get('activity_id');
    	// dd($activity_id);

    	$id_ses = Session::set('activity_id', $activity_id);

    	// dd($mechanic);

    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE id = '".$activity_id."' "));
    	// dd($activities);

    	if (!empty($activities)) {
    		// dd("activity exist");
    		$activity_id = $activities[0]->id;		
    		$activity_type = $activities[0]->activity_type;
    		$activity_status = $activities[0]->status;
    	}  else {
    		dd("activity not exist");

    	}



    	// use Request1;
		$getSheetName = Excel::load(Request::file('file'))->getSheetNames();
	    
	    foreach($getSheetName as $sheetName)
	    {
	        //if ($sheetName === 'Product-General-Table')  {
	    	//selectSheetsByIndex(0)
	           	//DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	            //DB::table('users')->truncate();
	
	            //Excel::selectSheets($sheetName)->load($request->file('file'), function ($reader)
	            //Excel::selectSheets($sheetName)->load(Input::file('file'), function ($reader)
	            //Excel::filter('chunk')->selectSheetsByIndex(0)->load(Request::file('file'))->chunk(50, function ($reader)
	            Excel::filter('chunk')->selectSheets($sheetName)->load(Request::file('file'))->chunk(500, function ($reader)
	            
	            {
	                $readerarray = $reader->toArray();
	                //var_dump($readerarray);

	                foreach($readerarray as $row)
	                {
	                	
	                	$machine = $row['machine'];
	                	// dd($machine);

	                	$check_machine = DB::connection('sqlsrv2')->select(DB::raw("SELECT m.[MachNum], t.[Brand], t.[MaTyp], t.[MaCod]
						  FROM [BdkCLZG].[dbo].[CNF_MachPool] as m
						  JOIN [BdkCLZG].[dbo].[CNF_MaTypes] as t ON t.[IntKey] = m.[MaTyCod]
						  WHERE m.[MachNum] = '".$machine."' "));

						if (empty($check_machine)) {
							// dd('Machine does not exist in Inteos, MASINA NE POSTOJI U INTEOSU');

							$msg = 'Machine does not exist in Inteos, MASINA '.$machine.' NE POSTOJI U INTEOSU';
							return view('Activity.error', compact('msg'));
						}

						$machine_brand = $check_machine[0]->Brand;
						$machine_type = $check_machine[0]->MaTyp;
						$machine_code = $check_machine[0]->MaCod;

						$mechanicid = Session::get('mechanicid');
				    	$mechanic = Session::get('mechanic');
				    	$mechanic_plant = Session::get('mechanic_plant');
				    	$activity_id = Session::get('activity_id');
				    	
				    	$activities = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM activities WHERE id = '".$activity_id."' "));
				    	// dd($activities);

				    	if (!empty($activities)) {
				    		// dd("activity exist");
				    		$activity_id = $activities[0]->id;		
				    		$activity_type = $activities[0]->activity_type;
				    		$activity_status = $activities[0]->status;
				    	}  else {
				    		dd("activity not exist");

				    	}


						$check_ifexist = DB::connection('sqlsrv')->select(DB::raw("SELECT id FROM machines WHERE activity_id = '".$activity_id."' AND machine = '".$machine."' "));
						// dd($check_ifexist);

						if (!empty($check_ifexist)) {
							// dd('Machine does not exist in Inteos, MASINA NE POSTOJI U INTEOSU');

							$msg = 'Machine alredy linked with this activity';
							return view('Activity.error', compact('msg'));
						}

						$start_time = date("H:i:s");

						try {
							$table = new Machines;

							$table->machine = strtoupper($machine);
							// var_dump($machine);
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

					}

	            });
	    }

		// return redirect('/');

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

	

}
