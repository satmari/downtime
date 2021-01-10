@extends('app')

@section('content')
<div class="container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Edit maintenance:</div>
				<br>

				<!-- <div class="alert alert-info" role="alert">Please insert comment without special characters \ / @ # $ % ^ * </div> -->
				
					{!! Form::model($maintenance , ['method' => 'POST', 'url' => 'maintenance/'.$maintenance->id /*, 'class' => 'form-inline'*/]) !!}

					<div class="panel-body">
						
						{!! Form::hidden('id', $maintenance->id, ['class' => 'form-control']) !!}
						
						<div class="panel-body">
						<p>Sort: </p>
							{!! Form::input('string', 'sort', null, ['class' => 'form-control']) !!}
						</div>

						<div class="panel-body">
						<p>Maintenance RS: </p>
							{!! Form::input('string', 'maintenance', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Maintenance EN: </p>
							{!! Form::input('string', 'maintenance_en', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Maintenance IT: </p>
							{!! Form::input('string', 'maintenance_it', null, ['class' => 'form-control']) !!}
						</div>
						

					<div class="panel-body">
						{!! Form::submit('Save', ['class' => 'btn btn-success center-block']) !!}
					</div>

					@include('errors.list')

					{!! Form::close() !!}
					<br>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/maintenance/delete/'.$maintenance->id]) !!}
					{!! Form::hidden('id', $maintenance->id, ['class' => 'form-control']) !!}
					{!! Form::submit('Delete', ['class' => 'btn  btn-danger btn-xs center-block']) !!}
					{!! Form::close() !!}
					
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/maintenance')}}" class="btn btn-default">Back</a>
					</div>
				</div>
					
			</div>
		</div>
	</div>
</div>

@endsection