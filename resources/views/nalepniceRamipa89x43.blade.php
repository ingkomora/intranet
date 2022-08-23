@extends('layouts.nalepnice')

@section('title', 'Nalepnice')

@section('style')
    <style>
        .a4 {
            position: absolute;
            left: -1.2cm;
            top: -1.2cm;
            width: 20.95cm;
            height: 29.65cm;
            margin: 0;
            padding: 0;
            /*border: solid thin red;*/
            box-sizing: border-box;
            overflow:hidden;
        }

        .printarea {
            position: absolute;
            left: 1.3cm;
            top: 1.2cm;
            width: 18.25cm;
            height: 27.25cm;
            padding: 0;
            margin: auto auto;
            /*border: solid thin #0a1520;*/
            box-sizing: border-box;
        }

        .row {
            /*border: solid thin green;*/
            box-sizing: border-box;
            margin: 0 0 0.3cm 0;
            padding: 0;
            height: 4.25cm;
            width: 100%;
        }

        .row:after {
            clear: both;
        }

        .col {
            left: 0;
            top: 0.85cm;
            width: 48%;
            height: 100%;
            /*border: solid thin blueviolet;*/
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            float: left;
        }

        .content {
            display: block;
            padding-left: 0.2cm;
        }

        .broj-strane {
            position: absolute;
            bottom: -0.3cm;
            right: -0.3cm;
            padding: 0;
            margin: 0;
            /*float: right;*/
        }
    </style>
@endsection
@section('content')
    <?php $count = 0; $n = 0 ?>
    <div class="a4">
        <div class="printarea">
            @foreach($result as $item)
                @if($count % 2 === 0)
                    <div class="row">
                        @endif
                        <div class="col" @if($count % 2 === 0) style="float: right;" @endif>
                            <span class="content">{{$item['category']}} ({{$item['id']}})</span>
                            <span class="content">Ime i prezime: <strong>{{$item['osoba']}}</strong></span>
                            <span class="content">Zavodni broj: <strong>{{$item['registry_number']}}</strong></span>
                            <span class="content">Datum prijema: <strong>{{$item['registry_date']}}</strong></span>
                            <span class="content">{{$item['prilogilidopuna']}}: <strong>{{$item['prilog']}}</strong></span>
                        </div>
                        <?php $count++; ?>
                        @if($count % 2 === 0)
                    </div>
                @endif
                @if($count == $n * 12 + 1)
                    <div class="broj-strane">Strana {{$n + 1}}</div>
                    <?php $n++; ?>
                @endif
                @if($count >= 12 AND $count % 12 == 0)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
