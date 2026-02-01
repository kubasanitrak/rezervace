jQuery(document).ready(function($) {
    
    let currentMonday = getMondayOfThisWeek();  // initial value

    function getMondayOfThisWeek() {
        const today = new Date();
        const day = today.getDay();
        const diff = day === 0 ? -6 : 1 - day; // Sunday = 0
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        monday.setHours(0, 0, 0, 0);
        return monday;
    }

    function formatDateForServer(date) {
        return date.toISOString().split('T')[0]; // YYYY-MM-DD
    }

    function updateWeekDisplay() {
        const sunday = new Date(currentMonday);
        sunday.setDate(currentMonday.getDate() + 6);

        const format = new Intl.DateTimeFormat('cs-CZ', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });

        const range = `${format.format(currentMonday)} – ${format.format(sunday)}`;
        $('#weekDisplay').text(range);
    }

    function loadSchedule() {
        $('#barre-admin-calendar').html('<div class="loading">Loading schedule...</div>');
        $('#prev-week, #next-week').prop('disabled', true);

        const fromDate = formatDateForServer(currentMonday);
        const toDate = formatDateForServer(new Date(currentMonday.getTime() + 6 * 24 * 60 * 60 * 1000));

        $.ajax({
            url: barreAdminAjax.ajaxurl,
            type: 'POST',
            data: {
                action:     'barre_admin_load_schedule',
                _ajax_nonce: barreAdminAjax.nonce,
                from_date:  fromDate,
                to_date:    toDate
            },
            success: function(response) {
                if (response.success) {
                    $('#barre-admin-calendar').html(response.data.html);
                    $('#weekDisplay').text(response.data.week_range);
                } else {
                    $('#barre-admin-calendar').html(
                        '<p style="color:#dc3545; padding:2rem; text-align:center;">' +
                        'Error loading schedule<br><small>' +
                        (response.data?.message || 'Unknown error') +
                        '</small></p>'
                    );
                }
            },
            error: function() {
                $('#barre-admin-calendar').html(
                    '<p style="color:#dc3545; padding:2rem; text-align:center;">Connection error</p>'
                );
            },
            complete: function() {
                $('#prev-week, #next-week').prop('disabled', false);
            }
        });
    }

    // Navigation handlers
    $('#prev-week').on('click', function() {
        currentMonday.setDate(currentMonday.getDate() - 7);
        updateWeekDisplay();
        loadSchedule();
    });

    $('#next-week').on('click', function() {
        currentMonday.setDate(currentMonday.getDate() + 7);
        updateWeekDisplay();
        loadSchedule();
    });

    // Initial load
    updateWeekDisplay();
    loadSchedule();

});