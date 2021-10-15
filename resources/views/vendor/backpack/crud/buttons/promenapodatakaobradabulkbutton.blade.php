@if ($crud->hasAccess('promenapodatakaobradabulk') && $crud->get('list.bulkActions'))
    <a
        href="javascript:void(0)"
        onclick="promenaPodatakaObradaBulkEntries(this)"
        class="btn btn-primary bulk-button"
    >
        <span class="ladda-label">
            <i class="la la-clone"></i> Obrada
        </span>
    </a>
@endif

@push('after_scripts')
    <script>
        if (typeof promenaPodatakaObradaBulkEntries != 'function') {
            function promenaPodatakaObradaBulkEntries(button) {

                if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0) {
                    new Noty({
                        type: "warning",
                        text: "<strong>Nije označena ni jedna stavka.</strong><br>Molimo Vas da odaberete jednu ili više stavki koje želite da obradite."
                    }).show();

                    return;
                }

                var message = "Da li ste sigurni da želite obradite označene stavke?\nBroj oznaćenih stavki: :number";
                message = message.replace(":number", crud.checkedItems.length);

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
                        delete: {
                            text: "Obrada",
                            value: true,
                            visible: true,
                            className: "bg-primary",
                        }
                    },
                }).then((value) => {
                    if (value) {
                        var ajax_calls = [];
                        var promena_podataka_email_obrada_route = "{{ url($crud->route) }}/promenapodatakaobradabulk";

                        // submit an AJAX delete call
                        $.ajax({
                            url: promena_podataka_email_obrada_route,
                            type: 'POST',
                            data: {entries: crud.checkedItems},
                            success: function (result) {
                                // console.log(result);
                                // Show an alert with the result
                                new Noty({
                                    type: "primary",
                                    text: "<strong>Ukupan broj označenih stavki:</strong> " + crud.checkedItems.length
                                }).show();
                                if (result['ok']) {
                                    new Noty({
                                        type: "success",
                                        text: "<strong>Broj uspešno obrađenih stavki:</strong> " + result['ok'].length
                                    }).show();
                                }
                                if (result['nok']) {
                                    new Noty({
                                        type: "warning",
                                        text: "<strong>Broj obrađenih stavki koje nisu ažurirane:</strong> " + result['nok'].length
                                    }).show();
                                }

                                crud.checkedItems = [];
                                crud.table.ajax.reload();
                            },
                            error: function (result) {
                                // Show an alert with the result
                                new Noty({
                                    type: "danger",
                                    text: "<strong>Obrada nije uspela</strong><br>Jedna ili više stavki nije obrađena. Molimo Vas da pokušate ponovo."
                                }).show();
                            }
                        });
                    }
                });
            }
        }
    </script>
@endpush
