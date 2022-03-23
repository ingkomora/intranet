<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon fas fa-tachometer"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@role('korisnik|urednik|admin|sluzba_rk')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-book"></i>OSOBE</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('osoba') }}'> Osobe</a></li>
        @role('admin|sluzba_rk')
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('osoba-edit') }}'> Ažuriranje adresa</a></li>
        @endrole
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zvanje') }}'> Zvanja</a></li>

    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole

@role('korisnik|urednik|admin|sluzba_rk')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-book"></i>REGISTAR</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='http://www.ingkomora.rs/clanovi/registar_pretraga.php' target="_blank"> App</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('osiguranje') }}'> Osiguranja</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('firma') }}'> Firme</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('regoblast') }}'> Stručne oblasti</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('regpodoblast') }}'> Uže stručne oblasti</a></li>
    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole

@role('korisnik|urednik|admin|sluzba_rk')
<li class="divider">
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-file-signature"></i>ZAHTEVI</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('request') }}'> Zahtevi</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('request-category') }}'> Kategorije zahteva</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('request-category-type') }}'> Tipovi kategorija zahteva</a></li>
    </ul>
</li>
@endrole

@role('korisnik|urednik|admin|sluzba_rk')
<li class="divider">
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-certificate"></i>LICENCE</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('licenca') }}'> Licence</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zahtevlicenca') }}'> Zahtevi licence</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('licenca-tip') }}'><i class='nav-icon la la-question'></i> Tipovi Licenci</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('unesi/obradizahtevsvecanaforma') }}'> Svečane forme</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('unesinovelicence') }}'> Unesi nove licence</a></li>
    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole

@role('korisnik|urednik|admin')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-file-signature"></i>STRUČNI ISPITI</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('siprijava') }}'> Prijave</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('sivrsta') }}'> Vrste ispita</a></li>
    </ul>
</li>
@endrole

@role('korisnik|urednik|admin|sluzba_rk')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-handshake"></i>ČLANSTVO</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('requestmembership') }}'> Zahtevi</a></li>
        @role('admin')
        {{--        <li class="nav-title">MIROVANJA</li>--}}
        {{--        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('clanstvo/mirovanja') }}'> Mirovanja</a></li>--}}
        {{--        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('clanstvo/obradamirovanja') }}'> Obrada zahteva</a></li>--}}
                <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('membership') }}'> Memberships</a></li>
        <li class="nav-title">PROMENA PODATAKA</li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('membership/promenapodataka') }}'> Promena podataka</a></li>
        @endrole
        {{--        @role('sluzba_rk')
                <li class="nav-title">PROMENA PODATAKA</li>
                <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('clanstvo/promenapodataka?active=true') }}'> Promena podataka</a></li>
                @endrole--}}
    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole
{{--backpack_user()->hasPermissionTo('kreiraj firmu')--}}
@role('admin|sluzba_opsta')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-book-open"></i>ZAVOĐENJE</a>
    <ul class="nav-dropdown-items">
        {{--        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zavodjenje/zavedi/si') }}'> Prijave za stručni ispit</a></li>--}}
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestlicence') }}'> Zahtevi za licence</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestsi') }}'> Prijave SI</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestclanstvo') }}'> Zahtevi članstvo</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestmirovanjeclanstva') }}'> Zahtevi mirovanje</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestsfl') }}'> Zahtevi za izdavanje svečane forme</a></li>
        <li class="divider">
        <li class="nav-title">Delovodnici</li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('document') }}'> Delovodnici</a></li>
        <li class="divider">
        @role('admin')
        <li class="nav-title">Admin</li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestpromenapodataka') }}'> Zahtevi za promenu podataka</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestresenjeclanstvo') }}'> Rešenja članstvo</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registerrequestregistar') }}'> Registar</a></li>
        @endrole
    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole

@role('admin')
<li class="divider">
<li class="nav-title">ADMIN</li>
<li class='nav-item'><a class='nav-link' href='/admin/log-viewer/logs'><i class="nav-icon fas fa-chart-pie"></i> APP LOG</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-tools"></i> RAZNO</a>
    <ul class="nav-dropdown-items">
        <li class="nav-title">Funkcioneri</li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('funkcioner') }}'> Funkcioneri</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('funkcioner-mandat') }}'> Mandati</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('funkcioner-mandat-tip') }}'> Tipovi mandata</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('funkcija') }}'> Funkcije</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('funkcija-tip') }}'> Tipovi funkcija</a></li>
        <li class="nav-title">Razno</li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('status') }}'> Statusi</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('logstatusgrupa') }}'> Log Statusi Grupe</a></li>
        {{--        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('brojac') }}'> Brojaci</a></li>--}}
        <li class="nav-item"><a class="nav-link py-1 pl-5" href="{{ backpack_url('elfinder') }}\"><i class="nav-icon la la-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-users"></i> AUTENTIKACIJA</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link py-1 pl-5" href="{{ backpack_url('user') }}"> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link py-1 pl-5" href="{{ backpack_url('role') }}"> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link py-1 pl-5" href="{{ backpack_url('permission') }}"> <span>Permissions</span></a></li>
    </ul>
</li>

<li class="nav-title">U PRIPREMI</li>
{{--<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zahtevtip') }}'> Tipovi zahteva</a></li>--}}
{{--<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('test') }}'> Test</a></li>--}}
{{--<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('sikandidat') }}'> SiKandidati</a></li>--}}
<!-- Users, Roles, Permissions -->

<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('document-category') }}'> Document categories</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('document-category-type') }}'>Document category types</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registry') }}'> Registries</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registry-department-unit') }}'> Registry Department Units</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('clanarina') }}'> Članarine</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('clanarina-old') }}'> Članarine Old</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registry-request-category') }}'> RegReqCat</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('registry-request-category') }}'> Registry request categories</a></li>
@endrole



