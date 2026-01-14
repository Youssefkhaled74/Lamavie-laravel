function showCustomAlert(message) {
    // Remove any existing alert
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Create alert container
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert';
    
    // Create alert content
    alertDiv.innerHTML = `
        <div class="custom-alert-content">
            <div class="custom-alert-header">
                <span>Restricted Action</span>
                <button class="custom-alert-close">&times;</button>
            </div>
            <div class="custom-alert-body">
                ${message}
            </div>
        </div>
    `;

    // Append to body
    document.body.appendChild(alertDiv);

    // Add close functionality
    const closeButton = alertDiv.querySelector('.custom-alert-close');
    closeButton.addEventListener('click', () => {
        alertDiv.remove();
    });

    // Auto-close after 5 seconds
    setTimeout(() => {
        if (alertDiv.isConnected) {
            alertDiv.remove();
        }
    }, 5000);
}