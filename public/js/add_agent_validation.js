$(document).ready(function() {
    $('#addUserForm').on('submit', function(e) {
        var email = $('#email').val();
        var validDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        var emailDomain = email.split('@')[1];

        if (email && validDomains.indexOf(emailDomain) === -1) {
            $('#emailError').text(
                'The email address must be one of the following domains: lithan.com, educlaas.com, learning.educlaas.com'
            );
            e.preventDefault(); // Prevent form submission
        } else {
            $('#emailError').text(''); // Clear any previous error message
        }
    });
});