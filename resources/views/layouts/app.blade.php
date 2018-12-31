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
<body>
    <svg class="d-none">
        <defs>
            <symbol id="icon-price-tag" viewBox="0 0 32 32">
            <title>price-tag</title>
            <path d="M30.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM23 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
            </symbol>
            <symbol id="icon-file-empty" viewBox="0 0 32 32">
            <title>file-empty</title>
            <path d="M28.681 7.159c-0.694-0.947-1.662-2.053-2.724-3.116s-2.169-2.030-3.116-2.724c-1.612-1.182-2.393-1.319-2.841-1.319h-15.5c-1.378 0-2.5 1.121-2.5 2.5v27c0 1.378 1.122 2.5 2.5 2.5h23c1.378 0 2.5-1.122 2.5-2.5v-19.5c0-0.448-0.137-1.23-1.319-2.841zM24.543 5.457c0.959 0.959 1.712 1.825 2.268 2.543h-4.811v-4.811c0.718 0.556 1.584 1.309 2.543 2.268zM28 29.5c0 0.271-0.229 0.5-0.5 0.5h-23c-0.271 0-0.5-0.229-0.5-0.5v-27c0-0.271 0.229-0.5 0.5-0.5 0 0 15.499-0 15.5 0v7c0 0.552 0.448 1 1 1h7v19.5z"></path>
            </symbol>
            <symbol id="icon-file-pdf" viewBox="0 0 32 32">
                <symbol id="icon-clock" viewBox="0 0 32 32">
                    <title>clock</title>
                    <path d="M20.586 23.414l-6.586-6.586v-8.828h4v7.172l5.414 5.414zM16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 28c-6.627 0-12-5.373-12-12s5.373-12 12-12c6.627 0 12 5.373 12 12s-5.373 12-12 12z"></path>
                </symbol>
                <title>file-pdf</title>
                <polygon fill="#fff" points="3.75,1 22,1 30,9 30,31 3.5,31" />
                <path fill="red" d="M26.313 18.421c-0.427-0.42-1.372-0.643-2.812-0.662-0.974-0.011-2.147 0.075-3.38 0.248-0.552-0.319-1.122-0.665-1.568-1.083-1.202-1.122-2.205-2.68-2.831-4.394 0.041-0.16 0.075-0.301 0.108-0.444 0 0 0.677-3.846 0.498-5.146-0.025-0.178-0.040-0.23-0.088-0.369l-0.059-0.151c-0.184-0.425-0.545-0.875-1.111-0.85l-0.341-0.011c-0.631 0-1.146 0.323-1.281 0.805-0.411 1.514 0.013 3.778 0.781 6.711l-0.197 0.478c-0.55 1.34-1.238 2.689-1.846 3.88l-0.079 0.155c-0.639 1.251-1.22 2.313-1.745 3.213l-0.543 0.287c-0.040 0.021-0.97 0.513-1.188 0.645-1.852 1.106-3.079 2.361-3.282 3.357-0.065 0.318-0.017 0.725 0.313 0.913l0.525 0.264c0.228 0.114 0.468 0.172 0.714 0.172 1.319 0 2.85-1.643 4.959-5.324 2.435-0.793 5.208-1.452 7.638-1.815 1.852 1.043 4.129 1.767 5.567 1.767 0.255 0 0.475-0.024 0.654-0.072 0.276-0.073 0.508-0.23 0.65-0.444 0.279-0.42 0.335-0.998 0.26-1.59-0.023-0.176-0.163-0.393-0.315-0.541zM6.614 25.439c0.241-0.658 1.192-1.958 2.6-3.111 0.088-0.072 0.306-0.276 0.506-0.466-1.472 2.348-2.458 3.283-3.106 3.577zM14.951 6.24c0.424 0 0.665 1.069 0.685 2.070s-0.214 1.705-0.505 2.225c-0.241-0.77-0.357-1.984-0.357-2.778 0 0-0.018-1.517 0.177-1.517v0zM12.464 19.922c0.295-0.529 0.603-1.086 0.917-1.677 0.765-1.447 1.249-2.58 1.609-3.511 0.716 1.303 1.608 2.41 2.656 3.297 0.131 0.111 0.269 0.222 0.415 0.333-2.132 0.422-3.974 0.935-5.596 1.558v0zM25.903 19.802c-0.13 0.081-0.502 0.128-0.741 0.128-0.772 0-1.727-0.353-3.066-0.927 0.515-0.038 0.986-0.057 1.409-0.057 0.774 0 1.004-0.003 1.761 0.19s0.767 0.585 0.637 0.667v0z"></path>
                <path fill="#666" d="M28.681 7.159c-0.694-0.947-1.662-2.053-2.724-3.116s-2.169-2.030-3.116-2.724c-1.612-1.182-2.393-1.319-2.841-1.319h-15.5c-1.378 0-2.5 1.121-2.5 2.5v27c0 1.378 1.121 2.5 2.5 2.5h23c1.378 0 2.5-1.122 2.5-2.5v-19.5c0-0.448-0.137-1.23-1.319-2.841v0zM24.543 5.457c0.959 0.959 1.712 1.825 2.268 2.543h-4.811v-4.811c0.718 0.556 1.584 1.309 2.543 2.268v0zM28 29.5c0 0.271-0.229 0.5-0.5 0.5h-23c-0.271 0-0.5-0.229-0.5-0.5v-27c0-0.271 0.229-0.5 0.5-0.5 0 0 15.499-0 15.5 0v7c0 0.552 0.448 1 1 1h7v19.5z"></path>
            </symbol>
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
    <nav class="navbar sticky-top navbar-expand-lg bg-white border-bottom mb-5">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name') }}
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <ul class="navbar-nav ml-auto">
                @guest
                {!! LinkHelper::primaryNavLink('login', 'Login') !!}
                {!! LinkHelper::primaryNavLink('register', 'Registration') !!}
                    @else
                {!! LinkHelper::primaryNavLink('dashboard', 'Dashboard')  !!}
                {!! LinkHelper::primaryNavLink('time.index', 'Time') !!}
                {!! LinkHelper::primaryNavLink('invoice.index', 'Invoices') !!}
                {!! LinkHelper::primaryNavLink('project.index', 'Projects') !!}
                {!! LinkHelper::primaryNavLink('client.index', 'Clients') !!}
                {!! LinkHelper::primaryNavLink('estimate.index', 'Estimates') !!}
                <li class="nav-item">
                    <a href="{{ route('logout') }}"
                       class="nav-link"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
                @endguest
            </ul>
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
        <div class="container mb-4">
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
