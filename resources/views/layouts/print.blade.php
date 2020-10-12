<!doctype html>
<html lang="sr">
<head>
    <title>@yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            margin: 0.7cm;
            padding: 0;
            font-family: DejaVu Serif;
            font-size: 10pt;
            {{--background-image: url({{storage_path('app/public/images/licenca_sablon2.png') }});--}}
            /*background-repeat: no-repeat;*/
        }

        .container {
            text-align: center;
        }
        .report {
            text-align: left;
        }

        #nadnaslov{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 5.8cm;
        }

        #naslov{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 0;
        }

        #linija1{
            width: 11cm;
            margin-top: 2.6cm;
            margin-bottom: 0;
        }
        #vrstalicencenaslov{
            font-size: 12pt;
            margin-top: 0;
        }
        #linija2{
            width: 11cm;
            margin-top: 0.1cm;
        }
        #naosnovu{
            font-size: 8.5pt;
            margin-top: 0.4cm;
        }
        #mgsi{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 0.6cm;
        }
        #utvrdjuje{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 0.2cm;
        }
        #ime{
            font-weight: bold;
            font-size: 12pt;
            margin-top: 0.3cm;
        }
        #zvanje{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 0;
        }
        #imalac{
            font-weight: bold;
            font-size: 8.5pt;
            margin: 0 auto;
            margin-top: 0.5cm;
            width: 14.2cm;
        }
        #oblast{
            font-size: 8.5pt;
            margin-top: 0.5cm;
        }
        #podoblast{
            font-size: 8.5pt;
            margin-top: 0.3cm;
        }
        #licencatekst{
            font-size: 8.5pt;
            margin-top: 0.6cm;
        }
        #licencadatum{
            font-size: 8.5pt;
            margin-top: -0.1cm;
        }
        #licenca{
            font-size: 12pt;
            margin-top: 0;
        }
        table {
            margin: 0;
        }
        #potpis {
            margin: 0 auto;
            position: fixed;
            left: 0.65cm;
            bottom: 6.2cm;
            width: 14.2cm;
        }
        .potpis{
            text-align: center;
            font-size: 8.5pt;
            padding-top: 0.5cm;
        }
        .datum, .list{
            font-size: 8.5pt;
        }

        h2 {
            text-align: center;
            margin: 0 0 1cm;
        }
        h3 {
            text-align: center;
            margin: 1.5cm 0 0;
        }

        h4 {
            padding: 5px;
            margin-bottom: 0;
            background-color: #DCDCDC;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>