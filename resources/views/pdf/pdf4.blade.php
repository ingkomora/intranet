@extends('layouts.print')
<h4>Podaci o referencama - Stručni poslovi građenja objekata, odnosno izvođenje radova</h4>

@foreach ($prijava->reference as $r)

    <h5>REFERENCA {{$loop->iteration}} </h5>
    <table border="1" cellspacing="0" cellpadding="2">
        <tr>
            <td style="width: 8cm">Naziv objekta, vrsta i opis radova sa osnovnim karakteristikama u zavisnosti od stručne, odnosno uže stručne oblasti</td>
            <td>{{$r->naziv}}</td>
        </tr>
        <tr>
            <td>Lokacija / tačna adresa objekta na kome su izvođeni radovi (Br. kat. parcele objekta na kome su izvođeni radovi:</td>
            <td>{{$r->lokacijamesto}}, {{$r->lokacijaopstina}}, {{$r->lokacijadrzava}} / {{$r->lokacijaadresa}}</td>
        </tr>
        <tr>
            <td>Period izvođenja radova</td>
            <td>{{$r->godinaizrade}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa investitora objekta</td>
            <td>{{$r->investitor}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa pravnog lica ili preduzetnika koje je izradilo radove</td>
            <td>{{$r->firma}}</td>
        </tr>
        <tr>
            <td>Uloga podnosioca zahteva u izvođenju radova</td>
            <td>{{$r->ulogaId->naziv}}</td>
        </tr>
        <tr>
            <td>Odgovorni izvođač radova na predmetnom objektu</td>
            @if($r->odgovorno_lice_licenca_id)
                <td>{{$r->licencaOdgovornoLice->osobaId->fullNameZvanje}} ({{$r->odgovorno_lice_licenca_id}})</td>
            @elseif($r->odgprojektant)
                <td>{{$r->odgovornolicestranac}} ({{$r->odgovornolicestranaclicenca}})</td>
            @else
                <td>>Nema podataka</td>
            @endif
        </tr>
    </table>
@endforeach
