
$(document).ready(function() {
    $('#container1').bind('copy paste', function(e) {
        e.preventDefault();
    });
});

  $(document).on('keydown', function(e) {
    if((e.ctrlKey || e.metaKey) && (e.key == "p" || e.charCode == 16 || e.charCode == 112 || e.keyCode == 80) ){
        e.cancelBubble = true;
        e.preventDefault();
        e.stopImmediatePropagation();
    }  
});

window.onbeforeprint = function(e) {
    document.body.style.visibility = "hidden";
    document.body.style.display = "none";
    location.reload();
};

