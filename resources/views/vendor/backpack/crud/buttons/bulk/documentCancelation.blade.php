@if ($crud->hasAccess('documentcancelation') && $crud->get('list.bulkActions'))
    <a
        href="javascript:void(0)"
        onclick="cancelBulkEntries(this)"
        class="btn bulk-button border"
    >
        <span class="ladda-label">
            <i class="las la-ban text-danger"></i> Storno
        </span>
    </a>
@endif

@push('after_scripts')
    <script>
        if (typeof cancelBulkEntries != 'function') {
            function cancelBulkEntries(button) {
                if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length === 0) {
                    new Noty({
                        type: "warning",
                        text: "<strong>Nije označen ni jedan dokument.</strong><br>Molimo Vas da odaberete jedann ili više dokumenata koje želite da stornirate."
                    }).show();

                    return;
                }

                let url = "{{ url($crud->route) }}";
                // var message = "Da li ste sigurni da želite da stornirate označene stavke?\nBroj označenih stavki: :number\n:items";
                var message = "Da li želite da stornirate označene stavke?\nBroj označenih stavki: :number\n:items";
                var items = '';
                $.each(crud.checkedItems, function (key, id) {
                    items += id + ','
                })
                message = message.replace(":number", crud.checkedItems.length);
                message = message.replace(":items", items);


                let el = document.createElement('div');

                el.innerHTML += '<div class="form-check">' +
                    '<input class="form-check-input" type="checkbox" value="1" id="cancelRequest" name="cancelRequest">' +
                    '<label class="form-check-label" for="cancelRequest">Otkaži zahtev </label>' +
                    '</div>';


                // show confirm message
                swal({
                    title: "{{ trans('backpack::base.warning') }}",
                    text: message,
                    content: el,
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "{{ trans('backpack::crud.cancel') }}",
                            value: null,
                            visible: true,
                            className: "bg-secondary",
                            closeModal: true,
                        },
                        zavedi: {
                            text: "Storno",
                            value: true,
                            visible: true,
                            className: "bg-danger",
                        }
                    },
                }).then((value) => {
                    // swal('You typed:' + input.value);
                    if (value) {
                        var ajax_calls = [];
                        let data = {
                            entries: crud.checkedItems,
                            route: '{{$crud->route}}',
                            cancelRequest: $('input#cancelRequest:checked').val(),
                        }
                        // submit an AJAX delete call
                        $.ajax({
                            url: "{{ url($crud->route) }}/document-cancelation",
                            type: 'POST',
                            data: data,

                            success: function (result) {

                                if (result['error']) {
                                    let errors = "<strong>Greška!:</strong><br><br> ";
                                    $.each(result['error'], function (id, message) {
                                        errors += message + ', ';
                                    });
                                    errors = errors.substring(0, errors.length - 2);

                                    new Noty({
                                        type: "error",
                                        timeout: 0,
                                        closeWith: ['click'],
                                        text: errors
                                    }).show();

                                } else if (result['info']) {
                                    var message = "<strong>Uspešno!</strong> " + result['info'].length;

                                    new Noty({
                                        type: "success",
                                        timeout: 0,
                                        closeWith: ['click'],
                                        text: message
                                    }).show();
                                }

                                crud.checkedItems = [];
                                crud.table.ajax.reload();
                            },
                            error: function (result) {
                                new Noty({
                                    type: "danger",
                                    text: "<strong>Broj označenih stavki koje nisu ažurirane:</strong> " + result.length
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
