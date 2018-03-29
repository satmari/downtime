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

use App\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;

use Session;
use Validator;

class InteosLoginController extends Controller {

	public function index()
	{	
		//
		$mechanicid = Session::get('mechanicid');
		if (isset($mechanicid)) {
			// $msg = 'Mechanic is not autenticated';
			// return view('InteosLogin.error',compact('msg'));
			return Redirect::to('afterlogin');
		}

		return view('InteosLogin.index');
	}

	public function logincheck(Request $request)
	{
		$this->validate($request, ['pin'=>'required|min:4|max:5']);
		$forminput = $request->all(); 

		$pin = $forminput['pin'];
		// dd($pin);

		$inteosmech = DB::connection('sqlsrv2')->select(DB::raw("SELECT Cod,Name FROM BdkCLZG.dbo.WEA_PersData WHERE Func = 2 AND FlgAct = 1 AND PinCode = '".$pin."'"));

		if (empty($inteosmech)) {
			$msg = 'Mechanic with this PIN is not active';
		    return view('InteosLogin.error',compact('msg'));
		
		} else {
			foreach ($inteosmech as $row) {
				$mechanicid = $row->Cod;
    			$mechanic = $row->Name;
    			Session::set('mechanicid', $mechanicid);
    			Session::set('mechanic', $mechanic);
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

    	return Redirect::to('afterlogin');
	}

	public function afterlogin()
	{

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
		// dd($mechanic);



    	$data = DB::connection('sqlsrv2')->select(DB::raw("SELECT cast(DL.[DeclSta] as date) as [Date],
		substring(convert(char(10),
		cast(DL.[DeclSta] as time(0))),1,5) as [Start],
		substring(convert(char(10), Cast(case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end as time(0))),1,5) as [Finished], 
		MDL.ModNam as [ModuleName], case when MM.Declaration IS null then dcl.Name else MM.Declaration end as [Declaration], 
		MTP.MaCod as [Type], 
		MCH.MachNum AS [Machine],
		cast(datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end)/60 as varchar(10)) + ':' + right('0' + cast(datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end)%60 as varchar(2)),2) as [Total_time], 
		cast(datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])/60 as varchar(10)) + ':' +  RIGHT('0' + cast(datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])%60 as varchar(2)),2) as [Waiting_time], 
		cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) as [Repair_time],
		MM.Responsible, 
		MM.Solution, 
		MM.MecKey,
		/*MM.Remark,
        DL.[Module] as [xModule Nr],
        DL.[DeclCod] as [xDeclCod],
		DL.[DeclMch] as [xDeclMch],
		DL.[DeclSta] as [xDeclSta],
		DL.[DeclEnd] as [xDeclCodEnd],
		DL.[DeclCat] as [xDeclCat],
		DL.[DeclSol] as [xDeclSol],
		DL.[MecKey] as [xMecKey],
		Cast(case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end as smalldatetime) as [xDeclEndTotal],*/
		mm.DeclCod
  FROM [BdkCLZG].[dbo].[CNF_DeclLog] as DL 
	  left join [BdkCLZG].[dbo].[WEA_Decl] as Dcl on dcl.Id = dl.DeclCod 
      left join [BdkCLZG].[dbo].[CNF_Modules] as MDL on MDL.Module = DL.Module 
      left join [BdkCLZG].[dbo].[WEA_PersData] as PERS on PERS.Cod = DL.MecKey
      left join [BdkCLZG].[dbo].[CNF_MachPool] as MCH on MCH.Cod = DL.DeclMch
      left join [BdkCLZG].[dbo].[CNF_MaTypes] as MTP on MTP.IntKey = MCH.MaTyCod
      left join [BdkCLZG].[dbo].[WEA_Decl] as Decl on Decl.Id = DL.DeclSol
      left join 
      (SELECT cast([DeclSta] as date) as [Date],
		substring(convert(char(10), cast([DeclSta] as time(0))),1,5) as [Start],
		substring(convert(char(10), cast([DeclEnd] as time(0))),1,5) as [Finished],
		MDL.ModNam as [ModuleName], 
		dcl.Name as Declaration, 
		MTP.MaCod as [Type], 
		MCH.MachNum AS [Machine],
		datediff(MINUTE,[DeclSta],[DeclEnd]) as Totaltime,
		PERS.Name as [Responsible],
		Decl.Name as [Solution], 
		DL.[Remark],
		DL.[Module],
		[DeclCod],
		[DeclMch],
		[DeclSta],
		[DeclEnd],
		[DeclCat],
		[DeclSol],
		[MecKey]
  FROM [BdkCLZG].[dbo].[CNF_DeclLog] as DL 
	  left join [BdkCLZG].[dbo].[WEA_Decl] as Dcl on dcl.Id = dl.DeclCod 
      left join [BdkCLZG].[dbo].[CNF_Modules] as MDL on MDL.Module = DL.Module 
      left join [BdkCLZG].[dbo].[WEA_PersData] as PERS on PERS.Cod = DL.MecKey
      left join [BdkCLZG].[dbo].[CNF_MachPool] as MCH on MCH.Cod = DL.DeclMch
      left join [BdkCLZG].[dbo].[CNF_MaTypes] as MTP on MTP.IntKey = MCH.MaTyCod
      left join [BdkCLZG].[dbo].[WEA_Decl] as Decl on Decl.Id = DL.DeclSol
      
      where [DeclEnd] is not null and ([DeclCod] = 2 or [DeclCod] = 4 or [DeclCod] = 12 or [DeclCod] = 14) ) as MM on MM.DeclSta = DL.DeclEnd
      where DL.[DeclEnd] is not null and (dl.[DeclCod] <> 2 and dl.[DeclCod] <> 4 and dl.[DeclCod] <> 12 and dl.[DeclCod] <> 14) and mm.DeclCod is not null 
      
      and Date >= '2018-03-28' 
      and MTP.MaCod <> 'CHANGE LAYOUT'
      and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
      
      and MM.MecKey = '".$mechanicid."'
     
      order by Date desc ,Start desc

	"));
	
	// dd($data);
	// dd(count($data));
	// dd($data[0]->Repair_time);
	
	$newarray = [];
	Session::set('newarray', NULL);
	
	for ($i=0; $i < count($data) ; $i++) { 

		// dd($data[$i]);

		$key = $data[$i]->Date." ".$data[$i]->Start." ".$data[$i]->Machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT mechanic_comment FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);

		if (empty($bd_data)) {
			// dd("empty - not found in local db");

			array_push($newarray, array(
		        "Date" => $data[$i]->Date,						//0
		        "Start" => $data[$i]->Start,					//1
		        "Finished" => $data[$i]->Finished,				//2
		        "Declaration" => $data[$i]->Declaration,		//3
		        "Type" => $data[$i]->Type,						//4
		        "Machine" => $data[$i]->Machine,				//5
		        "Total_time" => $data[$i]->Total_time,			//6
		        "Waiting_time" => $data[$i]->Waiting_time,		//7
		        "Repair_time" => $data[$i]->Repair_time,		//8
		        "Responsible" => $data[$i]->Responsible,		//9
		        "ModuleName" => $data[$i]->ModuleName,			//10
		        // "Solution" => $data[$i]->Solution,
		        // "DeclCod" => $data[$i]->DeclCod,
		        "MechComment" => "",							//11
		    ));

		} else {
			// dd("not empty - found in local db");

			if ($bd_data[0]->mechanic_comment) {
				
				// dd($data[$i]->Date);
				// dd($bd_data[0]->mechanic_comment);

				// array_push($newarray, array(
				// 	"Date" => $data[$i]->Date,						//0
			 //        "Start" => $data[$i]->Start,					//1
			 //        "Finished" => $data[$i]->Finished,				//2
			 //        "Declaration" => $data[$i]->Declaration,		//3
			 //        "Type" => $data[$i]->Type,						//4
			 //        "Machine" => $data[$i]->Machine,				//5
			 //        "Total_time" => $data[$i]->Total_time,			//6
			 //        "Waiting_time" => $data[$i]->Waiting_time,		//7
			 //        "Repair_time" => $data[$i]->Repair_time,		//8
			 //        "Responsible" => $data[$i]->Responsible,		//9
			 //        "ModuleName" => $data[$i]->ModuleName,			//10
			 //        // "Solution" => $data[$i]->Solution,
			 //        // "DeclCod" => $data[$i]->DeclCod,
			 //        "MechComment" => $bd_data[0]->mechanic_comment,	//11
		  //       ));

			} else {
				// dd($data[$i]->Date);
				// dd($bd_data[0]->mechanic_comment);

				array_push($newarray, array(
					"Date" => $data[$i]->Date,						//0
			        "Start" => $data[$i]->Start,					//1
			        "Finished" => $data[$i]->Finished,				//2
			        "Declaration" => $data[$i]->Declaration,		//3
			        "Type" => $data[$i]->Type,						//4
			        "Machine" => $data[$i]->Machine,				//5
			        "Total_time" => $data[$i]->Total_time,			//6
			        "Waiting_time" => $data[$i]->Waiting_time,		//7
			        "Repair_time" => $data[$i]->Repair_time,		//8
			        "Responsible" => $data[$i]->Responsible,		//9
			        "ModuleName" => $data[$i]->ModuleName,			//10
			        // "Solution" => $data[$i]->Solution,
			        // "DeclCod" => $data[$i]->DeclCod,
			        "MechComment" => "",							//11

		        ));
			}
		}
		// dd($newarray);
	}


	Session::set('newarray', $newarray);

	// dd($newarray);
	// print_r($newarray);

		return view('Mechanic.index', compact('newarray','mechanicid','mechanic'));
	}
		
	public function new_bd_comment(Request $request, $value)
	{	
		//
		// $this->validate($request, ['comment'=>'max:50']);
		$input = $request->all(); 
		// dd($input);
		// $value = $input['value'];
		// dd($value);

		$newarray = Session::get('newarray');
		// dd($newarray);

		$values = explode("_", $value);

		// print_r($values);
		// dd($values[9]);

		$date = $values[0];
		$start = $values[1];
		$finished = $values[2];
		$decl = $values[3];
		$type = $values[4];
		$machine = $values[5];
		$tot_time = $values[6];
		$wait_time = $values[7];
		$repair_time = $values[8];
		$responsible = $values[9];
		$modulename = $values[10];
		$mech_coment = $values[11];

		// $key = $date." ".$start." ".$machine;

		return view('Mechanic.add', compact('date','start','finished','decl','type','machine','tot_time','wait_time','repair_time','responsible','modulename','mech_coment'));
		

	}

	public function downtime_insert(Request $request)
	{
		$this->validate($request, ['new_mech_comment'=>'required']);

		$input = $request->all();
		// dd($input);

		$date = $input['date'];
		$start = $input['start'];
		$finished = $input['finished'];
		$decl = $input['decl'];
		$type = $input['type'];
		$machine = $input['machine'];
		$tot_time = $input['tot_time'];
		$wait_time = $input['wait_time'];
		$repair_time = $input['repair_time'];
		$responsible = $input['responsible'];
		$modulename = $input['modulename'];
		$mech_coment = $input['new_mech_comment'];

		$mechanicid = intval(Session::get('mechanicid'));
    	$mechanic = Session::get('mechanic');
		
		$key = $date." ".$start." ".$machine;
		// dd($key);

		// $total_time = strtotime($tot_time);
		// $wait_time = strtotime($wait_time);
		// $repair_time = strtotime($repair_time);
		
		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data[0]->id);

		$date = date('Y-m-d',strtotime($date));

		// if ($mech_coment == "" OR is_null($mech_coment)) {
		// 	dd("null or empty");
		// }

		if (empty($bd_data)) {
			// dd("empty");
			// dd($total_time);
			// var_dump("empty");

			// try {

				$table = new Downtime;
				$table->bd_key = $key;
				$table->bd_date = $date;
				$table->start = $start;
				$table->finished = $finished;
				
				$table->decl = $decl;
				$table->type = $type;
				$table->machine = $machine;
				$table->total_time = $tot_time;
				$table->wait_time = $wait_time;
				$table->repair_time = $repair_time;
				$table->responsible = $responsible;
				$table->module = $modulename;

				$table->mechanic_id = $mechanicid;
				$table->mechanic = $mechanic;

				$table->mechanic_comment = $mech_coment;
				
				$table->save();
			// }
			// catch (\Illuminate\Database\QueryException $e) {
			// 	return view('Mechanic.error');
			// }

			
		} else {
			// dd("not empty");

			$table = Downtime::findOrFail($bd_data[0]->id);		

			// try {
				
				// $table->bd_key = $key;
				// $table->bd_date = $bd_date;
				// $table->start = $start;
				// $table->finished = $finished;
				// $table->decl = $decl;
				// $table->type = $type;
				// $table->machine = $machine;
				// $table->total_time = $total_time;
				// $table->wait_time = $wait_time;
				// $table->repair_time = $repair_time;
				// $table->responsible = $responsible;

				$table->mechanic_id = $mechanicid;
				$table->mechanic = $mechanic;

				$table->mechanic_comment = $mech_coment;
											
				$table->save();
			// }
			// catch (\Illuminate\Database\QueryException $e) {
			// 	return view('Mechanic.error');			
			// }
			
		}

		return Redirect::to('/inteoslogin');
		

	}

	public function clear_session_mechanic()
	{

		Session::set('mechanicid', NULL);
		return view('InteosLogin.index');
	}

	public function afterloginall()
	{

		$mechanicid = Session::get('mechanicid');
    	$mechanic = Session::get('mechanic');
		// dd($mechanic);



    	$data = DB::connection('sqlsrv2')->select(DB::raw("SELECT cast(DL.[DeclSta] as date) as [Date],
		substring(convert(char(10),
		cast(DL.[DeclSta] as time(0))),1,5) as [Start],
		substring(convert(char(10), Cast(case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end as time(0))),1,5) as [Finished], 
		MDL.ModNam as [ModuleName], case when MM.Declaration IS null then dcl.Name else MM.Declaration end as [Declaration], 
		MTP.MaCod as [Type], 
		MCH.MachNum AS [Machine],
		cast(datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end)/60 as varchar(10)) + ':' + right('0' + cast(datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end)%60 as varchar(2)),2) as [Total_time], 
		cast(datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])/60 as varchar(10)) + ':' +  RIGHT('0' + cast(datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])%60 as varchar(2)),2) as [Waiting_time], 
		cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) as [Repair_time],
		MM.Responsible, 
		MM.Solution, 
		MM.MecKey,
		/*MM.Remark,
        DL.[Module] as [xModule Nr],
        DL.[DeclCod] as [xDeclCod],
		DL.[DeclMch] as [xDeclMch],
		DL.[DeclSta] as [xDeclSta],
		DL.[DeclEnd] as [xDeclCodEnd],
		DL.[DeclCat] as [xDeclCat],
		DL.[DeclSol] as [xDeclSol],
		DL.[MecKey] as [xMecKey],
		Cast(case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end as smalldatetime) as [xDeclEndTotal],*/
		mm.DeclCod
  FROM [BdkCLZG].[dbo].[CNF_DeclLog] as DL 
	  left join [BdkCLZG].[dbo].[WEA_Decl] as Dcl on dcl.Id = dl.DeclCod 
      left join [BdkCLZG].[dbo].[CNF_Modules] as MDL on MDL.Module = DL.Module 
      left join [BdkCLZG].[dbo].[WEA_PersData] as PERS on PERS.Cod = DL.MecKey
      left join [BdkCLZG].[dbo].[CNF_MachPool] as MCH on MCH.Cod = DL.DeclMch
      left join [BdkCLZG].[dbo].[CNF_MaTypes] as MTP on MTP.IntKey = MCH.MaTyCod
      left join [BdkCLZG].[dbo].[WEA_Decl] as Decl on Decl.Id = DL.DeclSol
      left join 
      (SELECT cast([DeclSta] as date) as [Date],
		substring(convert(char(10), cast([DeclSta] as time(0))),1,5) as [Start],
		substring(convert(char(10), cast([DeclEnd] as time(0))),1,5) as [Finished],
		MDL.ModNam as [ModuleName], 
		dcl.Name as Declaration, 
		MTP.MaCod as [Type], 
		MCH.MachNum AS [Machine],
		datediff(MINUTE,[DeclSta],[DeclEnd]) as Totaltime,
		PERS.Name as [Responsible],
		Decl.Name as [Solution], 
		DL.[Remark],
		DL.[Module],
		[DeclCod],
		[DeclMch],
		[DeclSta],
		[DeclEnd],
		[DeclCat],
		[DeclSol],
		[MecKey]
  FROM [BdkCLZG].[dbo].[CNF_DeclLog] as DL 
	  left join [BdkCLZG].[dbo].[WEA_Decl] as Dcl on dcl.Id = dl.DeclCod 
      left join [BdkCLZG].[dbo].[CNF_Modules] as MDL on MDL.Module = DL.Module 
      left join [BdkCLZG].[dbo].[WEA_PersData] as PERS on PERS.Cod = DL.MecKey
      left join [BdkCLZG].[dbo].[CNF_MachPool] as MCH on MCH.Cod = DL.DeclMch
      left join [BdkCLZG].[dbo].[CNF_MaTypes] as MTP on MTP.IntKey = MCH.MaTyCod
      left join [BdkCLZG].[dbo].[WEA_Decl] as Decl on Decl.Id = DL.DeclSol
      
      where [DeclEnd] is not null and ([DeclCod] = 2 or [DeclCod] = 4 or [DeclCod] = 12 or [DeclCod] = 14) ) as MM on MM.DeclSta = DL.DeclEnd
      where DL.[DeclEnd] is not null and (dl.[DeclCod] <> 2 and dl.[DeclCod] <> 4 and dl.[DeclCod] <> 12 and dl.[DeclCod] <> 14) and mm.DeclCod is not null 
      
      and Date >= '2018-03-28' 
      and MTP.MaCod <> 'CHANGE LAYOUT'
      and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
      
      and MM.MecKey = '".$mechanicid."'
     
      order by Date desc ,Start desc

	"));
	
	// dd($data);
	// dd(count($data));
	// dd($data[0]->Repair_time);
	
	$newarray = [];
	
	for ($i=0; $i < count($data) ; $i++) { 

		// dd($data[$i]);

		$key = $data[$i]->Date." ".$data[$i]->Start." ".$data[$i]->Machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT mechanic_comment FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);

		if (empty($bd_data)) {
			// dd("empty - not found in local db");

			array_push($newarray, array(
		        "Date" => $data[$i]->Date,						//0
		        "Start" => $data[$i]->Start,					//1
		        "Finished" => $data[$i]->Finished,				//2
		        "Declaration" => $data[$i]->Declaration,		//3
		        "Type" => $data[$i]->Type,						//4
		        "Machine" => $data[$i]->Machine,				//5
		        "Total_time" => $data[$i]->Total_time,			//6
		        "Waiting_time" => $data[$i]->Waiting_time,		//7
		        "Repair_time" => $data[$i]->Repair_time,		//8
		        "Responsible" => $data[$i]->Responsible,		//9
		        "ModuleName" => $data[$i]->ModuleName,			//10
		        // "Solution" => $data[$i]->Solution,
		        // "DeclCod" => $data[$i]->DeclCod,
		        "MechComment" => "",							//11
		    ));

		} else {
			// dd("not empty - found in local db");

			if ($bd_data[0]->mechanic_comment) {
				
				// dd($data[$i]->Date);
				// dd($bd_data[0]->mechanic_comment);

				array_push($newarray, array(
					"Date" => $data[$i]->Date,						//0
			        "Start" => $data[$i]->Start,					//1
			        "Finished" => $data[$i]->Finished,				//2
			        "Declaration" => $data[$i]->Declaration,		//3
			        "Type" => $data[$i]->Type,						//4
			        "Machine" => $data[$i]->Machine,				//5
			        "Total_time" => $data[$i]->Total_time,			//6
			        "Waiting_time" => $data[$i]->Waiting_time,		//7
			        "Repair_time" => $data[$i]->Repair_time,		//8
			        "Responsible" => $data[$i]->Responsible,		//9
			        "ModuleName" => $data[$i]->ModuleName,			//10
			        // "Solution" => $data[$i]->Solution,
			        // "DeclCod" => $data[$i]->DeclCod,
			        "MechComment" => $bd_data[0]->mechanic_comment,	//11
		        ));

			} else {
				// dd($data[$i]->Date);
				// dd($bd_data[0]->mechanic_comment);

				array_push($newarray, array(
					"Date" => $data[$i]->Date,						//0
			        "Start" => $data[$i]->Start,					//1
			        "Finished" => $data[$i]->Finished,				//2
			        "Declaration" => $data[$i]->Declaration,		//3
			        "Type" => $data[$i]->Type,						//4
			        "Machine" => $data[$i]->Machine,				//5
			        "Total_time" => $data[$i]->Total_time,			//6
			        "Waiting_time" => $data[$i]->Waiting_time,		//7
			        "Repair_time" => $data[$i]->Repair_time,		//8
			        "Responsible" => $data[$i]->Responsible,		//9
			        "ModuleName" => $data[$i]->ModuleName,			//10
			        // "Solution" => $data[$i]->Solution,
			        // "DeclCod" => $data[$i]->DeclCod,
			        "MechComment" => "",							//11

		        ));
			}
		}
		// dd($newarray);
	}

	// dd($newarray);
	// print_r($newarray);

		return view('Mechanic.index', compact('newarray','mechanicid','mechanic'));
	}

	
}
