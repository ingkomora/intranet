@if ($entry->documents->isNotEmpty())
    <a
        href="{{ url('/admin').'/request-external/'.$entry->getKey().'/document' }}"
        class="btn btn-sm btn-link border"
        data-toggle="tooltip"
        title="Dokumenta"
    >
        <i class="la la-2x fa-box-open text-primary"></i>
    </a>
@endif
