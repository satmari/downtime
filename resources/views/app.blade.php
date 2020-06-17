<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Downtime App</title>

	<!-- <link href="{{ asset('/css/app.css') }}" rel="stylesheet"> -->
	<!-- <link href="{{ asset('/css/css.css') }}" rel="stylesheet"> -->
	<!-- <link href="{{ asset('/css/custom.css') }}" rel="stylesheet"> -->


	<link href="{{ asset('/css/bootstrap.min.css') }}" rel='stylesheet' type='text/css'>
	<link href="{{ asset('/css/bootstrap-table.css') }}" rel='stylesheet' type='text/css'>
	<!-- <link href="{{ asset('/css/jquery.dataTables.min.css') }}" rel='stylesheet' type='text/css'> -->
	<link href="{{ asset('/css/jquery-ui.min.css') }}" rel='stylesheet' type='text/css'>
	<link href="{{ asset('/css/custom.css') }}" rel='stylesheet' type='text/css'>
	<link href="{{ asset('/css/app.css') }}" rel='stylesheet' type='text/css'>
	<link href="{{ asset('/css/choosen.css') }}" rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="http://172.27.161.171/preparation"><b>Preparation</b></a>
				<a class="navbar-brand" href="#">|</a>
				<a class="navbar-brand" href="http://172.27.161.171/trebovanje"><b>Trebovanje</b></a>
				<a class="navbar-brand" href="#">|</a>
				<a class="navbar-brand" href="http://172.27.161.171/downtime"><b>Downtime</b></a>
				<a class="navbar-brand" href="#">|</a>
				<a class="navbar-brand" href="http://172.27.161.171/cutting"><b>Cutting</b></a>
				<a class="navbar-brand" href="#">|</a>
				@if(Auth::check() && Auth::user()->level() == 4)
				<a class="navbar-brand" href="http://172.27.161.172/pdm"><span style="color:red;"><b>PDM</b></span></a></li>
				<a class="navbar-brand" href="">|</a>
				@endif

			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					{{--<li><a href="{{ url('/') }}">Home</a></li> --}}

					@if (Auth::guest())
					<li><a href="{{ url('/inteoslogin') }}">Mechanic Downtime</a></li>
					<li><a href="{{ url('/activity') }}">Mechanic Activity</a></li>
					@endif
					

					@if(Auth::check() && Auth::user()->level() == 4)
					<li><a href="{{ url('/inteoslogin2') }}">Lineleader Downtime</a></li>
					@endif

					@if(Auth::check() && Auth::user()->level() == 1)
					<li><a href="{{ url('/inteoslogin') }}">Mechanic Downtime</a></li>
					<li><a href="{{ url('/inteoslogin2') }}">Lineleader Downtime</a></li>
					<li><a href="{{ url('/bd_category') }}">BD Categories</a></li>
					<li><a href="{{ url('/machine_type') }}">Machine Types</a></li>
					<li><a href="{{ url('/bd_machine') }}">BD Category-Machine link</a></li>

					@endif
					
					
				</ul>
				 
				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="{{ url('/auth/login') }}">Module Login</a></li>
						{{--<li><a href="{{ url('/auth/register') }}">Register</a></li>--}}
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ url('/auth/logout') }}">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
				
			</div>
		</div>
	</nav>

	@yield('content')
<!-- Scripts -->
	
	<script src="{{ asset('/js/jquery.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/js/bootstrap-table.js') }}" type="text/javascript" ></script>
	<script src="{{ asset('/js/jquery-ui.min.js') }}" type="text/javascript" ></script>
	<!-- <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript" ></script>-->
	<!--<script src="{{ asset('/js/jquery.tablesorter.min.js') }}" type="text/javascript" ></script>-->
	<!--<script src="{{ asset('/js/custom.js') }}" type="text/javascript" ></script>-->
	<script src="{{ asset('/js/tableExport.js') }}" type="text/javascript" ></script>
	<!--<script src="{{ asset('/js/jspdf.plugin.autotable.js') }}" type="text/javascript" ></script>-->
	<!--<script src="{{ asset('/js/jspdf.min.js') }}" type="text/javascript" ></script>-->
	<script src="{{ asset('/js/FileSaver.min.js') }}" type="text/javascript" ></script>
	<script src="{{ asset('/js/bootstrap-table-export.js') }}" type="text/javascript" ></script>
	<script src="{{ asset('/js/choosen.js') }}" type="text/javascript" ></script>

	<script type="text/javascript">
	   $.ajaxSetup({
	       headers: {
	           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	       }
	   });
	</script>

<script type="text/javascript">
$(function() {
    	
	$('#style').autocomplete({
		minLength: 3,
		autoFocus: true,
		source: '{{ URL('getstyledata')}}'
	});
	$('#style_prev').autocomplete({
		minLength: 3,
		autoFocus: true,
		source: '{{ URL('getstyledata')}}'
	});
	// $('#module').autocomplete({
	// 	minLength: 1,
	// 	autoFocus: true,
	// 	source: '{{ URL('getmoduledata')}}'
	// });

	$('#filter').keyup(function () {

        var rex = new RegExp($(this).val(), 'i');
        $('.searchable tr').hide();
        $('.searchable tr').filter(function () {
            return rex.test($(this).text());
        }).show();
	});


	// $('#myTabs a').click(function (e) {
    // 		e.preventDefault()
    // 		$(this).tab('show')
	// });
	// $('#myTabs a:first').tab('show') // Select first tab

	$(function() {
    	$( "#datepicker" ).datepicker();
  	});
  	
	$('#sort').bootstrapTable({
    	
	});

	$(".chosen").chosen();


	//$('.table tr').each(function(){
  		
  		//$("td:contains('pending')").addClass('pending');
  		//$("td:contains('confirmed')").addClass('confirmed');
  		//$("td:contains('back')").addClass('back');
  		//$("td:contains('error')").addClass('error');
  		//$("td:contains('TEZENIS')").addClass('tezenis');

  		// $("td:contains('TEZENIS')").function() {
  		// 	$(this).index().addClass('tezenis');
  		// }
	//});

	// $('.days').each(function(){
	// 	var qty = $(this).html();
	// 	//console.log(qty);

	// 	if (qty < 7 ) {
	// 		$(this).addClass('zeleno');
	// 	} else if ((qty >= 7) && (qty <= 15)) {
	// 		$(this).addClass('zuto');
	// 	} else if (qty > 15 ) {	
	// 		$(this).addClass('crveno');
	// 	}
	// });


	// $('.status').each(function(){
	// 	var status = $(this).html();
	// 	//console.log(qty);

	// 	if (status == 'pending' ) {
	// 		$(this).addClass('pending');
	// 	} else if (status == 'confirmed') {
	// 		$(this).addClass('confirmed');
	// 	} else {	
	// 		$(this).addClass('back');
	// 	}
	// });

	// $('td').click(function() {
	//    	var myCol = $(this).index();
 	//    	var $tr = $(this).closest('tr');
 	//    	var myRow = $tr.index();

 	//    	console.log("col: "+myCol+" tr: "+$tr+" row:"+ myRow);
	// });

});
</script>
</body>
</html>
