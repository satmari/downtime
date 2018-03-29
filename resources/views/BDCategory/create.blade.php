@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
				<div class="panel-heading">Add BD Category</div>
				<br>

					<div class="alert alert-info" role="alert">Please insert comment without special characters \ / @ # $ % ^ * </div>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/bd_category_insert']) !!}

						<div class="panel-body">
						<p>BD Category ID: <span style="color:red;">*</span></p>
							{!! Form::text('bd_id', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
						</div>
						<div class="panel-body">
						<p>BD Category RS: </p>
							{!! Form::text('bd_rs', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>BD Category EN: </p>
							{!! Form::text('bd_en', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>BD Category IT:</p>
							{!! Form::text('bd_it', null, ['class' => 'form-control']) !!}
						</div>
						
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

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