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
                        <th scope="col">rok za naplatu</th>
                        <th scope="col">iznos za naplatu</th>
                        <th scope="col">iznos uplate</th>
                        <th scope="col">pretplata</th>
                        <th scope="col">datum uplate</th>
                        @role('admin')
                        <th scope="col">datumazuriranja_admin</th>
                        <th scope="col">azurirao_admin</th>
                        @endrole
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
                            @role('admin')
                            <td>{{$unos->datumazuriranja_admin}}</td>
                            <td>{{$unos->azurirao_admin}}</td>
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
