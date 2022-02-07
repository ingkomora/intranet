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
                if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length === 0) {
                    new Noty({
                        type: "warning",
                        text: "<strong>Nije označena ni jedna stavka.</strong><br>Molimo Vas da odaberete jednu ili više stavki koje želite da obradite."
                    }).show();

                    return;
                }

                let url = "{{ url($crud->route) }}";
                var message = "Da li ste sigurni da želite da zavedete označene stavke?\nBroj označenih stavki: :number\n:items";
                var items = '';
                $.each(crud.checkedItems, function (key, id) {
                    items += id + ','
                })
                message = message.replace(":number", crud.checkedItems.length);
                message = message.replace(":items", items);


                let el = document.createElement('div');
                el.innerHTML = '<div class="form-group row"><label for="registrydate" class="col-sm-3 col-form-label col-form-label-sm">Datum</label><input type="date" name="registrydate" id="registrydate" class="form-control form-control-sm mb-3 col-md-8"></div>';

                if (url.indexOf("licence") >= 0 || url.indexOf("si") >= 0) {
                    el.innerHTML += '<div class="form-group row"><label for="prilog" class="col-sm-2 col-form-label col-form-label-sm">Dopuna? </label><input type="checkbox" name="prilog" id="prilog" value="1" class="form-control form-control-sm mb-3 col-md-1"><label for="prilogtext" class="col-sm-2 col-form-label col-form-label-sm">Naziv:</label><input type="text" name="prilogtext" id="prilogtext" class="input-group mb-3 col-md-6"></div>';
                }


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
                        let data = {
                            entries: crud.checkedItems,
                            registry_date: registrydate.value,
                            route: '{{$crud->route}}'
                        }
                        if (url.indexOf("licence") >= 0 || url.indexOf("si") >= 0) {
                            data.prilog = $('input#prilog:checked').val();
                            data.prilog_text = prilogtext.value;
                        }
                        // submit an AJAX delete call
                        $.ajax({
                            url: request_zavodjenje_route,
                            type: 'POST',
                            data: data,

                            success: function (result) {
                                // Show an alert with the result
                                new Noty({
                                    type: "primary",
                                    text: "<strong>Ukupan broj označenih stavki:</strong> " + crud.checkedItems.length
                                }).show();
                                if (result['ERROR']) {
                                    let errors = "<strong>Zahtevi koji nisu ažurirani:</strong> <br> ";
                                    $.each(result['ERROR'], function (id, message) {
                                        errors += "<strong>" + id + "</strong>: " + message + "<br>";
                                    });
                                    new Noty({
                                        type: "error",
                                        text: errors
                                    }).show();
                                } else if (result['INFO']) {
                                    var message = "";
                                    var counter = 0;
                                    $.each(result['INFO'], function (id, value) {
                                        counter++;
                                        message += "<br>" + id + ': ' + value;
                                    })
                                    console.log(message);
                                    // Show an alert with the result
                                    new Noty({
                                        type: 'primary',
                                        layout: 'center',
                                        timeout: false,
                                        // closeWith: ['button'],
                                        // modal:true,
                                        text: "Broj uspešno obrađenih stavki:" + counter + message + "<br><a style='color: white;' href='" + result['pdf'] + "' target='_blank'><h2>PREUZMI PDF</h2></a>",
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
