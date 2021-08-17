<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            <p><strong>{{$entry->ime ?? ''}} {{$entry->prezime ?? ''}}</strong>, {{$entry->zvanjeId->naziv ?? ''}}<br><b>JMBG:</b> {{$entry->id}}, <b>LIB:</b> {{$entry->lib ?? 'nema lib'}}</p>

            <p><b>Licence:</b><br>
                @if($entry->licence->isNotEmpty())
                    @foreach($entry->licence as $item)
                        <strong>{{$item->id}} ({{$item->status}})</strong>, <strong>tip:</strong> {{$item->licencatip}} - {{$item->tipLicence->naziv}} ({{$item->tipLicence->idn}}), <b>zahtev:</b> {{$item->zahtev ?? 'nema zahtev'}}, {{$item->broj_resenja ?? 'nema rešenje'}}, {{$item->datumuo}}<br>
                    @endforeach
                @else Nema podataka o licencama
                @endif
            </p>
            <p><b>Zahtevi za licence:</b><br>
                @if($entry->zahteviLicence->isNotEmpty())
                    @foreach($entry->zahteviLicence as $item)
                        <strong>{{$item->id}}</strong> ({{$item->statusId->naziv ?? 'nema status'}}),
                        <strong>tip:</strong> {{$item->licencatip}} - {{$item->tipLicence->naziv}} ({{$item->tipLicence->idn}}),
                        <b>status:</b> {{$item->statusId->naziv ?? 'nema status'}}, <b>datum prijema:</b> {{$item->prijem ?? '-'}}<br>
                    @endforeach
                @else Nema podataka o zahtevima za izdavanje licence
                @endif
            </p>
            <p><b>Stručni ispit:</b><br>
                @if($entry->siPrijave->isNotEmpty())
                    @foreach($entry->siPrijave as $item)
                        <strong>{{$item->id}}</strong> ({{$item->status->naziv ?? 'nema status'}}), vrsta posla:<strong> {{$item->vrstaPosla->naziv}}</strong>, zahtev: {{$item->regOblast->naziv}}, {{$item->regPodOblast->naziv}}, {{$item->datum_prijema ?? ''}}, {{$item->zavodni_broj ?? ''}}<br>
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
