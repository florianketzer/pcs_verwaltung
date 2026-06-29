function addMaterialLine() {
    
    var code = '<div class="row material-line">';
    code += '<div class="col col-xs-11">';
    code += '<input type="text" name="material[]" class="form-control" placeholder="Materialbezeichnung"/>';
    code += '</div>';
    code += '<div class="col col-xs-1">';
    code += '<button type="button" class="btn btn-danger form-control" onclick="javascript:removeMaterialLine(this);"><i class="glyphicon glyphicon-remove"></i></button>';
    code += '</div>';
    code += '</div>';
    
    $('#material-liste').append(code);
}

function removeMaterialLine(element) {
    if(confirm('Wirklich löschen?')) {
        $(element).parents('div.material-line').remove();
    }
}

$(function() {
    
    $('#materialliste-form').on('submit', function(e) {
        e.preventDefault();
        
        // Leere Zeilen entfernen
        $('.material-line input').each(function() {
            if($(this).val() == '')
                $(this).parents('div.material-line').remove();
        });
        
        $.ajax({
            type: 'POST',
            url: '/services/materialliste_speichern.php',
            data: $('#materialliste-form').serialize(),
            success: function(msg) {
                message('success', 'Liste gespeichert');
                location.reload();
            },
            error: function() {
                message('error', 'Liste konnte nicht gespeichert werden');
            }
        });
    });
    
});
