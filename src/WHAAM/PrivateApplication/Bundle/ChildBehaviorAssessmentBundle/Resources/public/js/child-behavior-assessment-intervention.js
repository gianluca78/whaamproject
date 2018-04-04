jQuery(document).ready(function() {
    $('#add-strategy').on('click', function(e) {
        e.preventDefault();

        addInterventionStrategyForm();
    });

    childSiblingsIndex = (parseInt($('#child_behavior_assessment_intervention_countInterventionStrategies').val())-1);
});

$(document.body).on('click', 'a[id*=remove-strategy]' ,function(e){
    e.preventDefault();
    splitId = $(this).attr('id').split('-');

    index = parseInt(splitId[2]) - 1;

    $('#child_behavior_assessment_intervention_strategies_' + index + '_name').parent().parent().parent().parent().remove();
    $('#child_behavior_assessment_intervention_strategies_' + index + '_description').parent().parent().parent().parent().remove();
    $('#child_behavior_assessment_intervention_strategies_' + index + '_assignedUsers').parent().parent().parent().parent().parent().remove();

    $('#child_behavior_assessment_intervention_countInterventionStrategies').val(parseInt($('#child_behavior_assessment_intervention_countInterventionStrategies').val())-1);
    $(this).remove();
});

function addInterventionStrategyForm() {

    countInterventionStrategies = parseInt($('#child_behavior_assessment_intervention_countInterventionStrategies').val()) + 1;

    formIndex = (countInterventionStrategies=='0') ? '0' : countInterventionStrategies - 1;

    $('#child_behavior_assessment_intervention_countInterventionStrategies').val(countInterventionStrategies);

    htmlForm = $('#add-strategy').attr('data-prototype');

    htmlForm = htmlForm.replace(/__name__/gi, formIndex);
    htmlForm = htmlForm + '<a id="remove-strategy-' + countInterventionStrategies +'" href="#">' +
        $('#child_behavior_assessment_intervention_removeTranslation').val() +'</a>';

    $('#add-strategy').after(htmlForm);
}