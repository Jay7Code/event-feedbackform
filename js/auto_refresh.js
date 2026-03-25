/**
 * Intelligent Auto-Refresh
 * Refreshes the page every 60 seconds unless the user is interacting with an input,
 * selecting text, or has a modal open.
 */
(function() {
    let isInteracting = false;
    let refreshInterval = 60000; // 60 seconds

    // Track input focus
    document.addEventListener('focusin', function(e) {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
            isInteracting = true;
        }
    });

    document.addEventListener('focusout', function(e) {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
            isInteracting = false;
        }
    });

    setInterval(function() {
        // Check for open modals (welcomeModal or dynamically added modal-backdrop)
        const welcomeModal = document.getElementById('welcomeModal');
        const hasWelcomeModal = welcomeModal && window.getComputedStyle(welcomeModal).opacity !== '0' && window.getComputedStyle(welcomeModal).display !== 'none';
        
        const hasBackdropModal = document.querySelector('.modal-backdrop.show') !== null;
        
        // Check for text selection
        const hasSelection = window.getSelection().toString().length > 0;

        if (!isInteracting && !hasWelcomeModal && !hasBackdropModal && !hasSelection) {
            window.location.reload();
        }
    }, refreshInterval);
})();
    