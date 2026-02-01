<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */

?>
<div class="nav">
	<input type="checkbox" id="hmbrgID" class="nav-switch" >
	<!-- <input type="checkbox" id="hmbrgID" class="nav-switch" checked > -->
	<label for="hmbrgID" class="hamburger">
		<span class="">menu</span>
	</label>
	<input type="checkbox" id="logoBtnID" class="nav-switch logobtn-switch">
	<!-- <input type="checkbox" id="logoBtnID" class="nav-switch" checked > -->
	<label for="logoBtnID" class="logobtn">
		<span class=""></span>
	</label>

	<?php include('logo.php'); ?>
	
	<div class="menu top-menu">		
		<?php
			wp_nav_menu( array(
				'theme_location' => 'main-menu',
				'container' => '',
				'menu_class' => 'main-nav-list nav-list list-none',
			) );
			wp_nav_menu( array(
				'theme_location' => 'lang-menu',
				'container' => '',
				'menu_class' => 'nav-list list-none lang-menu',
			) );
		?>
	</div><!-- END DIV CLASS MENU -->
</div><!-- END NAV -->