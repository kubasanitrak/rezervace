document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('lkba-goods-table');
    if (!table) return;

    const states = ['pending', 'paid', 'used', 'canceled'];

    table.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-state-btn')) {
            const row = e.target.closest('tr');
            const id = row.dataset.id;
            const stateColumn = row.querySelector('.state-column');
            const currentState = stateColumn.querySelector('.state-badge').textContent.trim().toLowerCase();

            // Remove any existing form
            const existingForm = stateColumn.querySelector('.edit-form');
            if (existingForm) {
                existingForm.remove();
                return;
            }

            // Create select dropdown
            const form = document.createElement('div');
            form.className = 'edit-form';

            let optionsHtml = '';
            states.forEach(state => {
                const selected = state === currentState ? 'selected' : '';
                const display = state.charAt(0).toUpperCase() + state.slice(1);
                optionsHtml += `<option value="${state}" ${selected}>${display}</option>`;
            });

            form.innerHTML = `
                <select>${optionsHtml}</select>
                <button type="button" class="save-btn">Save</button>
                <button type="button" class="cancel-btn">Cancel</button>
            `;

            stateColumn.appendChild(form);

            // Focus the select
            form.querySelector('select').focus();

            // Save button
            form.querySelector('.save-btn').addEventListener('click', function () {
                const newState = form.querySelector('select').value;

                const formData = new FormData();
                formData.append('action', 'lkba_update_goods_state');
                formData.append('id', id);
                formData.append('state', newState);
                formData.append('nonce', lkbaNonce);

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        // Update badge
                        const badge = stateColumn.querySelector('.state-badge');
                        badge.textContent = newState.charAt(0).toUpperCase() + newState.slice(1);
                        badge.className = `state-badge state-${newState}`;

                        form.remove();
                    } else {
                        alert(result.data || 'Update failed');
                    }
                })
                .catch(() => alert('Connection error'));
            });

            // Cancel button
            form.querySelector('.cancel-btn').addEventListener('click', () => {
                form.remove();
            });

            // Optional: Allow pressing Escape to cancel
            form.querySelector('select').addEventListener('keydown', function (e) {
                if (e.key === 'Escape') form.remove();
            });
        }
    });
});