document.addEventListener('DOMContentLoaded', () => {

    // Global variables (add near the top of your script)
    let currentCancelResId = null;

    // Cancel button handler – opens confirmation modal instead of confirm()
    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            currentCancelResId = this.dataset.resId;

            // Show confirmation modal
            const confirmModal = document.getElementById('cancelConfirmModal');
            confirmModal.style.display = 'flex';
            setTimeout(() => confirmModal.classList.add('show'), 10);
        });
    });

    // Close cancel confirmation modal
    document.getElementById('closeCancelConfirm')?.addEventListener('click', closeCancelConfirmModal);
    document.getElementById('cancelCancel')?.addEventListener('click', closeCancelConfirmModal);

    function closeCancelConfirmModal() {
        const modal = document.getElementById('cancelConfirmModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            currentCancelResId = null;
        }, 300);
    }

    // Confirm cancel → send AJAX
    document.getElementById('confirmCancel')?.addEventListener('click', async function() {
        if (!currentCancelResId) return;

        const confirmBtn = this;
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Cancelling...';

        try {
            const response = await fetch(barreAjax.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'barre_cancel_reservation',
                    reservation_id: currentCancelResId,
                    _ajax_nonce: barreAjax.nonce
                })
            });

            const result = await response.json();

            closeCancelConfirmModal();

            // Show result modal instead of alert
            showCancelResultModal(
                result.success ? 'success' : 'error',
                result.success 
                    ? (result.data?.message || 'Reservation cancelled successfully.')
                    : (result.data?.message || 'Failed to cancel reservation.')
            );

            if (result.success) {
                // Optional: refresh page or remove row from DOM
                setTimeout(() => location.reload(), 1500);
            }

        } catch (err) {
            closeCancelConfirmModal();
            showCancelResultModal('error', 'Connection error: ' + err.message);
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Yes, Cancel';
        }
    });

    // Result modal helper
    function showCancelResultModal(type, message) {
        const modal = document.getElementById('cancelResultModal');
        const icon = document.getElementById('cancelIcon');
        const title = document.getElementById('cancelResultTitle');
        const msgEl = document.getElementById('cancelResultMessage');

        if (type === 'success') {
            icon.textContent = '✓';
            icon.style.color = '#28a745';
            title.textContent = 'Success';
            title.style.color = '#155724';
        } else {
            icon.textContent = '⚠';
            icon.style.color = '#dc3545';
            title.textContent = 'Error';
            title.style.color = '#721c24';
        }

        msgEl.textContent = message;

        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    // Close result modal
    document.getElementById('closeCancelResult')?.addEventListener('click', closeCancelResultModal);
    document.getElementById('closeCancelResultBtn')?.addEventListener('click', closeCancelResultModal);

    function closeCancelResultModal() {
        const modal = document.getElementById('cancelResultModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    function closeRescheduleModal() {
        const modal = document.getElementById('rescheduleModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    // Esc key support for all modals (add to existing keydown listener or here)
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (document.getElementById('cancelConfirmModal').style.display === 'flex') {
                closeCancelConfirmModal();
            }
            if (document.getElementById('cancelResultModal').style.display === 'flex') {
                closeCancelResultModal();
            }
            if (document.getElementById('rescheduleModal').style.display === 'flex') {
                closeRescheduleModal();
            }
            // ... add other modals if needed
        }
    });
// TODO
    // View details – simple alert for now (can be modal later)
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const resId = this.dataset.resId;
            alert(`Details for reservation #${resId}\n(Expand this into a modal later)`);
        });
    });
// TODO


    // --- Reschedule modal logic ---
    function getMondayOfCurrentWeek() {
        const today = new Date();
        const day = today.getDay();
        const diff = day === 0 ? -6 : 1 - day; // Sunday = 0
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        monday.setHours(0,0,0,0);
        return monday;
    }


let currentResId = null;
let selectedNewSlotId = null;
let resCurrentMonday = getMondayOfCurrentWeek(); // reuse your existing function

