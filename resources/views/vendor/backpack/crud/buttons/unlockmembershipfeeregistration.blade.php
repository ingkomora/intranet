@if ($crud->hasAccess('unlockmembershipfeeregistration') AND $entry->clan == 10)
    <a href="javascript:void(0)" onclick="unlockMembershipFeeRegistration(this)" data-route="{{ url($crud->route.'/'.$entry->getKey().'/unlockmembershipfeeregistration') }}" class="btn btn-sm btn-link" data-button-type="clone"><i class="la la-2x la-money" style="vertical-align:middle;"></i> Otključaj članarinu</a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
    if (typeof unlockMembershipFeeRegistration != 'function') {
        $("[data-button-type=clone]").unbind('click');

        function unlockMembershipFeeRegistration(button) {
            // ask for confirmation before deleting an item
            // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            $.ajax({
                url: route,
                type: 'POST',
                success: function (result) {
                    // console.log(result.message);
                    var message = result.message;

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
                        text: result.message
                    }).show();
                }
            });
        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('cloneEntry');
</script>
@if (!request()->ajax()) @endpush @endif