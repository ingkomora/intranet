@if (
    $crud->hasAccess('registardelation')
    && $entry->status_id == REQUEST_IN_PROGRESS
    && in_array($entry->request_category_id, [11, 14])
    && $entry->osoba->licence->whereIn('status', ['A', 'N'])->isNotEmpty()
    )
    <a
        href="{{ url($crud->route.'/'.$entry->getKey().'/registar-delation') }}"
        class="btn btn-sm btn-link text-danger">
        <i class="la la-sign-out-alt"></i> Obriši iz Registra
    </a>
@endif
