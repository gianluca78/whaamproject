$(document).ready(function() {

    updateTable();

    $('.icon-tool-select-abc').click(function(e){
        e.preventDefault();

        $('.table-content tr').removeClass('selected');
        $(this).parent().parent().addClass('selected');

        updateTable();
        updateABCdata($(this).children(":first").val());
    });
});

function updateABCdata(ABCid) {
    $.ajax({
        type: "POST",
        url: abcDataPath,
        data: { "ABCid": ABCid }
    })
        .success(function (data, textStatus, jqXHR) {
            ABCdata =  JSON.parse(data);

            moment.locale($('#user-locale').text().substring(0, 2));

            $('#abc-date').text(moment(ABCdata["date"].date).format('D MMM YYYY'));
            $('#abc-antecedent-where').text(ABCdata["where"]);
            $('#abc-antecedent-what').text(ABCdata["what"]);
            $('#abc-antecedent-who').text(ABCdata["who"]);
            $('#abc-antecedent-trigger').text(ABCdata["trigger"]);
            $('#abc-antecedent-child-reaction').text(ABCdata["consequence"]);
            $('#abc-antecedent-others-reaction').text(ABCdata["consequenceOther"]);
            $('#abc-observer').text(ABCdata["observer"]);

        })
        .error(function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
        })
}

function updateTable() {
    $('.table-content tr').each(function(){
        if($(this).hasClass('selected')) {
            $(this).children(":last").children(":first").hide();
        } else {
            $(this).children(":last").children(":first").show();
        }
    });
}