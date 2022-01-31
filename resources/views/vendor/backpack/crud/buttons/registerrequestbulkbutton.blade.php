@if ($crud->hasAccess('registerrequestbulk') && $crud->get('list.bulkActions'))
    <a
        href="javascript:void(0)"
        onclick="requestZavodjenjeBulkEntries(this)"
        class="btn btn-primary bulk-button"
    >
        <span class="ladda-label">
            <i class="la la-clone"></i> Zavođenje
        </span>
    </a>
@endif

@push('after_scripts')
    <script>
        if (typeof requestZavodjenjeBulkEntries != 'function') {
            function requestZavodjenjeBulkEntries(button) {

                if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0) {
                    new Noty({
                        type: "warning",
                        text: "<strong>Nije označena ni jedna stavka.</strong><br>Molimo Vas da odaberete jednu ili više stavki koje želite da obradite."
                    }).show();

                    return;
                }

                var message = "Da li ste sigurni da želite da zavedete označene stavke?\nBroj označenih stavki: :number\n:items";
                var items = '';
                $.each(crud.checkedItems, function (key, id) {
                    items += id + ','
                })
                message = message.replace(":number", crud.checkedItems.length);
                message = message.replace(":items", items);

                let el = document.createElement('div');
                let input = document.createElement('input');
                el.appendChild(input);

                input.type = 'date';
                input.name = 'registry-date';
                input.id = 'datepicker';
/*                $('#datepicker').datepicker({//ne radi
                    format: 'DD/MM/YYYY',
                    showTodayButton: true
                });*/
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
                            text: "Zavedi",
                            value: true,
                            visible: true,
                            className: "bg-primary",
                        }
                    },
                }).then((value) => {
                    // swal('You typed:' + input.value);
                    if (value) {
                        var ajax_calls = [];
                        var request_zavodjenje_route = "{{ url($crud->route) }}/registerrequestbulk";

                        // submit an AJAX delete call
                        $.ajax({
                            url: request_zavodjenje_route,
                            type: 'POST',
                            data: {entries: crud.checkedItems, registry_date: input.value, route: '{{$crud->route}}'},

                            success: function (result) {
                                // Show an alert with the result
                                new Noty({
                                    type: "primary",
                                    text: "<strong>Ukupan broj označenih stavki:</strong> " + crud.checkedItems.length
                                }).show();
                                if (result['INFO']) {
                                    var message = "";
                                    var counter = 0;
                                    $.each(result['INFO'], function (id, value) {
                                        counter++;
                                        message += "<br>" + id + ': ' + value;
                                    })
                                    console.log(message);
                                    // Show an alert with the result
                                    new Noty({
                                        type: 'danger',
                                        layout: 'center',
                                        timeout: false,
                                        // closeWith: ['button'],
                                        // modal:true,
                                        text: "Broj uspešno obrađenih stavki:" + counter + message + "<br><a style='color: white;' href='" + result['pdf'] + "' target='_blank'><h2>PREUZMI PDF</h2></a>",
                                    }).show();

                                }
                                if (result['ERROR']) {
                                    var errors = "<strong>Zahtevi koji nisu ažurirani:</strong> <br> ";
                                    $.each(result['ERROR'], function (id, message) {
                                        errors += "<strong>" + id + "</strong>: " + message + "<br>";
                                    });
                                    new Noty({
                                        type: "warning",
                                        text: errors
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
