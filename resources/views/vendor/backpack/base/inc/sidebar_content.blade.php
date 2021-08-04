<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon fas fa-tachometer"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@role('korisnik|urednik|admin')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-book"></i>OSOBE</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('osoba') }}'> Osobe</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('osobasi') }}'> Osobe SI</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zvanje') }}'> Zvanja</a></li>

    </ul>
</li>
<!-- Users, Roles, Permissions -->
@endrole

@role('korisnik|urednik|admin')
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

@role('korisnik|urednik|admin')
<li class="divider">
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-certificate"></i>LICENCE</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('licenca') }}'> Licence</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zahtevlicenca') }}'> Zahtevi Licence</a></li>
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
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('prijavasistara') }}'> Prijave stare</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('sivrsta') }}'> Vrste ispita</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fas fa-handshake"></i>ČLANSTVO</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('prijavaclanstvo') }}'> Prijave</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('unesinoveclanove') }}'> Obradi zahteve</a></li>
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
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('status') }}'> Statusi</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('brojac') }}'> Brojaci</a></li>
        <li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('logstatusgrupa') }}'> Log Statusi Grupe</a></li>
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

<li class="divider">
<li class="nav-title">U PRIPREMI</li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('zahtevtip') }}'> Tipovi zahteva</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('test') }}'> Test</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('sikandidat') }}'> SiKandidati</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('delovodnik') }}'> Delovodnik</a></li>
<li class='nav-item'><a class='nav-link py-1 pl-5' href='{{ backpack_url('delovodnikorganizacionejedinice') }}'> Organizacione Jedinice</a></li>
<!-- Users, Roles, Permissions -->
@endrole
