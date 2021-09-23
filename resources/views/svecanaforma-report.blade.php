@extends('layouts.printsf')

@section('title', 'Svečana forma - Izveštaj')

@section('content')
    <div>

{{--        <div id="report-header">--}}
{{--            <div id="logo"><img src="{{storage_path('app/public/images/iks_grb.png') }}"></div>--}}
            <div id="nadnaslov">Инжењерска комора Србије</div>
            <div id="naslov">Списак достављених свечаних форми лиценци на потпис</div>
            <hr id="linija1">
            <div id="podnaslov">Датум потписивања: <strong>{{ $dataOK[1]->datumStampe }}</strong> године</div>
            <hr id="linija2">
{{--        </div>--}}
        <div id="report-content">
            @foreach($dataOK as $osoba)

                <div id="list">{{$loop->iteration}}. <strong>{{$osoba->osobaImeRPrezime}}</strong>, {{$osoba->zvanjeskr}} {{$osoba->licenca}}</div>
            @endforeach
            {{--        <table id="potpis">
                        <tr>
                            <td style="width: 65%;"></td>
                            <td class="potpis">вд. руководиоца Стручних служби</td>
                        </tr>
                        <tr>
                            <td style="width: 65%;"></td>
                            <td class="potpis">Слободанка Симић</td>
                        </tr>
                        <tr style="height: 70px; padding-top: 0;">
                            <td class="datum" style="width: 60%;">У Београду, {{ date('d.m.Y.') }}</td>
                            <td></td>
                        </tr>
                    </table>--}}
        </div>

    </div>
@endsection
