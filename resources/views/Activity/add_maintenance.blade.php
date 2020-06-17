@extends('app')

@section('content')
<div class="container container-table">
	<div class="row vertical-center-row">

		<div class="text-center col-md-8 col-md-offset-2">
			<div class="panel panel-default">

				

				<div class="panel-heading">Choose maintenance tasks:</div> 

				{!! Form::open(['method'=>'POST', 'url'=>'add_maintenance_confirm']) !!}
				<meta name="csrf-token" content="{{ csrf_token() }}" />

				<table class="table" style="font-size: large">
					
				{!! Form::hidden('machine_id', $machine_id, ['class' => 'form-control']) !!}
				{!! Form::hidden('activity_id', $activity_id, ['class' => 'form-control']) !!}

					@foreach ($maintenance_checklist as $line)
					
					
							<div class="checkbox">
								<label style="width: 90%;" type="button" class="btn check btn-default"  data-color="primary">

									<input type="checkbox" class="btn check btn-lg" name="maintenance_code[]" value="{{ $line->id.'#'.$line->maintenance }}"

									@if (isset($maintenance_machine_check))
									@foreach ($maintenance_machine_check as $l)
									
										@if ($l->maintenance_id == $line->id)
										checked
										@endif

									@endforeach
									@endif
									>  
								    <input name="hidden[]" type='hidden' value="{{ $line->id.'#'.$line->maintenance }}"> 

								    {{ $line->maintenance }} &nbsp 
								    {{--{{ $line->maintenance_en }} &nbsp --}}
								    ({{ $line->maintenance_it }})
						
								</label>
							</div>
					
					@endforeach

				</table>

				<div class="panel-body">
					{!! Form::submit('Confirm', ['class' => 'btn btn-success center-block']) !!}
				</div>

				@include('errors.list')
				{!! Form::close() !!}
							
			</div>
		</div>

	</div>
</div>
@endsection