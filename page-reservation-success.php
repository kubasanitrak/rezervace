<?php
/**
 * Template Name: PAGE Success
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
                // template-barre-success.php or just a page with content
                get_header();

                if (isset($_GET['session_id'])) {
                    // Optional: verify session via Stripe API if needed
                    echo '<div class="success-notice">';
                    echo '<h1>Thank you for your reservation!</h1>';
                    echo '<p>Your payment was successful. You will receive a confirmation email shortly.</p>';
                    echo '<p><a href="' . home_url('/my-reservations') . '">View My Reservations</a></p>';
                    echo '</div>';
                } else {
                    echo '<p>No session information.</p>';
                }
            ?>

    </div>
		
	<?php endwhile; endif; ?>
<?php get_footer(); ?>