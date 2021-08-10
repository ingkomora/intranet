<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            Osobe:
            @foreach($entry->osobe as $osoba)
                <p>
                    <a href="http://www.ingkomora.rs/clanovi/registar_pretraga.php?lib={{$osoba->lib}}" target="_blank" title="Proveri osobu u Registru" class="pr-3">
                        <i class="nav-icon la la-book-open"></i> registar
                    </a>
                    JMBG: {{$osoba->id}}, LIB: {{$osoba->lib}}, <strong>{{$osoba->ime}} {{$osoba->prezime}}</strong>, {{$osoba->zvanjeId->naziv}}</p>
            @endforeach
        </div>
    </div>
</div>
<div class="clearfix"></div>
