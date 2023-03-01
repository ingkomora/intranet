@if ($crud->hasAccess('updatelicencastatus') AND $entry->licence->isNotEmpty())
    <a
        href="javascript:void(0)"
        onclick="updateLicencaStatus(this)"
        data-route="{{ url($crud->route.'/'.$entry->getKey().'/updatelicencastatus') }}"
        class="btn btn-sm btn-link"
        data-toggle="tooltip"
        title="Ažuriraj statuse licenci"
    >
        <i class="la la-2x la-certificate"></i>
    </a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
    if (typeof updateLicencaStatus != 'function') {
        $("[data-button-type=clone]").unbind('click');

        function updateLicencaStatus(button) {
            // ask for confirmation before deleting an item
            // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            $.ajax({
                url: route,
                type: 'POST',
                success: function (result) {
                    console.log(result);
                    var message = '<strong>Provera uspešno izvršena!</strong><br><strong>Osoba: </strong>' + result.osoba + '<br>';
                    $.each(result.licence, function (key, value) {
                        message += '<strong>' + key.substr(0, 1).toUpperCase() + key.substr(1) + '</strong>: ' + value + '<br>';
                    })

                    // Show an alert with the result
                    new Noty({
                        type: "success",
                        timeout: 10000,
                        progressBar: true,
                        text: message
                    }).show();

                    // Hide the modal, if any
                    $('.modal').modal('hide');

                    if (typeof crud !== 'undefined') {
                        crud.table.ajax.reload();
                    }
                },
                error: function (result) {
                    // Show an alert with the result
                    new Noty({
                        type: "error",
                        text: "<strong>Greška!</strong><br>Neuspešna provera i ažuriranje statusa licenci"
                    }).show();
                }
            });
        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('cloneEntry');
</script>
@if (!request()->ajax()) @endpush @endif
