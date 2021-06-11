@extends(backpack_view('blank'))

@section('title', 'Unošenje podataka:' . strtoupper($url))

@section('content')
    <div class="container pb-3">
        @if(!empty($message))
            <div class="alert alert-success mt-3 row">
                <div class="col-6" role="alert">{!! $message !!}</div>
                @if(!empty($status) AND $status)
                    <div class="col-6 text-right" role="alert">
                        <form id="download" action="/admin/downloadzip" method="POST">
                            @csrf
                            <input type="hidden" name="zipfile" value="{{$url}}">
                            <button id="download-button" type="submit" class="next btn btn-sm btn-warning px-3 ml-5">Preuzmi ZIP datoteku</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger" role="alert">{!! $error !!}</div>
            @endforeach
        @endif
        @if(!empty($url))
            <form id="jmbgFormular" class="form-horizontal" action="/admin/{{$action}}/{{$url}}" method="POST" enctype="multipart/form-data">
                @else
                    <form id="jmbgFormular" class="form-horizontal" action="/admin/{{$action}}" method="POST" enctype="multipart/form-data">
                        @endif
                        @csrf
                        <div class="form-group row">
                            <label for="upload" class="col-sm-3 col-form-label col-form-label-sm">Izaberi Excel datoteku</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control form-control-sm" id="upload" name="upload"/>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="datum" class="col-sm-3 col-form-label col-form-label-sm">Datum</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control form-control-sm" id="datum" name="datum"/>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="licence" class="col-sm-3 col-form-label col-form-label-sm">LICENCE:</label>
                            <div class="col-sm-9">
                                <textarea class="form-control form-control-sm" id="licence" name="licence" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jmbgs" class="col-sm-3 col-form-label col-form-label-sm">JMBG-ovi:</label>
                            <div class="col-sm-9">
                                <textarea class="form-control form-control-sm" id="jmbgs" name="jmbgs" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="col-sm-7 text-right">
                            <button id="obradi" type="submit" class="next btn btn-outline-primary px-3">{{$action}}</button>
                        </div>

                    </form>
    </div>

@endsection
