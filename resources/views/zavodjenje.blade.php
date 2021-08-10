@extends(backpack_view('blank'))

@section('title', 'Zavodjenje prijava za polaganje stručnog ispita')

@php
    $breadcrumbs = [
        'Admin' => backpack_url('dashboard'),
        'Zavođenje' => false,
    ];
@endphp

@section('content')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Zavođenje prijava za polaganje stručnog ispita</span>
        </h2>
    </div>
    <div class="container-fluid animated fadeIn my-5">
        <form id="prijaveFormular" action="/admin/zavedi" method="POST">
            @csrf
            <div class="form-group row">
                <label for="datum_prijema" class="col-sm-2 col-form-label">Datum prijema:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="datum_prijema" name="datum_prijema" data-provide="datepicker" placeholder="izaberi ili unesi datum prijema">
                </div>
            </div>
            <div class="form-group row">
                <label for="prijave" class="col-sm-2 col-form-label">Brojevi prijava:</label>
                <div class="col-sm-10">
                    <textarea class="form-control form-control-sm" id="prijave" name="prijave" rows="20" placeholder="unesi brojeve prijava za stručni ispit"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10 text-left">
                    <button id="zavedi" type="submit" class="next btn btn-outline-primary px-3">Zavedi unete prijave</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('after_scripts')
    <script>
        atachDatepicker();

        function atachDatepicker(){
            $('#datum_prijema').datepicker({
                format: "dd.mm.yyyy.",
                weekStart: 1,
                startView: 1,
                language: "sr-latin",
                orientation: "left top",
                uiLibrary: 'bootstrap4',
            });
        }
    </script>
@endsection
