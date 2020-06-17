@extends('app')

@section('content')
<div class="container container-table">
	<div class="row vertical-center-row">

			<div class="text-center col-md-6 col-md-offset-3">
				<div class="panel panel-default">
					<div class="panel-heading">Link machine to this <b>{{ $activity_type }}</b> activity &nbsp &nbsp &nbsp &nbsp 
					<a href="{{url('/afterlogin3')}}" class="btn btn-danger btn-xs">Go back</a> </div>

					<div class="panel-body">
						
							{!! Form::open(['method'=>'POST', 'url'=>'/add_machine']) !!}

							<div class="panel-body">

							<p>Scan / type Machine OS</p>
								{!! Form::text('machine', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
							</div>

							{!! Form::hidden('mechanic', $mechanic, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanicid', $mechanicid, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanic_plant', $mechanic_plant, ['class' => 'form-control']) !!}

							{!! Form::hidden('activity_id', $activity_id, ['class' => 'form-control']) !!}
							{!! Form::hidden('activity_type', $activity_type, ['class' => 'form-control']) !!}
							{!! Form::hidden('activity_status', $activity_status, ['class' => 'form-control']) !!}

							{!! Form::submit('---- Confirm ----', ['class' => 'btn btn-info center-block']) !!}

							<br>
							@include('errors.list')
							
							{!! Form::token() !!}
							{!! Form::close() !!}
					</div>
					{{-- 
					<div class="panel-footer">
						<a href="{{url('/afterlogin3')}}" class="btn btn-default center-block">Back</a>
					</div>
					--}}
				</div>
			</div>
				

		@if (!empty($machines))
			<div class="text-center col-md-8 col-md-offset-2">
				<div class="panel panel-default">

					<div class="panel-heading">Machines already asigned to this activity</div>
					<table class="table" style="font-size: large">
						
						@foreach ($machines as $line)
						
						<tr>
							<td><span >{{ $line->machine }}</span></td>
							<td><span >{{ substr($line->start_time, 0,-11) }}</span></td>
							<td><span >{{ $line->machine_brand }}</span></td>
							<td><span >{{ $line->machine_type }}</span></td>
							<td><span >{{ $line->machine_code }}</span></td>
							
							@if ($activity_type != 'SETTING')
							<td>
								<a href="{{url('/add_maintenance/'.$line->id.'  ')}}" class="btn btn-success center-block btn-xs"
							>Add Maintenance <b>({{ $line->c }})</b></a>
							</td>
							@endif

						</tr>
						
						@endforeach
						
					</table>
								
				</div>
			</div>
		@endif
		
	</div>
</div>
@endsection