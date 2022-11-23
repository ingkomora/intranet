@if ($crud->hasAccess('createrequeststopmembership') && $crud->get('list.bulkActions'))
    <a
        href="javascript:void(0)"
        onclick="createRequestStopMembershipBulkEntries(this)"
        class="btn btn-warning bulk-button"
    >
        <span class="ladda-label">
            <i class="las la-sign-out-alt"></i> Kreiraj zahtev za brisanje
        </span>
    </a>
@endif

@push('after_scripts')
    <script>
        if (typeof createRequestStopMembershipBulkEntries != 'function') {
            function createRequestStopMembershipBulkEntries(button) {
                if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length === 0) {
                    new Noty({
                        type: "warning",
                        text: "<strong>Nije označena ni jedna stavka.</strong><br>Molimo Vas da odaberete jednu ili više stavki."
                    }).show();

                    return;
                }

                var message = "Da li ste sigurni da želite da kreirate zahtev za prekid članstva i brisanje iz evidencije za označene stavke?\n\nBroj označenih stavki: :number\n:items";
                var items = '';
                $.each(crud.checkedItems, function (key, id) {
                    items += id + ', '
                })
                message = message.replace(":number", crud.checkedItems.length);
                message = message.replace(":items", items);


                // show confirm message
                swal({
                    title: "{{ trans('backpack::base.warning') }}",
                    text: message,
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "{{ trans('backpack::crud.cancel') }}",
                            value: null,
                            visible: true,
                            className: "bg-secondary",
                            closeModal: true,
                        },
                        ok: {
                            text: "Ok",
                            value: true,
                            visible: true,
                            className: "bg-primary",
                        }
                    },
                }).then((value) => {

                    if (value) {

                        var ajax_calls = [];

                        $.ajax({
                            url: "{{ url($crud->route) }}/create-request-stop-membership",
                            type: 'POST',
                            data: {entries: crud.checkedItems,},

                            success: function (result) {

                                // console.log(JSON.stringify(result))

                                // Show an alert with the result
                                new Noty({
                                    type: "primary",
                                    text: "<strong>Ukupan broj označenih stavki:</strong> " + crud.checkedItems.length
                                }).show();

                                if (result['not_affected']) {
                                    let errors = "<strong>Zahtevi za prekid članstva nisu kreirani za: </strong> <br> ";

                                    let message = "<br>";
                                    $.each(result['not_affected'], function (id, message) {
                                        errors += message + ", ";
                                    });

                                    // show an alert
                                    new Noty({
                                        type: "error",
                                        timeout: 0,
                                        closeWith: ['click'],
                                        text: errors
                                    }).show();


                                } else if (result['affected']) {

                                    // let message = "<br>";
                                    // let counter = 0;
                                    //
                                    // $.each(result['affected'], function (id, value) {
                                    //     counter++;
                                    //     message += value + ", ";
                                    // })
                                    //
                                    // // Show an alert with the result
                                    // new Noty({
                                    //     type: 'success',
                                    //     layout: 'center',
                                    //     timeout: false,
                                    //     closeWith: ['click'],
                                    //     text: "Broj uspešno obrađenih stavki: " + counter + message,
                                    // }).show();

                                }

                                crud.checkedItems = [];
                                crud.table.ajax.reload();
                            },
                            error: function (result) {
                                new Noty({
                                    type: "danger",
                                    text: "<strong>ajax error</strong>",
                                }).show();
                                // Show an alert with the result
                            }
                        });

                    }
                });
            }
        }
    </script>
@endpush
