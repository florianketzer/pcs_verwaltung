// Nachricht in der Messagebox ausgeben
function message(type, text) {
    $('#message-box').html('<span class="' + type + '">' + text +  '</span>');
}