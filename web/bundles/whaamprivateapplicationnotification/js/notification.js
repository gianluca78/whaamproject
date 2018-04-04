getMessages();
getNotifications();
updateNotReadMessagesNumber();
updateNotReadNotificationsNumber();

function getMessages()
{
    numberOfMessages = $(".icon-bar-mail").attr("data-count");
    newMessage = $("#translation-new-message").text();
    newMessageUpper = $("#translation-new-message").text().toUpperCase();
    newReply = $("#translation-new-reply").text();
    sentBy = $("#translation-sent-by").text();

    $.ajax({
        type: "GET",
        url: "/app_dev.php/messages/not-displayed-list"
    })
        .success(function (data, textStatus, jqXHR) {
            messages = $.parseJSON(data);

            for(i = 0; i < messages.length; i++) {
                duration = 3000 * messages.length;

                sender = (messages[i]['surname']) ? messages[i]['surname'] + ' ' + messages[i]['name'] : messages[i]['username'];

                if(messages[i]['answerId'] != null) {
                    messageText = newReply + ' "' + messages[i]['subject'] + '" ' + sentBy + sender;
                } else {
                    messageText = newMessage + ' "' + messages[i]['subject'] + '" ' + sentBy + sender;
                }

                $.growl({ title: newMessageUpper, message: messageText });

                updateMessageDisplayedStatus(messages[i]['messageStatusId'], messages[i]['answerStatusId']);
            }

            updateNotReadMessagesNumber();
        })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })

}

function getNotifications()
{
    numberOfNotification = $(".icon-bar-notification").attr("data-count");

    $.ajax({
        type: "GET",
        url: "/app_dev.php/notification/not-displayed-list"
    })
        .success(function (data, textStatus, jqXHR) {
            notifications = $.parseJSON(data);

            for(i = 0; i < notifications.length; i++) {
                duration = 3000 * notifications.length;

                $.growl({ title: notifications[i]['title'].toUpperCase(), message: notifications[i]['content'], duration: duration });
                updateNotificationDisplayedStatus(notifications[i]['id']);
            }

            updateNotReadNotificationsNumber();
        })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })

}

function refreshIcons()
{
    $(".Tool a").each(
        function () {
            var mn = $(this).attr("data-count");
            if (mn != '') {
                if (parseInt(mn) > 0) {
                    var pos = $(this).position();
                    $(this).parent().append("<span class='counter' style='left:" + (pos.left + 23) + "px'>" + mn + "</span>");
                }
            }
        });
}

function updateNotReadMessagesNumber()
{
    $.ajax({
        type: "GET",
        url: "/app_dev.php/messages/count-not-read"
    })
        .success(function (data, textStatus, jqXHR) {
            $(".icon-bar-mail").attr("data-count", data);

            refreshIcons();
        })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })
}

function updateNotReadNotificationsNumber()
{
    $.ajax({
        type: "GET",
        url: "/app_dev.php/notification/count-not-read"
    })
        .success(function (data, textStatus, jqXHR) {
            $(".icon-bar-notification").attr("data-count", data);

            refreshIcons();
        })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })
}

function updateMessageDisplayedStatus(messageStatusId, answerStatusId)
{
    $.ajax({
        type: "POST",
        url: "/app_dev.php/messages/update-displayed-status",
        data: {messageStatusId : messageStatusId, answerStatusId: answerStatusId }
    })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })
}

function updateNotificationDisplayedStatus(notificationId)
{
    $.ajax({
        type: "POST",
        url: "/app_dev.php/notification/update-displayed-status",
        data: {id : notificationId}
    })
        .error(function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        })
}

window.setInterval(function(){ getMessages(); getNotifications(); }, 30000);