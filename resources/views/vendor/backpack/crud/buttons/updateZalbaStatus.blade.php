@if ($crud->hasAccess('updatezalbastatus') AND !in_array($entry->status_id, [REQUEST_CREATED,REQUEST_SUBMITED,REQUEST_CANCELED]))
    <a
        href="{{ url($crud->route.'/'.$entry->getKey().'/updatezalbastatus') }}"
        class="btn btn-sm btn-link"
        data-toggle="tooltip"
        title="Ažuriraj status žalbe"
    >
        <i class="las la-2x la-marker"></i>
    </a>
@endif
