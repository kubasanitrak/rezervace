<?php
/**
 * Template Name: PAGE Checkout
 *
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

            if (!is_user_logged_in()) {
                echo '<div class="alert">Please <a href="' . wp_login_url(get_permalink()) . '">log in</a> or <a href="' . wp_registration_url() . '">register</a> to continue.</div>';
                get_footer();
                exit;
}

?>

        <div class="checkout-container">
            <h1>Review Your Reservation</h1>

            <div id="checkout-summary"></div>

            <div class="total-section">
                <h3>Total: <span id="checkout-total">0 Kč</span></h3>
            </div>

            <!-- <button id="confirm-and-pay" class="btn-pay">Proceed to Payment →</button>
            <button id="back-to-schedule">← Back to Schedule</button> -->
            <!-- in template-barre-checkout.php or similar -->

            

            <div class="checkout-actions">
                <button type="button" id="simulatePayment" class="btn-pay">
                    Simulate Payment (Test Mode)
                </button>
                <a href="/schedule" class="btn-secondary">Back to Schedule</a>
            </div>

            <!-- Fake payment modal -->
            <div id="fakePaymentModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="modal-close btn-icn--close" id="closeFakePayment">×</span>
                    <h2>Simulate Payment</h2>
                    <p>For testing: Click "Pay" to simulate successful payment.</p>
                    <div class="modal-actions">
                        <button id="cancelFakePayment" class="btn-secondary">Cancel</button>
                        <button id="confirmFakePayment" class="btn-primary">Pay (Simulate)</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

        
    <?php endwhile; endif; ?>
<?php get_footer(); ?>





