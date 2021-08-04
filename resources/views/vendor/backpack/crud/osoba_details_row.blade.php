<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>JMBG: {{$entry->id}}, LIB: {{$entry->lib}}, <strong>{{$entry->ime}} {{$entry->prezime}}</strong>, {{$entry->zvanjeId->naziv}}</p>
            <p>Licence:<br>
                @if($entry->licence->isNotEmpty())
                    @foreach($entry->licence as $item)
                        <strong>{{$item->id}} ({{$item->status}})</strong>, tip: <strong>{{$item->licencatip}} - {{$item->tipLicence->naziv}} ({{$item->tipLicence->idn}})</strong>, zahtev: {{$item->zahtev}}, {{$item->broj_resenja}}, {{$item->datumuo}}<br>
                    @endforeach
                @else Nema podataka o licencama
                @endif
            </p>
            <p>Zahtevi za licence:<br>
                @if($entry->zahteviLicence->isNotEmpty())
                    @foreach($entry->zahteviLicence as $item)
                        <strong>{{$item->id}} ({{$item->statusId->naziv}})</strong>, tip: <strong>{{$item->licencatip}} - {{$item->tipLicence->naziv}} ({{$item->tipLicence->idn}})</strong>, datum prijema: {{$item->prijem}}<br>
                    @endforeach
                @else Nema podataka o zahtevima za izdavanje licence
                @endif
            </p>
            <p>Stručni ispit:<br>
                @if($entry->siPrijave->isNotEmpty())
                    @foreach($entry->siPrijave as $item)
                        <strong>{{$item->id}} ({{$item->status->naziv}})</strong>, vrsta posla:<strong> {{$item->vrstaPosla->naziv}}</strong>, zahtev: {{$item->regOblast->naziv}}, {{$item->regPodOblast->naziv}}, {{$item->datum_prijema}}, {{$item->zavodni_broj}}<br>
                    @endforeach
                @else Nema podataka o stručnom ispitu
                @endif
            </p>
            @if(!empty($entry->clanarine->count()))
                <h5>Članarina:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">rok za naplatu</th>
                        <th scope="col">iznos za naplatu</th>
                        <th scope="col">iznos uplate</th>
                        <th scope="col">pretplata</th>
                        <th scope="col">datum uplate</th>
                        <th scope="col">napomena</th>
                    </tr>
                    </thead>
                    @foreach($entry->clanarine as $unos)
                        <tr>
                            <td>{{$unos->rokzanaplatu}}</td>
                            <td>{{$unos->iznoszanaplatu}}</td>
                            <td>{{$unos->iznosuplate}}</td>
                            <td>{{$unos->pretplata}}</td>
                            <td>{{$unos->datumuplate}}</td>
                            <td>{{$unos->napomena}}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>
<div class="clearfix"></div>
