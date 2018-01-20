<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} :: {{ $pageTitle }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body class="{{ $appSection or '' }}">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Brand -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    @guest
                    {!! LinkHelper::primaryNavLink('login', 'Login') !!}
                    {!! LinkHelper::primaryNavLink('register', 'Registration') !!}
                    @else
                    {!! LinkHelper::primaryNavLink('dashboard', 'Dashboard')  !!}
                    {!! LinkHelper::primaryNavLink('time.index', 'Time') !!}
                    {!! LinkHelper::primaryNavLink('invoice.index', 'Invoices') !!}
                    {!! LinkHelper::primaryNavLink('project.index', 'Projects') !!}
                    {!! LinkHelper::primaryNavLink('client.index', 'Clients') !!}
                    <li>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @if (Session::has('userMessage'))
        <div class="container">
            <div class="alert alert-{{ Session::get('userMessageType') }} alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ Session::get('userMessage') }}
            </div>
        </div>
    @endif

    <main id="app">
        @if (LinkHelper::showSubnav())
        <div class="container">
            <ul class="nav nav-tabs">
                @foreach (LinkHelper::getSubnav() as $link)
                    {!! $link !!}
                @endforeach
                @yield('subnav_supplemental')
            </ul>
        </div>
        @endif

        @yield('page_main')

    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    @yield('page_scripts')

</body>
</html>
