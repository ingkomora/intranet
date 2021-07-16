@extends(backpack_view('blank'))

@section('title', 'Unos novih licenci u bazu')

@section('content')

{{--    @toastr_css--}}
    @if(!empty(session('message')))
        <div class="alert alert-success mt-3" role="alert">{{ session('message') }}</div>
    @endif
    @if(!empty(session('messageNOK')))
        <div class="alert alert-danger mt-3" role="alert">{{ session('messageNOK') }}</div>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endforeach
    @endif

    <div class="container py-5 mt-5">
        <form id="licenceFormular" name="licenceFormular" class="form-horizontal" action="/admin/unesinovelicence" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <label for="upload" class="col-sm-3 col-form-label col-form-label-sm">Izaberi Excel datoteku</label>
                <div class="col-sm-5">
                    <input type="file" class="form-control form-control-sm" id="upload" name="upload"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm" name="licence[0][jmbg]" value="{{old('licence.0.jmbg')}}" placeholder="Unesite matični broj" data-inputmask="'mask': '9999999999999'" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm input0 licence" name="licence[0][broj]" value="{{old('licence.0.broj')}}" placeholder="broj licence" data-rule-remote="/admin/checklicencatip/" data-inputmask="'mask': '**9**9999[99]'" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm" name="licence[0][broj_resenja]" value="{{old('licence.0.broj_resenja')}}" placeholder="broj rešenja" required {{--autocomplete="off"--}}>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" name="licence[0][datum_resenja]" value="{{old('licence.0.datum_resenja')}}" data-provide="datepicker" placeholder="datum rešenja" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" name="licence[0][datum_prijema]" value="{{old('licence.0.datum_prijema')}}" data-provide="datepicker" placeholder="datum prijema" required>
                </div>
                <div class="col-sm-1 pr-0 pl-1">
                    <button id="add" type="button" class="btn-sm btn-success px-2" onclick="additional_fields();"><span><i class="fa fa-plus"></i></span></button>
                </div>
                <div class="col-sm-11 px-1 my-2">
                    <select class="form-control border form-control-sm" id="licence0tip" name="licence[0][tip]" required>
                        @if(old('licence.0.tip'))
                            <option selected value="{{old('licence.0.tip')}}">{{old('licence.0.tip')}}</option>
                        @else
                            <option selected disabled value="">Unesite broj licence iz rešenja u odgovarajuće polje</option>
                        @endif
                    </select>
                </div>
            </div>
            @if(!empty(old('licence')))
                <div id="additional_fields">
                    @for ($i = 1; $i < count(old('licence')); $i++)
                        <div class="form-group row removeclass">
                            <div class="col-sm-3 pr-0 pl-1">
                                <input type="text" class="form-control form-control-sm" name="licence[{{$i}}][jmbg]" value="{{ old('licence.'. $i .'.jmbg') }}" placeholder="Unesite matični broj" data-inputmask="'mask': '9999999999999'" required>
                            </div>
                            <div class="col-sm-2 pr-0 pl-1">
                                <input type="text" class="form-control form-control-sm input0 licence" name="licence[{{$i}}][broj]" value="{{ old('licence.' . $i . '.broj') }}" placeholder="broj licence" data-rule-remote="/admin/checklicencatip/" data-inputmask="'mask': '**9**9999[99]'" required>
                            </div>
                            <div class="col-sm-2 pr-0 pl-1">
                                <input type="text" class="form-control form-control-sm" name="licence[{{$i}}][broj_resenja]" value="{{ old('licence.' . $i . '.broj_resenja') }}" placeholder="broj rešenja" required>
                            </div>
                            <div class="col-sm-2 pr-0 pl-1">
                                <input type="text" class="form-control form-control-sm resenja-datum" name="licence[{{$i}}][datum_resenja]" value="{{ old('licence.' . $i . '.datum_resenja') }}" data-provide="datepicker" placeholder="datum rešenja" required>
                            </div>
                            <div class="col-sm-2 pr-0 pl-1">
                                <input type="text" class="form-control form-control-sm resenja-datum" name="licence[{{$i}}][datum_prijema]" value="{{ old('licence.' . $i . '.datum_prijema') }}" data-provide="datepicker" placeholder="datum prijema" required>
                            </div>
                            <div class="col-sm-1 pr-0 pl-1">
                                <button type="button" class="btn-sm btn-danger px-2" onclick="remove_additional_fields({{$i}});"><span><i class="fa fa-minus"></i></span></button>
                            </div>
                            <div class="col-sm-11 px-1 my-2">
                                <select class="form-control border form-control-sm" id="licence{{$i}}tip" name="licence[{{$i}}][tip]" required>
                                    <option selected value="{{ old('licence.' . $i . '.tip') }}">{{ old('licence.' . $i . '.tip') }}</option>
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>
            @else
                <div id="additional_fields"></div>
            @endif
            <div class="col-sm-7 text-right">
                <button type="submit" id="submitLicence" class="next btn btn-outline-primary px-3">Obradi unete licence</button>
            </div>
        </form>
    </div>
{{--    @toastr_js--}}
{{--    @toastr_render--}}

@endsection

@section('after_scripts')
    <script src="{{ asset('js/licence.js') }}"></script>
@endsection
