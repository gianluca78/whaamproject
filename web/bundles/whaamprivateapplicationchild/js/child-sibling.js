jQuery(document).ready(function() {
    $('#add-sibling').on('click', function(e) {
        e.preventDefault();

        addChildSiblingForm();
    });

    childSiblingsIndex = (parseInt($('input[id*=countChildSiblings]').val())-1);
});

$(document.body).on('click', 'a[id*=remove-sibling]' ,function(e){
    e.preventDefault();
    splitId = $(this).attr('id').split('-');

    index = parseInt(splitId[2]) - 1;

    $('input[id*=child_siblings_' + index + '_nickname]').parent().parent().remove();
    $('input[id*=child_siblings_' + index + '_name]').parent().parent().remove();
    $('select[id*=child_siblings_' + index + '_sex]').parent().parent().remove();
    $('select[id*=child_siblings_' + index + '_yearOfBirth]').parent().parent().remove();

    $('input[id*=countChildSiblings]').val(parseInt($('input[id*=countChildSiblings]').val())-1);
    $(this).remove();
});

function addChildSiblingForm() {

    countChildSiblings = parseInt($('input[id*=countChildSiblings]').val()) + 1;

    formIndex = (countChildSiblings=='0') ? '0' : countChildSiblings - 1;

    $('input[id*=countChildSiblings]').val(countChildSiblings);

    htmlForm = $('#add-sibling').attr('data-prototype');

    htmlForm = htmlForm.replace(/__name__/gi, formIndex);
    htmlForm = htmlForm + '<a id="remove-sibling-' + countChildSiblings +'" href="#">' +
        $('input[id*=removeTranslation]').val() +'</a>';

    $('#add-sibling').after(htmlForm);
}