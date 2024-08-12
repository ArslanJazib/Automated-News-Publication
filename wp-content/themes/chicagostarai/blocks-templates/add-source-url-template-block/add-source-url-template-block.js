function initializeAddSourceFormFunction() {
    let formCount = 1;
    // Apply the click event handler to the "Add source" button
    jQuery('.add-source-button').click(function (event) {
        event.preventDefault();
        if (formCount < maxSourceNumberUrl) {
            // Clone the source form fields
            const wrapperElement = jQuery('.add-source-input');
            const originalForm = wrapperElement.find('.add-source-main-form:last');
            const clonedFields = originalForm.clone();
            // Add a unique identifier to the cloned form
            clonedFields.addClass('cloned-form-' + formCount);

            // Show the close button for the cloned form
            clonedFields.find('.remove-source-form').show();

            // Clear the input fields in the cloned form
            clonedFields.find('input[type="text"]').val('');
            clonedFields.find('textarea').val('');
            clonedFields.find('input[type="email"]').val('');

            // Append the cloned form fields below the original form
            wrapperElement.append(clonedFields);
            formCount++;

            if(formCount >= maxSourceNumberUrl ){
                jQuery('.add-source-button').hide();
            }
        }
    });

    // Apply the click event handler to the "Remove" button directly
    jQuery('.add-source-input').on('click', '.remove-source-form', function () {
        if (formCount > 1) {
            jQuery(this).closest('.add-source-main-form').remove();
            formCount--; // Decrement the form count
        }

        jQuery('.add-source-button').show();
    });
    
    // Initially hide the close button
    jQuery('.remove-source-form').hide();
}

// Call the function to initialize the form functionality
initializeAddSourceFormFunction();
