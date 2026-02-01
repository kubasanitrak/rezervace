<?php
/**
 * Block Name: BLOCK-hp-grid-item
 *
 * This is the template that displays the featured WORKS block.
 * @param   array $block The block settings and attributes.
 */
?>

<?php
	
	$CLS_W = 'select-list--item';

	$imgOBJ = get_field( 'grid_item_img' );
	$imgID = $imgOBJ['ID'];

	$is_autoWIDTH = get_field( 'width_auto' );
	$numCOLS_value = get_field( 'grid_cols' );
	
	$linkToPOST = get_field( 'grid_item_link_to' );

	// Thumbnail size attributes.
    $size = 'large';
    $thumb = $imgOBJ['sizes'][ $size ];
    $img_W = $imgOBJ['sizes'][ $size . '-width' ];
    $img_H = $imgOBJ['sizes'][ $size . '-height' ];
	
	$style = "--ratio: " . $img_W . "/" . $img_H;


	$IS_LINK = false;

	if(! empty($linkToPOST)) :
		$itemTITLE = get_the_title( $linkToPOST->ID );
		$linkURL = get_permalink($linkToPOST->ID);
		$linkYEAR = get_field( 'proj_year', $linkToPOST->ID);
		$IS_LINK = true;
	endif;

	
	if(! empty($numCOLS_value) ) :
		$CLS_W .= " cols_";
		$CLS_W .= esc_attr($numCOLS_value['value']);
		// $style = "";
	endif;


	$classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;

?>

		<div style="<?php echo esc_attr( $style ); ?>" class="<?php echo esc_attr( $classes ); ?>">
			<div class="image-container" style="<?php echo esc_attr( $style ); ?>">
				<?php echo wp_get_attachment_image($imgID, 'full'); ?>
			</div>
			<?php if($IS_LINK) : ?>
				<div class="image-caption">
					<p class="plain select-list--item_year"><?php echo $linkYEAR; ?></p>
					<p class="plain select-list--item_title"> <?php echo esc_attr( $itemTITLE ); ?></p>
				</div>
				<a href="<?php echo $linkURL; ?>" class="proj-det-link abs-link"></a>
			<?php endif; ?>
		</div>