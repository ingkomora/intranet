<!doctype html>
<html lang="sr">
<head>
    <title>@yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            /*color: orangered;*/
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

        #nadnaslov{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 5.5cm;
        }

        #naslov{
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 0;
        }

        #report-header {
            position: relative;
            margin: 0 auto;
            font-family: DejaVu Sans;
            display: block;
        }

        #report-content {
            position: relative;
            font-family: DejaVu Sans;
            font-size: 11pt;
            text-align: left;
            margin-top: 1cm;
        }

        #logo{

            /*height: 2cm;*/
            display: block;
            margin: 0 auto;
        }
        #logo img{
            height: 2cm;
        }

        #report-header #nadnaslov{
            font-weight: bold;
            font-size: 10pt;
            margin-top: 2cm;
        }

        #report-header #naslov{
            font-weight: bold;
            font-size: 14pt;
            margin-top: 0;
        }

        #report-header #podnaslov{
            font-weight: bold;
            font-size: 10pt;
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
        #vrstaposla{
            font-weight: bold;
            font-size: 8.5pt;
            margin: 0 auto;
            margin-top: -0.1cm;
            width: 14.2cm;
        }
        #oblast{
            font-size: 8.5pt;
            margin-top: 0.2cm;
        }
        #podoblast{
            font-size: 8.5pt;
            margin-top: 0.2cm;
        }

        #lic-container{
            margin: 0 auto;
            position: fixed;
            left: 1.2cm;
            bottom: 9cm;
        }

        #licencatekst{
            font-size: 8.5pt;
            margin-top: 0.3cm;
        }
        #resenje{
            font-size: 8.5pt;
            margin-top: 0;
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
            left: 1.2cm;
            bottom: 6.2cm;
            width: 14.2cm;
        }
        .potpis{
            text-align: center;
            font-size: 8.5pt;
            padding-top: 0.5cm;
        }
        .datum{
            padding-top: 0.7cm;
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
