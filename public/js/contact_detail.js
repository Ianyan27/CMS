$(document).ready(function () {
    
    /*
        Handling the activities filter and search
    */
    // Handle filter button clicks
    $('.activity-button').click(function () {
        var filter = $(this).data('filter');

        // Remove 'active' class from all buttons and add to the clicked button
        $('.activity-button').removeClass('active-activity-button');
        $(this).addClass('active-activity-button');

        // Filter activity items
        $('.activity-item').each(function () {
            var itemType = $(this).data('type');

            if (filter === 'all' || itemType === filter) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Handle search input
    $('#search-button').click(function () {
        var searchTerm = $('#search-input').val().toLowerCase();

        // Filter activity items based on search term
        $('.activity-item').each(function () {
            var itemText = $(this).text().toLowerCase();

            if (itemText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Handle search input for real-time filtering
    $('#search-input').on('input', function () {
        $('#search-button').click(); // Trigger the search button click event
    });
    
    
    /**
     * 
     * Handling the file name adding in the activity add pop up
     */
    $('#activity-attachment').on('change', function () {
        var fileList = this.files;
        var $fileNamesContainer = $('#file-names');
        $fileNamesContainer.empty(); // Clear the container

        for (var i = 0; i < fileList.length; i++) {
            var fileName = $('<div></div>').addClass('file-name').text(fileList[i].name);
            $fileNamesContainer.append(fileName);
        }
    });
    
    /**
     * 
     * Styling the overflow for activity notification
     */
    function checkOverflow() {
        var $activities = $('#activity-container .activities');
        if ($activities[0].scrollHeight > $activities.innerHeight()) {
            $activities.addClass('scrollable');
        } else {
            $activities.removeClass('scrollable');
        }
    }

    // Check on page load
    checkOverflow();

    // Optional: Check on window resize if your layout changes
    $(window).on('resize', checkOverflow);

    /**
     * 
     * Show required message for add new activity 
     */
    
     // Validate the attachment input on form submission
    function validateFileInput(formId, fileInputId, errorId) {
            $(formId).on('submit', function (e) {
                console.log(`Validating form: ${formId}`);
                const attachmentInput = $(fileInputId);

                if (!attachmentInput.val()) { // Check if file input is empty
                    $(errorId).show(); // Show the error message
                    attachmentInput.removeAttr('required'); // Temporarily remove required attribute
                    e.preventDefault(); // Prevent form submission
                } else {
                    $(errorId).hide(); // Hide the error message
                }

                // Re-add required attribute after submission is handled
                attachmentInput.attr('required', true);
            });

            // Hide the error message when a file is selected
            $(fileInputId).on('change', function () {
                if ($(this).val()) {
                    $(errorId).hide(); // Hide the error message
                }
            });
        }

        // Validate first form
        validateFileInput('#addActivityForm', '#activity-attachment', '#attachment-error');

        // Validate second form with different IDs for inputs
        validateFileInput('#addArchiveActivityForm', '#activity-attachment-archive', '#attachment-error-archive');
});