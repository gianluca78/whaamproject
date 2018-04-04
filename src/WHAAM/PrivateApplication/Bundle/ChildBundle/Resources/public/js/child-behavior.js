$(document).ready(function() {
    showHideBehaviors();
});

$("#childBehavior_behaviorCategory").change(function () {
    updateBehaviors(this);
});

$('#childBehavior_hasOtherBehavior').click(function() {
    showHideBehaviors();
});

$('#remove-other-behavior').click(function(e) {
    e.preventDefault();

    removeOtherBehavior();
});

function removeOtherBehavior() {
    $('#childBehavior_otherBehavior').val('');
    $('#childBehavior_hasOtherBehavior').val($('#childBehavior_hasOtherBehavior').prop('checked', false));

    showHideBehaviors();
}

function showHideBehaviors() {
    if ($('#childBehavior_hasOtherBehavior').is(':checked')) {

        $('#childBehavior_behaviorCategory').parent().parent().parent().parent().hide();
        $('#childBehavior_behaviorCategory').val('');
        $('#childBehavior_behavior').parent().parent().parent().parent().hide();
        $('#childBehavior_behavior').val('');
        $('#childBehavior_hasOtherBehavior').parent().hide();

        $('#childBehavior_otherBehavior').parent().parent().parent().parent().show();
        $('#remove-other-behavior').show();
    } else {
        $('#childBehavior_behaviorCategory').parent().parent().parent().parent().show();
        $('#childBehavior_behavior').parent().parent().parent().parent().show();
        $('#childBehavior_hasOtherBehavior').parent().show();

        $('#childBehavior_otherBehavior').parent().parent().parent().parent().hide();
        $('#remove-other-behavior').hide();
    }
}

function updateBehaviors() {
    if($("#childBehavior_behaviorCategory").val()) {
        $.ajax({
            type: "POST",
            url: "/app_dev.php/behavior-categories/behaviors",
            data: {"id": $("#childBehavior_behaviorCategory").val()}
        })
            .success(function (data, textStatus, jqXHR) {
                $("#childBehavior_behavior").html(data);
            })
            .error(function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            });
    } else {
        $('#childBehavior_behavior option').remove();
    }
}
