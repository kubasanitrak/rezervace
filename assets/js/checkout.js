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

    // Proceed to payment
    document.getElementById('confirm-and-pay').addEventListener('click', async () => {
        const btn = document.getElementById('confirm-and-pay');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        try {
            const response = await fetch(barreCheckoutAjax.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'barre_process_basket',
                    basket: JSON.stringify(basket),
                    _ajax_nonce: barreCheckoutAjax.nonce
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.data?.message || 'Server error');
            }

            // Redirect to Stripe
            if (result.data?.checkout_url) {
                window.location.href = result.data.checkout_url;
            } else {
                throw new Error('No checkout URL received');
            }

        } catch (err) {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.textContent = 'Proceed to Payment →';
        }
    });
});