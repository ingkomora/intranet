@extends('layouts.print')
<h4>Podaci o referencama - Stručni poslovi urbanističkog planiranja</h4>

@foreach ($prijava->reference as $r)

    <h5>REFERENCA {{$loop->iteration}} </h5>
    <table border="1" cellspacing="0" cellpadding="2">
        <tr>
            <td style="width: 8cm">Tačan (potpun) naziv urbanističkog plana u čijoj izradi je podnosilac zahteva učestvovao/la, sa kratkim opisom plana, obuhvatom plana i navedenim službenim glasilom gde je plan objavljen</td>
            <td>{{$r->naziv}}</td>
        </tr>
        <tr>
            <td>Vrsta urbanističkog plana</td>
            <td>{{$r->vrstaPlana->naziv}}</td>
        </tr>
        <tr>
            <td>Period izrade plana</td>
            <td>{{$r->godinaizrade}}</td>
        </tr>
        <tr>
            <td>Godina donošenja plana</td>
            <td>{{$r->godinausvajanja}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa pravnog lica koje je izradilo plan</td>
            <td>{{$r->firma}}</td>
        </tr>
        <tr>
            <td>Uloga podnosioca zahteva u izradi plana</td>
            <td>{{$r->ulogaId->naziv}}</td>
        </tr>
        <tr>
            <td>Odgovorni urbanista na predmetnom planu</td>
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
