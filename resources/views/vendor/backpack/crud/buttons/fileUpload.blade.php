@if (($crud->hasAccess('fileUpload')))
    <a
        href="{{ url($crud->route.'/file-upload') }}"
        class="btn btn-link text-danger"
        data-toggle="tooltip"
        title="Operacije za učitane datoteke"
    >
            <span class="ladda-label">
            <i class="las la-2x la-file-upload text-danger"></i>
        </span>
    </a>
@endif
