        const showContactsBtn = document.getElementById('show-contacts');
        const showArchiveBtn = document.getElementById('show-archive');
        const showDiscardBtn = document.getElementById('show-discard');

        const contactsContainer = document.getElementById('contacts');
        const archiveContainer = document.getElementById('archive');
        const discardContainer = document.getElementById('discard');

        // Function to hide all tables
        function hideAllTables() {
            contactsContainer.style.display = 'none';
            archiveContainer.style.display = 'none';
            discardContainer.style.display = 'none';
        }

        // Show Contacts Table (default)
        showContactsBtn.addEventListener('click', function() {
            hideAllTables();
            contactsContainer.style.display = 'block';
        });

        // Show Archive Table
        showArchiveBtn.addEventListener('click', function() {
            hideAllTables();
            archiveContainer.style.display = 'block';
        });

        // Show Discard Table
        showDiscardBtn.addEventListener('click', function() {
            hideAllTables();
            discardContainer.style.display = 'block';
        });