<?php
/**
 * Block Name: BLOCK-rozvrh-table
 * 
 * @param   array $block The block settings and attributes.
 * 
 */
?>

<?php
// Create class attribute allowing for custom "className" values.
    $CLS_W = 'customtable';
    
    // Create class attribute allowing for custom "className" values.
    $classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;
    
?>
    <div class="<?php echo esc_attr($classes); ?>">
        
        <?php if(get_field("timetable_label")): ?>
            <div class="customtable-header"><p class="strong"><?php echo get_field("timetable_label"); ?></p></div>
        <?php endif; ?>

    <?php
        if( have_rows('timetable') ):
        $TEMP_MARKUP = "";
        // $BOOKING_URL = "rezervace";
        $BOOKING_URL = "";

        $CURR_LANG = pll_current_language( 'slug' );
        $BTN_LABEL = str_contains(strtolower($CURR_LANG), 'cs') ? 'Rezervovat' : 'Book';

        // $BOOKING_MENU = get_menu_items_by_registered_slug( 'rezervace-menu' );
        // foreach ( (array) $BOOKING_MENU as $key => $BOOKING_MENU_ITEM ) :
        //     if( str_contains(strtolower($BOOKING_MENU_ITEM->title), 'booking') ) :
        //         $BOOKING_URL = $BOOKING_MENU_ITEM->url;
        //     endif;
        // endforeach;

    ?>
    <?php
            
            // Loop through rows.
            while( have_rows('timetable') ) : 
                the_row();

        
        #print_r($BOOKING_MENU);

                $INSTRUCTOR_ID = '';
                $counter = 0;

                $TEMP_INSTRUCTORS = get_sub_field('timetable_instructor');
                $TEMP_MARKUP .= '<div class="customtable-row mar-T-0">';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_TIME">';
                $TEMP_MARKUP .= get_sub_field('timetable_time');
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_COURSE">';
                $TEMP_MARKUP .= get_sub_field('timetable_item');
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_INSTRUCTOR">';
                foreach ($TEMP_INSTRUCTORS as $INSTRUCTOR => $TEMP_ID) :
                    $TEMP_MARKUP .= '<span class="customtable-col--item">';
                    $TEMP_MARKUP .= get_the_title($TEMP_ID);
                    $TEMP_MARKUP .= '</span>';
                    $INSTRUCTOR_ID .= strval(get_field("instructor_id_booking_related", $TEMP_ID));
                    if($counter > 0) :
                        $INSTRUCTOR_ID .= ',';
                    endif;
                    $counter ++;
                endforeach;
                $TEMP_MARKUP .= '</div>';
                // if(get_sub_field("bpa_lesson_id")):
                //     $TEMP_MARKUP .= '<div class="customtable-col customtable-col_BOOK"><a class="customtable-col--item_link caps" href="';
                //     $TEMP_MARKUP .= $BOOKING_URL;
                //     $TEMP_MARKUP .= '?s_id=';
                //     $TEMP_MARKUP .= strval(get_sub_field("bpa_lesson_id"));
                //     if($INSTRUCTOR_ID) :
                //         $TEMP_MARKUP .= '&sm_id=' . $INSTRUCTOR_ID;
                //     endif;
                //     $TEMP_MARKUP .= '"';
                //     $TEMP_MARKUP .= '>';
                //     $TEMP_MARKUP .= $BTN_LABEL;
                //     $TEMP_MARKUP .= '</a></div>';
                // endif;
                $TEMP_MARKUP .= '</div><!-- END ROW -->';

            // End loop.
            endwhile;
            print $TEMP_MARKUP;
    ?>
    <?php
        endif;
    ?>
    </div>