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

class InteosLogin2Controller extends Controller {	

	public function __construct()
	{
		$this->middleware('auth');
		// Session::set('leaderid', NULL);
	}

	public function index()
	{	
		//
		$leaderid = Session::get('leaderid');
		if (isset($leaderid)) {
			
			return Redirect::to('afterlogin2');
		}

		return view('InteosLogin2.index');
	}

	public function logincheck(Request $request)
	{
		$this->validate($request, ['pin'=>'required|min:4|max:5']);
		$forminput = $request->all(); 

		$pin = $forminput['pin'];
		// dd($pin);

		$inteosll = DB::connection('sqlsrv2')->select(DB::raw("SELECT Cod,Name FROM BdkCLZG.dbo.WEA_PersData WHERE Func = 23 AND FlgAct = 1 AND PinCode = '".$pin."'"));

		if (empty($inteosll)) {
			$msg = 'LineLeader with this PIN is not active';
		    return view('InteosLogin2.error',compact('msg'));
		
		} else {
			foreach ($inteosll as $row) {
				$leaderid = $row->Cod;
    			$leader = $row->Name;
    			Session::set('leaderid', $leaderid);
    			Session::set('leader', $leader);
    		}

   			if (Auth::check())
			{
			    $userId = Auth::user()->id;
			    $module = Auth::user()->name;
			} else {
				$msg = 'Modul is not autenticated';
				return view('InteosLogin2.error',compact('msg'));
			}
			$module_line = substr($module, 0, 1);
    		$module_name = substr($module, 1, 3);
    		
    		$module = $module_line." ".$module_name;

    		Session::set('module', $module);
    	}

    	$leaderid = Session::get('leaderid');
    	$leader = Session::get('leader');
    	$module = Session::get('module');
    	
		// dd($leader);
    	// dd($module);

		// return view('LineLeader.index', compact('leaderid','leader','module'));
		return Redirect::to('afterlogin2');
	}

	public function afterlogin()
	{

		$leaderid = Session::get('leaderid');
    	$leader = Session::get('leader');
    	$module = Session::get('module');

    	// dd($leaderid);

    	if ($leaderid == '130') {

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
				PERS.Name as LineLeader,
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
		      and MM.ModuleName = mdl.ModNam and mm.Machine = MCH.MachNum /* composite key to connect lines with waiting time to lines with repairing time*/
		      -- excluding lines with repairing time zero
		      and (datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])) <> 0

		      
		      and Date >= '2018-04-15'
		      --and MTP.MaCod <> 'CHANGE LAYOUT'
		      --and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
		     
		      and  ModuleName <> 'SAMPLE' and ModuleName <> 'Z 999'

		      order by ModuleName asc, Date desc ,Start desc

