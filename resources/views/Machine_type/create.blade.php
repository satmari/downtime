@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
				<div class="panel-heading">Add Machine Type</div>
				<br>

					{!! Form::open(['method'=>'POST', 'url'=>'/machine_type_insert']) !!}

						<div class="panel-body">
						<p>Machine Type CODE: <span style="color:red;">*</span></p>
							{!! Form::text('machine_code', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
						</div>
						<div class="panel-body">
						<p>Machine Type Description: </p>
							{!! Form::text('machine_desc', null, ['class' => 'form-control']) !!}
						</div>

						<div class="panel-body">
						<p>Machine Type Group: </p>
							{!! Form::text('machine_group', null, ['class' => 'form-control']) !!}
						</div>
						
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

					{!! Form::close() !!}
				
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/machine_type')}}" class="btn btn-default">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection