<?php the_post_thumbnail( 'full', array( 'class' => 'alignleft' ) ); ?>


<!-- EVEN / ODD POSTS TARGETING: -->
<?php
//I will use WP_Query class instance
$args( 'post_type' => 'recipe', 'posts_per_page' => 5 );

//Set up a counter
$counter = 0;

//Preparing the Loop
$query = new WP_Query( $args );

//In while loop counter increments by one $counter++
if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); $counter++;

    //We are in loop so we can check if counter is odd or even
    if( $counter % 2 == 0 ) : //It's even

        the_title(); //Echo the title of post
        the_content(); //Echo the content of the post

    else: //It's odd

        if( has_post_thumbnail() ) : //If the post has the post thumbnail, show it
            the_post_thumbnail();
        endif;

    endif;
endwhile; wp_reset_postdata(); endif;
 ?>

<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>
<div id="post" class"your-class" style="background-image: url('<?php echo $thumb['0'];?>')">
<p>text demo</p>
</div>



<div class="article-item--meta">
                                <!-- AVATAR -->
                                <div class="article-item--meta_img">
                                    <?php #echo get_avatar( $author_email, '60' ); ?>
                                    <?php 
                                        if(get_field("avatar_img")):
                                            echo get_field("avatar_img"); 
                                        endif;
                                        echo get_avatar( $post->post_author,'100');
                                    ?>
                                </div>
                                <div class="article-item--meta_caption">
                                    <p class="minor article-item--meta_author">
                                        <a class="author--link" href="<?php get_the_author_posts_link(); ?>">
                                            <?php 
                                                echo get_the_author($post->post_author);
                                            ?>
                                        </a>
                                    </p>
                                   
                                </div>
                            </div>


                            <div class="article--item---footer">
    <ul class="aktuality-list--item---taglist list-none no-indent">
    <?php 
        $categories = get_the_category();
        foreach ( $categories as $key=>$category ) :
    ?>
        <li class="minor">
            <a class="taglist-item" href="<?php echo get_category_link( $category ); ?>"><?php echo $category->name; ?></a>
        </li>
    <?php
        endforeach;
    ?>
    </ul>
</div>




<!-- RESEARCH PROGRAMMES SNIPPET -->

         <?php $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'category_name' => 'research-programs',
                'posts_per_page' => -1,
            );
            $arr_posts = new WP_Query( $args );
             
            $alltags = array();
            if ( $arr_posts->have_posts() ) : 
        ?>
                    
        <div class="section scroll-trigger line-top section-article--list" id="clankyID">
            <?php 
            

             ?>
            <div class="page-title-container">
                <h2 class="page-title "><?php print get_the_category($arr_posts->posts[0]->ID)[0]->name; ?></h2>
            </div>
            <?php
                while ( $arr_posts->have_posts() ) : $arr_posts->the_post();
            ?>
                    <div class="article-item ">
                        
                        <div class="title-container">
                            <h3 class="section-title "><a class="article-item--caption_title_link" href="<?php the_permalink();?>"><?php the_title(); ?></a></h3>
                        </div>
                        <?php 
                            $PROFILE_NAME = get_field( 'profile_name' );
                            $PROFILE_MEMBERSHIP = get_field( 'profile_membership' );
                            $PROFILE_ROLE = get_field( 'profile_role' );
                        ?>
                        <?php 
                            if ( $PROFILE_NAME || $PROFILE_MEMBERSHIP || $PROFILE_ROLE):
                        ?>
                            <div class="subtitle-container">
                                <h5>
                                    <?php
                                        if($PROFILE_ROLE):
                                            echo $PROFILE_ROLE;
                                        endif;
                                    ?>
                                </h5>
                                <h5>
                                    <?php
                                        if($PROFILE_NAME):
                                            echo $PROFILE_NAME;
                                        endif;
                                    ?>
                                </h5>
                                <h5>
                                    <?php
                                        if($PROFILE_MEMBERSHIP):
                                            echo $PROFILE_MEMBERSHIP;
                                        endif;
                                    ?>
                                </h5>
                            </div>
                        <?php endif; ?>
                        <p class="article-item--caption_excerpt"><?php echo get_the_excerpt(); ?></p>
                        <div class="article-item--footer">
                            <div class="wp-block-button is-style-outline">
                                <a class="wp-block-button__link" href="<?php the_permalink();?>">Celý článek</a>
                            </div>
                        </div>
                    </div> <!-- END ITEM -->
<?php endwhile; ?>

        </div> <!-- END CONTENT -->
<?php endif; wp_reset_query(); 
        ?>    


        <!-- SNIPPETS -->

        sass --watch style.scss:style.css

browser-sync start --proxy "http://localhost:8888/2replace/" --files "index.php"

cd /Users/kuba/Sites_MAMP/2replace/wp-content/themes/rezervace_theme/assets/css


.eo-booking-form-login-form
.login-username {}
.login-password {}
label,
input[type="text"],
input[type="password"] {}


                input[type="submit"],
                .button button-primary

            </p><a href="https://new.rezervace_theme.cz/wp-login.php?action=lostpassword" title="Password Lost and Found">Zapomněli jste heslo?</a></div>



<div id="" class="eo-booking-field">
    <label class="eo-booking-label" ><span class="strong">Obchodní podmínky</span></label>
    <div class="eo-booking-field-terms-conditions-text">Platbu za kurzy / kroužky je potřeba odeslat do 10 dnů od začátku dané aktivity.</div>
    <div class="eo-booking-field-terms-conditions-text">Podrobné znění podmínek <a class="terms-conditions-link" href="https://new.rezervace_theme.cz/obchodni-podminky/">najdete zde</a>.</div>
</div>


<label>
    <input type="checkbox" name="eventorganiser[booking][7]" class="eo-booking-field-terms-conditions" value="1"> 
    Přečetl(a) jsem si uvedené podmínky a souhlasím s nimi.</label>



<p class="plain">Podrobné znění podmínek <a class="terms-conditions-link" href="https://new.rezervace_theme.cz/obchodni-podminky/">najdete zde</a>.</p>


<div class="eo-booking-notice eo-booking-notice-info eo-booking-notice-discount-code" style=""><p>Invalid discount code</p></div>