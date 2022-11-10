@extends('layouts.nalepnice')

@section('style')
    <style>
        .a4 {
            position: absolute;
            left: -1.2cm;
            top: -1.2cm;
            width: 10cm;
            height: 5cm;
            margin: 0;
            padding: 0;
            /*border: solid thin red;*/
            /*box-sizing: border-box;*/
            /*background-color: #af38a3;*/
            /*overflow: hidden;*/
        }

        .printarea {
            position: relative;
            width: 98%;
            height: 100%;
            margin: auto 5px!important;
            /*border: solid thin red;*/
            /*box-sizing: border-box;*/
            /*background-color: #00aced;*/
        }

        .content {
            display: block;
            padding-left: 0.2cm;
        }

    </style>
@endsection
@section('content')
    <div class="a4">
        <div class="printarea">
            @foreach($result as $item)
                <span class="content">{{$item['category']}}</span>
                <br>
                <span class="content">Broj: <strong>{{$item['id']}}</strong></span>
                <span class="content">Ime i prezime: <strong>{{$item['osoba']}}</strong></span>
                <span class="content">Zavodni broj: <strong>{{$item['registry_number']}}</strong></span>
                <span class="content">Datum prijema: <strong>{{$item['registry_date']}}</strong></span>
                <span class="content">{{$item['prilogilidopuna']}}: <strong>{{$item['prilog']}}</strong></span>
                @if(!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
