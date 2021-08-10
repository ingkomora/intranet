@extends('layouts.print')

@section('title', 'Prijava za polaganje stručnog ispita')

@section('content')
    <div>
        <div id="header">
            <div id="barcode">
                {!! $barcode1 !!}
            </div>
            <div id="naslov">PRIJAVA ZA POLAGANJE STRUČNOG ISPITA</div>
            <div>Datum: <strong>{{date("d.m.Y.", strtotime($prijava->updated_at))}}</strong></div>
            <div>Broj prijave: <strong>{{$prijava->id}}</strong></div>
            <hr style="width: 8cm; margin-left: 0;">
        </div>
        <h3 id="naslov">MINISTARSTVO GRAĐEVINARSTVA,<br>SAOBRAĆAJA I INFRASTRUKTURE</h3>
        <div id="adresa">11000 Beograd<br>Nemanjina 22-26</div>
        <div id="predmet"><em><strong>PREDMET:</strong> Prijava za polaganje stručnog ispita i dobijanja licence</em></div>
        <div id="zakon">U skladu sa članom 161. Zakona o planiranju i izgradnji („Službeni glasnik RS”, br. 72/09, 81/09 – ispravka, 64/10 – US, 24/11, 121/12, 42/13 – US, 50/13 – US, 98/13 – US, 132/14, 145/14, 83/18, 31/19, 37/19 – dr. zakon i 9/2020) i članom 10. Pravilnika o polaganju stručnog ispita u oblasti prostornog i urbanističkog planiranja, izrade tehničke dokumentacije, građenja i energetske efikasnosti, kao i licencama za prostornog planera, urbanistu, arhitektu urbanistu, inženjera, arhitektu, pejzažnog arhitektu i izvođača i registrima licenciranih lica („Službeni glasnik RSˮ, br. 2/2021) podnosim prijavu za polaganje stručnog ispita:</div>
        <br>
        <div id="ispit">
            <hr>
            <table>
                <tr>
                    <td style="font-weight: bold" class="pdf-first-column-width">ZVANJE:</td>
                    <td>{{$prijava->zvanje->naziv}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold" class="pdf-first-column-width">VRSTA STRUČNIH POSLOVA:</td>
                    <td>{{$prijava->vrstaPosla->naziv}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold" class="pdf-first-column-width">STRUČNA OBLAST:</td>
                    <td>{{$prijava->regOblast->naziv}}</td>
                </tr>
                @if(!in_array($prijava->regPodOblast->id,[1,4,21,29]))
                    {{--                @if(!in_array($prijava->regOblast->id,[1,3,8]))--}}
                    <tr>
                        <td style="font-weight: bold" class="pdf-first-column-width">UŽA STRUČNA OBLAST:</td>
                        <td>{{$prijava->regPodOblast->naziv}}</td>
                    </tr>
                @endif
                <tr>
                    <td style="font-weight: bold" class="pdf-first-column-width">VRSTA LICENCE:</td>
                    <td>{{$vrsta_licence}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold" class="pdf-first-column-width">VRSTA ISPITA:</td>
                    <td>{{$prijava->siVrsta->naziv}}</td>
                </tr>
                @if($prijava->si_vrsta_id != "4")
                    <tr>
                        <td style="font-weight: bold" class="pdf-first-column-width">TEMA:</td>
                        <td>{{$prijava->tema}}</td>
                    </tr>
                @endif
            </table>
            <hr>
        </div>

        <h4>Podaci o podnosiocu prijave</h4>

        <table>
            <tr>
                <td class="pdf-first-column-width">Matični broj građana - JMBG</td>
                <td>{{$prijava->osoba->id}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Ime (roditelj) Prezime</td>
                <td>{{$prijava->osoba->ime}} ({{$prijava->osoba->roditelj}}) {{$prijava->osoba->prezime}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Datum rođenja</td>
                <td>{{date("d.m.Y.", strtotime($prijava->osoba->datumrodjenja))}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Mesto i država rođenja</td>
                <td>{{$prijava->osoba->rodjenjemesto}}@if($prijava->osoba->rodjenjeopstina)(opština {{$prijava->osoba->rodjenjeopstina}}) @endif, {{$prijava->osoba->rodjenjedrzava}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Zvanje</td>
                <td>{{$prijava->zvanje->naziv}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Kontakt telefoni</td>
                <td>{{$prijava->osoba->mobilnitel}}@if($prijava->osoba->kontakttel), {{$prijava->osoba->kontakttel}} @endif</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Privatna adresa elektronske pošte</td>
                <td>{{$prijava->osoba->kontaktemail}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Poštanski broj i mesto</td>
                <td>{{$prijava->osoba->prebivalistebroj}} {{$prijava->osoba->prebivalistemesto}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Opština</td>
                <td>{{$prijava->osoba->opstinaId->ime}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Adresa</td>
                <td>{{$prijava->osoba->prebivalisteadresa}}</td>
            </tr>
        </table>

        <h4>Podaci o @if(($prijava->osoba->zaposlen == "1")) trenutnom @else prethodnom @endif zaposlenju</h4>

        <table>
            <tr>
                <td class="pdf-first-column-width">Naziv preduzeća</td>
                <td>{{$prijava->osoba->firmanaziv}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Opština i mesto</td>
                <td>{{$prijava->osoba->firmaopstina}}, {{$prijava->osoba->firmamesto}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Vrsta poslova</td>
                <td>{{$prijava->osoba->vrsta_poslova}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Godine radnog iskustva</td>
                <td>{{$prijava->osoba->godine_radnog_iskustva}}</td>
            </tr>
            @if($prijava->osoba->firmatel)
                <tr>
                    <td class="pdf-first-column-width">Kontakt telefon na poslu</td>
                    <td>{{$prijava->osoba->firmatel}}</td>
                </tr>
            @endif
            @if($prijava->osoba->firmaemail)
                <tr>
                    <td class="pdf-first-column-width">Adresa elektronske pošte na poslu</td>
                    <td>{{$prijava->osoba->firmaemail}}</td>
                </tr>
            @endif
        </table>

        <h4 class="section">Podaci o obrazovanju</h4>

        @if($prijava->osoba->mrfakultet)
            <table>
                <tr>
                    <th style="width: 8cm">Master studije</th>
                    <th></th>
                </tr>
                <tr>
                    <td class="pdf-first-column-width">Naziv obrazovne ustanove</td>
                    <td>{{$prijava->osoba->mrfakultet}}</td>
                </tr>
                <tr>
                    <td class="pdf-first-column-width">Mesto i država</td>
                    <td>{{$prijava->osoba->mrmesto}}, {{$prijava->osoba->mrdrzava}}</td>
                </tr>
                <tr>
                    @if(empty($prijava->osoba->mrodsek))
                        <td class="pdf-first-column-width">Smer (studijski program)</td>
                        <td>{{$prijava->osoba->mrsmer}}</td>
                    @else
                        <td class="pdf-first-column-width">Odsek/Smer (studijski program)</td>
                        <td>{{$prijava->osoba->mrodsek}} / {{$prijava->osoba->mrsmer}}</td>
                    @endif
                </tr>
                <tr>
                    <td class="pdf-first-column-width">Godina završetka</td>
                    <td>{{$prijava->osoba->mrgodina}}</td>
                </tr>
                <tr>
                    <td class="pdf-first-column-width">Broj diplome</td>
                    <td>{{$prijava->osoba->mrbroj}}</td>
                </tr>
            </table>
        @endif
        <table>
            <tr>
                <th class="pdf-first-column-width">Osnovne studije</th>
                <th></th>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Naziv obrazovne ustanove</td>
                <td>{{$prijava->osoba->diplfakultet}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Mesto i država</td>
                <td>{{$prijava->osoba->diplmesto}}, {{$prijava->osoba->dipldrzava}}</td>
            </tr>
            <tr>
                @if(empty($prijava->osoba->diplodsek))
                    <td class="pdf-first-column-width">Smer</td>
                    <td>{{$prijava->osoba->diplsmer}}</td>
                @else
                    <td class="pdf-first-column-width">Odsek/Smer (studijski program)</td>
                    <td>{{$prijava->osoba->diplodsek}} / {{$prijava->osoba->diplsmer}}</td>
                @endif
            </tr>
            <tr>
                <td class="pdf-first-column-width">Godina završetka</td>
                <td>{{$prijava->osoba->diplgodina}}</td>
            </tr>
            <tr>
                <td class="pdf-first-column-width">Broj diplome</td>
                <td>{{$prijava->osoba->diplbroj}}</td>
            </tr>
        </table>

        @include('pdf.pdf'.$prijava->vrsta_posla_id)

        <div class="section no-break">
            <h4>Uz prijavu dostavljam</h4>
            <ol class="dostavljam ">
                @if($prijava->si_vrsta_id == "4")
                    <li>Kopiju putne isprave i prijavu boravišta</li>
                    <li>Dokaz o priznavanju strane visokoškolske isprave</li>
                    <li>Dokaz o upisu u registar nadležnog tela odnosno kopiju licence ili drugog ovlašćenja</li>
                @else
                    <li>Fotokopiju lične karte ili očitanu ličnu kartu</li>
                    <li>Izvod iz matične knjige rođenih</li>
                    <li>Overenu fotokopiju diplome osnovnih akademskih, odnosno strukovnih studija i overen dodatak diplomi</li>
                    @if($prijava->osoba->mrfakultet)
                        <li>Overenu fotokopiju diplome master akademskih, odnosno strukovnih studija i overen dodatak diplomi</li>
                    @endif
                    {{--
                                        @if($prijava->strucni_rad === 1)
                                            <li>Stručni rad sa potvrdom o učešću u izradi stručnog rada (u slučaju kada Kandidat uz prijavu dostavlja stručni rad) potpisanu od strane licenciranog lica koje je rukovodilo izradom planskog dokumenta ili urbanističkog projekta, projekta za građevinsku dozvolu, projekta za izvođenje ili elaborata i studije</li>
                                        @endif
                    --}}
                @endif
                <li>Potvrdu poslodavca o ostvarenom stručnom iskustvu</li>
                <li>Potvrdu licenciranog lica</li>
                @if(in_array($prijava->osoba->zvanje, ARHITEKTE))
                    <li>Potvrdu pravnog lica ili preduzetnika</li>
                @endif
            </ol>
        </div>
        <div>
            <table id="potpis">
                <tr style="height: 50px; padding-top: 0;">
                    <td style="width: 70%;"></td>
                    <td style="height: 50px; text-align: center; font-weight: bold;">PODNOSILAC PRIJAVE</td>
                </tr>
                <tr style="height: 50px; padding-top: 0">
                    <td style="width: 30%">U __________________________, {{date("d.m.Y.")}}</td>
                    <td>________________________________</td>
                </tr>
            </table>
        </div>

        <div id="napomena">
            <span id="napomenaText"><b>NAPOMENA:</b><br>Odštampanu prijavu potpisati i zajedno sa ostalom dokumentacijom predati lično na pisarnicu ili poslati poštom na adresu Ministarstva građevinarstva, saobraćaja i infrastrukture, Nemanjina 22-26, Beograd</span>
        </div>
    </div>
    {{--    @include('si.pdfpotvrda'.$prijava->vrsta_posla_id)--}}
@endsection
