function initializeUseDifferentSourceFunction() {
    let formCount = 1;
    const maxFormCount = maxNumberUrl; // Set the maximum form count
    
    // Apply the click event handler to the "Add News Feed" button
    jQuery('.add-feed-button').click(function (event) {
        event.preventDefault();
        
        if (formCount < maxFormCount) {
            // Clone the form up to the maxFormCount times
            const wrapperElement = document.querySelector('.source-input');
            const originalForm = wrapperElement.querySelector('.main-form');
            const clonedForm = originalForm.cloneNode(true);

            // Add a unique identifier to the cloned form
            clonedForm.classList.add('cloned-form-' + formCount);

            // Show the close button for the cloned form
            jQuery(clonedForm).find('.remove-source-form').show();

            // Clear the input field in the cloned form
            const emailInput = clonedForm.querySelector('input[type="email"]');
            emailInput.value = '';

            // Append the cloned form below the original form
            wrapperElement.append(clonedForm);

            formCount++; // Increment the form count

            // Check if the maximum form count has been reached
            if (formCount >= maxFormCount) {
                // Hide the "Add News Feed" button
                jQuery('.add-feed-button').hide();
            }
        }
    });

    // Use event delegation for dynamically added "close" buttons
    jQuery('.source-input').on('click', '.remove-source-form', function () {
        if (formCount > 1) {
            jQuery(this).closest('.main-form').remove();
            formCount--; // Decrement the form count
        }

        // Show the "Add News Feed" button after removing a form
        jQuery('.add-feed-button').show();
    });

    // Initially hide the close button
    jQuery('.remove-source-form').hide();
}
// Call the function to initialize the form functionality
initializeUseDifferentSourceFunction();
