<html>
    <head>
        <title>Toils :: {{ $page_title }}</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2-bootstrap.min.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    </head>
    <body class="{{ $app_section or '' }}">
        <main class="container">
            <header class="primary">
                <h1>{{ $page_title }}</h1>
		<div class="row">
                    <nav class="col-sm-6">
                        @yield('nav_primary')
                    </nav>
                    <nav class="col-sm-6 text-right">
			@section('nav_supplemental')
			    <ul class="list-inline">
				<li>{!! link_to_route('dashboard', 'Dashboard') !!}</li>
				<li>{!! link_to_route('time.index', 'Time') !!}</li>
				<li>{!! link_to_route('projects', 'Projects') !!}</li>
				<li>{!! link_to_route('clients', 'Clients') !!}</li>
                                <li>{!! link_to_route('logout', 'Log out') !!}</li>
			    </ul>
			@show
                    </nav>
                </div>
            </header>

	    @if (Session::has('userMessage'))
	    <div class="alert alert-{{ Session::get('userMessage')[0] }} warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		{{ Session::get('userMessage')[1] }}
	    </div>
	    @endif

            @yield('page_main')
        </main>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script type="text/javascript">
            $('select').select2({'theme': 'bootstrap'});
        </script>

	@yield('page_scripts')
    </body>
</html>
