<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('osoba') }}'><i class='nav-icon fa fa-user'></i> Osobe</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('firma') }}'><i class='nav-icon fa fa-building'></i> Firme</a></li>
@role('admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('licenca') }}'><i class='nav-icon la la-question'></i> Licence</a></li>
{{--<li class='nav-item'><a class='nav-link' href='{{ backpack_url('tag') }}'><i class='nav-icon fa fa-question'></i> Tags</a></li>--}}
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('zahtev') }}'><i class='nav-icon la la-question'></i> Zahtevi</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('osiguranje') }}'><i class='nav-icon fa fa-building'></i> Osiguranja</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('prijava') }}'><i class='nav-icon fa fa-address-book'></i> Prijave SI</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('zvanje') }}'><i class='nav-icon fa fa-question'></i> Zvanja</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('regoblast') }}'><i class='nav-icon fa fa-question'></i> RegOblast</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('regpodoblast') }}'><i class='nav-icon fa fa-question'></i> RegPodOblast</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('sivrsta') }}'><i class='nav-icon fa fa-question'></i> SiVrste</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}\"><i class="nav-icon la la-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>
<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
@endrole

