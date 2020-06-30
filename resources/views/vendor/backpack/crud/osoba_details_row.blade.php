<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            {{--			{{ trans('backpack::crud.details_row') }}--}}
            <p>JMBG:{{$entry->id}}, LIB:{{$entry->lib}}, <strong>{{$entry->ime}} {{$entry->prezime}}</strong>, {{$entry->zvanjeId->naziv}}</p>
            <p>Licence:<br>
                @foreach($entry->licence as $licenca)
                    <strong>{{$licenca->id}}</strong>, tip:<strong>{{$licenca->tipLicence->idn}}</strong>, zahtev: {{$licenca->zahtev}}, {{$licenca->broj_resenja}}, {{$licenca->datumuo}}, {{$licenca->tipLicence->naziv}}<br>
                @endforeach
            </p>
        </div>
    </div>
</div>
<div class="clearfix"></div>