@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-6 col-md-offset-3">
            <div class="panel panel-default">
				<div class="panel-heading">Add/edit Downtime</div>
				<br>

				<div class="alert alert-info" role="alert">Please insert comment without special characters \ / @ # $ % ^ * </div>

					{!! Form::open(['method'=>'POST', 'url'=>'/downtime_insert']) !!}

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


						<div class="panel-body">
						<p>Mechanic comment: <span style="color:red;">*</span></p>
							{!! Form::text('new_mech_comment', $mech_coment, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
						</div>
												
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

					{!! Form::close() !!}
				
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/inteoslogin')}}" class="btn btn-default">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection