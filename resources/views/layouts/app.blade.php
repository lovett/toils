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
    <svg class="hidden">
        <defs>
            <symbol id="icon-blocked" viewBox="0 0 32 32">
                <title>blocked</title>
                <path d="M27.314 4.686c-3.022-3.022-7.040-4.686-11.314-4.686s-8.292 1.664-11.314 4.686c-3.022 3.022-4.686 7.040-4.686 11.314s1.664 8.292 4.686 11.314c3.022 3.022 7.040 4.686 11.314 4.686s8.292-1.664 11.314-4.686c3.022-3.022 4.686-7.040 4.686-11.314s-1.664-8.292-4.686-11.314zM28 16c0 2.588-0.824 4.987-2.222 6.949l-16.727-16.727c1.962-1.399 4.361-2.222 6.949-2.222 6.617 0 12 5.383 12 12zM4 16c0-2.588 0.824-4.987 2.222-6.949l16.727 16.727c-1.962 1.399-4.361 2.222-6.949 2.222-6.617 0-12-5.383-12-12z"></path>
            </symbol>
            <symbol id="icon-checkmark" viewBox="0 0 32 32">
                <title>checkmark</title>
                <path d="M27 4l-15 15-7-7-5 5 12 12 20-20z"></path>
            </symbol>
        </defs>
    </svg>
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
