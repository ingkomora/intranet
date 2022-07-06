@if ($crud->hasAccess('updatezalbastatus') AND !in_array($entry->status_id, [REQUEST_CREATED,REQUEST_SUBMITED,REQUEST_CANCELED]))
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/updatezalbastatus') }}" class="btn btn-sm btn-link"><i class="fa fa-list"></i> Ažuriraj Status žalbe</a>
@endif
