@if ($crud->hasAccess('promenapodatakaemail'))
    @if($entry->obradjen === backpack_user()->id)
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/promenapodatakaemail') }}"
            class="btn btn-sm btn-link"
            data-toggle="tooltip"
            title="Izmeni imejl adresu"
        >
            <i class="la la-2x la-pencil"></i>
        </a>
    @elseif($entry->obradjen === backpack_user()->id + 100 OR $entry->obradjen === backpack_user()->id + 200)
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/promenapodatakaemail') }}"
            class="btn btn-sm btn-link"
            data-toggle="tooltip"
            title="Izmeni imejl adresu"
        >
            <i class="la la-2x la-pencil"></i>
        </a>
    @elseif($entry->obradjen === backpack_user()->id + 200)
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/promenapodatakaemail') }}"
            class="btn btn-sm btn-link"
            data-toggle="tooltip"
            title="Izmeni imejl adresu"
        >
            <i class="la la-2x la-pencil"></i>
        </a>
    @endif
@endif
