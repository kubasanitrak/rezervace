<?php
/**
 * Block Name: BLOCK-grid-row
 *
 * This is the template that displays the featured WORKS block.
 * @param   array $block The block settings and attributes.
 */
?>

<?php 
	
	$isScrollable = get_field( 'grid_row_is_scrollable' );

	$class_name = 'select-list--row';

	if ( ! empty( $isScrollable ) ) :
    	$class_name .= ' ' . 'select-list--scroll_H';
	endif;

?>

<div class="section section-select-list select-list">
	<div class="<?php echo esc_attr( $class_name ); ?>"	>
		<InnerBlocks/>
	</div>
</div>