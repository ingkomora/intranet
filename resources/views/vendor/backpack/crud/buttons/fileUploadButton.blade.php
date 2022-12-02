@if (($crud->hasAccess('fileUpload')))
    <a
        href="{{ url($crud->route.'/file-upload') }}"
        class="btn btn-link text-danger"
    >
            <span class="ladda-label">
            <i class="las la-file-import text-danger"></i> File Upload
        </span>
    </a>
@endif
