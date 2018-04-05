@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
            <div class="panel panel-default">
				<div class="panel-heading">Downtime table over 30 min repairing time for <b>{{$mechanic}}</b> </div>
                <a href="afterlogin" class="btn btn-success btn-xs ">List of downtimes without comment</a>
                <a href="afterloginall" class="btn btn-info btn-xs ">List of ALL downtimes</a>
                <a href="clear_session_mechanic" class="btn btn-danger btn-xs ">Logout Mechanic</a>
				
				<div class="input-group"> <span class="input-group-addon">Filter</span>
                    <input id="filter" type="text" class="form-control" placeholder="Type here...">
                </div>

                <table class="table table-striped table-bordered" id="sort" 
                data-show-export="true"
                data-export-types="['excel']"
                >
                <!--
                data-show-export="true"
                data-export-types="['excel']"
                data-search="true"
                data-show-refresh="true"
                data-show-toggle="true"
                data-query-params="queryParams" 
                data-pagination="true"
                data-height="300"
                data-show-columns="true" 
                data-export-options='{
                         "fileName": "preparation_app", 
                         "worksheetName": "test1",         
                         "jspdf": {                  
                           "autotable": {
                             "styles": { "rowHeight": 20, "fontSize": 10 },
                             "headerStyles": { "fillColor": 255, "textColor": 0 },
                             "alternateRowStyles": { "fillColor": [60, 69, 79], "textColor": 255 }
                           }
                         }
                       }'
                -->
                    <thead>
                        <tr>
                            <!-- <th data-sortable="true">Id</th> -->
                            
                            <th>Date</th>
                            <th>Start</th>
                            <th>Fisnish</th>
                            <th>Declared</th>
                            <th>Module</th>
                            <th>Responsible</th>
                            <th>M Type</th>
                            <th>Machine</th>
                            <th>Tot time</th>
                            <th>Wait time</th>
                            <th>Repair time</th>

                            <th>Mechanic Comment</th>

                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="searchable">
                    

                    

                    @foreach ($newarray as $line)
                        <tr>

                            <td>{{ $line['Date'] }}</td>
                            <td>{{ $line['Start'] }}</td>
                            <td>{{ $line['Finished'] }}</td>
                            <td>{{ $line['Declaration'] }}</td>
                            <td>{{ $line['ModuleName'] }}</td>
                            <td>{{ $line['Responsible'] }}</td>
                            <td>{{ $line['Type'] }}</td>
                            <td>{{ $line['Machine'] }}</td>
                            <td>{{ $line['Total_time'] }}</td>
                            <td>{{ $line['Waiting_time'] }}</td>
                            <td>{{ $line['Repair_time'] }}</td>

                            <td>{{ $line['MechComment'] }}</td>

                            {{-- 
                            <td>
                                <a href="new_bd_comment/{{ $line['Date'].'_'.$line['Start'].'_'.$line['Finished'].'_'.$line['Declaration'].'_'.$line['Type'].'_'.$line['Machine'].'_'.$line['Total_time'].'_'.$line['Waiting_time'].'_'.$line['Repair_time'].'_'.$line['Responsible'].'_'.$line['ModuleName'].'_'.$line['MechComment']}}" class="btn btn-info btn-xs center-block">
                                Add/Edit</a>
                            </td>
                            --}}
                            <td>
                                <a href="new_bd_comment/{{ $line['Date'].'_'.$line['Start'].'_'.$line['Machine'] }}" class="btn btn-info btn-xs center-block">
                                Add/Edit</a>
                            </td>

                        </tr>
                        
                    @endforeach

                    </tbody>	
			</div>
		</div>
	</div>
</div>
@endsection