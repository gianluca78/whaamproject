$(document).ready(function() {
    hideShowHealthProfessionalFields();
    hideShowOtherNation();
});

$("input[id*=isHealthProfessional]").change(function() {
    hideShowHealthProfessionalFields();
});

$("select[id*=user_nation]").change(function() {
    hideShowOtherNation();
});

function hideShowHealthProfessionalFields() {
    if ($("input[id*=isHealthProfessional]").is(":checked")) {
        $("input[id*=healthProfessionalClientsAgeRange]").closest('tr').show();
        $("input[id*=healthProfessionalSpecialties]").closest('tr').show();
        $("input[id*=healthProfessionalTreatmentApproaches]").closest('tr').show();
        $("input[id*=healthProfessionalTreatmentModalities]").closest('tr').show();

        $("input[id*=healthProfessionalClientsAgeRange]").parent().parent().parent().parent().parent().show();
        $("input[id*=healthProfessionalSpecialties]").parent().parent().parent().parent().parent().show();
        $("input[id*=healthProfessionalTreatmentApproaches]").parent().parent().parent().parent().parent().show();
        $("input[id*=healthProfessionalTreatmentModalities]").parent().parent().parent().parent().parent().show();
    } else {
        $("input[id*=healthProfessionalClientsAgeRange]").parent().parent().parent().parent().parent().hide();
        $("input[id*=healthProfessionalSpecialties]").parent().parent().parent().parent().parent().hide();
        $("input[id*=healthProfessionalTreatmentApproaches]").parent().parent().parent().parent().parent().hide();
        $("input[id*=healthProfessionalTreatmentModalities]").parent().parent().parent().parent().parent().hide();
    }
}

function hideShowOtherNation() {
    if ($("select[id*=nation]").val() == 6) {
        $("input[id*=otherNation]").closest('tr').show();
        $("input[id*=otherNation]").parent().parent().show();
    } else {
        $("input[id*=otherNation]").closest('tr').hide();
        $("input[id*=otherNation]").parent().parent().hide();
    }
}