<style>
    .custom-alert-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55); /* Matches .alert-overlay */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .custom-alert-modal {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2); /* Matches .alert-modal */
        padding: 28px 32px;
        max-width: 640px;
        width: 92%;
        text-align: center;
        animation: fadeIn 0.5s ease forwards; /* Matches fadeIn animation */
    }

    .custom-alert-modal h3 {
        font-size: 1.75rem;
        margin-bottom: 12px;
        color: #1e3a8a; /* Matches alert-modal h3 */
        font-weight: 800;
    }

    .custom-alert-modal p {
        font-size: 1rem;
        color: #475569; /* Matches alert-modal p */
        margin-bottom: 18px;
    }

    .custom-alert-actions {
        display: flex;
        gap: 12px;
        justify-content: center; /* Matches .alert-actions */
    }

    .custom-alert-actions .btn-lg {
        padding: 0.8rem 1.4rem;
        font-size: 1rem; /* Matches .alert-actions .btn-lg */
    }

    .custom-alert-btn {
        background-color: #2563eb; /* Matches --primary-light */
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 0.8rem 1.4rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .custom-alert-btn:hover {
        background-color: #1e40af; /* Darker blue for hover */
        transform: translateY(-2px);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 576px) {
        .custom-alert-modal {
            width: 95%;
            padding: 20px 24px;
        }

        .custom-alert-modal h3 {
            font-size: 1.5rem;
        }

        .custom-alert-modal p {
            font-size: 0.9rem;
            margin-bottom: 14px;
        }

        .custom-alert-actions .btn-lg {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
    }
</style>

<script>
    function showCustomAlert(message) {
        // Remove any existing alert
        const existingAlert = document.querySelector('.custom-alert-overlay');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-alert-overlay';
        
        // Create alert modal
        const modal = document.createElement('div');
        modal.className = 'custom-alert-modal';
        
        // Create alert content
        modal.innerHTML = `
            <h3>Restricted Action</h3>
            <p>${message}</p>
            <div class="custom-alert-actions">
                <button class="custom-alert-btn btn-lg">Okay</button>
            </div>
        `;

        // Append to overlay and body
        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Add button click handler
        const okButton = modal.querySelector('.custom-alert-btn');
        okButton.addEventListener('click', () => {
            overlay.remove();
        });

        // Add backdrop click handler
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.remove();
            }
        });

        // Auto-close after 7 seconds
        setTimeout(() => {
            if (overlay.isConnected) {
                overlay.remove();
            }
        }, 7000);
    }
</script>