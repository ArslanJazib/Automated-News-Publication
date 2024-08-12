jQuery(document).ready(function() {
    // This code runs when the document is fully loaded and ready.
    
    jQuery(".btn-instruction").click(function() {
        // When an element with class "btn-instruction" is clicked.
        
        jQuery(".instructions-list").toggleClass('active');
        // Toggle the class 'active' on elements with class "instructions-list".
    });
});