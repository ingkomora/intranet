@if (($crud->hasAccess('membershipapproving') && $entry->status_id == REQUEST_IN_PROGRESS))
        <a
            href="{{ url($crud->route.'/'.$entry->getKey().'/membershipapproving') }}"
            class="btn btn-sm btn-link">
            <i class="la la-user"></i> Odobri članstvo
        </a>
@endif
