<?php
/**
 * Displays social links
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */

?>
<div class="content">
	<div class="fcbk-mail-tel">
			<?php wp_nav_menu( array(
						'theme_location' => 'social-links-menu',
						'container' => '',
						'menu_class' => 'social-links-list list-none',
					) ); ?>
	</div>
</div>