$(document).ready(function() {
    $('.intervention').each(function(index, value){
        updateStrategyData($(value).attr('intervention-id'), $(value).attr('strategy-view'), 'left');
    });
});

$('.arrow-left').click(function(e){
    e.preventDefault();

    interventionId = $(this).parent().prev().attr('intervention-id');
    indexStrategy = parseInt($(this).parent().prev().attr('strategy-view')) - 1;

    updateStrategyData(interventionId, indexStrategy, 'right');

    $(this).parent().prev().attr('strategy-view', indexStrategy);
});

$('.arrow-right').click(function(e){
    e.preventDefault();

    interventionId = $(this).parent().prev().attr('intervention-id');
    indexStrategy = parseInt($(this).parent().prev().attr('strategy-view')) + 1;

    updateStrategyData(interventionId, indexStrategy, 'left');

    $(this).parent().prev().attr('strategy-view', indexStrategy);
});

function formatStrategyData(data) {
    return '<p>' +
    '<strong>' + data.name + '</strong> ' +
    data.description +
    '</p>' +
    '<div class="tableContainer">' +
    '<div>' +
    '<div class="label">' + $("#translation-assigned-to").text() + '</div>' +
    '<div class="value">' + data.assignedUsers + '</div>' +
    '</div>' +
    '</div>';
}

function manageArrowIcons(interventionId, data) {
    console.log(data);

    if(!data || data.numberOfStrategies == 1) {
        $('[intervention-id = ' + interventionId + '] + div a.arrow-left').hide();
        $('[intervention-id = ' + interventionId + '] + div a.arrow-right').hide();
    }

    if(data && data.indexStrategy == 0 && data.numberOfStrategies != 1) {
        $('[intervention-id = ' + interventionId + '] + div a.arrow-left').hide();
        $('[intervention-id = ' + interventionId + '] + div a.arrow-right').show();
    }

    if(data && data.indexStrategy == data.numberOfStrategies - 1 && data.numberOfStrategies != 1) {
        $('[intervention-id = ' + interventionId + '] + div a.arrow-left').show();
        $('[intervention-id = ' + interventionId + '] + div a.arrow-right').hide();
    }
}

function updateStrategyData(interventionId, indexStrategy, animationDirection) {
    $.ajax({
        type: "POST",
        url: childBehaviorAssessmentInterventionStrategyDataPath,
        data: { "interventionId": interventionId, "indexStrategy": indexStrategy }
    })
        .success(function (data, textStatus, jqXHR) {
            strategyData =  (data) ? JSON.parse(data) : null;

            if(strategyData) {
                $('[intervention-id = ' + interventionId + '] div').html(formatStrategyData(strategyData));

                $('[intervention-id = ' + interventionId + '] div').effect('slide', {'direction': animationDirection});
            }

            manageArrowIcons(interventionId, strategyData);

        })
        .error(function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
        })
}