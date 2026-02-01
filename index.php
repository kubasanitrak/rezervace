<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>


<?php endwhile; endif; ?>
<h1>index test</h1>
<?php get_footer(); ?>