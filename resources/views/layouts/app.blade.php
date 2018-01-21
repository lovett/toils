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
            <symbol id="icon-bullhorn" viewBox="0 0 32 32">
            <title>bullhorn</title>
            <path d="M32 13.414c0-6.279-1.837-11.373-4.109-11.413 0.009-0 0.018-0.001 0.027-0.001h-2.592c0 0-6.088 4.573-14.851 6.367-0.268 1.415-0.438 3.102-0.438 5.047s0.171 3.631 0.438 5.047c8.763 1.794 14.851 6.367 14.851 6.367h2.592c-0.009 0-0.018-0.001-0.027-0.001 2.272-0.040 4.109-5.134 4.109-11.413zM27.026 23.102c-0.293 0-0.61-0.304-0.773-0.486-0.395-0.439-0.775-1.124-1.1-1.979-0.727-1.913-1.127-4.478-1.127-7.223s0.4-5.309 1.127-7.223c0.325-0.855 0.705-1.54 1.1-1.979 0.163-0.182 0.48-0.486 0.773-0.486s0.61 0.304 0.773 0.486c0.395 0.439 0.775 1.124 1.1 1.979 0.727 1.913 1.127 4.479 1.127 7.223s-0.4 5.309-1.127 7.223c-0.325 0.855-0.705 1.54-1.1 1.979-0.163 0.181-0.48 0.486-0.773 0.486zM7.869 13.414c0-1.623 0.119-3.201 0.345-4.659-1.48 0.205-2.779 0.323-4.386 0.323-2.096 0-2.096 0-2.096 0l-1.733 2.959v2.755l1.733 2.959c0 0 0 0 2.096 0 1.606 0 2.905 0.118 4.386 0.323-0.226-1.458-0.345-3.036-0.345-4.659zM11.505 20.068l-4-0.766 2.558 10.048c0.132 0.52 0.648 0.782 1.146 0.583l3.705-1.483c0.498-0.199 0.698-0.749 0.444-1.221l-3.853-7.161zM27.026 17.148c-0.113 0-0.235-0.117-0.298-0.187-0.152-0.169-0.299-0.433-0.424-0.763-0.28-0.738-0.434-1.726-0.434-2.784s0.154-2.046 0.434-2.784c0.125-0.33 0.272-0.593 0.424-0.763 0.063-0.070 0.185-0.187 0.298-0.187s0.235 0.117 0.298 0.187c0.152 0.169 0.299 0.433 0.424 0.763 0.28 0.737 0.434 1.726 0.434 2.784s-0.154 2.046-0.434 2.784c-0.125 0.33-0.272 0.593-0.424 0.763-0.063 0.070-0.185 0.187-0.298 0.187z"></path>
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
