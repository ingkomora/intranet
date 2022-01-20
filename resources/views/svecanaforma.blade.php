@extends('layouts.printsf')

@section('title', 'Svecana forma')

@section('content')
    <div>

        <div id="nadnaslov">Република Србија</div>
        <div id="naslov">МИНИСТАРСТВО ГРАЂЕВИНАРСТВА, САОБРАЋАЈА И ИНФРАСТРУКТУРЕ</div>
        <hr id="linija1">
        <div id="vrstalicencenaslov">ЛИЦЕНЦА ЗА {{ $vrstaLicenceNaslov }}</div>
        <hr id="linija2">
        <div id="naosnovu">На основу члана 162. Закона о планирању и изградњи</div>
        <div id="mgsi">МИНИСТАРСТВО ГРАЂЕВИНАРСТВА, САОБРАЋАЈА И ИНФРАСТРУКТУРЕ</div>
        <div id="utvrdjuje">утврђује да је</div>
        <div id="ime">{{$osobaImeRPrezime}}</div>
        <div id="zvanje">{{$zvanje}}</div>
        @if(!empty($nazivLicence))
            <div id="imalac">{{$nazivLicence}}</div>
        @else
            @if(!empty($vrstaPoslaGen))
                <div id="imalac">{{$vrstaLicence}}</div>
                <div id="vrstaposla">{{$vrstaPoslaGen}} из</div>
            @else
                <div id="imalac">{{$vrstaLicence}} из</div>
            @endif
            <div id="oblast"><strong>СТРУЧНЕ ОБЛАСТИ</strong> <br> {{$strucnaOblast}}</div>
            @if(!in_array($uzaStrucnaOblastId, [1, 2, 4, 21, 29]) AND $podOblastVisible)
                <div id="podoblast"><strong>УЖЕ СТРУЧНЕ ОБЛАСТИ</strong> <br> {{$uzaStrucnaOblast}}</div>
            @else
                <div id="podoblast">&nbsp; <br> &nbsp;</div>
            @endif
        @endif

        <div id="lic-container">
            <div id="licencatekst">Број лиценце</div>
            <div id="licenca">{{$licenca}}</div>
            <div id="resenje">издата решењем број {{$brojResenja}} од {{$datumResenja}}</div>
        </div>
        <table id="potpis">
            <tr>
                <td style="width: 48%;"></td>
                <td style="width: 52%;" class="potpis">МИНИСТАР</td>
            </tr>
            <tr>
                <td></td>
                <td class="potpis">Томислав Момировић</td>
            </tr>
            <tr style="height: 50px;padding-top: 0;">
                <td class="datum" style="width: 60%;">У Београду,<br>{{ $datumStampe }} године</td>
                {{--                <td class="datum" style="width: 60%;">У Београду, {{ $datumStampe }}</td>--}}
                {{--                <td class="datum" style="width: 60%;">У Београду, {{ date('d.m.Y.') }}</td>--}}

                <td></td>
            </tr>
        </table>
    </div>

@endsection
