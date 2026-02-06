document.addEventListener('DOMContentLoaded', () => {
    console.log("loaded schedule.js");
    console.log( barreAjax.example_variable );

    // =============================================
    //   BARRE Schedule - Week View (Vanilla JS)
    // =============================================


    const calendarContainer = document.querySelector('.week-view');
    const weekRangeEl = document.querySelector('.current-week-range');
    const prevBtn = document.querySelector('.prev-week');
    const nextBtn = document.querySelector('.next-week');

    let currentMonday = getMondayOfCurrentWeek();

    // Time slots (you can adjust)
    const timeSlots = [
        '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
        '12:00', '13:00', '16:00', '17:00', '18:00', '19:00', '20:00'
    ];

    function getMondayOfCurrentWeek() {
        const today = new Date();
        const day = today.getDay();
        const diff = day === 0 ? -6 : 1 - day; // Sunday = 0
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        monday.setHours(0,0,0,0);
        return monday;
    }

    function formatDate(date) {
        return date.toLocaleDateString('cs-CZ', {
            weekday: 'short',
            day: 'numeric',
            month: 'numeric'
        });
    }

    function updateWeekRange() {
        const sunday = new Date(currentMonday);
        sunday.setDate(currentMonday.getDate() + 6);
        
        const rangeText = `${formatDate(currentMonday)} – ${formatDate(sunday)}`;
        weekRangeEl.textContent = rangeText;
    }

    // =============================================
    //   Connect to lesson slots (update createLessonSlot)
    // =============================================

    function createLessonSlot(lesson) {
        const slot = document.createElement('div');
        // ... other classes ...
        slot.className = 'lesson-slot';

        // Use server-provided value — do NOT recalculate here
        const available = lesson.available ?? (lesson.capacity - lesson.used);
        const used = lesson.used ?? (lesson.capacity - lesson.available);

        let statusClass = 'available';
        if (available <= 0)      statusClass = 'full';
        else if (available <= 3) statusClass = 'almost-full';

        slot.classList.add(statusClass);

        slot.innerHTML = `
            <div class="lesson-name">${lesson.name}</div>
            <div class="lesson-time">${lesson.start_time} – ${lesson.end_time}</div>
            <div class="lesson-instructor">👤 ${lesson.instructor}</div>
            <div class="capacity-info">
                ${available > 0 ? `${used}/${lesson.capacity}` : 'Full'}
            </div>
        `;

        if (available > 0) {
            slot.addEventListener('click', () => {
                openAddToBasketModal({
                    id:         lesson.id,
                    name:       lesson.name,
                    date:       lesson.date,
                    start_time: lesson.start_time,
                    end_time:   lesson.end_time,
                    instructor: lesson.instructor,
                    price:      lesson.price,
                    available:  available,           // ← must pass correct value
                    capacity:   lesson.capacity      // optional, for safety
                });
            });
        }

        return slot;
    }

    function renderCalendar(lessonsData = {}) {
        calendarContainer.innerHTML = '';

        // Time column
        const timeCol = document.createElement('div');
        timeCol.className = 'time-column';
        timeSlots.forEach(time => {
            const slot = document.createElement('div');
            slot.className = 'time-header';
            slot.textContent = time;
            timeCol.appendChild(slot);
        });
        calendarContainer.appendChild(timeCol);

        // 7 days
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(currentMonday);
            dayDate.setDate(currentMonday.getDate() + i);
            const dateStr = dayDate.toISOString().split('T')[0]; // YYYY-MM-DD

            const dayHeader = document.createElement('div');
            dayHeader.className = 'day-header';
            dayHeader.textContent = formatDate(dayDate);
            calendarContainer.appendChild(dayHeader);

            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';

            const dayLessons = lessonsData[dateStr] || [];

            // Create slots for each time
            timeSlots.forEach(time => {
                const lesson = dayLessons.find(l => l.start_time === time);
                const slotDiv = document.createElement('div');
                
                if (lesson) {
                    slotDiv.appendChild(createLessonSlot({
                        ...lesson,
                        date: dateStr
                    }));
                } else {
                    slotDiv.className = 'lesson-slot empty';
                }
                
                dayColumn.appendChild(slotDiv);
            });

            calendarContainer.appendChild(dayColumn);
        }
    }

    async function fetchBARRELessons(from, to) {

        if (!window.barreAjax) {
            console.error('barreAjax object not found. Make sure wp_localize_script is set.');
            throw new Error('Missing AJAX configuration');
        }

        const formData = new FormData();
        formData.append('action', 'barre_load_schedule');
        formData.append('from_date', from);
        formData.append('to_date', to);
        formData.append('_ajax_nonce', barreAjax.nonce);


        try {
            const response = await fetch(barreAjax.ajaxurl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.data?.message || 'Server returned error');
            }

            // Expected format: { "2026-01-12": [lesson1, lesson2, ...], ... }
            return data.data.lessons || {};

        } catch (error) {
            console.error('Schedule loading failed:', error);
            throw error;
        }
    }
    function loadWeek() {
        updateWeekRange();
        
        const calendarContainer = document.querySelector('.week-view');
        const loadingHTML = '<div class="loading" style="grid-column: 2 / -1; text-align:center; padding:4rem;">Loading schedule...</div>';
        calendarContainer.innerHTML = loadingHTML;

        const mondayStr = currentMonday.toISOString().split('T')[0];
        const sunday = new Date(currentMonday);
        sunday.setDate(currentMonday.getDate() + 6);
        const sundayStr = sunday.toISOString().split('T')[0];

        fetchBARRELessons(mondayStr, sundayStr)
            .then(data => renderCalendar(data))
            .catch(err => {
                calendarContainer.innerHTML = `
                    <div style="grid-column: 2 / -1; text-align:center; padding:4rem; color:#dc3545;">
                        Failed to load schedule<br>
                        <small>${err.message}</small>
                    </div>`;
            });
    }

    // Navigation
    prevBtn.addEventListener('click', () => {
        currentMonday.setDate(currentMonday.getDate() - 7);
        loadWeek();
    });

    nextBtn.addEventListener('click', () => {
        currentMonday.setDate(currentMonday.getDate() + 7);
        loadWeek();
    });

    // Initial load
    loadWeek();



    // ====================
    //     BASKET SYSTEM
    // ====================

    const BASKET_KEY = 'barre_reservation_basket';

    let basket = JSON.parse(sessionStorage.getItem(BASKET_KEY)) || [];

    // Format: [
    //   { lessonId: 123, name: "...", date: "2026-01-12", time: "18:00", price: 400, persons: 1 },
    //   ...
    // ]

    function saveBasket() {
        sessionStorage.setItem(BASKET_KEY, JSON.stringify(basket));
        updateBasketUI();
    }

    function updateBasketUI() {
        const countEl = document.getElementById('basketCount');
        const itemsContainer = document.getElementById('basketItems');
        const totalEl = document.getElementById('basketTotal');
        const checkoutBtn = document.getElementById('goToCheckout');

        if (!countEl) return;

        countEl.textContent = basket.length;

        if (basket.length === 0) {
            itemsContainer.innerHTML = '<p style="text-align:center;color:#777;">Your basket is empty</p>';
            totalEl.textContent = '0 Kč';
            checkoutBtn.disabled = true;
            return;
        }

        let total = 0;
        let html = '';

        basket.forEach((item, index) => {
            const itemTotal = item.price * item.persons;
            total += itemTotal;

            html += `
                <div class="basket-item">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small>${item.date} • ${item.time} • ${item.persons} person(s)</small>
                    </div>
                    <div style="text-align:right;">
                        ${itemTotal} Kč
                        <button class="remove-item" data-index="${index}" style="font-size:0.8em;color:#dc3545;border:none;background:none;cursor:pointer;">×</button>
                    </div>
                </div>
            `;
        });

        itemsContainer.innerHTML = html;
        totalEl.textContent = total.toLocaleString('cs-CZ') + ' Kč';
        checkoutBtn.disabled = false;

        // Add remove handlers
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                basket.splice(index, 1);
                saveBasket();
            });
        });
    }

    // Toggle basket visibility
    document.addEventListener('click', function(e) {
        const toggle = document.getElementById('toggleBasket');
        const content = document.getElementById('basketContent');

        if (e.target === toggle || toggle?.contains(e.target)) {
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
            toggle.textContent = content.style.display === 'none' ? '▼' : '▲';
        }
        // Close when clicking outside (optional)
        else if (!document.getElementById('basketFloat')?.contains(e.target)) {
            if (content) content.style.display = 'none';
            if (toggle) toggle.textContent = '▼';
        }
    });



    // Add to basket from lesson slot

    /*/
    // Clear basket button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'clearBasket') {
            if (confirm("Clear entire basket?")) {
                basket = [];
                saveBasket();
            }
        }
    });
    /*/
    // === REPLACE CONFIRM() FOR CLEAR BASKET ===
    document.addEventListener('click', function(e) {
        if (e.target.id === 'clearBasket') {
            if (basket.length === 0) {
                showErrorModal('Your basket is already empty.');
            } else {
                showClearConfirmModal();
            }
        }
    });
    //*/

    // Checkout button – later will redirect to checkout page
    document.addEventListener('click', function(e) {
        if (e.target.id === 'goToCheckout') {
            if (basket.length === 0) return;
            
            // Later: window.location = '/checkout';
            alert("Redirecting to checkout page...\n(In real project this would go to payment step)");
            // You can also send basket to server via AJAX here
        }
    });

    // Initial UI update
    document.addEventListener('DOMContentLoaded', () => {
        updateBasketUI();
    });

