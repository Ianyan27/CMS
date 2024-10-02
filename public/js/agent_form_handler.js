$(document).ready(function() {
    $('#teamMembers').change(function() {
        // Get the selected option
        var selectedOption = $(this).find('option:selected');

        // Extract data attributes
        var agentName = selectedOption.data('name');
        var agentEmail = selectedOption.data('email');
        var hubspotId = selectedOption.data('hubspot-id');

        // Populate the fields with the selected agent's data
        $('#agentName').val(agentName);
        $('#email').val(agentEmail);
        $('#hubspotId').val(hubspotId);
    });

    // show modal error
    $("#errorModal").modal('show');

    $('[data-toggle="tooltip"]').tooltip();

    $('#addSalesAgentForm').on('submit', function(e) {
        var email = $('#email').val();
        var validDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        var emailDomain = email.split('@')[1];
        var isValid = validDomains.indexOf(emailDomain) !== -1;

        if (email && !isValid) {

            $('#emailError').text(
                'The email address must be one of the following domains: lithan.com, educlaas.com, learning.educlaas.com'
            );
            e.preventDefault(); // Prevent form submission
        } else {
            $('#emailError').text(''); // Clear any previous error message
        }
    });

    // Optional: Clear the error message when the modal is hidden
    $('#errorModal').on('hidden.bs.modal', function() {
        $('#emailError').text('');
    });
});