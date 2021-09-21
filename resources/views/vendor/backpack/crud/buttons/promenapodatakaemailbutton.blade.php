@if ($crud->hasAccess('promenapodatakaemail'))
    @if($entry->obradjen === backpack_user()->id)
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/promenapodatakaemail') }}"
            class="btn btn-sm btn-link">
            <i class="la la-pencil"></i> Obradi
        </a>
    @elseif($entry->obradjen === backpack_user()->id + 100)
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/promenapodatakaemail') }}"
            class="btn btn-sm btn-link">
            <i class="la la-pencil"></i> Izmeni
        </a>
    @endif
@endif