//*/
    // Global variables for modal
    const modal = document.getElementById('addToBasketModal');
    let currentLessonToAdd = null;

    // Open modal when lesson is clicked
    function openAddToBasketModal(lesson) {
        currentLessonToAdd = lesson;

        // Fill modal content
        document.getElementById('modalLessonName').textContent = lesson.name;
        document.getElementById('modalLessonInfo').innerHTML = `
            ${lesson.date} • ${lesson.start_time} – ${lesson.end_time}<br>
            Instructor: ${lesson.instructor}<br>
            Price per person: ${lesson.price} Kč
        `;
        
        const available = lesson.available;
        document.getElementById('modalAvailable').textContent = available;

        const select = document.getElementById('modalPersons');
        select.innerHTML = ''; // clear previous options

        for (let i = 1; i <= 3; i++) {
            if (i <= available) {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = `${i} person${i > 1 ? 's' : ''}`;
                select.appendChild(opt);
            }
        }

        // Default to 1 or max available
        select.value = Math.min(1, available);

        // Initial price calculation
        updateModalTotalPrice();

        // Show modal
        document.getElementById('addToBasketModal').style.display = 'flex';
    }

    // Update total price live when persons change
    function updateModalTotalPrice() {
        if (!currentLessonToAdd) return;
        
        const persons = parseInt(document.getElementById('modalPersons').value) || 0;
        const total = persons * currentLessonToAdd.price;
        
        document.getElementById('modalTotalPrice').textContent = 
            total.toLocaleString('cs-CZ') + ' Kč';
        
        document.getElementById('confirmAdd').disabled = (persons < 1 || persons > currentLessonToAdd.available);
    }

    // Close modal handlers
    function closeModal() {
        document.getElementById('addToBasketModal').style.display = 'none';
        currentLessonToAdd = null;
    }

    document.getElementById('closeModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelAdd')?.addEventListener('click', closeModal);

    // Close when clicking outside content
    document.getElementById('addToBasketModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    // Esc key to close
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal?.style.display === 'flex') {
            closeModal();
        }
    });
