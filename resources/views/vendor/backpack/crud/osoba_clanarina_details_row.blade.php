<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>
                <strong>{{$entry->ime ?? ''}} {{$entry->prezime ?? ''}}</strong>, {{$entry->zvanjeId->naziv ?? ''}}
                <br>
                <strong>Članstvo:</strong>
                @switch($entry->clan)
                    @case(-1) {{"Funkcioner"}} @break
                    @case(AKTIVAN) {{"Član IKS"}} @break
                    @case(NEAKTIVAN) {{"Nije član IKS"}} @break
                    @case(100) {{"Na čekanju"}} @break
                    @case(10) {{"Priprema se za brisanje iz članstva"}} @break
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
            @role('admin')
            @if($entry->memberships->isNotEmpty())
                <h5>Memberships:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>started_at</th>
                        <th>ended_at</th>
                        <th>status</th>
{{--                        <th>note</th>--}}
                        <th>created_at</th>
                        <th>updated_at</th>
                    </tr>
                    </thead>
                    @foreach($entry->memberships as $membership)
                        <tr>
                            <td>{{$membership->id}}</td>
                            <td>{{!empty($membership->started_at) ? \Carbon\Carbon::parse($membership->started_at)->format('d.m.Y.') : '-'}}</td>
                            <td>{{!empty($membership->ended_at) ? \Carbon\Carbon::parse($membership->ended_at)->format('d.m.Y.') : '-'}}</td>
                            <td>{{$membership->status_id}}</td>
{{--                            <td>{{$membership->note}}</td>--}}
                            <td>{{!empty($membership->created_at) ? \Carbon\Carbon::parse($membership->created_at)->format('d.m.Y.') : '-'}}</td>
                            <td>{{!empty($membership->updated_at) ? \Carbon\Carbon::parse($membership->updated_at)->format('d.m.Y.') : '-'}}</td>
                        </tr>
                    @endforeach
                </table>
            @else No data has been found
            @endif
            @endrole

            @if($entry->licence->isNotEmpty())
                <h5>Licence:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Broj</th>
                        <th scope="col">Tip</th>
                        <th scope="col">Oznaka</th>
                        <th scope="col">Datum sticanja</th>
                        <th scope="col">Preuzeta</th>
                        <th scope="col">Status</th>
                        <th scope="col">Datum ukidanja</th>
                        <th scope="col">Razlog ukidanja</th>
                        <th scope="col">Napomena</th>
                    </tr>
                    </thead>
                    @foreach($entry->licence as $licenca)
                        <tr>
                            <td>{{$licenca->id}}</td>
                            <td>{{$licenca->licencatip}}</td>
                            <td>{{$licenca->tipLicence->oznaka}}</td>
                            <td>{{!empty($licenca->datumuo) ? \Carbon\Carbon::parse($licenca->datumuo)->format('d.m.Y.') : '-'}}</td>
                            <td>{{$licenca->preuzeta === 1 ? 'Preuzeta': 'Nije preuzeta'}}</td>
                            <td>{{$licenca->status}}</td>
                            <td>{{!empty($licenca->datumukidanja) ? \Carbon\Carbon::parse($licenca->datumukidanja)->format('d.m.Y.') : '-'}}</td>
                            <td>{{$licenca->razlogukidanja}}</td>
                            <td>{{$licenca->napomena}}</td>
                        </tr>
                    @endforeach
                </table>
            @else Nema podataka o licencama
            @endif

            <br><br>

            @if(!empty($entry->clanarine->count()))
                <h5>Članarina:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Rok za naplatu</th>
                        <th scope="col">Iznos za naplatu</th>
                        <th scope="col">Iznos uplate</th>
                        <th scope="col">Preneto</th>
                        <th scope="col">Datum uplate</th>
                        <th scope="col">Ažurirao</th>
                        <th scope="col">Datum ažuriranja</th>
                        @role('admin')
                        <th scope="col">Ažurirao admin</th>
                        <th scope="col">Datum ažuriranja admin</th>
                        @endrole
                        <th scope="col">Napomena</th>
                    </tr>
                    </thead>
                    @foreach($entry->clanarine as $unos)
                        <tr>
                            <td>{{\Carbon\Carbon::parse($unos->rokzanaplatu)->format('d.m.Y.')}}</td>
                            <td>{{$unos->iznoszanaplatu}}</td>
                            <td>{{$unos->iznosuplate}}</td>
                            <td>{{$unos->pretplata}}</td>
                            <td>{{!empty($unos->datumuplate) ? \Carbon\Carbon::parse($unos->datumuplate)->format('d.m.Y.') : '-'}}</td>
                            <td>{{$unos->appKorisnik->nalog ?? "-"}}</td>
                            <td>{{!empty($unos->datumazuriranja) ? \Carbon\Carbon::parse($unos->datumazuriranja)->format('d.m.Y. H:m:s') : '-'}}</td>
                            @role('admin')
                            <td>{{$unos->appAdmin->nalog ?? "-"}}</td>
                            <td>{{!empty($unos->datumazuriranja_admin) ? \Carbon\Carbon::parse($unos->datumazuriranja_admin)->format('d.m.Y. H:m:s') : '-'}}</td>
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
