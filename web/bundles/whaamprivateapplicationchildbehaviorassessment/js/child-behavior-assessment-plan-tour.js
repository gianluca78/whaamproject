var tour = {
    id: "child-behavior-assessment-plan-tour",
    showPrevButton: true,
    steps: [
        {
            title: tourContents[0][0],
            content: tourContents[0][1],
            target: document.querySelector("#MainBody"),
            placement: "top",
            xOffset: "center",
            yOffset: 250
        },
        {
            title: tourContents[1][0],
            content: tourContents[1][1],
            target: document.querySelector("#MainBody"),
            placement: "top",
            xOffset: "center",
            yOffset: 220
        },
        {
            title: tourContents[2][0],
            content: tourContents[2][1],
            target: document.querySelector("#ButtonContainer a:nth-child(1)"),
            placement: "bottom"
        },
        {
            title: tourContents[3][0],
            content: tourContents[3][1],
            target: document.querySelector(".ToolContainer a:nth-child(1)"),
            placement: "bottom"
        },
        {
            title: tourContents[4][0],
            content: tourContents[4][1],
            target: document.querySelector(".goto-abc"),
            placement: "left",
            yOffset: -10,
            xOffset: -30
        }
    ]
};

$(document).ready(function() {
    usernameChildBehaviorAssessmentPlanTourBase = username + 'ChildBehaviorAssessmentPlanTourBase';
    usernameChildBehaviorAssessmentPlanTourComplete = username + 'ChildBehaviorAssessmentPlanTourComplete';

    if(localStorage.getItem(usernameChildBehaviorAssessmentPlanTourBase) !== null &&
        localStorage.getItem(usernameChildBehaviorAssessmentPlanTourComplete) === null &&
        $('table.TableContent tr').length > 0) {
        hopscotch.startTour(tour, 3);
        localStorage.setItem(usernameChildBehaviorAssessmentPlanTourComplete, true);
    }

    if(localStorage.getItem(usernameChildBehaviorAssessmentPlanTourBase) === null) {
        if($('table.TableContent tr').length > 0) {
            localStorage.setItem(usernameChildBehaviorAssessmentPlanTourComplete, true);
        } else {
            tour.steps.splice(3, 2);
        }

        hopscotch.startTour(tour);
        localStorage.setItem(usernameChildBehaviorAssessmentPlanTourBase, true);
    }
});