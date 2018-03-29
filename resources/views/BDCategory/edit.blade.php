@extends('app')

@section('content')
<div class="container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Edit BD Category:</div>
				<br>

				<div class="alert alert-info" role="alert">Please insert comment without special characters \ / @ # $ % ^ * </div>
				
					{!! Form::model($bd_category , ['method' => 'POST', 'url' => 'bd_category/'.$bd_category->id /*, 'class' => 'form-inline'*/]) !!}

					<div class="panel-body">
						
						{!! Form::hidden('bd_id', $bd_category->bd_id, ['class' => 'form-control']) !!}
						
						<div class="panel-body">
						<p>BD Category RS: </p>
							{!! Form::input('string', 'bd_rs', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>BD Category EN: </p>
							{!! Form::input('string', 'bd_en', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>BD Category IT: </p>
							{!! Form::input('string', 'bd_it', null, ['class' => 'form-control']) !!}
						</div>
						

					<div class="panel-body">
						{!! Form::submit('Save', ['class' => 'btn btn-success center-block']) !!}
					</div>

					@include('errors.list')

					{!! Form::close() !!}
					<br>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/bd_category/delete/'.$bd_category->id]) !!}
					{!! Form::hidden('id', $bd_category->id, ['class' => 'form-control']) !!}
					{{--{!! Form::submit('Delete', ['class' => 'btn  btn-danger btn-xs center-block']) !!}--}}
					{!! Form::close() !!}
					
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/bd_category')}}" class="btn btn-default">Back</a>
					</div>
				</div>
					
			</div>
		</div>
	</div>
</div>

@endsection