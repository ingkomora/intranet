@extends('layouts.nalepnice')

@section('title', 'Nalepnice - ' . $oblast)

@section('style')
    <style>

        .container {
            width: 21cm;
            /*height: 29.7cm;*/
            /*margin: 0;*/
            padding: 0;
            margin: 0 0 -2cm 0.1cm;

        }

        .row {
            width: 21cm;
            margin: 0;
            /*margin-bottom: 0.1cm;*/
        }

        .col-6 {
            /*background-color: #3f9ae5;*/
            /*display: inline-block;*/
            /*width: 50%;*/
            width: 8.9cm;
            height: 4.2cm;
            /*box-sizing: border-box;*/
            /*border: solid thin #0a1520;*/
            float: left;
            margin: 0;
            padding: 0 0.5cm;
        }

        .row:after {
            /*.col-6:nth-child(2n+1) {*/
            /*    background-color: red;*/
            clear: both;
        }

        p {
            margin: 0
        }
        .broj-strane{
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
    @foreach($result AS $item)
        @if($count%2 == 0)
            <div class="row mx-0">
                @endif
                <div class="col-6">
                    <p><span style="font-size: smaller">{{$item['category']}} ({{$item['id']}})</span>
                        <br>Ime i prezime: <strong>{{$item['osoba']}}</strong>
                        <br>Zavodni broj: <strong>{{$item['registry_number']}}</strong>
                        <br>Datum prijema: <strong>{{$item['registry_date']}}</strong>
                        <br>Prilog:</p>
                </div>
                <?php $count++; ?>
                @if($count%2 == 0)
            </div>
        @endif
        @if($count == $n*12+1)
            <div class="broj-strane">Strana {{$n+1}}</div>
                <?php $n++; ?>
        @endif
        @if($count>=12 AND $count%12 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach

@endsection