document.querySelectorAll('.btn-reschedule').forEach(btn => {
    btn.addEventListener('click', function() {
        currentResId = this.dataset.resId;
        // Optional: store original lesson ID if needed later
        // const originalLessonId = this.dataset.lessonId;

        document.getElementById('rescheduleCurrentInfo').textContent = 
            'Select a new available time slot below';

        // Reset selection
        selectedNewSlotId = null;
        document.getElementById('confirmReschedule').disabled = true;

        // Show modal
        const modal = document.getElementById('rescheduleModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);

        // Load initial week
        loadRescheduleCalendar();
    });
});

// Close modal
document.getElementById('closeReschedule')?.addEventListener('click', closeRescheduleModal);
document.getElementById('cancelReschedule')?.addEventListener('click', closeRescheduleModal);

function closeRescheduleModal() {
    const modal = document.getElementById('rescheduleModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        currentResId = null;
        selectedNewSlotId = null;
    }, 300);
}

// Week navigation
document.getElementById('resPrevWeek')?.addEventListener('click', () => {
    resCurrentMonday.setDate(resCurrentMonday.getDate() - 7);
    loadRescheduleCalendar();
});

document.getElementById('resNextWeek')?.addEventListener('click', () => {
    resCurrentMonday.setDate(resCurrentMonday.getDate() + 7);
    loadRescheduleCalendar();
});

// Load calendar for rescheduling
function loadRescheduleCalendar() {
    const container = document.getElementById('rescheduleCalendar');
    container.innerHTML = '<div class="loading">Loading available slots...</div>';

    const mondayStr = resCurrentMonday.toISOString().split('T')[0];
    const sunday = new Date(resCurrentMonday);
    sunday.setDate(resCurrentMonday.getDate() + 6);
    const sundayStr = sunday.toISOString().split('T')[0];

    fetch(barreAjax.ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'barre_load_available_slots_reschedule',
            from_date: mondayStr,
            to_date: sundayStr,
            _ajax_nonce: barreAjax.nonce
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            renderRescheduleCalendar(data.data.slots, mondayStr);
            document.getElementById('resWeekRange').textContent = data.data.week_range;
        } else {
            container.innerHTML = '<p style="color:#dc3545; padding:2rem; text-align:center;">' +
                                 (data.data?.message || 'Error loading slots') + '</p>';
        }
    })
    .catch(() => {
        container.innerHTML = '<p style="color:#dc3545; padding:2rem; text-align:center;">Connection error</p>';
    });
}

