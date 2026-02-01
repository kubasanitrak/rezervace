<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
<div class="section" data-theme="default">
            <div class="scroll-trigger line-top mar-B"></div>
            <h1 class="entry-title"><?php printf( __( 'Search Results for: %s', 'rezervace_theme' ), get_search_query() ); ?></h1>
</div>
<div class="section" data-theme="default">
<?php while ( have_posts() ) :
	the_post();
?>
	<div class="scroll-trigger line-top line-top--blue pad-T"></div>
	<div class="article-item flow mar-B">
		<div class="article-item--content">
			<div class="article-item--caption">
    			<h2 class="article-item--caption_title"><a class="article-item--caption_title_link" href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
        		<p class="article-item--caption_excerpt"><?php echo get_the_excerpt(); ?></p>
			</div>
		</div>
		<div class="article-item--footer">
            <div class="wp-block-button is-style-outline">
                <a class="wp-block-button__link" href="<?php the_permalink();?>"><?php _e('Read full article', 'rezervace_theme'); ?></a>
            </div>
        </div>
	</div>
<?php endwhile; ?>
<?php else : ?>
    <div class="scroll-trigger line-top mar-B"></div>
    <div class="flow mar-B">
		<h2 class="entry-title"><?php _e( 'Nothing Found', 'rezervace_theme' ); ?></h2>
		<p><?php _e( 'Sorry, nothing matched your search. Please try again.', 'rezervace_theme' ); ?></p>
	<!-- </div> -->
<?php endif; ?>
</div><!-- END SECTION CONTENT -->
<?php get_footer(); ?>