			"));
			// dd($data);

	} else {

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
				PERS.Name as LineLeader,
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
		      and MM.ModuleName = mdl.ModNam and mm.Machine = MCH.MachNum /* composite key to connect lines with waiting time to lines with repairing time*/
		      -- excluding lines with repairing time zero
		      and (datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])) <> 0

		      and Date >= '2018-04-15' 
		      --and MTP.MaCod <> 'CHANGE LAYOUT'
		      --and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
		      
		      and  ModuleName = '".$module."'
		     
		      order by Date desc ,Start desc

			"));
			// dd($data);

	}
	
	
	//Categories
	// $category_data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM d_b__categories ORDER BY id asc"));
	// dd($category_data);
	
	//Styles
	// $style_data = DB::connection('sqlsrv3')->select(DB::raw("SELECT * FROM styles ORDER BY id asc"));
	// dd($style_data);

	$newarray = [];
	Session::set('newarray', NULL);

	$newarray_all = [];
	Session::set('newarray_all', NULL);

	$newarray_with_values = [];
	Session::set('newarray_with_values', NULL);

	for ($i=0; $i < count($data) ; $i++) { 
		// dd($data[$i]);

		$key = $data[$i]->Date." ".$data[$i]->Start." ".$data[$i]->Machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT bd_category_id,bd_category,style,style_prev FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);

		if (empty($bd_data)) {
			// dd("empty - not found in local db");

			array_push($newarray, array(
		        "Date" => $data[$i]->Date,					//0
		        "Start" => $data[$i]->Start,				//1
		        "Finished" => $data[$i]->Finished,			//2
		        "Declaration" => $data[$i]->Declaration,	//3
		        "Type" => $data[$i]->Type,					//4
		        "Machine" => $data[$i]->Machine,			//5
		        "Total_time" => $data[$i]->Total_time,		//6
		        "Waiting_time" => $data[$i]->Waiting_time,	//7
		        "Repair_time" => $data[$i]->Repair_time,	//8
		        "Responsible" => $data[$i]->Responsible,	//9
		        "ModuleName" => $data[$i]->ModuleName,		//10
		        // "Solution" => $data[$i]->Solution,
		        // "DeclCod" => $data[$i]->DeclCod,
		        // "MechComment" => "",
		        "BD_Category_id" => "",						//11
		        "BD_Category" => "",						//12
		        "Style" => "",								//13
		        "Style_prev" => ""							//14

		    ));

		} else {
			// dd("not empty - found in local db");


			if (is_null($bd_data[0]->bd_category_id) OR is_null($bd_data[0]->style)) { 

				if (is_null($bd_data[0]->bd_category_id)) {
				$bd_category_id = "";
				} else {
					$bd_category_id = $bd_data[0]->bd_category_id;
				}

				if (is_null($bd_data[0]->bd_category)) {
					$bd_category = "";
				} else {
					$bd_category = $bd_data[0]->bd_category;
				}

				if (is_null($bd_data[0]->style)) {
					$style = "";
				} else {
					$style = $bd_data[0]->style;
				}

				if (is_null($bd_data[0]->style_prev)) {
					$style_prev = "";
				} else {
					$style_prev = $bd_data[0]->style_prev;
				}

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
		        // "MechComment" => $bd_data[0]->mechanic_comment,
		        "BD_Category_id" => $bd_category_id,			//11
	        	"BD_Category" => $bd_category,					//12
	        	"Style" => strtoupper($style),					//13
	        	"Style_prev" => strtoupper($style_prev)			//14

		        ));

			} else {

				if (is_null($bd_data[0]->bd_category_id)) {
				$bd_category_id = "";
				} else {
					$bd_category_id = $bd_data[0]->bd_category_id;
				}

				if (is_null($bd_data[0]->bd_category)) {
					$bd_category = "";
				} else {
					$bd_category = $bd_data[0]->bd_category;
				}

				if (is_null($bd_data[0]->style)) {
					$style = "";
				} else {
					$style = $bd_data[0]->style;
				}

				if (is_null($bd_data[0]->style_prev)) {
					$style_prev = "";
				} else {
					$style_prev = $bd_data[0]->style_prev;
				}

				array_push($newarray_with_values, array(
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
		        // "MechComment" => $bd_data[0]->mechanic_comment,
		        "BD_Category_id" => $bd_category_id,			//11
	   	      	"BD_Category" => $bd_category,					//12
	   		    "Style" => strtoupper($style),   				//13
	   		    "Style_prev" => strtoupper($style_prev)			//14

		        ));
			}
		}
		// dd($newarray);
	}

	// dd($newarray);
	$newarray_all = array_merge($newarray, $newarray_with_values);
	// dd($newarray_all);

	Session::set('newarray', $newarray);
	Session::set('newarray_with_values', $newarray_with_values);
	Session::set('newarray_all', $newarray_all);


	return view('LineLeader.index', compact('newarray','leaderid','leader','module'));
	}

	// ne koristi se vise
	public function new_bd_info(Request $request, $value) 
	{	
		$input = $request->all();
		// dd($value);

		// $newarray = Session::get('newarray');
		$newarray_all = Session::get('newarray_all');
		// dd($newarray_all);

		foreach ($newarray_all as $line => $l) {
			// dd($l['Date']);

			$key = $l['Date']."_".$l['Start']."_".$l['Machine'];
			// dd($key);

			if ($key == $value) {
				
				// dd($key." and ".$value);

				$date = $l['Date'];
				$start = $l['Start'];
				$finished = $l['Finished'];
				$decl = $l['Declaration'];
				$type = $l['Type'];
				$machine = $l['Machine'];
				$tot_time = $l['Total_time'];
				$wait_time = $l['Waiting_time'];
				$repair_time = $l['Repair_time'];
				$responsible = $l['Responsible'];
				$modulename = $l['ModuleName'];
				$bd_category_id = $l['BD_Category_id'];
				$bd_category = $l['BD_Category'];
				$style = strtoupper($l['Style']);
				$style_prev = strtoupper($l['Style_prev']);

			}
		}
		// dd($bd_category_id);

		//BD Categories
		$category_data = DB_Category::orderBy('bd_id')->lists('bd_rs','bd_id'); //pluck
		
		// $category_data = DB::connection('sqlsrv')->select(DB::raw("SELECT bd_id,bd_rs FROM b_d__machine_links WHERE machine_code = '".$type."' "));
		// dd($category_data[0]->bd_id);

		//Styles
		$style_data = DB::connection('sqlsrv3')->select(DB::raw("SELECT style FROM styles WHERE LEN(style) < 9 ORDER BY style asc"));
		// dd($style_data);

		if ($type == 'CHANGE LAYOUT') {
			// dd("machine type is CHANGE LAYOUT");

			return view('LineLeader.add_cs', compact('date','start','finished','decl','type','machine','tot_time','wait_time','repair_time','responsible','modulename','bd_category_id','bd_category','style','style_prev','category_data','style_data'));
		}

		return view('LineLeader.add', compact('date','start','finished','decl','type','machine','tot_time','wait_time','repair_time','responsible','modulename','bd_category_id','bd_category','style','category_data','style_data'));
	}

	public function new_bd_info_test(Request $request, $value)
	{	
		$input = $request->all();
		// dd($value);

		// $newarray = Session::get('newarray');
		$newarray_all = Session::get('newarray_all');
		// dd($newarray_all);

		foreach ($newarray_all as $line => $l) {
			// dd($l['Date']);

			$key = $l['Date']."_".$l['Start']."_".$l['Machine'];
			// dd($key);

			if ($key == $value) {
				
				// dd($key." and ".$value);

				$date = $l['Date'];
				$start = $l['Start'];
				$finished = $l['Finished'];
				$decl = $l['Declaration'];
				$type = $l['Type'];
				$machine = $l['Machine'];
				$tot_time = $l['Total_time'];
				$wait_time = $l['Waiting_time'];
				$repair_time = $l['Repair_time'];
				$responsible = $l['Responsible'];
				$modulename = $l['ModuleName'];
				$bd_category_id = $l['BD_Category_id'];
				$bd_category = $l['BD_Category'];
				$style = strtoupper($l['Style']);
				$style_prev = strtoupper($l['Style_prev']);

			}
		}
		// dd($bd_category_id);

		//BD Categories
		$category_data_o = DB_Category::orderBy('bd_id')->lists('bd_rs','bd_id'); //pluck
		
		// dd($type);
		$category_data = DB::connection('sqlsrv')->select(DB::raw("SELECT l.bd_id,c.bd_rs FROM [downtime].[dbo].[b_d__machine_links] as l 
			JOIN [downtime].[dbo].[d_b__categories] as c on l.bd_id = c.bd_id
			WHERE machine_code = '".$type."' "));
		// dd($category_data);
		// dd("test");

		//Styles
		$style_data = DB::connection('sqlsrv3')->select(DB::raw("SELECT style FROM styles WHERE LEN(style) < 9 ORDER BY style asc"));
		//dd($style_data);

		if ($type == 'CHANGE LAYOUT') {
			// dd("machine type is CHANGE LAYOUT");

			return view('LineLeader.add_cs', compact('date','start','finished','decl','type','machine','tot_time','wait_time','repair_time','responsible','modulename','bd_category_id','bd_category','style','style_prev','category_data','style_data'));
		}

		return view('LineLeader.add_test', compact('date','start','finished','decl','type','machine','tot_time','wait_time','repair_time','responsible','modulename','bd_category_id','bd_category','style','category_data','category_data_o','style_data'));
	}

	public function downtime_insert(Request $request)
	{
		$this->validate($request, ['bd_id'=>'required', 'style2'=>'required']);

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
		// $mech_coment = $input['new_mech_comment'];

		$bd_category_id = $input['bd_id'];
		// $bd_category_id = 'BD16';

		// $style = strtoupper($input['style']);
		$style2 = strtoupper($input['style2']);

		// $mechanicid = intval(Session::get('mechanicid'));
    	// $mechanic = Session::get('mechanic');
		
		$key = $date." ".$start." ".$machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);
		// dd($bd_data[0]->id);

		$date = date('Y-m-d',strtotime($date));

		$bd_category_list = DB::connection('sqlsrv')->select(DB::raw("SELECT bd_rs FROM d_b__categories WHERE bd_id = '".$bd_category_id."'"));
		// dd($bd_category_list[0]->bd_rs);
		$bd_category = $bd_category_list[0]->bd_rs;

		$leaderid = intval(Session::get('leaderid'));
    	$leader = Session::get('leader');
    	// $module = Session::get('module');
    	// dd($leaderid);

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

				// $table->mechanic_id = $mechanicid;
				// $table->mechanic = $mechanic;
				// $table->mechanic_comment = $mech_coment;

				$table->leader_id = $leaderid;
				$table->leader = $leader;
				$table->bd_category_id = $bd_category_id;
				$table->bd_category = $bd_category;
				$table->style = strtoupper($style2);
				
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

				// $table->mechanic_id = $mechanicid;
				// $table->mechanic = $mechanic;
				// $table->mechanic_comment = $mech_coment;

				$table->leader_id = $leaderid;
				$table->leader = $leader;
				$table->bd_category_id = $bd_category_id;
				$table->bd_category = $bd_category;
				$table->style = strtoupper($style2);
				
				$table->save();
			// }
			// catch (\Illuminate\Database\QueryException $e) {
			// 	return view('Mechanic.error');			
			// }
		}

		return Redirect::to('/inteoslogin2');
	}

	public function downtime_insert_cs(Request $request)
	{
		$this->validate($request, ['style2'=>'required', 'style_prev'=>'required']);

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
		// $mech_coment = $input['new_mech_comment'];

		// dd($input['bd_id']);
		if ($input['bd_id'] == "" OR $input['bd_id'] == NULL) {

			$bd_category_id = 'BD17'; // Promena modela/ Change Layout
				
		} else {

			$bd_category_id = $input['bd_id'];
		}

		
		// $style = strtoupper($input['style']);
		$style = strtoupper($input['style2']);
		$style_prev = strtoupper($input['style_prev']);

		// $mechanicid = intval(Session::get('mechanicid'));
    	// $mechanic = Session::get('mechanic');
		
		$key = $date." ".$start." ".$machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);
		// dd($bd_data[0]->id);

		$date = date('Y-m-d',strtotime($date));

		$bd_category_list = DB::connection('sqlsrv')->select(DB::raw("SELECT bd_rs FROM d_b__categories WHERE bd_id = '".$bd_category_id."'"));
		// dd($bd_category_list[0]->bd_rs);
		$bd_category = $bd_category_list[0]->bd_rs;

		$leaderid = intval(Session::get('leaderid'));
    	$leader = Session::get('leader');
    	// $module = Session::get('module');
    	// dd($leaderid);

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

				// $table->mechanic_id = $mechanicid;
				// $table->mechanic = $mechanic;
				// $table->mechanic_comment = $mech_coment;

				$table->leader_id = $leaderid;
				$table->leader = $leader;
				$table->bd_category_id = $bd_category_id;
				$table->bd_category = $bd_category;
				$table->style = strtoupper($style);
				$table->style_prev = strtoupper($style_prev);
				
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

				// $table->mechanic_id = $mechanicid;
				// $table->mechanic = $mechanic;
				// $table->mechanic_comment = $mech_coment;

				$table->leader_id = $leaderid;
				$table->leader = $leader;
				$table->bd_category_id = $bd_category_id;
				$table->bd_category = $bd_category;
				$table->style = strtoupper($style);
				$table->style_prev = strtoupper($style_prev);
				
				$table->save();
			// }
			// catch (\Illuminate\Database\QueryException $e) {
			// 	return view('Mechanic.error');			
			// }
		}

		return Redirect::to('/inteoslogin2');
	}

	public function clear_session_lineleader()
	{
		// $data = DB::connection('sqlsrv3')->table('styles')->select('style')->where('style','LIKE', $term.'%')->take(10)->get();
		// dd($data);

		Session::set('leaderid', NULL);
		// return view('InteosLogin2.index');
		return Redirect::to('inteoslogin2');
	}

	public function afterloginall()
	{

		$leaderid = Session::get('leaderid');
    	$leader = Session::get('leader');
    	$module = Session::get('module');

    	// dd($leaderid);

    if ($leaderid == '130') {

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
				PERS.Name as LineLeader,
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
		      and MM.ModuleName = mdl.ModNam and mm.Machine = MCH.MachNum /* composite key to connect lines with waiting time to lines with repairing time*/
		      -- excluding lines with repairing time zero
		      and (datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])) <> 0
		      
		      
		      and Date >= '2018-04-15' 
		      --and MTP.MaCod <> 'CHANGE LAYOUT'
		      --and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
		     
		      and  ModuleName <> 'SAMPLE' and ModuleName <> 'Z 999'

		      order by ModuleName asc, Date desc ,Start desc

			"));
			// dd($data);

	} else {

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
				PERS.Name as LineLeader,
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
		      and MM.ModuleName = mdl.ModNam and mm.Machine = MCH.MachNum /* composite key to connect lines with waiting time to lines with repairing time*/
		      -- excluding lines with repairing time zero
		      and (datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd])) <> 0
		      
		      
		      and Date >= '2018-04-15'
		      --and MTP.MaCod <> 'CHANGE LAYOUT'
		      --and cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))/60 as varchar(10)) + ':' + right('0' + cast((datediff(MINUTE,DL.[DeclSta],case when MM.DeclEnd IS null then DL.[DeclEnd] else MM.DeclEnd end) - datediff(MINUTE,DL.[DeclSta],DL.[DeclEnd]))%60 as varchar(2)),2) >= '0:30'
		      
		      and  ModuleName = '".$module."'
		     
		      order by Date desc ,Start desc

			"));
			// dd($data);

	}
	
	
	//Categories
	// $category_data = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM d_b__categories ORDER BY id asc"));
	// dd($category_data);
	
	//Styles
	// $style_data = DB::connection('sqlsrv3')->select(DB::raw("SELECT * FROM styles ORDER BY id asc"));
	// dd($style_data);

	$newarray = [];
	Session::set('newarray', NULL);

	$newarray_all = [];
	Session::set('newarray_all', NULL);

	$newarray_with_values = [];
	Session::set('newarray_with_values', NULL);

	for ($i=0; $i < count($data) ; $i++) { 
		// dd($data[$i]);

		$key = $data[$i]->Date." ".$data[$i]->Start." ".$data[$i]->Machine;

		$bd_data = DB::connection('sqlsrv')->select(DB::raw("SELECT bd_category_id,bd_category,style,style_prev FROM downtimes WHERE bd_key = '".$key."'"));
		// dd($bd_data);

		if (empty($bd_data)) {
			// dd("empty - not found in local db");

			array_push($newarray, array(
		        "Date" => $data[$i]->Date,					//0
		        "Start" => $data[$i]->Start,				//1
		        "Finished" => $data[$i]->Finished,			//2
		        "Declaration" => $data[$i]->Declaration,	//3
		        "Type" => $data[$i]->Type,					//4
		        "Machine" => $data[$i]->Machine,			//5
		        "Total_time" => $data[$i]->Total_time,		//6
		        "Waiting_time" => $data[$i]->Waiting_time,	//7
		        "Repair_time" => $data[$i]->Repair_time,	//8
		        "Responsible" => $data[$i]->Responsible,	//9
		        "ModuleName" => $data[$i]->ModuleName,		//10
		        // "Solution" => $data[$i]->Solution,
		        // "DeclCod" => $data[$i]->DeclCod,
		        // "MechComment" => "",
		        "BD_Category_id" => "",						//11
		        "BD_Category" => "",						//12
		        "Style" => "",								//13
		        "Style_prev" => ""							//14

		    ));

		} else {
			// dd("not empty - found in local db");


			if (is_null($bd_data[0]->bd_category_id) OR is_null($bd_data[0]->style)) { 

				if (is_null($bd_data[0]->bd_category_id)) {
				$bd_category_id = "";
				} else {
					$bd_category_id = $bd_data[0]->bd_category_id;
				}

				if (is_null($bd_data[0]->bd_category)) {
					$bd_category = "";
				} else {
					$bd_category = $bd_data[0]->bd_category;
				}

				if (is_null($bd_data[0]->style)) {
					$style = "";
				} else {
					$style = $bd_data[0]->style;
				}

				if (is_null($bd_data[0]->style_prev)) {
					$style_prev = "";
				} else {
					$style_prev = $bd_data[0]->style_prev;
				}

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
		        // "MechComment" => $bd_data[0]->mechanic_comment,
		        "BD_Category_id" => $bd_category_id,			//11
	        	"BD_Category" => $bd_category,					//12
	        	"Style" => strtoupper($style), 					//13
	        	"Style_prev" => strtoupper($style_prev)			//14

		        ));

			} else {

				if (is_null($bd_data[0]->bd_category_id)) {
				$bd_category_id = "";
				} else {
					$bd_category_id = $bd_data[0]->bd_category_id;
				}

				if (is_null($bd_data[0]->bd_category)) {
					$bd_category = "";
				} else {
					$bd_category = $bd_data[0]->bd_category;
				}

				if (is_null($bd_data[0]->style)) {
					$style = "";
				} else {
					$style = $bd_data[0]->style;
				}

				if (is_null($bd_data[0]->style_prev)) {
					$style_prev = "";
				} else {
					$style_prev = $bd_data[0]->style_prev;
				}

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
		        // "MechComment" => $bd_data[0]->mechanic_comment,
		        "BD_Category_id" => $bd_category_id,			//11
	   	      	"BD_Category" => $bd_category,					//12
   		     	"Style" => strtoupper($style), 					//13
   		     	"Style_prev" => strtoupper($style_prev)			//14
		        ));


			}
	
		}
		// dd($newarray);
	}
	// dd($newarray);

	// dd($newarray);
	$newarray_all = array_merge($newarray, $newarray_with_values);
	// dd($newarray_all);

	Session::set('newarray', $newarray);
	Session::set('newarray_with_values', $newarray_with_values);
	Session::set('newarray_all', $newarray_all);

	return view('LineLeader.index', compact('newarray','leaderid','leader','module'));
	}

}
