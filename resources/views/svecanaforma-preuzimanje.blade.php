@extends('layouts.printsf')

@section('title', 'Svecana forma')

@section('content')
    <div>

        <div id="nadnaslov">Република Србија</div>
        <div id="naslov">Инжењерска комора србије</div>
        <hr id="linija1">
        <div id="vrstalicencenaslov">{{ $vrstaLicenceNaslov }}</div>
        <hr id="linija2">
        <div id="tekst">Овим путем изјављујем да са преузео/ла образац свечане форме лиценце број: , који </div>
        <div id="mgsi">МИНИСТАРСТВО ГРАЂЕВИНАРСТВА, САОБРАЋАЈА И ИНФРАСТРУКТУРЕ</div>
        <div id="utvrdjuje">утврђује да је</div>
        <div id="ime">{{$osobaImeRPrezime}}</div>
        <div id="zvanje">{{$zvanje}}</div>

        <div id="licencatekst">Број лиценце</div>
        <div id="licenca">{{$licenca}}</div>
        <table id="potpis">
            <tr style="height: 70px; padding-top: 0;">
                <td class="datum" style="width: 60%;">У Београду, дана {{ $datumStampe }} године</td>
{{--                <td class="datum" style="width: 60%;">У Београду, {{ $datumStampe }}</td>--}}
                {{--                <td class="datum" style="width: 60%;">У Београду, {{ date('d.m.Y.') }}</td>--}}

                <td></td>
            </tr>
            <tr>
                <td style="width: 64%;border-top: solid black thin"></td>
                <td class="potpis">(име и презиме)</td>
            </tr>
            <tr>
                <td style="width: 64%;border-top: solid black thin"></td>
                <td class="potpis">(број личне карте)</td>
            </tr>
            <tr>
                <td style="width: 64%;border-top: solid black thin"></td>
                <td class="potpis">(потпис)</td>
            </tr>
        </table>
    </div>

@endsection
