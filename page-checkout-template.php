<?php
/**
 * Template Name: PAGE Checkout
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

            <button id="confirm-and-pay" class="btn-pay">Proceed to Payment →</button>
            <button id="back-to-schedule">← Back to Schedule</button>
        </div>

    </div>


        
    <?php endwhile; endif; ?>
<?php get_footer(); ?>





