@extends('app')

@section('content')
<div class="container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Edit Machine type:</div>
				<br>

				{!! Form::model($machine_type , ['method' => 'POST', 'url' => 'machine_type/'.$machine_type->id /*, 'class' => 'form-inline'*/]) !!}

					<div class="panel-body">
						
						{!! Form::hidden('machine_code', $machine_type->machine_code, ['class' => 'form-control']) !!}
						
						<div class="panel-body">
						<p>Machine Description: </p>
							{!! Form::input('string', 'machine_desc', null, ['class' => 'form-control']) !!}
						</div>
						
					<div class="panel-body">
						{!! Form::submit('Save', ['class' => 'btn btn-success center-block']) !!}
					</div>

					@include('errors.list')

					{!! Form::close() !!}
					<br>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/machine_type/delete/'.$machine_type->id]) !!}
					{!! Form::hidden('id', $machine_type->id, ['class' => 'form-control']) !!}
					{{--{!! Form::submit('Delete', ['class' => 'btn  btn-danger btn-xs center-block']) !!}--}}
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