$( "#message_child" ).change(function() {
    $.ajax({
        type: "POST",
        url: "/app_dev.php/children/network-data",
        data: { 'id': $(this).val() }
    })
        .success(function (data, textStatus, jqXHR) {
            $('#message_recipientUsers option').remove();

            users = $.parseJSON(data);

            for(i = 0; i < users.length; i++) {
                $('#message_recipientUsers').append("<option value='" + users[i].id + "'>" + users[i].user + "</option>");
            }

            $("#message_recipientUsers").chosen({
                placeholder_text_multiple: 'Select some options',
                placeholder_text_single: 'Select an option',
                no_results_text: 'No results match'
            });
        })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })

});
