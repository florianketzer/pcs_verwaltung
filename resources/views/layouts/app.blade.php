<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="/css/bootstrapValidator/bootstrapValidator.min.css" rel="stylesheet">
        <link href="/css/jquery-ui/jquery-ui.min.css" rel="stylesheet">
        <link href="/css/jquery.fileupload/css/jquery.fileupload.css" rel="stylesheet">
        <link href="/css/timepicker/css/bootstrap-timepicker.css" rel="stylesheet">
        <link href="/css/main.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">

            <header class="container">
                <a href="/" class="logo"><img src="{{asset('images/pcs_logo_new.png')}}" alt="PCS"/></a>
                <ul class="header-menu">
                    @if(auth()->check())
                        <li>Sie sind eingeloggt als <u><a href="{{ route('profile.show') }}">{{auth()->user()->username}}</a></u></li>
                        @if(auth()->user()->usergroups->contains(3))
                            <li><a href="{{route('materials.index')}}">Materialliste</a></li>
                            <li><a href="{{route('importcsv')}}">CSV Import</a></li>
                            <li><a href="{{route('servicecontracts.index')}}">Serviceverträge</a></li>
                        @endif
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Anmelden</a></li>
                    @endif
                </ul>
            </header>

            @yield('content')
        </div>

        <script src="/js/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
        <script src="/js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="/js/jquery.mobile/jquery.mobile.custom.min.js" type="text/javascript"></script>
        <script src="/js/bootstrapValidator/bootstrapValidator.min.js" type="text/javascript"></script>
        <script src="/js/main.js" type="text/javascript"></script>
        <script src="/js/jquery.fileupload/js/jquery.iframe-transport.js" type="text/javascript"></script>
        <script src="/js/jquery.fileupload/js/jquery.fileupload.js" type="text/javascript"></script>
        <script src="/js/timepicker/js/bootstrap-timepicker.js" type="text/javascript"></script>
        <script src="/js/signature_pad/signature_pad.min.js" type="text/javascript"></script>
        <script src="/js/moment.js/moment-with-locales.min.js" type="text/javascript"></script>
        <script src="/js/arbeitsbericht.js" type="text/javascript"></script>
        <script src="/js/materialliste.js" type="text/javascript"></script>
    </body>
</html>
