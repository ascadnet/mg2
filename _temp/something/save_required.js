
$(document).ready(function() {
    needToConfirm = false;
    window.onbeforeunload = askConfirm;
});

function askConfirm() {
    if (needToConfirm) {
        return "Your unsaved data will be lost.";
    }
}

$("select, input, textarea").change(function() {
    needToConfirm = true;
});