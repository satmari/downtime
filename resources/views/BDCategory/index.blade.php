@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
            <div class="panel panel-default">
                <div class="panel-heading">BD Category Table</div>
                
                
                <div class="panel-body">
                    <div class="">
                        <a href="{{url('/bd_category_new')}}" class="btn btn-default btn-info">Add BD Category</a>
                    </div>
                </div>
                

                <div class="input-group"> <span class="input-group-addon">Filter</span>
                    <input id="filter" type="text" class="form-control" placeholder="Type here...">
                </div>

                <table class="table table-striped table-bordered" id="sort" 
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
                            <!-- <td>Id</td> -->
                            <td><b>BD Category Id</b></td>
                            <td><b>BD Category RS</b></td>
                            <td><b>BD Category EN</b></td>
                            <td><b>BD Category IT</b></td>
                            
                            <td></td>
                        </tr>
                    </thead>
                    <tbody class="searchable">
                    @foreach ($data as $req)
                        <tr>
                            {{--<td>{{ $req->id }}</td>--}}
                            <td>{{ $req->bd_id }}</td>
                            <td>{{ $req->bd_rs }}</td>
                            <td>{{ $req->bd_en }}</td>
                            <td>{{ $req->bd_it }}</td>
                            
                            
                            <td><a href="{{ url('/bd_category/edit/'.$req->id) }}" class="btn btn-info btn-xs center-block">Edit</a></td>
                            
                        </tr>
                    @endforeach
                    
                    </tbody>                
            </div>
        </div>
    </div>
</div>
@endsection