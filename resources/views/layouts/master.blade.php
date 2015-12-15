<html>
    <head>
        <title>Toils :: {{ $page_title }}</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    </head>
    <body>
        <main class="container">
	    <header class="primary">
		<h1 class="text-center">{{ $page_title }}</h1>
	    </header>
            @yield('page_main')
        </main>
    </body>
</html>