//*/
// === REPLACE OLD PROMPT + ALERTS IN CONFIRM ADD ===
document.getElementById('confirmAdd')?.addEventListener('click', function() {
    if (!currentLessonToAdd) return;

    const persons = parseInt(document.getElementById('modalPersons').value);

    if (persons > currentLessonToAdd.available) {
        showErrorModal('Not enough spots available!');
        return;
    }

    const item = {
        lessonId: currentLessonToAdd.id,
        name: currentLessonToAdd.name,
        date: currentLessonToAdd.date,
        time: currentLessonToAdd.start_time,
        price: currentLessonToAdd.price,
        persons: persons
    };

    // Check for duplicate
    const alreadyExists = basket.some(i => i.lessonId === item.lessonId);
    if (alreadyExists) {
        showErrorModal('This lesson is already in your basket.');
        closeModal(); // close person selection modal
        return;
    }

    // SUCCESS → add and show modal
    basket.push(item);
    saveBasket();
    updateBasketUI();

    showSuccessModal(`${item.name} × ${persons} person${persons > 1 ? 's' : ''} added to basket!`);
    closeModal(); // close person selection modal
});
/*/
    // Confirm → add to basket
    document.getElementById('confirmAdd')?.addEventListener('click', function() {
        if (!currentLessonToAdd) return;

        const persons = parseInt(document.getElementById('modalPersons').value);

        if (persons > currentLessonToAdd.available) {
            alert('Not enough spots available!');
            showErrorModal('Not enough spots available!');
            return;
        }

        const item = {
            lessonId: currentLessonToAdd.id,
            name: currentLessonToAdd.name,
            date: currentLessonToAdd.date,
            time: currentLessonToAdd.start_time,
            price: currentLessonToAdd.price,
            persons: persons
        };

        // Check duplicate (same lesson already in basket)
        const alreadyExists = basket.some(i => i.lessonId === item.lessonId);
        if (alreadyExists) {
            alert('This lesson is already in your basket.');
            showErrorModal('This lesson is already in your basket.');
            closeModal();
            return;
        }

        basket.push(item);
        saveBasket();
        alert(`Added: ${item.name} × ${persons} person(s)`);
        closeModal();
    });
    //*/

    // Update price when selection changes
    document.getElementById('modalPersons')?.addEventListener('change', updateModalTotalPrice);



    // === REUSABLE MODAL FUNCTIONS ===
    function showSuccessModal(message) {
        document.getElementById('successMessage').textContent = message;
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function showErrorModal(message) {
        document.getElementById('errorMessage').textContent = message;
        const modal = document.getElementById('errorModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function showClearConfirmModal() {
        const modal = document.getElementById('clearConfirmModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeAllModals() {
        document.querySelectorAll('.modal').forEach(m => {
            m.classList.remove('show');
            setTimeout(() => m.style.display = 'none', 300);
        });
    }

    // Close handlers
    document.querySelectorAll('.modal-close, #closeErrorBtn, #continueShopping, #cancelClear').forEach(el => {
        el?.addEventListener('click', closeAllModals);
    });

    document.getElementById('viewBasketBtn')?.addEventListener('click', () => {
        closeAllModals();
        document.getElementById('basketContent').style.display = 'block';
    });

    document.getElementById('confirmClear')?.addEventListener('click', () => {
        basket = [];
        saveBasket();
        closeAllModals();
        updateBasketUI(); // refresh floating basket
        showSuccessModal('Your basket has been cleared.');
    });

    // Esc key for all modals
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllModals();
    });
    

});