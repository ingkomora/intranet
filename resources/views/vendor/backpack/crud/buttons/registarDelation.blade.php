@if (
    $crud->hasAccess('registardelation')
    && $entry->status_id == REQUEST_IN_PROGRESS
    && in_array($entry->request_category_id, [11, 14])
    && $entry->osoba->licence->whereIn('status', ['A', 'N'])->isNotEmpty()
    )
    <a
        href="{{ url($crud->route.'/'.$entry->getKey().'/registar-delation') }}"
        class="btn btn-sm btn-link"
        data-toggle="tooltip"
        title="Obriši iz Registra"
    >
        <i class="las la-2x la-user-minus"></i>
    </a>
@endif
