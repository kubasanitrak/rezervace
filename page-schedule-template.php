<?php
/**
 * Template Name: PAGE SCHEDULE
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */

     get_header(); 


?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

     <div class="section section-mar-T full-bleed">
		
	
			<h1 class="page-title <?php echo $CLS; ?>"><?php echo the_title(); ?></h1>
	
			<?php the_content(); ?>
			<!-- template-schedule.php or any page template -->
                <div class="barre-schedule-container">
                    <div class="calendar-header">
                        <button class="nav-btn prev-week">← Previous Week</button>
                        <h2 class="current-week-range"></h2>
                        <button class="nav-btn next-week">Next Week →</button>
                    </div>

                    <div class="calendar-view week-view">
                        <!-- Will be filled by JS -->
                    </div>

                    <div class="legend">
                        <div class="status available">Available</div>
                        <div class="status almost-full">Almost Full</div>
                        <div class="status full">Full</div>
                    </div>
                </div>
            
            <!-- Add this somewhere visible – e.g. fixed bottom right corner -->
                <div class="basket-float" id="basketFloat">
                    <div class="basket-header">
                        <span>Your Basket (<span id="basketCount">0</span>)</span>
                        <button class="basket-toggle" id="toggleBasket">▼</button>
                    </div>
                    
                    <div class="basket-content" id="basketContent" style="display:none;">
                        <div id="basketItems"></div>
                        <div class="basket-total">
                            Total: <strong id="basketTotal">0 Kč</strong>
                        </div>
                        <button class="btn-checkout" id="goToCheckout" disabled>Proceed to Checkout</button>
                        <button class="btn-clear" id="clearBasket">Clear</button>
                    </div>
                </div>

		</div><!-- END SECTION -->
		
	<?php endwhile; endif; ?>


<script>
// =============================================
//   BARRE Schedule - Week View (Vanilla JS)
// =============================================