// Render week grid (similar to main calendar but clickable)
function renderRescheduleCalendar(slotsByDate, startDate) {
    const container = document.getElementById('rescheduleCalendar');
    container.innerHTML = '';

    const grid = document.createElement('div');
    grid.className = 'calendar-grid';

    // Time column
    const times = ['07:00','08:00','09:00','10:00','16:00','17:00','18:00','19:00']; // example
    const timeCol = document.createElement('div');
    times.forEach(t => {
        const div = document.createElement('div');
        div.className = 'time-header';
        div.textContent = t;
        timeCol.appendChild(div);
    });
    grid.appendChild(timeCol);

    // Days
    let current = new Date(startDate);
    for (let d = 0; d < 7; d++) {
        const dateStr = current.toISOString().split('T')[0];
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        dayHeader.textContent = current.toLocaleDateString('cs-CZ', {weekday:'short', day:'numeric', month:'short'});
        grid.appendChild(dayHeader);

        const dayCol = document.createElement('div');
        times.forEach(time => {
            const slotDiv = document.createElement('div');
            slotDiv.className = 'slot-option disabled';

            const slot = slotsByDate[dateStr]?.find(s => s.start_time.startsWith(time));
            if (slot && slot.available >= 1) {   // at least 1 spot free
                slotDiv.className = 'slot-option';
                slotDiv.innerHTML = `
                    ${slot.name}<br>
                    <small>${slot.instructor} · ${slot.available}/${slot.capacity}</small>
                `;
                slotDiv.dataset.slotId = slot.id;

                slotDiv.addEventListener('click', () => {
                    // Deselect others
                    document.querySelectorAll('.slot-option').forEach(s => s.classList.remove('selected'));
                    slotDiv.classList.add('selected');
                    selectedNewSlotId = slot.id;
                    document.getElementById('confirmReschedule').disabled = false;
                });
            }

            dayCol.appendChild(slotDiv);
        });

        grid.appendChild(dayCol);
        current.setDate(current.getDate() + 1);
    }

    container.appendChild(grid);
}
/*
// Confirm reschedule → AJAX
document.getElementById('confirmReschedule')?.addEventListener('click', async () => {
    if (!currentResId || !selectedNewSlotId) return;
// console.log(currentResId);
// console.log(selectedNewSlotId);
    // return;

    const btn = document.getElementById('confirmReschedule');
    btn.disabled = true;
    btn.textContent = 'Rescheduling...';


    try {
        const response = await fetch(barreAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'barre_reschedule_reservation', // barre_reschedule_reservation
                reservation_id: currentResId,
                new_lesson_id: selectedNewSlotId,
                _ajax_nonce: barreAjax.nonce
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(result.data.message || 'Successfully rescheduled!');
            location.reload();
        } else {
            alert('Error: ' + (result.data?.message || 'Failed to reschedule'));
        }
    } catch (err) {
        alert('Confirm Reschedule reservation - Connection error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Confirm New Slot';
    }
});


*/
// Confirm reschedule → AJAX + nice modal result
document.getElementById('confirmReschedule')?.addEventListener('click', async () => {
    if (!currentResId || !selectedNewSlotId) return;

    const btn = document.getElementById('confirmReschedule');
    btn.disabled = true;
    btn.textContent = 'Rescheduling...';

    try {
        const response = await fetch(barreAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'barre_reschedule_reservation',
                reservation_id: currentResId,
                new_lesson_id: selectedNewSlotId,
                _ajax_nonce: barreAjax.nonce
            })
        });

        const result = await response.json();

        // Close the calendar modal first
        closeRescheduleModal();

        // Show result modal instead of alert
        if (result.success) {
            showRescheduleResultModal(
                'success',
                result.data?.message || 'Successfully rescheduled!'
            );
            // Auto-refresh page after a short delay
            setTimeout(() => location.reload(), 1800);
        } else {
            showRescheduleResultModal(
                'error',
                result.data?.message || 'Failed to reschedule.'
            );
        }

    } catch (err) {
        closeRescheduleModal();
        showRescheduleResultModal(
            'error',
            'Connection error: ' + (err.message || 'Unknown issue')
        );
    } finally {
        btn.disabled = false;
        btn.textContent = 'Confirm New Slot';
    }
});

// Reusable result modal function for reschedule
function showRescheduleResultModal(type, message) {
    const modal = document.getElementById('rescheduleResultModal');
    const icon  = document.getElementById('resIcon');
    const title = document.getElementById('resResultTitle');
    const msgEl = document.getElementById('resResultMessage');

    if (type === 'success') {
        icon.textContent = '✓';
        icon.className = 'modal-icon success';
        title.textContent = 'Success';
        title.className = 'success';
    } else {
        icon.textContent = '⚠';
        icon.className = 'modal-icon error';
        title.textContent = 'Error';
        title.className = 'error';
    }

    msgEl.textContent = message;

    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

// Close result modal
document.getElementById('closeResResult')?.addEventListener('click', closeResResultModal);
document.getElementById('closeResResultBtn')?.addEventListener('click', closeResResultModal);

function closeResResultModal() {
    const modal = document.getElementById('rescheduleResultModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

// Add Esc key support (if not already global)
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (document.getElementById('rescheduleModal').style.display === 'flex') {
            closeRescheduleModal();
        }
        if (document.getElementById('rescheduleResultModal').style.display === 'flex') {
            closeResResultModal();
        }
    }
});

    // document.querySelectorAll('.btn-reschedule').forEach(btn => {
    //     btn.addEventListener('click', function() {
    //         const resId = this.dataset.resId;
    //         // For now just open modal – later fetch available slots
    //         document.getElementById('rescheduleModal').style.display = 'flex';
    //         setTimeout(() => document.getElementById('rescheduleModal').classList.add('show'), 10);
    //     });
    // });

    // // Close reschedule modal
    // document.getElementById('closeReschedule')?.addEventListener('click', () => {
    //     const m = document.getElementById('rescheduleModal');
    //     m.classList.remove('show');
    //     setTimeout(() => m.style.display = 'none', 300);
    // });
});