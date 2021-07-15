/*
*  Custom javascript za clanstvo
*/


$('.resenja-datum').datepicker({
    format: "dd.mm.yyyy.",
    weekStart: 1,
    startView: 1,
    language: "sr-latin",
    orientation: "left top",
    uiLibrary: 'bootstrap4',
});

var Form = $('form');
Form.validate(
    {
        onfocusout: function (element) {
            if (element.name === 'broj') {
                this.element(element);
            }
        },
        highlight: function (element) {
            $(element).closest("input").addClass("border-danger");
        },
        unhighlight: function (element) {
            $(element).closest("input").removeClass("border-danger");
        },
        errorPlacement: function (error, element) {
            $(element).parent('div').append(error);
        },
    }
);
$('#prijaveFormular').on("blur", 'input[name*="[broj]"]', function () {

    var brojPrijave = $(this).val();
    var prijavaId = $(this).attr('name').replace(/^.*(\d).*$/, '$1');
    var osobaDiv = $('div#osoba' + prijavaId);
    osobaDiv.html();

    if (brojPrijave) {
        $.ajax({
            url: '/admin/getprijavaclan/' + brojPrijave,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (Object.keys(data).length !== 0) {
                    if (data.status >= 11 && data.status <= 12) {
                        $(osobaDiv).html('<div class="bg-success">' + data.ime + '(' + data.jmbg + ')</div>');
                    } else {
                        $(osobaDiv).html('<div class="bg-warning">' + data.status + '</div>');
                    }
                } else {
                    $(osobaDiv).html('Proverite broj prijave', '');
                }
            },
            error: function (request, error) {
                // console.log(" Can't do because: " + error);
            }
        });
    } else {
        // console.log('nije nista upisano u polje za licencu');
    }

    // });

});


var room = 0;

function additional_fields() {

    room++;
    var objTo = document.getElementById('additional_fields');
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group row removeclass" + room);
    var rdiv = 'removeclass' + room;
    divtest.innerHTML = '\
                <div class="col-sm-3 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="prijave[' + room + '][broj]" placeholder="broj prijave" required data-rule-remote="/checkprijavaclan/">\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="prijave[' + room + '][broj-resenja]" placeholder="broj rešenja UO" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum rešenja UO" name="prijave[' + room + '][datum-resenja]" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm " name="prijave[' + room + '][zavodni-broj]" placeholder="zavodni broj" required>\
                </div>\
                <div class="col-sm-2 pr-0 pl-1">\
                    <input type="text" class="form-control form-control-sm resenja-datum" data-provide="datepicker" placeholder="datum prijema" name="prijave[' + room + '][datum-prijema]" required>\
                </div>\
                <div class="col-sm-1 pr-0 pl-1">\
                    <button type="button" class="btn-sm btn-danger px-2" onclick="remove_additional_fields(' + room + ');"><span><i class="fa fa-minus"></i></span></button>\
                </div>\
                <div class="col-sm-12 pr-0 pl-1">\
                <div id="osoba' + room + '" ></div>\
                </div>\
                <div class = "clear"></div>';

    objTo.appendChild(divtest)
}

function remove_additional_fields(rid) {

    $('.removeclass' + rid).remove();
}
