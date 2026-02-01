<?php
/**
 * Template Name: PAGE
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

     $SHOW_TITLE = get_field('show_title');
      
     $IS_PADDED = get_field('is_padded');
	$CLS = $IS_PADDED ? 'padded-content' : '';
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php if ( is_front_page() ) : ?>
	<div class="section section-mar-T full-bleed section-hp-content">
     	
<?php else : ?>
     <div class="section section-mar-T full-bleed">
<?php endif; ?>
		
	<?php
	      if ( $SHOW_TITLE ) :
	?>
			<h1 class="page-title <?php echo $CLS; ?>"><?php echo the_title(); ?></h1>
	<?php
	      endif;
	?>
			<?php the_content(); ?>
		</div><!-- END SECTION -->
		
	<?php endwhile; endif; ?>


	<?php get_footer(); ?>