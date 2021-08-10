<!doctype html>
<html lang="sr">
<head>
    <title>Prijava SI - @yield('title')</title>
    {{--<meta charset="UTF-8">--}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        h3 {
            text-align: center;
            margin: 1cm 0 0;
        }

        h4 {
            padding: 5px;
            margin-bottom: 0;
            background-color: #DCDCDC;
        }
        .naslov{
            position: absolute;
            top: -0.5cm;
            z-index: 500;
            /*float: left;*/
            /*margin-top: -1cm;*/
        }

        .page-break {
            page-break-after: always;
        }
    </style>
        @yield('style')
</head>
<body>
<div class="naslov">@yield('title')</div>
<div class="container">
    @yield('content')
</div>
</body>
</html>