<html>
    <head>
        <title>Toils :: {{ $page_title }}</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    </head>
    <body>
        <main class="container">
	    <header class="primary">
		<h1>{{ $page_title }}</h1>
		<nav>
		    @if (is_array($next_action))
		    <a href="{{ $next_action['link'] }}">{{ $next_action['label'] }}</a>
		    @endif
		</nav>
	    </header>
            @yield('page_main')
        </main>
    </body>
</html>
