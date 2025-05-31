// Function to add confirmation events to forms
function setupConfirmationEvents() {
    // Confirmation before deleting
    const deleteForms = document.querySelectorAll('form[action="delete_servico.php"]');
    deleteForms.forEach(form => {
        form.removeEventListener('submit', handleFormSubmission); // Prevent duplicate listeners
        form.addEventListener('submit', handleFormSubmission);
    });

    // Confirmation for concluding tasks
    const concluirForms = document.querySelectorAll('form[action="concluir_servico.php"]');
    concluirForms.forEach(form => {
        form.removeEventListener('submit', handleFormSubmission); // Prevent duplicate listeners
        form.addEventListener('submit', handleFormSubmission);
    });
}

function handleFormSubmission(e) {
    let message = '';
    if (this.action.includes('delete_servico.php')) {
        message = 'Tem certeza que deseja excluir este registro permanentemente?';
    } else if (this.action.includes('concluir_servico.php')) {
        message = 'Deseja marcar esta tarefa como concluída?';
    }

    if (message && !confirm(message)) {
        e.preventDefault();
    }
}

// Initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupConfirmationEvents();

    // Reapply events when the content is altered (for pages with dynamic updates, like filtering)
    // Using MutationObserver to detect changes in the DOM, specifically in the table content.
    const tableContainer = document.querySelector('.table-container'); // Or a more specific container if available
    if (tableContainer) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    setupConfirmationEvents();
                }
            });
        });

        observer.observe(tableContainer, {
            childList: true, // Observe direct children additions/removals
            subtree: true // Observe all descendants
        });
    }
});