var vertragWirdAngezeigt = false;
var lieferscheinWirdAngezeigt = false;

var signaturePadKundendienst;
var signaturePadKunde;

$(function() {

    // $('#arbeitszeit-datum-prepare').datepicker();
    // $('#arbeitsbericht-datum').datepicker();
    $.datepicker.setDefaults({
       prevText: '&#x3c;zurück', prevStatus: '',
        prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
        nextText: 'Vor&#x3e;', nextStatus: '',
        nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
        currentText: 'heute', currentStatus: '',
        todayText: 'heute', todayStatus: '',
        clearText: '-', clearStatus: '',
        closeText: 'schließen', closeStatus: '',
        monthNames: ['Januar','Februar','März','April','Mai','Juni',
        'Juli','August','September','Oktober','November','Dezember'],
        monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dez'],
        dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
        dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
        dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
      showMonthAfterYear: false,
      showOn: 'both',
      buttonImage: 'media/img/calendar.png',
      buttonImageOnly: true,
      dateFormat:'dd.mm.yy'
    });
    
    // Eintrag aus der Materialliste in das Vorbereitungsfeld setzen
    $('#zusatz-material-select').on('change', function() {
        if($(this).val() != '') {
            $('#zusatz-material-prepare').val($(this).val());
        }
    });
    
    // Eintrag für Zusatzmaterial anfügen
    $('#add-additional-material').click(function() {
        var code = '<div class="row zusatzmaterial-line">';
        code += '<div class="col col-xs-8">';
        code += '<label for="zusatz-material">Materialbezeichnung</label>';
        code += '<input type="text" name="zusatz-material[]" class="form-control" value="' + $('#zusatz-material-prepare').val() + '"/>';
        code += '</div>';
        code += '<div class="col col-xs-2">';
        code += '<label>Menge</label>';
        code += '<input name="zusatz-material-menge[]" type="text" class="form-control" value="' + $('#zusatz-material-menge-prepare').val() + '"/>';
        code += '</div>';
        
        code += '<div class="col col-xs-2">';
        code += '<label>&nbsp;</label>';
        code += '<button class="btn btn-danger form-control" type="button" onclick="javascript:removeZusatzMaterial(this);"><i class="glyphicon glyphicon-remove"></i></button>';
        code += '</div>';
        code += '</div>';
        
        $('#zusatzmaterial-liste').prepend(code);
        
        $('#zusatz-material-select option')[0].selected = true;
        $('#zusatz-material-prepare').val('');
        $('#zusatz-material-menge-prepare').val('');
    });
    
    $('#add-unused-material').click(function() {
        var code = '<div class="col col-xs-4 unused-material-line">';
        code += '<table class="input">';
        code += '<tr>';
        code += '<td>';
        code += '<label>Pos.Nr.</label>';
        code += '<input type="text" class="form-control" name="unused-posnr[]" value="' + $('#unused-posnr-prepare').val() + '"/>';
        code += '</td>';
        code += '<td>';
        code += '<label>Menge</label>';
        code += '<input type="text" class="form-control" name="unused-menge[]" value="' + $('#unused-menge-prepare').val() + '"/>';
        code += '</td>';
        code += '<td>';
        code += '<label>&nbsp;</label>';
        code += '<button type="button" class="btn btn-danger form-control" onclick="javascript:removeUnusedMaterial(this);"><i class="glyphicon glyphicon-remove"></i></button>';
        code += '</td>';
        code += '</tr>';
        code += '</table>';
        code += '</div>';
        
        $('#unused-material-liste').prepend(code);
        
        $('#unused-posnr-prepare').val('');
        $('#unused-menge-prepare').val('');
    });
    
    /*********************************************
     * Auftragsbestätigung / Lieferschein-Upload * 
     *********************************************/
    
    // Fileupload handlen (Lieferscheine)
    $('#lieferschein-upload').fileupload({
        url: '/upload',
        dataType: 'json',
        dropZone: null,
        type: 'POST',
        done: function(e, data) {
            // $.each(data.result.files, function(index, file) {
            //     assignFile(file.name,  'lieferschein');
            // });
        },
        fail: function(e, data) {
            // console.log('dang');
        }
    });
    
    /*****************************************
     * Zusatzdokument-Upload (Service-Email) * 
     *****************************************/
    
    // Fileupload handlen (Lieferscheine)
    $('#zusatzdokument-upload').fileupload({
        url: 'services/upload/',
        dataType: 'json',
        dropZone: null,
        type: 'POST',
        done: function(e, data) {
            // $.each(data.result.files, function(index, file) {
            //     assignFile(file.name,  'zusatzdokument');
            // });
        },
        fail: function(e, data) {
            // console.log('dang');
        }
    });
    
    /*****************
     * Arbeitszeiten * 
     *****************/
    
    bindTimepicker();
    
    bindZeitberechnung();
    
    // Zeitberechnung triggern
    $('.zeit').change();
    
    /****************
     * Unterschrift * 
     ****************/
    
    $('#add-unterschrift-kundendienst').click(function() {
        $('#section-unterschriften').hide();
        $('#section-unterschrift-kundendienst').show();
    });
    $('#add-unterschrift-kunde').click(function() {
        $('#section-unterschriften').hide();
        $('#section-unterschrift-kunde').show();
    });
    
    var canvasKundendienst = document.querySelector('canvas#canvas-unterschrift-kundendienst');
    signaturePadKundendienst = new SignaturePad(canvasKundendienst);
    var canvasKunde = document.querySelector('canvas#canvas-unterschrift-kunde');
    signaturePadKunde = new SignaturePad(canvasKunde);
    
    $('#btn-unterschrift-kundendienst-speichern').click(function() {
        var unterschrift = signaturePadKundendienst.toDataURL();

        // Unterschrift als Bild speichern
        $.ajax({
            type: 'POST',
            url: '/workreports/savesignature',
            data: {
                '_token': $('input[name="_token"]').val(),
                'unterschrift': unterschrift,
                'typ': 'kundendienst',
                'arbeitsbericht_id': $('#aid').val(),
                'name_ausgeschrieben': $('#name-kundendienst-ausgeschrieben').val()
            },
            success: function(msg) {
                message('success', 'Unterschrift gespeichert ');
                
                console.log(msg.file);

                $('#img-unterschrift-kundendienst').attr('src', msg.file + '?' + (new Date()).getTime()).removeClass('hidden');
            },
            error: function(obj, text, error) {
                message('error', obj.responseText);
            }
        });
        
        // Unterschriftfeld schließen
        $('#section-unterschriften').show();
        $('#section-unterschrift-kundendienst').hide();
    });
    $('#btn-unterschrift-kundendienst-refresh').click(function() {
        signaturePadKundendienst.clear();
    });
    $('#btn-unterschrift-kundendienst-loeschen').click(function() {
        // Unterschriftfeld schließen
        $('#section-unterschriften').show();
        $('#section-unterschrift-kundendienst').hide();
    });
    
    $('#btn-unterschrift-kunde-speichern').click(function() {
        var unterschrift = signaturePadKunde.toDataURL();

        // Unterschrift als Bild speichern
        $.ajax({
            type: 'POST',
            url: '/workreports/savesignature',
            data: {
                '_token': $('input[name="_token"]').val(),
                'unterschrift': unterschrift,
                'typ': 'kunde',
                'arbeitsbericht_id': $('#aid').val(),
                'name_ausgeschrieben': $('#name-kunde-ausgeschrieben').val()
            },
            success: function(msg) {
                message('success', 'Unterschrift gespeichert ' + msg);
                
                $('#img-unterschrift-kunde').attr('src', msg.file + '?' + (new Date()).getTime()).removeClass('hidden');
            },
            error: function(obj, text, error) {
				alert('Unterschrift konnte nicht gespeichert werden!');
                message('error', obj.responseText);
            }
        });
        
        // Unterschriftfeld schließen
        $('#section-unterschriften').show();
        $('#section-unterschrift-kunde').hide();
    });
    $('#btn-unterschrift-kunde-refresh').click(function() {
        signaturePadKunde.clear();
    });
    $('#btn-unterschrift-kunde-loeschen').click(function() {
        $('#section-unterschriften').show();
        $('#section-unterschrift-kunde').hide();
    });
    
    /*******************************
     * Formvalidierung/Speicherung * 
     *******************************/
    
    // Validator auf das Form legen
    $('#arbeitsbericht-form').bootstrapValidator({
        submitButtons: ''
    });
    
    // Standardverhalten des Forms ausschalten
    $('#arbeitsbericht-form').on('submit', function(e) {
        e.preventDefault();
    });
    
    // Form abschicken, Daten speichern
    $('#arbeitsbericht-form #speichern').click(function(evt) {
        evt.preventDefault();
        arbeitsberichtSpeichern();
    });
    $('#arbeitsbericht-form #speichern-und-senden').click(function(evt) {
        evt.preventDefault();
        arbeitsberichtSpeichern(true);
    });
    
    /*****************************
     * Arbeitsbericht entsperren * 
     *****************************/
    
    $('#btn-unlock').click(function() {
        arbeitsberichtEntsperren();
    });
    
    /**************************
     * Arbeitsbericht löschen * 
     **************************/
    
    $('#btn-loeschen').click(function() {
        arbeitsberichtLoeschen();
    });
});

function bindTimepicker() {
    $('.arbeitszeit-line .zeit').each(function() {
        if(!$(this).hasClass('zeit-bound')) {
            $(this).timepicker({
                showMeridian: false,
                minuteStep: 5,
                defaultTime: '00:00'
            });
            $(this).addClass('zeit-bound');
        }
    });
}

function bindZeitberechnung() {
    $('.zeit').each(function() {
        if(!$(this).hasClass('berechnung-bound')) {
            $(this).on('change', function() {
                var parentLine = $(this).parents('tr.arbeitszeit-line');
                
                stundenRechnen(
                    parentLine.find('.zeit-reise-von'),
                    parentLine.find('.zeit-arbeit-von'),
                    parentLine.find('.zeit-arbeit-bis'),
                    parentLine.find('.zeit-reise-bis'),
                    parentLine.find('.zeit-reisestunden'),
                    parentLine.find('.zeit-arbeitsstunden')
                );
            });
            $(this).addClass('berechnung-bound');
        }
    });
}

function aufrunden(zeit, min) {
    var ROUNDING = min * 60 * 1000; // 15 min in ms
    return moment(Math.ceil((zeit) / ROUNDING) * ROUNDING); // auf 15 min aufrunden
}

function stundenRechnen(reiseVon, arbeitVon, arbeitBis, reiseBis, reisestunden, arbeitsstunden, ueberstunden) {
    var reiseVon = moment(reiseVon.val(), 'HH:mm');
    var reiseBis = moment(reiseBis.val(), 'HH:mm');
    var arbeitVon = moment(arbeitVon.val(), 'HH:mm');
    var arbeitBis = moment(arbeitBis.val(), 'HH:mm');
    var reiseDauer = reiseBis.diff(reiseVon);
    var arbeitsDauer = arbeitBis.diff(arbeitVon);
    
    reiseDauer = moment.utc(reiseDauer).diff(moment.utc(arbeitsDauer));
    reiseDauer = aufrunden(reiseDauer, 15);
    arbeitsDauer = aufrunden(arbeitsDauer, 15);
    
    // Arbeits- und Reisestunden errechnen
    reisestunden.val(moment.utc(reiseDauer).format('HH:mm'));
    arbeitsstunden.val(moment.utc(arbeitsDauer).format('HH:mm'));
    
    // Reisestunden aufaddieren
    var reiseStundenSumme = 0;
    $('.zeit-reisestunden').each(function() {
        
        // Die Vorbereitungszeile nicht mit in die Summe einbeziehen
        if($(this).parents('tr.arbeitszeit-line').hasClass('prepare')) return;

        var zeit = moment.duration(moment($(this).val(), 'HH:mm').format('HH:mm'));
        reiseStundenSumme += zeit.asMinutes();
    });

    var stunden = Math.floor(reiseStundenSumme / 60);
    var minuten = reiseStundenSumme % 60;
    $('#summe-reisestunden').val(stunden + ':' + (minuten < 10 ? '0' : '') + minuten);
    
    // Arbeitsstunden aufaddieren
    var arbeitsStundenSumme = 0;
    $('.zeit-arbeitsstunden').each(function() {
        
        // Die Vorbereitungszeile nicht mit in die Summe einbeziehen
        if($(this).parents('tr.arbeitszeit-line').hasClass('prepare')) return;

        var zeit = moment.duration(moment($(this).val(), 'HH:mm').format('HH:mm'));
        arbeitsStundenSumme += zeit.asMinutes();
    });

    Number.prototype.padDigit = function () {
        return (this < 10) ? '0' + this : this;
    }

    var stunden = Math.floor(arbeitsStundenSumme / 60);
    var minuten = arbeitsStundenSumme % 60;
    var t1 = stunden.padDigit() + ':' + minuten.padDigit();

    $('#summe-arbeitsstunden').val(t1);
    //$('#summe-arbeitsstunden').val(moment.utc(arbeitsStundenSumme).format('HH:mm'));
    
    // Überstunden aufaddieren
    var ueberStundenSumme = moment.duration();
    $('.zeit-ueberstunden').each(function() {
        
        // Die Vorbereitungszeile nicht mit in die Summe einbeziehen
        if($(this).parents('tr.arbeitszeit-line').hasClass('prepare')) return;
        
        ueberStundenSumme += moment.duration(moment($(this).val(), 'HH:mm'));
    });
    $('#summe-ueberstunden').val(moment.utc(ueberStundenSumme).format('HH:mm'));
}

function noTravel(element) {
	var parentLine = $(element).parents('tr').prev('tr');
	parentLine.find('.zeit-reise-von').val(parentLine.find('.zeit-arbeit-von').val());
	parentLine.find('.zeit-reise-bis').val(parentLine.find('.zeit-arbeit-bis').val());
	// Zeitberechnung triggern
    $('.zeit').change();
}

function toggleVertrag() {
    $('#area-kundeninfo, #area-lieferschein').hide();
    if(vertragWirdAngezeigt) {
        $('#area-vertrag').hide();
        $('#area-kundeninfo').show();
    } else {
        $('#area-vertrag').show();
    }
    vertragWirdAngezeigt = !vertragWirdAngezeigt;
}

function toggleLieferschein(id) {
    $('#area-kundeninfo, #area-vertrag').hide();
    if(lieferscheinWirdAngezeigt) {
        $('.area-lieferschein').hide();
        $('#area-kundeninfo').show();
    } else {
        $('#area-lieferschein-' + id).show();
    }
    lieferscheinWirdAngezeigt = !lieferscheinWirdAngezeigt;
}

function deleteLieferschein(id) {
    if(confirm('Sind Sie sicher, daß Sie dieses Dokument löschen möchten?')) {
        unAssignFile(id);
    }
}

// Zusatzmaterial-Zeile entfernen
function removeZusatzMaterial(element) {
    if(!confirm('Wirklich entfernen?')) return;
    
    $(element).parents('div.zusatzmaterial-line').remove();
}

// Nicht verwendetes Material entfernen
function removeUnusedMaterial(element) {
    if(!confirm('Wirklich entfernen?')) return;
    
    $(element).parents('div.unused-material-line').remove();
}

function addArbeitszeiten() {
    var code = '<tr class="arbeitszeit-line">';
    code += '<td>';
    code += '<label>Arbeitsdatum</label>';
    code += '<input type="text" class="form-control" name="arbeitszeit-datum[]" value="' + ("0" + new Date($('#arbeitszeit-datum-prepare').val()).getDate()).slice(-2) + '.' +("0" + (new Date($('#arbeitszeit-datum-prepare').val()).getMonth()+1)).slice(-2) + '.' +new Date($('#arbeitszeit-datum-prepare').val()).getUTCFullYear()+'"/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Reise von</label>';
    code += '<input type="text" class="form-control zeit zeit-reise-von" name="arbeitszeit-reise-von[]" value="' + $('#arbeitszeit-reise-von-prepare').val() + '"/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Arbeit von</label>';
    code += '<input type="text" class="form-control zeit zeit-arbeit-von" name="arbeitszeit-arbeit-von[]" value="' + $('#arbeitszeit-arbeit-von-prepare').val() + '"/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Arbeit bis</label>';
    code += '<input type="text" class="form-control zeit zeit-arbeit-bis" name="arbeitszeit-arbeit-bis[]" value="' + $('#arbeitszeit-arbeit-bis-prepare').val() + '"/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Reise bis</label>';
    code += '<input type="text" class="form-control zeit zeit-reise-bis" name="arbeitszeit-reise-bis[]" value="' + $('#arbeitszeit-reise-bis-prepare').val() + '"/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Reisestunden</label>';
    code += '<input type="text" class="form-control zeit-reisestunden" value="' + $('#arbeitszeit-reisestunden-prepare').val() + '" readonly/>';
    code += '</td>';
    code += '<td>';
    code += '<label>Arbeitsstunden</label>';
    code += '<input type="text" class="form-control zeit-arbeitsstunden" value="' + $('#arbeitszeit-arbeitsstunden-prepare').val() + '" readonly/>';
    code += '</td>';
    // code += '<td>';
    // code += '<label>Überstunden</label>';
    // code += '<input type="text" class="form-control zeit-ueberstunden" name="arbeitszeit-ueberstunden[]" value="' + $('#arbeitszeit-ueberstunden-prepare').val() + '"/>';
    // code += '</td>';
    code += '<td>';
    code += '<label>&nbsp;</label>';
    code += '<button type="button" class="btn btn-danger form-control" onclick="javascript:removeArbeitszeit(this);"><i class="glyphicon glyphicon-remove"></i></button>';
    code += '</td>';
    code += '</tr>';
	code += '<tr>';
	code += '<td/>';
	code += '<td colspan="4">';
	code += '<div class="no-travel" onclick="javascript:noTravel(this);">Keine Reisezeit</div>';
	code += '</td>';
	code += '<td colspan="4"/>';
	code += '</tr>';
    code += '<tr>';
    code += '<td colspan="9">';
    code += '<textarea class="form-control" placeholder="Durchgeführte Arbeiten" name="durchgefuehrte-arbeiten[]">' + $('#durchgefuehrte-arbeiten-prepare').val() + '</textarea>';
    code += '</td>';
    code += '</tr>';
    
    $('#arbeitszeiten-insert-marker').before(code);
    
    // Vorauswahl-Felder löschen
    $('#arbeitszeit-datum-prepare, #arbeitszeit-reise-von-prepare, #arbeitszeit-arbeit-von-prepare, #arbeitszeit-arbeit-bis-prepare, #arbeitszeit-reise-bis-prepare, #arbeitszeit-reisestunden-prepare, #arbeitszeit-arbeitsstunden-prepare, #arbeitszeit-ueberstunden-prepare, #durchgefuehrte-arbeiten-prepare').val('');
    
    bindTimepicker();
    
    bindZeitberechnung();
    
    // Zeitberechnung triggern
    $('.zeit').change();
}

function removeArbeitszeit(element) {
    var parent = $(element).parents('tr.arbeitszeit-line');
    parent.next('tr').remove();
	parent.next('tr').remove();
    parent.remove();
    
    // Zeitberechnung triggern
    $('.zeit').change();
}

function arbeitsberichtSpeichern(senden) {
    
    // Wenn das Formular versendet werden soll, muß eine gültige Email-Adresse im Feld stehen
    if(senden) {
        var validator = $('#arbeitsbericht-form').data('bootstrapValidator');
        validator.validate();
        if(!validator.isValid()) {
            return;
        }
        
        // Vor dem Versenden locken
        
    }

    $.ajax({
        type: 'POST',
        url: '/workreports',
        data: $('#arbeitsbericht-form').serialize(),
        success: function(msg) {
            message('success', 'Arbeitsbericht gespeichert ' + msg);
            if(senden) {
                arbeitsberichtSenden();
            } else {
                // window.location = window.location;
                location.reload(true);
            }
        },
        error: function(obj, text, error) {
            message('error', obj.responseText);
        }
    });
}

function arbeitsberichtSenden() {
    $.ajax({
        type: 'POST',
        // url: '/services/pdf_erstellen.php',
        url: '/workreports/createpdf',
        data: {
            '_token': $('input[name="_token"]').val(),
            'arbeitsbericht_id': $('#aid').val(),
            'email': $('#email').val()
        },
        success: function() {
            message('success', 'Arbeitsbericht gespeichert und gesendet');
            // window.location = window.location;
            location.reload(true);
        },
        error: function(obj, text, error) {
            message('error', obj.responseText);
        }
    });
}

function arbeitsberichtEntsperren() {
    $.ajax({
        type: 'POST',
        // url: '/services/arbeitsbericht_entsperren.php',
        url: '/workreports/unlock',
        data: {
            '_token': $('input[name="_token"]').val(),
            'arbeitsbericht_id': $('#aid').val()
        },
        success: function() {
            message('success', 'Arbeitsbericht entsperrt');
            location.reload(true);
        },
        error: function(obj, text, error) {
            message('error', obj.responseText);
        }
    });
}

function arbeitsberichtLoeschen() {
    if(confirm('Sind Sie sicher, daß Sie diesen Bericht löschen möchten?')) {
        $.ajax({
            type: 'POST',
            url: '/workreports/delete',
            data: {
                '_token': $('input[name="_token"]').val(),
                'arbeitsbericht_id': $('#aid').val()
            },
            success: function() {
                message('success', 'Arbeitsbericht gelöscht');
                location.href = '/';
            },
            error: function(obj, text, error) {
                message('error', obj.responseText);
            }
        });
    }
}

function assignFile(fileName, typ) {
    $.ajax({
        type: 'POST',
        url: '/services/arbeitsbericht_datei_zuweisen.php',
        data: {
            'fileName': fileName,
            'berichtId': $('#aid').val(),
            'typ': typ
        },
        success: function(msg) {
            message('success', 'Datei gespeichert');
            location.reload(true);
        },
        error: function(obj, text, error) {
            message('error', 'Datei konnte nicht gespeichert werden. Fehlermeldung: ' + obj.responseText);
        }
    });
}

function unAssignFile(id) {
    $.ajax({
        type: 'POST',
        url: '/deletefile',
        data: {
            '_token': $('input[name="_token"]').val(),
            'berichtId': $('#aid').val(),
            'documentId': id
        },
        success: function(msg) {
            message('success', 'Datei entfernt');
            location.reload(true);
        },
        error: function(obj, text, error) {
            location.reload(true);
            message('error', 'Datei konnte nicht entfernt werden. Fehlermeldung: ' + obj.responseText);
        }
    });
}