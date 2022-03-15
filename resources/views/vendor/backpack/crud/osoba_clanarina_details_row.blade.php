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

            <p><b>Licence:</b>
                @if($entry->licence->isNotEmpty())
                    @foreach($entry->licence as $item)
                        {{$item->id}} ({{$item->status}})
                    @endforeach
                @else Nema podataka o licencama
                @endif
            </p>

            @if(!empty($entry->clanarine->count()))
                <h5>Članarina:</h5>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Rok za naplatu</th>
                        <th scope="col">Iznos za naplatu</th>
                        <th scope="col">Iznos uplate</th>
                        <th scope="col">Pretplata</th>
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
                            <td>{{!empty($unos->datumazuriranja) ? \Carbon\Carbon::parse($unos->datumazuriranja)->format('d.m.Y. H:m:s') : '-'}}</td>
                            <td>{{$unos->appKorisnik->nalog ?? "-"}}</td>
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
