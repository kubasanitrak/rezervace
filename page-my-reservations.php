<?php
/**
 * Template Name: PAGE My Reservations
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */


    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit;
    }
         get_header(); 


    if ( have_posts() ) : while ( have_posts() ) : the_post();

    $current_user_id = get_current_user_id();

    global $wpdb;
    $res_table   = $wpdb->prefix . 'barre_reservations';
    $lessons_tbl = $wpdb->prefix . 'barre_lessons';

    $my_bookings = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            r.id, r.lesson_id, r.num_persons, r.status, r.created_at, r.cancelled_at,
            l.name, l.date, l.start_time, l.duration, l.price
         FROM $res_table r
         INNER JOIN $lessons_tbl l ON r.lesson_id = l.id
         WHERE r.user_id = %d
         ORDER BY COALESCE(r.cancelled_at, r.created_at) DESC, l.date DESC",
        $current_user_id
    ), ARRAY_A);

?>
<?php
    $confirmed = [];
    $cancelled = [];
    foreach ($my_bookings as $b) {
        if ($b['status'] === 'confirmed') $confirmed[] = $b;
        else $cancelled[] = $b;
    }
?>
<div class="section section-mar-T full-bleed">
    <h1 class="page-title <?php echo $CLS; ?>"><?php echo the_title(); ?></h1>
    <h2>Confirmed Reservations (<?= count($confirmed) ?>)</h2>

    <?php if (empty($confirmed)): ?>
        <p>No confirmed reservations.</p>
        <?php else: ?>
            <table class="reservations-table">
                <!-- same thead as before -->
                 <thead>
                    <tr>
                        <th>Class</th>
                        <th>Date & Time</th>
                        <th>Persons</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($confirmed as $booking):
                        $start_datetime = $booking['date'] . ' ' . $booking['start_time'];
                        $can_cancel     = strtotime($start_datetime) > (time() + 24 * 3600); // >24h before
                    ?>
                    <!-- same row structure as before, with Cancel button if allowed -->
                    <tr>
                            <td><?= esc_html($booking['name']) ?></td>
                            <td><?= date_i18n('D, d.m.Y H:i', strtotime($start_datetime)) ?></td>
                            <td><?= $booking['num_persons'] ?></td>
                            <td><?= number_format($booking['price'] * $booking['num_persons'], 0) ?> Kč</td>
                            <td>
                                <span class="status-badge confirmed">Confirmed</span>
                            </td>
                            <td class="actions">
                                <button class="btn-small view-details" 
                                        data-res-id="<?= $booking['id'] ?>">
                                    Details
                                </button>

                                <?php if ($can_cancel): ?>
                                    <button class="btn-small btn-cancel" 
                                            data-res-id="<?= $booking['id'] ?>">
                                        Cancel
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Too late to cancel</span>
                                <?php endif; ?>

                                <!-- <button class="btn-small btn-reschedule disabled" disabled>
                                    Reschedule (coming soon)
                                </button> -->
                                <button class="btn-small btn-reschedule" 
                                        data-res-id="<?= $booking['id'] ?>"
                                        data-lesson-id="<?= $booking['lesson_id'] ?>">
                                    Reschedule
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2 style="margin-top:3rem;">Cancelled Reservations (<?= count($cancelled) ?>)</h2>

        <?php if (empty($cancelled)): ?>
            <p>No cancelled reservations.</p>
        <?php else: ?>
            <table class="reservations-table cancelled-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Date & Time</th>
                        <th>Persons</th>
                        <th>Price</th>
                        <th>Cancelled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cancelled as $booking): ?>
                        <tr class="cancelled-row">
                            <td><?= esc_html($booking['name']) ?></td>
                            <td><?= date_i18n('D, d.m.Y H:i', strtotime($booking['date'] . ' ' . $booking['start_time'])) ?></td>
                            <td><?= $booking['num_persons'] ?></td>
                            <td><?= number_format($booking['price'] * $booking['num_persons'], 0) ?> Kč</td>
                            <td><?= $booking['cancelled_at'] ? date_i18n('d.m.Y H:i', strtotime($booking['cancelled_at'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

</div>

<!-- Cancel confirmation modal -->
<div id="cancelConfirmModal" class="modal" style="display:none; ">
    <div class="modal-content">
        <span class="modal-close btn-icn--close" id="closeCancelConfirm">×</span>
        <h2>Cancel Reservation?</h2>
        <p id="cancelMessage">This will cancel your booking. Are you sure?</p>
        <div class="modal-actions">
            <button id="cancelCancel" class="btn-secondary">No, keep it</button>
            <button id="confirmCancel" class="btn-danger">Yes, Cancel</button>
        </div>
    </div>
</div>

<!-- Cancel success/error modal -->
<div id="cancelResultModal" class="modal" style="display:none; ">
    <div class="modal-content">
        <span class="modal-close btn-icn--close" id="closeCancelResult">×</span>
        <div class="modal-icon" id="cancelIcon">?</div>
        <h2 id="cancelResultTitle">Result</h2>
        <p id="cancelResultMessage"></p>
        <div class="modal-actions">
            <button id="closeCancelResultBtn" class="btn-primary">OK</button>
        </div>
    </div>
</div>

<!-- Reschedule modal with calendar picker -->
<div id="rescheduleModal" class="modal" style="display:none; ">
    <div class="modal-content" style="max-width:900px; width:95%;">
        <span class="modal-close btn-icn--close" id="closeReschedule">×</span>
        <h2>Reschedule Your Booking</h2>
        <p id="rescheduleCurrentInfo"></p>

        <div class="reschedule-controls">
            <button id="resPrevWeek" class="button">← Previous Week</button>
            <span id="resWeekRange">Loading week...</span>
            <button id="resNextWeek" class="button">Next Week →</button>
        </div>

        <div id="rescheduleCalendar" class="calendar-container">
            <!-- Will be filled with week grid via JS/AJAX -->
            <div class="loading">Loading available slots...</div>
        </div>

        <div class="modal-actions" style="margin-top:1.5rem;">
            <button id="cancelReschedule" class="btn-secondary">Cancel</button>
            <button id="confirmReschedule" class="btn-primary" disabled>Confirm New Slot</button>
        </div>
    </div>
</div>


        
    <?php endwhile; endif; ?>
<?php get_footer(); ?>