jQuery(document).ready(function($) {
    /*
    function loadAdminSchedule() {
        $('#barre-schedule-calendar').html('<p>Loading...</p>');

        $.ajax({
            url: barreAdminAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'barre_admin_load_schedule',
                _ajax_nonce: barreAdminAjax.nonce,
                week_start: 'current' // you can later add real date param
            },

            error: function (xhr, ajaxOptions, thrownError) {
                // alert(xhr.status);
                $('#barre-schedule-calendar').html('<p style="color:red">Status: ' + xhr.status + '</p><br><p style="color:red">Error: ' + thrownError + '</p>');
                
            },
            success: function(response) {
                if (response.success) {
                    $('#barre-schedule-calendar').html(response.data.html);
                } else {
                    $('#barre-schedule-calendar').html('<p style="color:red">Error loading schedule</p>');
                }
            }
        });
    }

    loadAdminSchedule();

    */
    /*
    async function fetchBarreLessons(from, to) {
        $('#barre-schedule-calendar').html('<p>Loading...</p>');

        const response = await fetch(yogaAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                // action: 'barre_load_schedule',
                action: 'barre_admin_load_schedule',
                from_date: from,
                to_date: to,
                _ajax_nonce: barreAjax.nonce
            })
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.data?.message || 'Failed to load schedule');
        }

        return result.data.lessons;
    }
    */
    // Example how admin JS can use the richer response
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

    // renderAdminCalendar();

    $('#prev-week, #next-week').on('click', function() {
        alert('Week navigation - to be implemented');
        // → update week_start param and reload
    });
});