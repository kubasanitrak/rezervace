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
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    });

    // Close modal
    function closeFakeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    closeBtn?.addEventListener('click', closeFakeModal);
    cancelBtn?.addEventListener('click', closeFakeModal);
    modal?.addEventListener('click', e => { if (e.target === modal) closeFakeModal(); });

    // const BASKET_KEY = 'barre_reservation_basket';

    // function saveBasket() {
    //     sessionStorage.setItem(BASKET_KEY, JSON.stringify(basket));
    // }

    // Simulate payment via AJAX
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
                // if (data.data?.clear_client_basket) {
                    // basket = [];
                    sessionStorage.removeItem('barre_reservation_basket'); // your key
                    // sessionStorage.removeItem(BASKET_KEY); // your key
                    // saveBasket(); // if you have this function
                    // updateBasketUI(); // refresh floating basket if visible
                // }
                // Redirect to success page
                window.location.href = '/rezervace/reservation-success/?simulated=1';
            } else {
                alert('Simulation failed: ' + (data.data?.message || 'Unknown error'));
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Pay (Simulate)';
            }
        })
        .catch(() => {
            alert('Connection error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Pay (Simulate)';
        });
    });

    // Esc key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeFakeModal();
    });

});