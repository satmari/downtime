@extends('app')

@section('content')

<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
				<div class="panel-heading">Add/Edit Downtime Category or Style</div>
				<br>
				
					{!! Form::open(['method'=>'POST', 'url'=>'/downtime_insert2']) !!}

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
						<p>Downtime Category: <span style="color:red;">*</span></p>
							{!! Form::select('bd_id', ['' => ''] + $category_data, $bd_category,['class' => 'form-control' , 'autofocus' => 'autofocus' ]) !!}
						</div>
						
						{{-- 
						<div class="panel-body">
						<p>Style: <span style="color:red;">*</span></p>
							<select name="style" class="form-control">
								<option value="" selected></option>
							@foreach ($style_data as $line)
								<option value="{{ $line->style }}" 
									{{ $style == $line->style ? 'selected="selected"' : '' }}

									>{{ $line->style }}
								</option>
							@endforeach
							</select>
						</div>
						--}}

						<div class="panel-body">
						<p>Style:  <span style="color:red;">*</span></p>
							{!! Form::text('style', $style, ['id' => 'style', 'class' => 'form-control']) !!}
						</div>
												
						{!! Form::submit('Add', ['class' => 'btn  btn-success center-block']) !!}

						@include('errors.list')

					{!! Form::close() !!}
				
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/inteoslogin2')}}" class="btn btn-default">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection