function assignFile(fileName, typ) {
    if($('#user-id').val() == '') {
        message('error', 'Bitte erst den Kunden speichern!');
        return;
    }
    
    $.ajax({
        type: 'POST',
        url: '/services/datei_zuweisen.php',
        data: {
            'fileName': fileName,
            'userId': $('#user-id').val(),
            'typ': typ
        },
        success: function(msg) {
			var datum = new Date();
			var datumFormatiert = datum.getDate() + '.' + (datum.getMonth()+1) + '.' + datum.getFullYear();
            var btnCode = '<span class="document" data-document-id="' + msg + '">' + fileName + '<small><em>[' + datumFormatiert + ']</em></small> <button type="button" class="btn btn-danger btn-xs" onclick="javascript:delDoc(' + msg + ');"><i class="glyphicon glyphicon-remove"></i></button></span>';
            $('#' + typ + '-liste').append(btnCode);
            message('success', 'Datei gespeichert');
        },
        error: function(obj, text, error) {
            message('error', 'Datei konnte nicht gespeichert werden. Fehlermeldung: ' + obj.responseText);
        }
    });
}

// Dokument löschen (erst prüfen, ob die Datei noch irgendwo zugewiesen ist)
function delDoc(docId) {
	$.ajax({
        type: 'POST',
        url: '/services/dateizuweisungen_pruefen.php',
        data: {
            'docId': docId
        },
        success: function(anzahl) {
            if(anzahl > 0) {
				alert('Diese Datei kann nicht gelöscht werden, weil sie noch Arbeitsberichten zugewiesen ist.');
			} else {
				delDocForReal(docId);
			}
        },
        error: function(obj, text, error) {
            message('error', 'Datei konnte nicht gelöscht werden. Fehlermeldung: ' + obj.responseText);
        }
    });
}

// Dokument löschen
function delDocForReal(docId) {
    if(!confirm('Wirklich löschen?')) return;
    
    $.ajax({
        type: 'POST',
        url: '/services/datei_loeschen.php',
        data: {
            'docId': docId
        },
        success: function(msg) {
            $('.document').each(function() {
                if($(this).attr('data-document-id') == docId) {
                    $(this).remove();
                }
            });
            message('success', 'Datei gelöscht');
        },
        error: function(obj, text, error) {
            message('error', 'Datei konnte nicht gelöscht werden. Fehlermeldung: ' + obj.responseText);
        }
    });
}

function kundeSpeichern(weiterleiten) {
    var validator = $('#kunde-anlegen-form').data('bootstrapValidator');
    validator.validate();
    if(!validator.isValid()) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: '/services/kunde_speichern.php',
        data: $('#kunde-anlegen-form').serialize(),
        success: function(msg) {
            $('#user-id').val(msg);
            message('success', 'Kunde gespeichert');
            if(weiterleiten) {
                location.href = 'arbeitsbericht.php?uid=' + msg;
            }
        },
        error: function(obj, text, error) {
            message('error', obj.responseText);
        }
    });
}

$(function() {
    
    // Validator auf das Form legen
    $('#kunde-anlegen-form').bootstrapValidator({
        submitButtons: ''
    });
    
    // Standardverhalten des Forms ausschalten
    $('#kunde-anlegen-form').on('submit', function(e) {
        e.preventDefault();
    });
    
    // Form abschicken, Daten speichern
    $('#kunde-anlegen-form #speichern').click(function() {
        kundeSpeichern();
    });
    $('#kunde-anlegen-form #speichern-und-arbeitsbericht').click(function() {
        kundeSpeichern(true);
    });
    
    $('#vertrag-upload, #lieferschein-upload').click(function(e) {
        if($('#user-id').val() == '') {
            e.preventDefault();
            message('error', 'Bitte erst den Kunden speichern!');
        }
    });
    
    // Fileupload handlen (Vertrag)
    $('#vertrag-upload').fileupload({
        url: 'services/upload/',
        dataType: 'json',
        dropZone: null,
        type: 'POST',
        done: function(e, data) {
            $.each(data.result.files, function(index, file) {
                assignFile(file.name, 'vertrag');
            });
        },
        fail: function(e, data) {
            console.log('dang');
        }
    });
    
    // Fileupload handlen (Lieferscheine)
    $('#lieferschein-upload').fileupload({
        url: 'services/upload/',
        dataType: 'json',
        dropZone: null,
        type: 'POST',
        done: function(e, data) {
            $.each(data.result.files, function(index, file) {
                assignFile(file.name, 'lieferschein');
            });
        },
        fail: function(e, data) {
            console.log('dang');
        }
    });
});