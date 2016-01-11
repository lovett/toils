<html>
    <head>
        <title>Toils :: {{ $page_title }}</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    </head>
    <body class="{{ $app_section or '' }}">
        <main class="container">
	    <header class="primary">
		<h1>{{ $page_title }}</h1>
		<nav class="row">
		    <div class="col-sm-6">
			@yield('nav_primary')
		    </div>
		    <div class="col-sm-6 text-right">
			@yield('nav_supplemental')
		    </div>
		</nav>
	    </header>
            @yield('page_main')
        </main>
    </body>
</html>
