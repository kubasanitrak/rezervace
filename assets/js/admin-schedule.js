jQuery(document).ready(function($) {
    
    let currentMonday = getMondayOfCurrentWeek();  // renamed for clarity

    /**
     * Returns the Date object for Monday of the current week
     * (Monday is day 1, Sunday is day 0 in getDay())
     */
    function getMondayOfCurrentWeek() {
        const today = new Date();
        const day = today.getDay();           // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        
        let diff;
        if (day === 0) {
            // Sunday → go back 6 days to last Monday
            diff = -6;
        } else {
            // Monday → Saturday → subtract (day - 1) days
            diff = 1 - day;
        }
        
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        monday.setHours(0, 0, 0, 0);
// FIX TIMEZONE DIFF DUE TO METHOD TOISOSTRING() CONVERT TIME ALWAYS TO UTC
        monday.setMinutes(monday.getMinutes() - monday.getTimezoneOffset());
        return monday;
    }

    function formatDateForServer(date) {
        return date.toISOString().split('T')[0]; // YYYY-MM-DD
    }

    function updateWeekDisplay() {
        const sunday = new Date(currentMonday);
        sunday.setDate(currentMonday.getDate() + 6);

        const format = new Intl.DateTimeFormat('cs-CZ', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });

        // Example: Po 27. led 2026 – Ne 2. úno 2026
        const range = `${format.format(currentMonday)} – ${format.format(sunday)}`;
        $('#weekDisplay').text(range);
// console.log(range);
    }

    function loadSchedule() {
        $('#barre-admin-calendar').html('<div class="loading">Načítám rozvrh...</div>');
        $('#prev-week, #next-week').prop('disabled', true);

        const fromDate = formatDateForServer(currentMonday);
        const toDate   = formatDateForServer(new Date(currentMonday.getTime() + 6 * 24 * 60 * 60 * 1000));

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
                    $('#weekDisplay').text(response.data.week_range || 'Načteno');
                } else {
                    $('#barre-admin-calendar').html(
                        '<p style="color:#dc3545; padding:2rem; text-align:center;">' +
                        'Chyba při načítání rozvrhu<br><small>' +
                        (response.data?.message || 'Neznámá chyba') +
                        '</small></p>'
                    );
                }
            },
            error: function() {
                $('#barre-admin-calendar').html(
                    '<p style="color:#dc3545; padding:2rem; text-align:center;">Chyba spojení</p>'
                );
            },
            complete: function() {
                $('#prev-week, #next-week').prop('disabled', false);
            }
        });
    }

    // Navigation
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

    // Optional: button "Tento týden" (Today / This week)
    // You can add this button to HTML: <button id="this-week">Tento týden</button>
    $('#this-week').on('click', function() {
        currentMonday = getMondayOfCurrentWeek();
        updateWeekDisplay();
        loadSchedule();
    });

    // Initial load
    updateWeekDisplay();
    loadSchedule();
});