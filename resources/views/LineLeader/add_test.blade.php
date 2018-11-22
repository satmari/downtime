@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center  col-md-12 col-md-offset-0">
        <!-- <div class="text-center col-md-8 col-md-offset-2"> -->
            <div class="panel panel-default">
				<div class="panel-heading">Add/Edit Downtime Category or Style</div>
				<br>
				
					{!! Form::open(['method'=>'POST', 'url'=>'/downtime_insert2_test']) !!}

						{!! Form::hidden('date', $date, ['class' => 'form-control']) !!}
						{!! Form::hidden('start', $start, ['class' => 'form-control']) !!}
						{!! Form::hidden('finished', $finished, ['class' => 'form-control']) !!}
						{!! Form::hidden('decl', $decl, ['class' => 'form-control']) !!}
						{!! Form::hidden('type', $type, ['class' => 'form-control']) !!}
						{!! Form::hidden('machine', $machine, ['class' => 'form-control']) !!}
						{!! Form::hidden('tot_time', $tot_time, ['class' => 'form-control']) !!}
						{!! Form::hidden('wait_time', $wait_time, ['class' => 'form-control']) !!}
						{!! Form::hidden('repair_time', $repair_time, ['class' => 'form-control']) !!}
						{!! Form::hidden('responsible', $responsible, ['class' => 'form-control']) !!}
						{!! Form::hidden('modulename', $modulename, ['class' => 'form-control']) !!}


						{{-- 
						<div class="panel-body">
						<p>Downtime Category: <span style="color:red;">*</span></p>
							{!! Form::select('bd_id', ['' => ''] + $category_data_o, $bd_category_id,['class' => 'form-control' , 'autofocus' => 'autofocus' ]) !!}
						</div>
						--}}
						

						{{--
						<div class="panel-body">
						<p>Downtime Category: <span style="color:red;">*</span></p>
							<select name="bd_id" class="form-control">
								<option value="" selected></option>
							@foreach ($category_data as $line)
								<option value="{{ $line->bd_id }}" 
									>{{ $line->bd_rs }}
								</option>
							@endforeach
							</select>
						</div>
						--}}

{{--TEST--}}
                          <!-- <div class="row"> -->
                            <!-- <br><br> -->
                                <div class="panel-body visina-panel">
                                  <p>Downtime Category: <span style="color:red;">*</span></p>
                                  @foreach ($category_data as $type)
                                    
                                    @if ($type->bd_id == $bd_category_id) 
                                    	<div class="col-md-2 visina-basic">
                                    		<div class="visina_text"><b>{{ $type->bd_rs }}</b></div>
	                                      	{!! Form::radio('bd_id', $type->bd_id, $bd_category_id, ['id' => 'check', 'class' => '']); !!}
                                            <br>
                                        </div>

                                    @else
                                      	<div class="col-md-2 visina-basic">
                                      		<div class="visina_text"><b>{{ $type->bd_rs }}</b></div>
		                                    {!! Form::radio('bd_id', $type->bd_id, NULL, ['id' => 'check', 'class' => '']); !!}
		                                    <br>
	                                    </div>
                                    @endif
                                    	
                                  @endforeach
                                </div>
                            <!-- </div> -->                        
{{--TEST--}}


						
						<div class="panel-body">
						<p>Style: <span style="color:red;">*</span></p>
							<select name="style2" class="chosen">
								<option value="" selected></option>
							@foreach ($style_data as $line)
								<option value="{{ $line->style }}" 
									{{ $style == $line->style ? 'selected="selected"' : '' }}

									>{{ $line->style }}
								</option>
							@endforeach
							</select>
						</div>
						
						{{-- 
						<div class="panel-body">
						<p>Style:  <span style="color:red;">*</span></p>
							{!! Form::text('style', $style, ['id' => 'style', 'class' => 'form-control']) !!}
						</div>
						--}}
												
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

					{!! Form::close() !!}
				
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/inteoslogin2')}}" class="btn btn-default">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection