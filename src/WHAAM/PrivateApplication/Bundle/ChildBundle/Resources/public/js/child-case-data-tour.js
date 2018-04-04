var tour = {
    id: "child-case-data-tour",
    showPrevButton: true,
    steps: [
        {
            title: tourContents[0][0],
            content: tourContents[0][1],
            target: document.querySelector("#MainBody"),
            placement: "right",
            xOffset: -700,
            yOffset: 175
        },
        {
            title: tourContents[1][0],
            content: tourContents[1][1],
            target: document.querySelector(".BoxInner"),
            placement: "bottom",
            xOffset: 380
        },
    ]
};

$(document).ready(function() {
    usernameChildCaseDataTour = username + '-' + 'ChildCaseData';

    if(localStorage.getItem(usernameChildCaseDataTour) !== null) {
        hopscotch.startTour(tour);
        localStorage.setItem(usernameChildCaseDataTour, true);
    }
});