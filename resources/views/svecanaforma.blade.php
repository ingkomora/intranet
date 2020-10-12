@extends('layouts.print')

@section('title', 'Svecana forma')

@section('content')
    <div>

        <div id="nadnaslov">Република Србија</div>
        <div id="naslov">МИНИСТАРСТВО ГРАЂЕВИНАРСТВА, САОБРАЋАЈА И ИНФРАСТРУКТУРЕ</div>
        <hr id="linija1">
        <div id="vrstalicencenaslov">{{ $vrstaLicenceNaslov }}</div>
        <hr id="linija2">
        <div id="naosnovu">На основу члана 162. Закона о планирању и изградњи</div>
        <div id="mgsi">МИНИСТАРСТВО ГРАЂЕВИНАРСТВА, САОБРАЋАЈА И ИНФРАСТРУКТУРЕ</div>
        <div id="utvrdjuje">утврђује да је</div>
        <div id="ime">{{$osobaImeRPrezime}}</div>
        <div id="zvanje">{{$zvanje}}</div>
        @if(!empty($nazivLicence))
            <div id="imalac">ималац лиценце {{$nazivLicence}}</div>
        @else
            <div id="imalac">ималац лиценце {{$vrstaLicence}} за</div>
            <div id="oblast"><strong>СТРУЧНУ ОБЛАСТ</strong> <br> {{$strucnaOblast}}</div>
            @if(!in_array($uzaStrucnaOblastId, [1, 2, 4, 21, 29]))
                <div id="podoblast"><strong>УЖУ СТРУЧНУ ОБЛАСТ</strong> <br> {{$uzaStrucnaOblast}}</div>
            @else
                <div id="podoblast">&nbsp; <br> &nbsp;</div>
            @endif
        @endif
        <div id="licencatekst">Број лиценце</div>
        <div id="licenca">{{$licenca}}</div>
        <table id="potpis">
            <tr>
                <td style="width: 64%;"></td>
                <td class="potpis">ПОТПРЕДСЕДНИЦА ВЛАДЕ<br>И МИНИСТАРКА</td>
            </tr>
            <tr>
                <td style="width: 64%;"></td>
                <td class="potpis">Проф. др Зорана З. Михајловић</td>
            </tr>
            <tr style="height: 70px; padding-top: 0;">
                <td class="datum" style="width: 60%;">У Београду, {{ $datumStampe }} године</td>
                {{--                <td class="datum" style="width: 60%;">У Београду, {{ date('d.m.Y.') }}</td>--}}

                <td></td>
            </tr>
        </table>
    </div>

@endsection