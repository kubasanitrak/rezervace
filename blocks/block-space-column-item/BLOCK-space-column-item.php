<?php
/**
 * Block Name: BLOCK-space-column-item
 *
 * This is the template that displays the featured WORKS block.
 * @param   array $block The block settings and attributes.
 */
?>

<?php
	
	$CLS_W = 'space-columns--item';

	$is_autoWIDTH = get_field( 'space_cols-auto' );
	$numCOLS_value = get_field( 'space_cols' );

	$style = "flex-basis: 100%";
	
	if(! empty($numCOLS_value) ) :
		$CLS_W .= " colwidth_";
		$CLS_W .= esc_attr($numCOLS_value['value']);
		$style = "";
	endif;


	$classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;

?>

		<div style="<?php echo esc_attr( $style ); ?>" class="<?php echo esc_attr( $classes ); ?>">
			<InnerBlocks/>
		</div>