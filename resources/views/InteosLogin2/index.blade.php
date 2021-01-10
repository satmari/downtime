@extends('app')

@section('content')
<div class="container container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Login:</div>			
				<br>				
				{!! Form::open(['method'=>'POST', 'url'=>'/logincheck2']) !!}
								
				<div class="panel-body">
					<p>Lineleader PIN code (Inteos)</p>
					{{-- {!! Form::number('pin', null, ['id' => 'pin', 'class' => 'form-control', 'autofocus' => 'autofocus']) !!} --}}
					{!! Form::input('number', 'pin', null, ['id' => 'pin', 'class' => 'form-control', 'autofocus' => 'autofocus']) !!}
				</div>
				
				<div class="panel-body">
					{!! Form::submit('Confirm', ['class' => 'btn btn-success center-block']) !!}
				</div>

				@include('errors.list')
				
				{!! Form::token() !!}
				{!! Form::close() !!}

				{{--
				<hr>
				<div class="panel-body">
					<a href="{{url('/')}}" class="btn btn-default center-block">Back</a>
				</div>
				--}}
		
			</div>
		</div>
		
		<div class="text-center col-md-8 col-md-offset-2">
			<div class="panel panel-default">

				<table class="table" style="font-size: large">
					<tr>
						<td>Informacije:</td>
					</tr>
					
					<tr>
						<td><span style="color:red"><b>Sve probleme obavezno prijaviti IT sektoru.</b></span></td>
					</tr>
					<tr>
						<td><span style="color:green"><b>Zatvarajte tabove u browseru!</b></span></td>
					</tr>
					

					
				</table>
							
			</div>
		</div>
		
	</div>
</div>
@endsection