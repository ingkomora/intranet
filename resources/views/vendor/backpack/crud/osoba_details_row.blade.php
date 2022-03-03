<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>
                <strong>{{$entry->ime ?? ''}} {{$entry->prezime ?? ''}}</strong>, {{$entry->zvanjeId->naziv ?? ''}}
                <br>
                <strong>Članstvo:</strong>
                @switch($entry->clan)
                @case($entry->clan == AKTIVAN) {{"Član IKS"}}
                @case($entry->clan == NEAKTIVAN) {{"Nije član IKS"}}
                @case($entry->clan == 100) {{"Na čekanju"}}
                @endswitch
                <br>
                <strong>Napomena:</strong> {{!empty($entry->napomena) ? $entry->napomena : "Nema napomene"}}
                <br>
                <b>Registar:</b>
                @if($entry->lib)
                    @if($entry->licence->isEmpty())
                        Nema podataka upisanih u Registar.
                    @else
                        <a
                            href="http://www.ingkomora.rs/clanovi/registar_pretraga.php?lib={{$entry->lib ?? 'nema lib'}}"
                            target="_blank">
                            <i class="nav-icon fas fa-book"></i> Pogledaj podatke upisane u Registar</a>
                    @endif
                @else
                    Nije dodeljen lib
                @endif
            </p>
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
                        <th scope="col">datumazuriranja</th>
                        <th scope="col">azurirao_korisnik</th>
                        @role('admin')
                        <th scope="col">datumazuriranja_admin</th>
                        <th scope="col">azurirao_admin</th>
                        @endrole
                        <th scope="col">napomena</th>
                    </tr>
                    </thead>
                    @foreach($entry->clanarine as $unos)
{{--                        @dd($unos->appKorisnik)--}}
                        <tr>
                            <td>{{$unos->rokzanaplatu}}</td>
                            <td>{{$unos->iznoszanaplatu}}</td>
                            <td>{{$unos->iznosuplate}}</td>
                            <td>{{$unos->pretplata}}</td>
                            <td>{{$unos->datumuplate}}</td>
                            <td>{{$unos->datumazuriranja}}</td>
                            <td>{{$unos->appKorisnik->nalog??""}}</td>
                            @role('admin')
                            <td>{{$unos->datumazuriranja_admin}}</td>
                            <td>{{$unos->appAdmin->nalog??""}}</td>
                            @endrole
                            <td>{{$unos->napomena}}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>
<div class="clearfix"></div>
