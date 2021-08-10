<!doctype html>
<html lang="sr">
<head>
    <title>@yield('title')</title>
    {{--<meta charset="UTF-8">--}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10pt;
        }

        table {
            margin: 0;
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

        #header {
            width: 10cm;
            /*float: left;*/
        }

        #predmet {
            margin-bottom: 10px;
        }

        #adresa {
            margin: 10px 0 10px 10cm;
            text-align: right;
            font-weight: bold;
        }

        #zakon, #dostavljam {
            text-align: justify;
        }

        #no-break {
            page-break-inside: avoid;
        }

        .section{
            margin-top: 2cm;
        }

        ol > li {
            margin-bottom: 5px;
        }

        #potpis {
            /*margin-top: 10px;*/
            position: relative;
            width: 18cm;
            /*bottom: 300px;*/
        }

/*        #potpis-abs {
            position: absolute;
            width: 18cm;
            bottom: 300px;
        }*/

        #napomena {
            width: 100%;
            border: 1px solid;
            /*position: absolute;*/
            margin-top: 50px;
            /*bottom: 40px;*/
            padding: 10px 25px;
            page-break-inside: avoid;
        }

        #napomenaText {
            font-size: 10pt;
            padding: 10px;
            text-align: justify;
        }

        .page-break {
            page-break-before: always;
        }

        /*        .logo-container{
                    width: 21cm;
                }*/
        .naslov {
            position: relative;
            top: 1cm;
        }

        .logo {
            position: relative;
            left: 8.5cm;
            top: -2cm;
            /*width: 21cm;*/
            margin: 1cm auto;
            /*margin-top: -1cm;*/
        }

        .footer {
            width: 100%;
            color: #999999;
            font-size: 8pt;
            text-align: center;
            position: absolute;
            bottom: -1cm;
            padding: 10px;
        }

        .pdf-first-column-width {
            width: 8cm !important;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