document.addEventListener('DOMContentLoaded', () => {
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
    /*/
    function createLessonSlot(lesson) {
        const slot = document.createElement('div');
        slot.className = 'lesson-slot';
        
        // Determine status
        const available = lesson.capacity - lesson.used;
        let statusClass = 'available';
        if (available <= 0) statusClass = 'full';
        else if (available <= 3) statusClass = 'almost-full';
        
        slot.classList.add(statusClass);
        
        slot.innerHTML = `
            <div class="lesson-name">${lesson.name}</div>
            <div class="lesson-time">${lesson.start_time} – ${lesson.end_time}</div>
            <div class="lesson-instructor">👤 ${lesson.instructor}</div>
            <div class="capacity-info">
                ${available > 0 ? `${available}/${lesson.capacity}` : 'Full'}
            </div>
        `;

        if (available > 0) {
            slot.addEventListener('click', () => {
                // Here you will later open modal / add to basket
                // alert(`Selected: ${lesson.name} at ${lesson.start_time} on ${lesson.date}`);
                // Future: addToBasket(lesson.id, 1);
            });
        }

        return slot;
    }
    /*/

    // =============================================
    //   Connect to lesson slots (update createLessonSlot)
    // =============================================

    function createLessonSlot(lesson) {
        const slot = document.createElement('div');
        // ... rest of your existing code ...
        slot.className = 'lesson-slot';

        const available = lesson.capacity - lesson.used;
        let statusClass = 'available';
        if (available <= 0) statusClass = 'full';
        else if (available <= 3) statusClass = 'almost-full';

        slot.classList.add(statusClass);

        // ... your existing innerHTML ...
        slot.innerHTML = `
            <div class="lesson-name">${lesson.name}</div>
            <div class="lesson-time">${lesson.start_time} – ${lesson.end_time}</div>
            <div class="lesson-instructor">👤 ${lesson.instructor}</div>
            <div class="capacity-info">
                ${available > 0 ? `${available}/${lesson.capacity}` : 'Full'}
            </div>
        `;

        if (available > 0) {
            slot.addEventListener('click', () => {
                addToBasket({
                    id: lesson.id,
                    name: lesson.name,
                    date: lesson.date,
                    start_time: lesson.start_time,
                    capacity: lesson.capacity,
                    used: lesson.used,
                    price: lesson.price
                });
            });
        }

        return slot;
    }
    //*/

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


    
    //*/
    // Simulated / placeholder AJAX function
    async function fetchBARRELessons(from, to) {
        // In real project replace with real AJAX call:
        // return fetch(ajaxurl, {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //     body: new URLSearchParams({
        //         action: 'barre_load_schedule',
        //         from_date: from,
        //         to_date: to,
        //         _ajax_nonce: barreAjax.nonce
        //     })
        // }).then(r => r.json());

        // For demo - fake data
        return new Promise(resolve => {
            setTimeout(() => {
                const fakeData = {};
                const sampleLessons = [
                    { name: "Morning Flow", start_time: "07:00", end_time: "08:15", instructor: "Anna", capacity: 14, used: 8 },
                    { name: "Power Vinyasa", start_time: "18:00", end_time: "19:30", instructor: "Martin", capacity: 16, used: 15 },
                    { name: "Gentle BARRE", start_time: "09:00", end_time: "10:15", instructor: "Lucie", capacity: 12, used: 12 }
                ];

                for (let i = 0; i < 7; i++) {
                    const d = new Date(currentMonday);
                    d.setDate(currentMonday.getDate() + i);
                    const dateKey = d.toISOString().split('T')[0];
                    
                    if (i % 2 === 0) { // some days have classes
                        fakeData[dateKey] = [
                            ...sampleLessons.slice(0, 2),
                            { ...sampleLessons[2], used: 0 }
                        ];
                    }
                }
                resolve(fakeData);
            }, 800);
        });
    }
    // ==========================================
    //           Navigation & Data Loading
    // ==========================================
    function loadWeek() {
        updateWeekRange();

        const mondayStr = currentMonday.toISOString().split('T')[0];
        const sunday = new Date(currentMonday);
        sunday.setDate(currentMonday.getDate() + 6);
        const sundayStr = sunday.toISOString().split('T')[0];

        // Real implementation will use AJAX here
        fetchBARRELessons(mondayStr, sundayStr)
            .then(data => renderCalendar(data))
            .catch(err => {
                console.error('Failed to load lessons:', err);
                calendarContainer.innerHTML = '<p style="grid-column: 2 / -1; text-align:center; padding:2rem;">Error loading schedule...</p>';
            });
    }
/*/ REPLACE WITH REAL AJAX = Real WordPress AJAX Loading
    async function fetchBARRELessons(from, to) {
        // WordPress localized script variables (you need to enqueue them)
        // Assume you have something like:
        // wp_localize_script('barre-schedule-js', 'barreAjax', [
        //     'ajaxurl' => admin_url('admin-ajax.php'),
        //     'nonce'   => wp_create_nonce('barre_schedule_nonce')
        // ]);
        // In real project replace with real AJAX call:
        // return fetch(ajaxurl, {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //     body: new URLSearchParams({
        //         action: 'barre_load_schedule',
        //         from_date: from,
        //         to_date: to,
        //         _ajax_nonce: barreAjax.nonce
        //     })
        // }).then(r => r.json());

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
//*/
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
    function addToBasket(lesson) {
        // You can later add nice modal for selecting number of persons
        const persons = prompt(`How many persons for "${lesson.name}"?\n(1–3, available: ${lesson.capacity - lesson.used})`, "1");

        const numPersons = parseInt(persons);
        if (!numPersons || numPersons < 1 || numPersons > 3) {
            alert("Please select 1 to 3 persons.");
            return;
        }

        if (numPersons > (lesson.capacity - lesson.used)) {
            alert("Not enough spots available!");
            return;
        }

        const item = {
            lessonId: lesson.id,
            name: lesson.name,
            date: lesson.date,
            time: lesson.start_time,
            price: lesson.price,
            persons: numPersons
        };

        // Very simple duplicate check – you can improve later
        const alreadyExists = basket.some(i => i.lessonId === lesson.id);
        if (alreadyExists) {
            alert("This lesson is already in your basket.");
            return;
        }

        basket.push(item);
        saveBasket();
        alert(`Added: ${lesson.name} × ${numPersons} person(s)`);
    }
    // Clear basket button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'clearBasket') {
            if (confirm("Clear entire basket?")) {
                basket = [];
                saveBasket();
            }
        }
    });

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
});
</script>


	<?php get_footer(); ?>