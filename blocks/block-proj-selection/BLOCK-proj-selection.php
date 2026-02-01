<?php
/**
 * Block Name: BLOCK_customgooglemap
 *
 * This is the template that displays the featured news block.
 * @param   array $block The block settings and attributes.
 */
?>
<!-- 

PICK POST FROM CERTAIN CATEGORY / PREDEFINED CATEGORY
- GET ITS TITLE, YEAR (ACF), PERMALINK
PICK IMAGE FROM MEDIA LIBRARY

-->
<?php
/*
// Create class attribute allowing for custom "className" values.
    $CLS_W = ' lazyload';
    
    // Create class attribute allowing for custom "className" values.
    $classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;
    $id = 'rezervace-' . $block['id'];

$argType = get_field( 'loop_argument_type' );
if( $argType == "count" ) :
  $args = array( 
    // 'orderby' => 'title',
    // 'post_type' => 'news',
    'category_name' => 'news',
    'posts_per_page' => get_field( 'news_count' )
  );
else:
  $news = get_field( 'select_news' );
  $args = array( 
    // 'orderby' => 'title',
    // 'post_type' => 'news',
    // 'category_name' => 'news',
    'post__in' => $news
  );
endif;

$title = get_field( 'news_title' );

$the_query = new WP_Query( $args );
$counter = 0;//Set up a counter

if ( $the_query->have_posts() ) : 
*/
    ?>
    <div class="posts-grid--container <?php echo esc_attr($classes); ?>">
<?php 
/*
  while ( $the_query->have_posts() ) : 
    $the_query->the_post(); 
    $counter++;
    
    $thumbsize = "thumbnail";
    if( $counter == 1 ) :
      $thumbsize = "large";
    endif;
*/
    ?>
        <div class="posts-grid--item">
          <div class="posts-grid--item---column posts-grid--item---column_img">
            <a class="posts-grid--item---column_link" href="<?php echo get_permalink(); ?>">
              <!-- <img src="<?php #echo get_the_post_thumbnail_url(); ?>" class="" /> -->
              <?php the_post_thumbnail($thumbsize); ?>
            </a>
          </div>
          <div class="posts-grid--item---column posts-grid--item---column_txt">
            <h4 class="posts-grid--item---column_title color-red1">
                <a class="posts-grid--item---column_link" href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
            <?php 
              if( $counter == 1 ) :
            ?>
              <p class="posts-grid--item---column_pubdate plain ntmd-ff"><?php echo get_the_date(); ?></p>
            <?php endif;  ?>
          </div>
        </div> <!-- END POSTGRID ITEM -->

  <?php endwhile; ?>
    </div> <!-- END POSTSGRID BLOCK -->
<?php else: __( 'Sorry, there are no posts to display', 'rezervace_theme' ); ?>
<?php endif;  ?>
