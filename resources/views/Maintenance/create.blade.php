@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
				<div class="panel-heading">Add new Maintenance</div>
				<br>

					<!-- <div class="alert alert-info" role="alert">Please insert comment without special characters \ / @ # $ % ^ * </div> -->
					
					{!! Form::open(['method'=>'POST', 'url'=>'/maintenance_insert']) !!}

						<div class="panel-body">
						<p>Sort: </p>
							{!! Form::text('sort', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
						</div>
						<div class="panel-body">
						<p>Maintenance RS: <span style="color:red;">*</span></p>
							{!! Form::text('maintenance', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Maintenance EN: </p>
							{!! Form::text('maintenance_en', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Maintenance IT:</p>
							{!! Form::text('maintenance_it', null, ['class' => 'form-control']) !!}
						</div>
						
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

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