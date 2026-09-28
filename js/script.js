// Confirm delete actions
function confirmDelete(itemType) {
    return confirm(`Are you sure you want to delete this ${itemType}?`);
}

// Character counter for thread and reply forms
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('.character-count');
    
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const maxLength = this.getAttribute('maxlength');
            const currentLength = this.value.length;
            const counter = document.getElementById(this.getAttribute('data-counter'));
            
            if (counter) {
                counter.textContent = `${currentLength}/${maxLength}`;
                
                // Change color when approaching limit
                if (currentLength > maxLength * 0.9) {
                    counter.classList.add('text-danger');
                } else {
                    counter.classList.remove('text-danger');
                }
            }
        });
    });
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});