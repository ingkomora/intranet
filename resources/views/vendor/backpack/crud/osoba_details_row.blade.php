<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            {{--			{{ trans('backpack::crud.details_row') }}--}}
            <p>JMBG:{{$entry->id}}, LIB:{{$entry->lib}}, <strong>{{$entry->ime}} {{$entry->prezime}}</strong>, {{$entry->zvanjeId->naziv}}</p>
            <p>Licence:<br>
                @foreach($entry->licence as $licenca)
                    <strong>{{$licenca->id}} ({{$licenca->status}})</strong>, tip:<strong>{{$licenca->tipLicence->idn}}</strong>, zahtev: {{$licenca->zahtev}}, {{$licenca->broj_resenja}}, {{$licenca->datumuo}}, {{$licenca->tipLicence->naziv}}<br>
                @endforeach
            </p>
            @if(!empty($entry->clanarine->count()))
                <h5>Èlanarina:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">rok za naplatu</th>
                        <th scope="col">iznos za naplatu</th>
                        <th scope="col">iznos uplate</th>
                        <th scope="col">pretplata</th>
                        <th scope="col">datum uplate</th>
                    </tr>
                    </thead>
                    @foreach($entry->clanarine as $unos)
                        <tr>
                            <td>{{$unos->rokzanaplatu}}</td>
                            <td>{{$unos->iznoszanaplatu}}</td>
                            <td>{{$unos->iznosuplate}}</td>
                            <td>{{$unos->pretplata}}</td>
                            <td>{{$unos->datumuplate}}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>
<div class="clearfix"></div>