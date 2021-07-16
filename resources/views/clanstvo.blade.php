@extends(backpack_view('blank'))

@section('title', 'Obrada odobrenih zahteva za prijem u članstvo')

@section('content')

    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger" role="alert">{!! $error !!}</div>
        @endforeach
    @endif

    @if(!empty($message))
        <div class="alert alert-success mt-3 row">{!! $message !!}</div>
    @endif
    @if(!empty($errormessage))
        <div class="alert alert-warning mt-3 row">{!! $errormessage !!}</div>
    @endif
    <div class="container pb-3" style="margin-top: 200px">
        <form id="prijaveFormular" class="form-horizontal" action="/admin/unesinoveclanove" method="POST" enctype="multipart/form-data" {{--autocomplete="off"--}}>
            @csrf
            <div class="form-group row">
                <label for="upload" class="col-sm-3 col-form-label col-form-label-sm">Izaberi Excel datoteku</label>
                <div class="col-sm-5">
                    <input type="file" class="form-control form-control-sm" id="upload" name="upload" disabled/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][broj]" placeholder="broj prijave" value="{{old('prijave[0][broj]')}}" required {{--data-rule-remote="/admin/checkprijavaclan/"--}}>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][broj-resenja]" placeholder="broj rešenja UO" value="{{old('prijave[0][broj-resenja]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum rešenja UO" name="prijave[0][datum-resenja]" value="{{old('prijave[0][datum-resenja]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][zavodni-broj]" placeholder="zavodni broj" value="{{old('prijave[0][zavodni-broj]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum prijema" name="prijave[0][datum-prijema]" value="{{old('prijave[0][datum-prijema]')}}" required>
                </div>
                <div class="col-sm-1 pr-0 pl-1">
                    <button id="add" type="button" class="btn-sm btn-success px-2" onclick="additional_fields();"><span><i class="fa fa-plus"></i></span></button>
                </div>
                <div class="col-sm-12 pr-0 pl-1">
                    <div id="osoba0"></div>
                </div>
            </div>
            <div id="additional_fields"></div>
            <div class="col-sm-7 text-right">
                <button type="submit" class="next btn btn-outline-primary px-3">Obradi unete prijave</button>
            </div>
        </form>
    </div>

@endsection

@section('after_scripts')
    <script src="{{ asset('js/clanstvo.js') }}"></script>
@endsection
