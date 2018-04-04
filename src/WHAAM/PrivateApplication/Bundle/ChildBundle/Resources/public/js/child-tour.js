var tour = {
    id: "children-tour",
    showPrevButton: true,
    steps: [
        {
            title: tourContents[0][0],
            content: tourContents[0][1],
            target: document.querySelector(".Logo"),
            placement: "bottom"
        },
        {
            title: tourContents[1][0],
            content: tourContents[1][1],
            target: document.querySelector(".Tool a"),
            placement: "left",
            yOffset: -20,
            xOffset: -5
        },
        {
            title: tourContents[2][0],
            content: tourContents[2][1],
            target: document.querySelector(".breadcrumbs div"),
            placement: "bottom"
        },
        {
            title: tourContents[3][0],
            content: tourContents[3][1],
            target: document.querySelector("#ButtonContainer a"),
            placement: "left",
            yOffset: -9
        },
        {
            title: tourContents[4][0],
            content: tourContents[4][1],
            target: document.querySelector("table.TableContent tr td div.Content h3:nth-child(2)"),
            placement: "right",
            xOffset: -45
        },
        {
            title: tourContents[5][0],
            content: tourContents[5][1],
            target: document.querySelector(".icon-tool-search"),
            placement: "bottom",
            xOffset: 3
        },
        {
            title: tourContents[6][0],
            content: tourContents[6][1],
            target: document.querySelector(".goto-casedata"),
            placement: "left"
        },
        {
            title: tourContents[7][0],
            content: tourContents[7][1],
            target: document.querySelector(".goto-behaviours"),
            placement: "left"
        },
        {
            title: tourContents[8][0],
            content: tourContents[8][1],
            target: document.querySelector(".goto-network"),
            placement: "left"
        },
        {
            title: tourContents[9][0],
            content: tourContents[9][1],
            target: document.querySelector(".goto-invitation"),
            placement: "left"
        }
    ]
};

$(document).ready(function() {
    usernameChildrenTourBase = username + '-' + 'ChildrenTourBase';
    usernameChildrenTourComplete = username + '-' + 'ChildrenTourComplete';

    if(localStorage.getItem(usernameChildrenTourBase) !== null &&
        localStorage.getItem(usernameChildrenTourComplete) === null &&
        $('table.TableContent tr').length > 0) {
        hopscotch.startTour(tour, 4);
        localStorage.setItem(usernameChildrenTourComplete, true);
    }

    if(localStorage.getItem(usernameChildrenTourBase) === null) {
        if($('table.TableContent tr').length > 0) {
            localStorage.setItem(usernameChildrenTourComplete, true);
        } else {
            tour.steps.splice(4, 6);
        }

        hopscotch.startTour(tour);
        localStorage.setItem(usernameChildrenTourBase, true);
    }
});