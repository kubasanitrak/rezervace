// shared-frontend.js
// Global shared helpers for modals, messages, utils
// Loaded on schedule, checkout and my-reservations pages

window.barreShared = window.barreShared || {};

/**
 * Opens a modal with fade-in animation
 * @param {string} modalId - ID of the modal element
 */
barreShared.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return console.warn(`Modal #${modalId} not found`);
    
    modal.style.display = 'flex';
    // Small delay ensures transition works
    setTimeout(() => modal.classList.add('show'), 10);
};

/**
 * Closes a modal with fade-out animation
 * @param {string} modalId - ID of the modal element
 */
barreShared.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 320); // match CSS transition duration
};

/**
 * Shows a reusable success/error/info result modal
 * @param {'success'|'error'|'info'} type 
 * @param {string} message 
 * @param {string} [modalId='resultModal'] - optional custom modal ID
 */
barreShared.showResultModal = function(type, message, modalId = 'resultModal') {
    const modal = document.getElementById(modalId);
    if (!modal) return console.warn(`Result modal #${modalId} not found`);

    const icon  = modal.querySelector('.modal-icon');
    const title = modal.querySelector('h2');
    const msgEl = modal.querySelector('p');

    if (!icon || !title || !msgEl) return;

    // Reset classes
    icon.className = 'modal-icon';
    title.className = '';

    if (type === 'success') {
        icon.textContent = '✓';
        icon.classList.add('success');
        title.textContent = 'Success';
        title.classList.add('success');
    } else if (type === 'error') {
        icon.textContent = '⚠';
        icon.classList.add('error');
        title.textContent = 'Error';
        title.classList.add('error');
    } else {
        icon.textContent = 'ℹ';
        title.textContent = 'Info';
    }

    msgEl.textContent = message;

    barreShared.openModal(modalId);
};

/**
 * Global Esc key handler – closes all visible modals
 */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal[style*="display: flex"]').forEach(modal => {
            barreShared.closeModal(modal.id);
        });
    }
});

// document.getElementById('addToBasketModal')?.addEventListener('click', function(e) {
//     if (e.target === this) closeModal();
// });

// Optional: global loading indicator helper (can be used later)
barreShared.showLoading = function(elementOrId) {
    const el = typeof elementOrId === 'string' 
        ? document.getElementById(elementOrId) 
        : elementOrId;
    if (el) el.innerHTML = '<div class="loading">Loading...</div>';
};

barreShared.hideLoading = function(elementOrId) {
    const el = typeof elementOrId === 'string' 
        ? document.getElementById(elementOrId) 
        : elementOrId;
    if (el) el.innerHTML = '';
};