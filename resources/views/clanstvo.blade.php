@extends(backpack_view('blank'))

@section('title', 'Obrada odobrenih zahteva za prijem u članstvo')

@php
    $breadcrumbs = [
        'Admin' => backpack_url('dashboard'),
        'Članstvo' => false,
        'Obrada zahteva' => false,
    ];
@endphp

@section('content')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Obrada odobrenih zahteva</span>
        </h2>
    </div>

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
    <div class="container-fluid animated fadeIn my-5">
        <form id="prijaveFormular" class="form-horizontal" action="/admin/unesinoveclanove" method="POST" enctype="multipart/form-data" {{--autocomplete="off"--}}>
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
                        <a href='{{asset("")}}' class="input-group-text btn btn-outline-warning">Preuzmi excel šablon</a>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
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

            {{--<div class="form-group row">
                <label for="upload" class="col-sm-3 col-form-label col-form-label-sm">Izaberi Excel datoteku</label>
                <div class="col-sm-5">
                    <input type="file" class="form-control form-control-sm" id="upload" name="upload" disabled/>
                </div>
            </div>--}}
            <div class="form-group row my-5">
                <div class="col-sm-3 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][broj]" placeholder="broj zahteva" value="{{old('prijave[0][broj]')}}" required {{--data-rule-remote="/admin/checkprijavaclan/"--}}>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][broj-resenja]" placeholder="broj odluke UO" value="{{old('prijave[0][broj-resenja]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum odluke UO" name="prijave[0][datum-resenja]" value="{{old('prijave[0][datum-resenja]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm " name="prijave[0][zavodni-broj]" placeholder="zavodni broj zahteva" value="{{old('prijave[0][zavodni-broj]')}}" required>
                </div>
                <div class="col-sm-2 pr-0 pl-1">
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum prijema zahteva" name="prijave[0][datum-prijema]" value="{{old('prijave[0][datum-prijema]')}}" required>
                </div>
                <div class="col-sm-1 pr-0 pl-1">
                    <button id="add" type="button" class="btn-sm btn-success px-2" onclick="additional_fields();"><span><i class="fa fa-plus"></i></span></button>
                </div>
                <div class="col-sm-12 pr-0 pl-1">
                    <div id="osoba0"></div>
                </div>
            </div>
            <div id="additional_fields"></div>
            <div class="col-sm-12 text-left">
                <button type="submit" class="next btn btn-outline-primary px-3">Obradi unete prijave</button>
            </div>
        </form>
    </div>

@endsection

@section('after_scripts')
    <script src="{{ asset('js/clanstvo.js') }}"></script>
@endsection
