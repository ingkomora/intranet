@extends(backpack_view('blank'))

@section('title', 'Unos novih licenci u bazu')

@php
    $breadcrumbs = [
        'Admin' => backpack_url('dashboard'),
        'Licence' => false,
        'Unošenje novih licenci' => false,
    ];
@endphp

@section('content')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Upisivanje novih licenci</span>
        </h2>
    </div>

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
    <div class="container-fluid animated fadeIn my-5">
        <form id="licenceFormular" name="licenceFormular" class="form-horizontal" action="/admin/unesinovelicence" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="input-group mb-3 col-md-5">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01">Izaberi Excel datoteku</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="upload" name="upload" aria-describedby="inputGroupFileAddon01">
                        <label class="custom-file-label" for="upload"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group-prepend">
                        <a href='{{asset("download/tmpl_upis_licenci_u_registar.xlsx")}}' class="input-group-text btn btn-outline-warning">Preuzmi excel šablon</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group-prepend">
                        <table class="table table-sm">
                            <thead class="thead-light">
                            <tr>
                                <th colspan="2">UPUTSTVO</th>
                                <th colspan="2">LEGENDA za excel šablon</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="2">U polja unosite brojeve i tekst <b>bez razmaka</b></td>
                                <td class="bg-success"></td>
                                <td>Obavezna polja</td>
                            </tr>
                            <tr>
                                <td colspan="2">Koristite <b>latinično</b> pismo</td>
                                <td class="bg-secondary"></td>
                                <td>Polja koja program ne koristi prilikom unosa licence</td>
                            </tr>
                            <tr>
                                <td colspan="2">Datum se unosi u formatu: <b>GGGG-MM-DD</b></td>
                                <td class="bg-warning"></td>
                                <td>Polja od kojih je obavezno samo jedno od ponuđenih</td>
                            </tr>
                            <tr>
                                <td colspan="2">Excel šablon sadrži karticu <code>INFO</code> gde možete dodatno da se informišete.</td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr>

            <div class="input-group my-5">
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
            <div class="col-sm-12 text-left">
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
