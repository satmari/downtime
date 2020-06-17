@extends('app')

@section('content')
<div class="container container-table">
	<div class="row vertical-center-row">
			<div class="text-center col-md-6 col-md-offset-3">
				<div class="panel panel-default">
					<div class="panel-heading">Add acivity (mechanic: <b>{{ $mechanic }}</b>) &nbsp &nbsp &nbsp &nbsp 

						{{--<a href="{{url('/clear_session_mechanic2')}}" class="btn btn-danger btn-xs">Logout mechanic</a>--}}

					</div>
					<br>


					<div class="panel-body">
						
							{{--<a href="{{url('/maintanance/')}}" class="btn btn-info center-block">Maintenance</a>--}}

							{!! Form::open(['method'=>'POST', 'url'=>'/maintenance']) !!}
							{!! Form::hidden('mechanic', $mechanic, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanicid', $mechanicid, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanic_plant', $mechanic_plant, ['class' => 'form-control']) !!}
							{!! Form::submit('---- Maintenance ----', ['class' => 'btn btn-info center-block']) !!}
							{!! Form::close() !!}

						
					</div>
					<div class="panel-body">

							{{--<a href="{{url('/setting/')}}" class="btn btn-info center-block">Setting</a>--}}


							{!! Form::open(['method'=>'POST', 'url'=>'/setting']) !!}
							{!! Form::hidden('mechanic', $mechanic, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanicid', $mechanicid, ['class' => 'form-control']) !!}
							{!! Form::hidden('mechanic_plant', $mechanic_plant, ['class' => 'form-control']) !!}
							{!! Form::submit('--------  Setting --------', ['class' => 'btn btn-primary center-block']) !!}
							{!! Form::close() !!}


						
					</div>
					<br>
					
					
				</div>
			</div>

		
	</div>
</div>
@endsection