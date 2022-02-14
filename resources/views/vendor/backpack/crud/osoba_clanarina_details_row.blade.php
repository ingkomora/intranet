<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>
                <strong>{{$entry->ime ?? ''}} {{$entry->prezime ?? ''}}</strong>, {{$entry->zvanjeId->naziv ?? ''}}
                <br>
                <strong>Članstvo:</strong> {{$entry->clan == 1 ? $entry->clan = "Član IKS" : "Nije član IKS"}}
                <br>
                <strong>Napomena:</strong> {{!empty($entry->napomena) ? $entry->napomena : "Nema napomene"}}
                <br>
                <b>LIB:</b>
                @if($entry->lib)
                    <a
                        href="http://www.ingkomora.rs/clanovi/registar_pretraga.php?lib={{$entry->lib ?? 'nema lib'}}"
                        target="_blank">{{$entry->lib}}</a>
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
