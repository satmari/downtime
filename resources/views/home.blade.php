@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">

					<p>For Modules: Please login on top right menu.</p>
					<p>For Mechanics: Choose Mechanic Downtime on top menu.</p>

				</div>
			</div>
		</div>

		<div class="text-center col-md-8 col-md-offset-2">
			<div class="panel panel-default">

				<table class="table" style="font-size: large">
					<tr>
						<td>Informacije:</td>
					</tr>
					{{-- 
					<tr>
						<td><span style="color:blue"><b>Aplikacija Downtime pocinje sa radom od 01.04.2018. za sve informacije obratite se normircima</b></span></td>
					</tr>
					--}}
					<tr>
						<td><span style="color:red"><b>Sve probleme obavezno prijaviti IT sektoru.</b></span></td>
					</tr>
					<tr>
						<td><span style="color:green"><b>Zatvarajte tabove u browseru!</b></span></td>
					</tr>
					<tr>
						<td><span style="color:darkorchid">Da li imate predlog za pozadinsku sliku aplikacije?</span></td>
					</tr>
					
				</table>
							
			</div>
		</div>
	</div>
</div>
@endsection
