<?php
/**
 * Block Name: BLOCK-space-column-item
 *
 * This is the template that displays the featured WORKS block.
 * @param   array $block The block settings and attributes.
 */
?>

<?php
	
	$CLS_W = 'custom-columns--item';

	// IF IS IMAGE
	// ADD CLASS
	// custom-columns--item_IMG
	$is_IMG = get_field( 'is_image' );
	$BLOCKS_MARKUP = '<InnerBlocks ';
	
	if(! empty($is_IMG) ) :
		$CLS_W .= " custom-columns--item_IMG";
		// $allowed_inner_blocks = ['core/image'];
		$BLOCKS_MARKUP .= 'allowedBlocks="';
		$BLOCKS_MARKUP .= esc_attr(wp_json_encode(['core/image']));
		$BLOCKS_MARKUP .= '" ';
	else :
		$CLS_W .= " custom-columns--item_CAPTION";
	endif;
	
	$BLOCKS_MARKUP .= '/>';


	$classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;


	/*
		<?php $allowed_inner_blocks = ['acf/proj-info-col']; ?>
        <InnerBlocks allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_inner_blocks)); ?>" />

        const ALLOWED_BLOCKS = [ 'core/image', 'core/paragraph' ];
		<InnerBlocks allowedBlocks={ ALLOWED_BLOCKS } />;
        
		<?php $allowed_inner_blocks = ['core/image']; ?>
        <InnerBlocks allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_inner_blocks)); ?>" />
	*/
?>

		<div class="<?php echo esc_attr( $classes ); ?>">
			
			<?php print $BLOCKS_MARKUP; ?>
			<!-- <InnerBlocks/> -->
		</div>

