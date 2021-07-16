@extends(backpack_view('blank'))

@section('title', 'Unos novih licenci u bazu')

@section('content')


<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js" crossorigin="anonymous"></script>

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
{{--@endsection--}}

{{--@section('script')--}}
    <script type="application/javascript">
        // $(function () {
            console.log( "ready!" );

            $('.resenja-datum').datepicker({
                format: "dd.mm.yyyy.",
                weekStart: 1,
                startView: 1,
                language: "sr-latin",
                orientation: "left top",
                uiLibrary: 'bootstrap4',
            });

            var Form = $('form');
            Form.validate(
                {
                    onfocusout: function (element) {
                        if (element.name === 'jmbg') {
                            this.element(element);
                        }
                    },
                    highlight: function (element) {
                        $(element).closest("input").addClass("border-danger");
                        $(element).closest("select").addClass("border-danger");
                    },
                    unhighlight: function (element) {
                        $(element).closest("input").removeClass("border-danger");
                        $(element).closest("select").removeClass("border-danger");
                    },
                    errorPlacement: function (error, element) {
                        $(element).parent('div').append(error);
                    },
                }
            );
            $('#licenceFormular').on("blur", 'input[name*="[broj]"]', function () {

                var licencatip = $(this).val();
                licencatip = (licencatip.replace(/\s/g, '')).substring(0, 3);

                var prijavaId = $(this).attr('name').replace(/^.*(\d).*$/, '$1');
                var licDiv = $('select#licence' + prijavaId + 'tip');
                if (licencatip) {
                    $.ajax({
                        url: '/admin/licencatip/' + licencatip,
                        type: "GET",
                        dataType: "JSON",
                        success: function (data) {
                            $(licDiv).find('option').remove().end();
                            if (Object.keys(data).length !== 0) {

                                if (Object.keys(data).length > 1) {
                                    $(licDiv).append(new Option('Izaberite naziv licence koji odgovara nazivu sa rešenja', ''));
                                }
                                $.each(data, function (key, value) {
                                    if (localStorage.getItem('tip' + prijavaId) === key) {
                                        localStorage.removeItem('tip' + prijavaId);
                                        $(licDiv).append(new Option(value, key, false, true));
                                    } else {
                                        $(licDiv).append(new Option(value, key));
                                    }
                                });
                            } else {
                                $(licDiv).append(new Option('Proverite broj licence', ''));
                            }
                        },
                        error: function (request, error) {
                            // console.log(" Can't do because: " + error);
                        }
                    });
                } else {
                    // console.log('nije nista upisano u polje za licencu');
                }

                // });

            });

            $('#licenceFormular').on("blur", 'input[name*="[broj]"]', function () {

                var licenca = $(this).val();

                var prijavaId = $(this).attr('name').replace(/^.*(\d).*$/, '$1');

                var jmbg = $('input[name="licence[' + prijavaId + '][jmbg]"]').val();
                console.log("jmbg: " + jmbg);

                var licDiv = $('select#licence' + prijavaId + 'tip');
                if (licenca) {
                    $.ajax({
                        url: '/admin/checkzahtev/' + licenca + '/' + jmbg,
                        type: 'GET',
                        // data:data,
                        dataType: 'JSON',
                        // contentType : false,
                        processData: false,
                        success: function (data) {
                            /*                            $(licDiv).find('option').remove().end();
                                                        if (Object.keys(data).length !== 0) {

                                                            if (Object.keys(data).length > 1) {
                                                                $(licDiv).append(new Option('Izaberite naziv licence koji odgovara nazivu sa rešenja', ''));
                                                            }
                                                            $.each(data, function (key, value) {
                                                                if (localStorage.getItem('tip' + prijavaId) === key) {
                                                                    localStorage.removeItem('tip' + prijavaId);
                                                                    $(licDiv).append(new Option(value, key, false, true));
                                                                } else {
                                                                    $(licDiv).append(new Option(value, key));
                                                                }
                                                            });
                                                        } else {
                                                            $(licDiv).append(new Option('Proverite broj licence', ''));
                                                        }*/
                        },
                        error: function (request, error) {
                            // console.log(" Can't do because: " + error);
                        }
                    });
                } else {
                    // console.log('nije nista upisano u polje za licencu');
                }

                // });

            });

            $('input[name="upload"]').on("blur", function () {
                var file = document.forms['licenceFormular']['upload'].files[0];

                if (file !== undefined) {
                    console.log(file.name);
                    $('#submitLicence').attr('formnovalidate', '');
                } else {
                    $('#submitLicence').removeAttr('formnovalidate');
                }

            });


        // });

        var room = 0;

        function additional_fields() {

            room = $('div#additional_fields .form-group.row').length;
console.log(room);
            room++;
            var objTo = document.getElementById('additional_fields');
            var divtest = document.createElement("div");
            divtest.setAttribute("class", "form-group row removeclass" + room);
            var rdiv = 'removeclass' + room;
            divtest.innerHTML = '\
                <div class="col-sm-3 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="licence[' + room + '][jmbg]" placeholder="Unesite matični broj" data-inputmask="\'mask\': \'9999999999999\'" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="licence[' + room + '][broj]" placeholder="broj licence" data-rule-remote="/admin/checklicencatip/" data-inputmask="\'mask\': \'**9**9999[99]\'" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="licence[' + room + '][broj_resenja]" placeholder="broj rešenja" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm resenja-datum" name="licence[' + room + '][datum_resenja]" data-provide="datepicker" placeholder="datum rešenja" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm resenja-datum" name="licence[' + room + '][datum_prijema]" data-provide="datepicker" placeholder="datum prijema" required>\
                </div>\
                <div class="col-sm-1 pr-0 pl-1">\
                    <button type="button" class="btn-sm btn-danger px-2" onclick="remove_additional_fields(' + room + ');"><span><i class="fa fa-minus"></i></span></button>\
                </div>\
                <div class="col-sm-11 px-1 my-2">\
                    <select id="licence' + room + 'tip" class="form-control border form-control-sm" name="licence[' + room + '][tip]">\
                        <option selected disabled value="">Unesite broj licence iz rešenja u odgovarajuće polje</option>\
                    </select>\
                </div>\
                <div class="col-sm-12 pr-0 pl-1">\
                <div id="osoba' + room + '" ></div>\
                </div>\
                <div class = "clear"></div>';

            objTo.appendChild(divtest)
        }

        function remove_additional_fields(rid) {
            $('.removeclass' + rid).remove();
        }

    </script>
@endsection
