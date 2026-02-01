jQuery(document).ready(function($) {

    let currentMonday = getMondayOfCurrentWeek();
    const _prevBtn = document.getElementById('prev-week');
    const _nextBtn = document.getElementById('next-week');

    function getMondayOfCurrentWeek() {
        const today = new Date();
        const day = today.getDay();
        const diff = day === 0 ? -6 : 1 - day; // Sunday = 0
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        monday.setHours(0,0,0,0);
        return monday;
    }
    
    // Example how admin JS can use the richer response
    /*
    function renderAdminCalendar(data) {
        const container = document.getElementById('barre-admin-calendar');
        let html = '<div class="admin-week-grid">';

        // Days headers...
        // ...

        for (const date in data.lessons) {
            const lessons = data.lessons[date];
            let dayHtml = `<div class="admin-day"><h4>${date}</h4>`;

            lessons.forEach(lesson => {
                const statusClass = lesson.available <= 0 ? 'full' :
                                   (lesson.available <= 3 ? 'almost-full' : 'available');

                dayHtml += `
                    <div class="admin-lesson-slot ${statusClass}">
                        <div class="time">${lesson.start_time} – ${lesson.end_time}</div>
                        <strong>${lesson.name}</strong>
                        <div class="instructor">👤 ${lesson.instructor}</div>
                        <div class="occupancy">
                            ${lesson.used}/${lesson.capacity} (${lesson.occupancy})
                        </div>
                        <div class="price">${lesson.price_formatted}</div>
                        <div class="actions">
                            <a href="${lesson.edit_url}" class="button button-small">Edit</a>
                            ${lesson.can_delete ? 
                                `<button class="button button-small button-link-delete js-delete-lesson" 
                                         data-id="${lesson.id}" 
                                         data-nonce="${lesson.delete_nonce}">Delete</button>` : ''}
                        </div>
                    </div>`;
            });

            dayHtml += '</div>';
            html += dayHtml;
        }

        container.innerHTML = html;
    }
    // Initial load
    // renderAdminCalendar();
    */

    function loadAdminSchedule(weekStart = null) {
        $('#barre-admin-calendar').html('<div class="loading">Loading schedule...</div>');

        const data = {
            action:     'barre_admin_load_schedule',
            _ajax_nonce: barreAdminAjax.nonce,
        };

        // console.log(weekStart);

        if (weekStart) {
            data.from_date = weekStart;
            // You can also send to_date = +6 days if you want
        }

        $.ajax({
            url: barreAdminAjax.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#barre-admin-calendar').html(response.data.html);
                    $('.current-week-display').text(response.data.week_range);

                    // Optional: attach delete handlers after render
                    $('.delete-lesson').on('click', function(e) {
                        e.preventDefault();
                        if (!confirm('Really delete this lesson?')) return;
                        // ... AJAX delete call ...
                    });
                } else {
                    $('#barre-admin-calendar').html('<p style="color:red">Error loading schedule</p>');
                }
            },
            error: function() {
                $('#barre-admin-calendar').html('<p style="color:red">Connection error</p>');
            }
            // error: function (xhr, ajaxOptions, thrownError) {
            //     // alert(xhr.status);
            //     $('#barre-admin-calendar').html('<p style="color:red">Status: ' + xhr.status + '</p><br><p style="color:red">Error: ' + thrownError + '</p>');
            // }
        });
    }

    // Initial load
    loadAdminSchedule();

    /*
    // Example: week navigation
    // $('#prev-week').on('click', function() {
        // You would calculate previous monday here
        // For simplicity - just reload with some param
        loadAdminSchedule(); // ← real version would send different from_date
        currentMonday.setDate(currentMonday.getDate() - 7);
        loadAdminSchedule(currentMonday);
    });
    */

    _prevBtn.addEventListener('click', () => {
        currentMonday.setDate(currentMonday.getDate() - 7);
// console.log(currentMonday);
        loadAdminSchedule(currentMonday);
    });

    _nextBtn.addEventListener('click', () => {
        currentMonday.setDate(currentMonday.getDate() + 7);
// console.log(currentMonday);
        loadAdminSchedule(currentMonday);
    });
});