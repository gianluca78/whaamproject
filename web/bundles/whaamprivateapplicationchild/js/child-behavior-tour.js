var tour = {
    id: "behaviors-tour",
    showPrevButton: true,
    steps: [
        {
            title: tourContents[0][0],
            content: tourContents[0][1],
            target: document.querySelector("#MainBody"),
            placement: "top",
            xOffset: "center",
            yOffset: 220
        },
        {
            title: tourContents[1][0],
            content: tourContents[1][1],
            target: document.querySelector(".goto-assessment"),
            placement: "left",
            yOffset: -10
        }
    ]
};

$(document).ready(function() {
    usernameChildBehaviorsTourBase = username + '-' + 'ChildBehaviorsTourBase';
    usernameChildBehaviorsTourComplete = username + '-' + 'ChildBehaviorsTourComplete';

    if(localStorage.getItem(usernameChildBehaviorsTourBase) !== null &&
        localStorage.getItem(usernameChildBehaviorsTourComplete) === null &&
        $('table.TableContent tr').length > 0) {
            hopscotch.startTour(tour, 1);
            localStorage.setItem(usernameChildBehaviorsTourComplete, true);
    }

    if(localStorage.getItem(usernameChildBehaviorsTourBase) === null) {
        if($('table.TableContent tr').length > 0) {
            localStorage.setItem(usernameChildBehaviorsTourComplete, true);
        } else {
            tour.steps.splice(1, 1);
        }

        hopscotch.startTour(tour);
        localStorage.setItem(usernameChildBehaviorsTourBase, true);
    }

});