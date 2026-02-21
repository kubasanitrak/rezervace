document.addEventListener('DOMContentLoaded', () => {
    const basket = JSON.parse(sessionStorage.getItem('barre_reservation_basket')) || [];

    if (basket.length === 0) {
        document.getElementById('checkout-summary').innerHTML = 
            '<p class="empty">Your basket is empty. <a href="/schedule">Go back to schedule</a></p>';
        document.getElementById('confirm-and-pay').disabled = true;
        return;
    }

    let total = 0;
    let html = '<ul class="checkout-items">';

    basket.forEach(item => {
        const itemTotal = item.price * item.persons;
        total += itemTotal;

        html += `
            <li class="checkout-item">
                <div class="item-info">
                    <strong>${item.name}</strong><br>
                    <span>${item.date} • ${item.time} • ${item.persons} person(s)</span>
                </div>
                <div class="item-price">${itemTotal.toLocaleString('cs-CZ')} Kč</div>
            </li>
        `;
    });

    html += '</ul>';

    document.getElementById('checkout-summary').innerHTML = html;
    document.getElementById('checkout-total').textContent = total.toLocaleString('cs-CZ') + ' Kč';

    // schedule.js or checkout-specific js
    const simulateBtn = document.getElementById('simulatePayment');
    const modal = document.getElementById('fakePaymentModal');
    const closeBtn = document.getElementById('closeFakePayment');
    const cancelBtn = document.getElementById('cancelFakePayment');
    const confirmBtn = document.getElementById('confirmFakePayment');

    // Open modal
    simulateBtn?.addEventListener('click', () => {
        barreShared.showResultModal(null, 'For testing: Click \"Pay\" to simulate successful payment.' );
    });

    // Close modal
    function closeFakeModal() {
        barreShared.closeModal('resultModal');
    }

    closeBtn?.addEventListener('click', closeFakeModal);
    cancelBtn?.addEventListener('click', closeFakeModal);
    modal?.addEventListener('click', e => { if (e.target === modal) closeFakeModal(); });

    // Simulate payment via AJAX
    /*/
    confirmBtn?.addEventListener('click', () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        fetch(barreAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'barre_simulate_payment',
                _ajax_nonce: barreAjax.nonce
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Clear local basket
                sessionStorage.removeItem('barre_reservation_basket'); // your key
                // Redirect to success page
                window.location.href = '/rezervace/reservation-success/?simulated=1';
            } else {
                barreShared.showResultModal('error', `Simulation failed: ' + (data.data?.message || 'Unknown error'`);
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Pay (Simulate)';
            }
        })
        .catch(() => {
            barreShared.showResultModal('error', 'Connection error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Pay (Simulate)';
        });
    });
    /*/
    confirmBtn?.addEventListener('click', async () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        try {
            const response = await fetch(barreAjax.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'barre_simulate_payment',
                    _ajax_nonce: barreAjax.nonce
                })
            });

            const data = await response.json();

            barreShared.closeModal('fakePaymentModal');

            if (data.success) {
                barreShared.showResultModal('success', 'Payment simulated successfully!');
                // Auto-redirect after short delay (user sees success message)
                setTimeout(() => {
                    window.location.href = '/rezervace/reservation-success/?simulated=1';
                }, 1500);
            } else {
                barreShared.showResultModal('error', data.data?.message || 'Simulation failed.');
            }
        } catch (err) {
            barreShared.closeModal('fakePaymentModal');
            barreShared.showResultModal('error', 'Connection error: ' + (err.message || 'Unknown issue'));
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Pay (Simulate)';
        }
    });
    //*/

});