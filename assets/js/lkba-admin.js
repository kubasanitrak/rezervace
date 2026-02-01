document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('lkba-goods-table');
    if (!table) return;

    const states = ['pending', 'paid', 'used', 'canceled'];

    table.addEventListener('click', e => {
        if (!e.target.classList.contains('edit-state-btn')) return;

        const row        = e.target.closest('tr');
        const id         = row.dataset.id;
        const stateCol   = row.querySelector('.state-column');
        const badge      = stateCol.querySelector('.state-badge');
        const current    = badge.textContent.trim().toLowerCase();

        // Remove any open form
        document.querySelectorAll('.edit-form').forEach(f => f.remove());

        // Create dropdown
        const form = document.createElement('div');
        form.className = 'edit-form';

        let options = '';
        states.forEach(s => {
            const selected = s === current ? 'selected' : '';
            const label    = s.charAt(0).toUpperCase() + s.slice(1);
            options += `<option value="${s}" ${selected}>${label}</option>`;
        });

        form.innerHTML = `
            <select>${options}</select>
            <button type="button" class="save-btn button button-primary button-small">Save</button>
            <button type="button" class="cancel-btn button button-small">Cancel</button>
        `;

        stateCol.appendChild(form);
        form.querySelector('select').focus();

        // Save
        form.querySelector('.save-btn').onclick = () => {
            const newState = form.querySelector('select').value;

            const data = new FormData();
            data.append('action', 'lkba_update_goods_state');
            data.append('id',     id);
            data.append('state',  newState);
            data.append('nonce',  lkbaNonce);

            fetch(ajaxUrl, { method: 'POST', body: data })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        badge.textContent = newState.charAt(0).toUpperCase() + newState.slice(1);
                        badge.className   = `state-badge state-${newState}`;
                        form.remove();
                    } else {
                        alert(res.data || 'Update failed');
                    }
                })
                .catch(() => alert('Connection error'));
        };

        // Cancel
        form.querySelector('.cancel-btn').onclick = () => form.remove();

        // Escape key
        form.querySelector('select').onkeydown = ev => {
            if (ev.key === 'Escape') form.remove();
        };
    });
});