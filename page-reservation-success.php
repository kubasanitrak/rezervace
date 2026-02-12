<?php
/**
 * Template Name: PAGE Success
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

           <?php
                // template-barre-success.php or just a page with content
                

                // if (isset($_GET['session_id'])) {
                //     // Optional: verify session via Stripe API if needed
                //     echo '<div class="success-notice">';
                //     echo '<h1>Thank you for your reservation!</h1>';
                //     echo '<p>Your payment was successful. You will receive a confirmation email shortly.</p>';
                //     echo '<p><a href="' . home_url('/my-reservations') . '">View My Reservations</a></p>';
                //     echo '</div>';
                // } else {
                //     echo '<p>No session information.</p>';
                // }
                if (isset($_GET['simulated'])) {
            ?>
                <div class="success-notice" style="text-align:center; padding:4rem 1rem;">
                    <h1 style="color:#28a745;">Thank You!</h1>
                    <div class="notice-row plain">
                        <p style="font-size:1.3rem; margin:2rem 0;">
                            Your reservation has been confirmed (simulation mode).
                        </p>
                    </div>
                    <div class="notice-row plain">
                        <p>You would now receive a confirmation email.</p>
                    </div>
                    <div class="notice-row btn-group">
                        <a href="<?php echo home_url('/my-reservations'); ?>" class="btn-primary">View My Reservations</a>
                        <a href="<?php echo home_url('/schedule'); ?>" id="bookAnother" class="btn-secondary">Book another class</a>
                    </div>
                </div>
            <?php
                } else {
                echo '<p>No payment information.</p>';
                }

            ?>

    </div>

    <?php if (is_page('reservation-success') || is_page_template('page-reservation-success.php')): ?>
    <script>
        // Clear basket on success page (safety net)
        if (typeof basket !== 'undefined') {
            basket = [];
            sessionStorage.removeItem('barre_reservation_basket');
            // Optional: update floating basket if it's loaded
            if (typeof updateBasketUI === 'function') {
                updateBasketUI();
            }
        }
        document.getElementById('bookAnother')?.addEventListener('click', function(e) {
            // e.preventDefault(); // optional — if you want to do something before redirect
            basket = [];
            sessionStorage.removeItem('barre_reservation_basket');
        });
    </script>
    <?php endif; ?>

		
	<?php endwhile; endif; ?>
<?php get_footer(); ?>