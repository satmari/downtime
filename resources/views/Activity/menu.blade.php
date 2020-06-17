@extends('app')

@section('content')
<div class="container container-table">
	<div class="row vertical-center-row">
			<div class="text-center col-md-6 col-md-offset-3">
				<div class="panel panel-default">
					<div class="panel-heading">Activity menu (mechanic: <b>{{ $mechanic }}</b>) &nbsp &nbsp &nbsp &nbsp 

						<a href="{{url('/clear_session_mechanic2')}}" class="btn btn-danger btn-xs">Logout mechanic</a>

					</div>

					<br>
					<div class="panel-body">
						<div class="">
							<a href="{{url('/add_activity')}}" class="btn btn-info center-block"
							

							@foreach ($activities as $line)

								@if ($line->status == 'OPEN')
								disabled
								@endif

							@endforeach


							>Add Activity</a>
						</div>
					</div>
					<div class="panel-body">
						<div class="">
							<a href="{{url('/stop_activity')}}" class="btn btn-info center-block"
							@if (empty($activities))
							disabled
							@endif
							>Stop Activity (with status Open)</a>
						</div>
					</div>
					
					<!-- <hr>
					<div class="panel-body">
						<div class="">
							<a href="{{url('/add_machine')}}" class="btn btn-success center-block"
							@if (empty($activities))
							disabled
							@endif

							@if (!empty($activities))
								@if ($activities[0]->activity_type == 'SETTING')
									disabled
								@endif
							@endif	


							>Add Machine</a>
						</div>
					</div> -->
				

					@if (($mechanicid == '89') OR ($mechanicid == '3217'))
					<hr>
					<div class="panel-body">
						<div class="">
							<a href="{{url('/maintenance')}}" class="btn btn-primary center-block">Maintenance checklist</a>
						</div>
					</div>
					<div class="panel-body">
						<div class="">
							<a href="{{url('/activity_table')}}" class="btn btn-primary center-block">Activity table (open acivity for all mechanics)</a>
						</div>
					</div>
					@endif
					
					
				</div>
			</div>
		
		@if (!empty($activities) )
		<div class="text-center col-md-8 col-md-offset-2">
			<div class="panel panel-default">


				<div class="panel-heading">Open <b>activities</b> for mechanic <b>{{ $mechanic }}</b> :</div>
				<table class="table" style="font-size: large">
					

					<tr>
						<td style="color:gray">Date</td>
						<td style="color:gray">Time</td>
						<td style="color:gray">Type</td>
						<td style="color:gray">Status</td>

					</tr>
					@foreach ($activities as $line)
					
					<tr>
						<td><span style="">{{ $line->date }}</span></td>
						<td><span style="">{{ substr($line->start_time, 0,-11) }}</span></td>
						<td><span style="green"><b>{{ $line->activity_type }}</b></span></td>
						<td>
							@if ($line->status == 'OPEN')
							<span style="color:red"><span></span>{{ $line->status}}</span>
							@endif
							@if ($line->status == 'PENDING')
							<span style="color:orange"><span></span>{{ $line->status}}</span>
							@endif

						</td>
						{{-- 
						<td>
							<a href="{{url('/add_machine')}}" class="btn btn-success center-block btn-xs"
							@if (empty($activities))
							disabled
							@endif

							@if (!empty($activities))
								@if ($activities[0]->activity_type == 'SETTING')
									disabled
								@endif
							@endif	


							>Edit Activity</a>
						</td>
						--}}
						<td>
							<a href="{{url('/add_machine_id/'.$line->id.' ')}}" class="btn btn-success center-block btn-xs"
							{{-- 
							@if (!empty($activities))
								@if ($line->activity_type == 'SETTING')
									disabled
								@endif
							@endif
							--}}

							>Edit Activity</a>
						</td>
						{{-- 
						<td>
							<a href="{{url('/stop_activity_id/'.$line->id.'  ')}}" class="btn btn-success center-block btn-xs"
							>Stop Activity</a>
						</td>
						--}}
					</tr>
					
					@endforeach
					
				</table>
							
			</div>
		</div>
		@endif

		

	</div>
</div>
@endsection