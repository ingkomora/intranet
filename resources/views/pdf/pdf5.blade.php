@extends('layouts.print')
<h4>Podaci o referencama - Stručni poslovi izrade Elaborata energetske efikasnosti i energetska sertifikacija zgrada</h4>

@foreach ($prijava->reference as $r)

    <h5>REFERENCA {{$loop->iteration}} </h5>
    <table border="1" cellspacing="0" cellpadding="2">
        <tr>
            <td style="width: 8cm">Tačan (potpun) naziv projekta za koji je izrađen Elaborat energetske efikasnosti, odnosno  za koji je izvršena energetska sertifikacija, sa opisom projekta u čijoj izradi je podnosilac zahteva učestvovao</td>
            <td>{{$r->naziv}}</td>
        </tr>
        <tr>
            <td>Lokacija zgrade za koju je izrađen Elaborat energetske efikasnosti, odnosno za koju je izvršena energetska sertifikacija (adresa/kat.parc./k.o.)</td>
            <td>{{$r->lokacijamesto}}, {{$r->lokacijaopstina}}, {{$r->lokacijadrzava}} / {{$r->lokacijaadresa}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa investitora zgrade</td>
            <td>{{$r->investitor}}</td>
        </tr>
        <tr>
            <td>Naziv i adresa pravnog lica/preduzetnika u okviru koga je izrađen Elaborat energetske efikasnosti i izvršena energetska sertifikacija</td>
            <td>{{$r->firma}}</td>
        </tr>
        <tr>
            <td>Period izrade Elaborata energetske efikasnosti i energetske sertifikacije</td>
            <td>{{$r->godinaizrade}}</td>
        </tr>
        <tr>
            <td>Uloga podnosioca zahteva u izradi Elaborata energetske efikasnosti / energetskoj sertifikaciji</td>
            <td>{{$r->ulogaId->naziv}}</td>
        </tr>
        <tr>
            <td>Ime, prezime i broj licence odgovornog inženjera za energetsku efikasnost zgrada</td>
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
