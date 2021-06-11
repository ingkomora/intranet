<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">
            {{--			{{ trans('backpack::crud.details_row') }}--}}
            <h4>{{$entry->osoba->ime}} {{$entry->osoba->prezime}}</h4>
            <p>Datum prijema:<strong>{{$entry->datum_prijema}}</strong>, zavodni broj:<strong>{{$entry->zavodni_broj}}</strong>, barcode: <strong>{{$entry->barcode}}</strong>, broj odluke UO: <strong>{{$entry->broj_odluke_uo}}</strong>, status: {{$entry->status_id}} ({{$entry->status->naziv}}), napomena: {{$entry->napomena}}</p>
            <h5>Zahtevi Licence:</h5>
            <ol>
                @foreach($entry->zahteviLicenceOsoba as $zahtevlicenca)
{{--                    {{dd($entry->with('zahteviLicence.licenca')->get()}}--}}
                <li><strong>{{$zahtevlicenca->id}} ({{$zahtevlicenca->status}} - {{$zahtevlicenca->statusId->naziv}})</strong>, tip:<strong>{{$zahtevlicenca->licencatip}}</strong>, broj licence: <strong>{{$zahtevlicenca->licenca_broj}}</strong>, Broj i datum rešenja: <strong>{{$zahtevlicenca->licenca_broj_resenja}}</strong> od <strong>{{$zahtevlicenca->licenca_datum_resenja}}</strong><br>
            <h5>Licenca:</h5>
{{--                    {{$zahtevlicenca->with('licenca')->get()}}--}}
                    <strong>{{$zahtevlicenca->licencaOsoba->id}} ({{$zahtevlicenca->licencaOsoba->status}})</strong>,
                    tip:<strong>{{$zahtevlicenca->licencaOsoba->licencatip}}</strong>, datumUO: <strong>{{$zahtevlicenca->licencaOsoba->datumuo}}</strong>, ažurirano: <strong>{{$zahtevlicenca->licencaOsoba->updated_at}}</strong>, kreirano: <strong>{{$zahtevlicenca->licencaOsoba->created_at}}</strong>
                </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
<div class="clearfix"></div>