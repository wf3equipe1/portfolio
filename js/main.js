$(document).ready(function() {
    $('.flexslider').flexslider();

    $(':radio.readmsg').change(function(){
        $.get("readmsg.php", {checked: $(this).val(), article: $(this).parent().attr('action')});
    });
});
