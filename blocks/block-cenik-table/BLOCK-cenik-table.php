<?php
/**
 * Block Name: BLOCK-cenik-table
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
        if( have_rows('pricelist') ):
        $TEMP_MARKUP = "";
    ?>
    <?php
            
            // Loop through rows.
            while( have_rows('pricelist') ) : 
                the_row();
                $TEMP_MARKUP .= '<div class="customtable-row mar-T-0">';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_ITEM">';
                $TEMP_MARKUP .= get_sub_field('pricelist_item');
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col-row">';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_PRICE">';
                $TEMP_MARKUP .= get_sub_field('pricelist_item_price');
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '<div class="customtable-col customtable-col_CURR">';
                $TEMP_MARKUP .= get_sub_field('currency');
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '</div>';
                $TEMP_MARKUP .= '</div><!-- END ROW -->';

            // End loop.
            endwhile;
            print $TEMP_MARKUP;
        ?>
    <?php
        endif;
        ?>
    </div>