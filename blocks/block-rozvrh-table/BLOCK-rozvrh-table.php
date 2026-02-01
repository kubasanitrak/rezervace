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
        <?php
            // $TEMP_DATE = get_the_date( 'Ymd' );
            // $CURR_DAY;
            // $CURR_MONTH;
            // $CURR_YEAR;
            $RESERVATION_DATE = '';
            $TIMETABLE_DAY = '';
            $TEST = 'test';
        ?>

        <?php if(get_field("timetable_label")): ?>
            <div class="customtable-header"><p class="strong"><?php echo get_field("timetable_label"); ?></p></div>
        <?php endif; ?>
        <?php
            if(get_field("timetable_day")):
                /*

                    2012-10-11 as $date and 5 as $day, I want to get 2012-10-12, if I've got 0 as $day, 2012-10-14
                    $dayofweek = date('w', strtotime($date));
                    $RESERVATION_DATE    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date)));

                    $dayofweek = date('w', strtotime(2012-10-11));
                    $RESERVATION_DATE    = date('Y-m-d', strtotime((5 - $dayofweek).' day', strtotime(2012-10-11)));
                */
                // $CURR_DAY = date('w');
                // $CURR_MONTH = date('m');
                // $CURR_YEAR = date( 'Y' );
                $CURR_DATE = date('Y-m-d');

                $TIMETABLE_DAY = get_field("timetable_day");
                $CURR_DAY = date('w', strtotime($CURR_DATE));
                $RESERVATION_DATE = date('Y-m-d', strtotime(($TIMETABLE_DAY - $CURR_DAY).' day', strtotime($CURR_DATE)));
                if($CURR_DAY > $TIMETABLE_DAY) :
                    $RESERVATION_DATE = date('Y-m-d', strtotime($RESERVATION_DATE. ' + 7 days'));
                endif;
                ?>
                <!-- <div class="customtable-header">
                    <p class="strong">timetable value: <?php #echo get_field("timetable_day"); ?></p>
                    <p class="strong">current day of the week: <?php #echo $CURR_DAY; ?></p>
                    <p class="strong"><?php #echo $RESERVATION_DATE; ?></p>
                </div> -->

            <?php endif; ?>

    <?php
        if( have_rows('timetable') ):
        $TEMP_MARKUP = "";

        $BOOKING_URL = "";
        $CURR_LANG = pll_current_language( 'slug' );
        $BTN_LABEL = str_contains(strtolower($CURR_LANG), 'cs') ? 'Rezervovat' : 'Book';

    ?>
    <?php
            
            // Loop through rows.
            while( have_rows('timetable') ) : 
                the_row();

        
        #print_r($BOOKING_MENU);

                $INSTRUCTOR_ID = '';
                $counter = 0;
                //strtotime($date)

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
                    // $INSTRUCTOR_ID .= strval(get_field("instructor_id_wpuser_related", $TEMP_ID));
                    if($counter > 0) :
                        $INSTRUCTOR_ID .= ',';
                    endif;
                    $counter ++;
                endforeach;
                $TEMP_MARKUP .= '</div>';
                
                if(get_sub_field("booking_url")):
                    $TEMP_MARKUP .= '<div class="customtable-col customtable-col_BOOK"><a class="customtable-col--item_link caps" href="';
                    $TEMP_MARKUP .= get_sub_field("booking_url"); //https://barreacademy.reservio.com/events
                    if($RESERVATION_DATE != '') :
                        $TEMP_MARKUP .= '?day=';
                        $TEMP_MARKUP .= $RESERVATION_DATE;
                    endif;
                    $TEMP_MARKUP .= '">';
                    $TEMP_MARKUP .= $BTN_LABEL;
                    $TEMP_MARKUP .= '</a></div>';
                endif;
                
                $TEMP_MARKUP .= '</div><!-- END ROW -->';

            // End loop.
            endwhile;
            print $TEMP_MARKUP;
    ?>
    <?php
        endif;
    ?>
    </div>