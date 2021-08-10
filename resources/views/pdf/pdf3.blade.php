@extends('layouts.print')

<h4>Podaci o referencama - Stručni poslovi izrade tehničke dokumentacije</h4>
@foreach ($prijava->reference as $r)

    <h5>REFERENCA {{$loop->iteration}} </h5>
    <table border="1" cellspacing="0" cellpadding="2">
        <tr>
            <td style="width: 8cm">Naziv i opis projekta (vrsta projekta) sa podacima o objektu (kategorija, klasa, namena, površina, spratnost i dr.) za koji je izrađen projekat koji se navodi kao referenca - u zavisnosti od stručne, odnosno uže stručne oblasti za koju se polaže stručni ispit</td>
            <td>{{$r->naziv}}</td>
        </tr>
        <tr>
            <td>Lokacija projektovanog objekta (adresa/kat.parc./k.o.)</td>
            <td>{{$r->lokacijamesto}}, {{$r->lokacijaopstina}}, {{$r->lokacijadrzava}} / {{$r->lokacijaadresa}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa investitora projektovanog objekta</td>
            <td>{{$r->investitor}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa pravnog lica/preduzetnika koje je izradilo tehničku dokumentaciju/projekat</td>
            <td>{{$r->firma}}</td>
        </tr>
        <tr>
            <td>Period izrade projekta</td>
            <td>{{$r->godinaizrade}}</td>
        </tr>
        <tr>
            <td>Uloga podnosioca zahteva u izradi tehničke dokumentacije</td>
            <td>{{$r->ulogaId->naziv}}</td>
        </tr>
        <tr>
            <td>Ime, prezime i broj licence imenovanog odgovornog projektanta za projekat koji se navodi kao referenca</td>
            @if($r->odgovorno_lice_licenca_id)
                <td>{{$r->licencaOdgovornoLice->osobaId->fullNameZvanje}} ({{$r->odgovorno_lice_licenca_id}})</td>
            @elseif($r->odgprojektant)
                <td>{{$r->odgovornolicestranac}} ({{$r->odgovornolicestranaclicenca}})</td>
            @else
                <td>Nema podataka</td>
            @endif
        </tr>
    </table>
@endforeach
