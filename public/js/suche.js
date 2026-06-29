$(function() {
    $('#suche-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/services/suche.php',
            data: $('#suche-form').serialize(),
            success: function(msg) {
                $('#ergebnis').html(msg);
                
                $('#suche-firma, #suche-ansprechpartner, #suche-kundennummer').blur();
            },
            error: function(obj, text, error) {
                alert('Konnte Suche nicht ausführen (Fehler:' + obj.responseText + ')');
            }
        });
    });
});
