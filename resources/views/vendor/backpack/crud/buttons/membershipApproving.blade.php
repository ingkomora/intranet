@if (($crud->hasAccess('membershipapproving') && $entry->status_id == REQUEST_IN_PROGRESS))
    <a
        href="{{ url($crud->route.'/'.$entry->getKey().'/membershipapproving') }}"
        class="btn btn-sm btn-link"
        data-toggle="tooltip"
        title="Odobri članstvo"
    >
        <i class="la la-2x la-user"></i>
    </a>
@endif
