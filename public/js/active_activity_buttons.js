const buttons = document.querySelectorAll('.activity-button');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove 'active' class from all buttons
                    buttons.forEach(btn => btn.classList.remove('active'));

                    // Add 'active' class to the clicked button
                    this.classList.add('active');

                    // Show the corresponding section (you likely already have this)
                    showSection(this.getAttribute('onclick').split("'")[1]);
                });
        });