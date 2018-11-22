@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
				<div class="panel-heading">Add BD Category and Machine link</div>
				<br>

					{!! Form::open(['method'=>'POST', 'url'=>'/bd_machine_insert']) !!}

						<div class="panel-body">
						<p>Machine Type: <span style="color:red;">*</span></p>
							{!! Form::select('machine_code', ['' => ''] + $machine_data, null,['class' => 'form-control' , 'autofocus' => 'autofocus' ]) !!}
						</div>
						
						<div class="panel-body">
						<p>Downtime Category: <span style="color:red;">*</span></p>
							{!! Form::select('bd_id', ['' => ''] + $category_data, null,['class' => 'form-control' , 'autofocus' => 'autofocus' ]) !!}
						</div>
						
						
						{!! Form::submit('Link', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

					{!! Form::close() !!}
				
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/bd_machine')}}" class="btn btn-default">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection