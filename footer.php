<?php
/**
 * Displays footer
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */

?>

<div class="section section-footer scroll-trigger mar-T full-bleed">
	
	
		<div class="padded-content footer-cols">
			<div class="footer-col footer-col_LABEL">
				<h2 class="footer-col--headline"><?php _e('Sledujte nás', 'rezervace_theme'); ?></h2>
			</div>
			
		<?php
			wp_nav_menu( array(
				'theme_location' => 'fcbk-insta',
				'container' => '',
				'menu_class' => 'footer-col footer-col_LINKS menu-social-links-menu-container nav-list list-none',
			) );
		?>
		</div>
</div>


	
</div> <!-- END WRAPPER -->

<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/wp-block-fix-style.css?v02-01-2025.01" />

<!-- Javascript at the bottom for fast page loading -->
<script src="<?php echo  get_template_directory_uri(); ?>/assets/js/libs/jquery-3.6.1.min.js"></script>
<script src="<?php echo  get_template_directory_uri(); ?>/assets/js/libs/ls.unveilhooks.min.js"></script>

	<script>
		(function() {

						const debounce = function(func, delay){
							let timer;
							return function () {     //anonymous function
							const context = this; 
							const args = arguments;
							clearTimeout(timer); 
							timer = setTimeout(()=> {
											func.apply(context, args)
										},delay);
							}
						}
						// THROTTLE
						const throttle = (func, limit) => {
							let lastFunc;
							let lastRan;
							return function() {
								const context = this;
								const args = arguments;
								if (!lastRan) {
									func.apply(context, args)
									lastRan = Date.now();
								} else {
									clearTimeout(lastFunc);
									lastFunc = setTimeout(function() {
											if ((Date.now() - lastRan) >= limit) {
												func.apply(context, args);
												lastRan = Date.now();
											}
									 }, limit - (Date.now() - lastRan));
								}
							}
						}

						const	wrapper = document.getElementById('wrapper-id'),
								content = document.getElementById("contentID"),
								body = document.body,
								_siteHEADER = document.getElementById("headerID"),
								_siteLOGO = document.getElementById("logoID"),
								_hambBTN = document.getElementById("hmbrgID"),
								_logoBTN = document.getElementById("logoBtnID");

						let		$scrollDir = "down",
								lastScrollTop,
								_scrollDist = 0,
								_scrollMin = 20,
								iOis_supp = false,
								offsetY,
								_len,
								$sT,
								transT = 0.875,
								_navSwitches = document.querySelectorAll(".nav-switch"),
								_prevDefault = document.querySelectorAll(".prevent-default"),
								observedItem = document.querySelectorAll(".scroll-trigger");

						window.addEventListener('load', docLoaded);

						function docLoaded() {
							window.removeEventListener('load', docLoaded);  
							if(wrapper.classList.contains('loading') ) {
										wrapper.classList.remove('loading');
							}
							setUpIObserver();
							addMenuBtnListeners();
							prevenDefaults();
						}
						
						function prevenDefaults() {
							for(let i=0; i<_prevDefault.length; ++i) {
								_prevDefault[i].addEventListener("click", function(event){
									event.preventDefault();
								});
							}
						}

						function setUpIObserver() {

							var observer = new IntersectionObserver(
								function (entries, observer) {
									entries.forEach(function (entry) {
										if (entry.intersectionRatio > 0) {
											entry.target.classList.add("inView");
											// markMenuItemOnScroll(entry.target.id);
										} else {
											// entry.target.classList.remove("inView");
										}
									});
								},
								{
									// rootMargin: "0px 0px -100px 0px",
									rootMargin: "-100px 0px -100px 0px",
									threshold: 0
								}
							);
							observedItem.forEach(function (obs) {
								// console.log("tick");
								observer.observe(obs);
							});
						}

						window.addEventListener('scroll', 
							throttle(function () {
								// toggleMenu(false);
								body.classList.remove("firsttime-load");

								$sT = window.pageYOffset;
								// $sT = this.scrollTop();
								$scrollDir = $sT > lastScrollTop ? "down" : "up";
								if($scrollDir=="up") _scrollDist = lastScrollTop - $sT;
								lastScrollTop = $sT;
								
								if(($scrollDir=="up" && $sT < 150) || ($scrollDir == "up" && _scrollDist > _scrollMin)) {
									toggleSiteHeader(false);
									// return;
								} else if($scrollDir=="down" && $sT > 150) {
									toggleSiteHeader(true);
								}
								// toggleSiteHeader($sT > 80);
							}, 500)
						);

						function toggleSiteHeader(param) {
							if( param ) {
								_siteHEADER.classList.add('header--scrolled');
							} else {
								_siteHEADER.classList.remove('header--scrolled');
							}
							_hambBTN.checked = false;
							_logoBTN.checked = false;
						}


						function addMenuBtnListeners() {
							for(let i=0; i<_navSwitches.length; ++i) {
								_navSwitches[i].addEventListener('click', menuBtnChange);
							}
						}

						function menuBtnChange() {
// console.log(event.target);
							for(let i=0; i<_navSwitches.length; ++i) {
								if( _navSwitches[i] != event.target ) {
									_navSwitches[i].checked = event.target.checked;
								}
							}
						}
			//*/ / / / / / / / / / / 
			// IOS and Android detection => ASUME MOBILE=TOUCH DEVICES
					const $isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
					const $isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
					
					const ua = navigator.userAgent.toLowerCase();
					const $isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
			//*/ / / / / / / / / / / 
			//*/ /PRODUCTION
					if(!$isIOS && !$isAndroid) {
			/*/ // DEVELOPMENT
					if(!$isSafari && !$isAndroid) {
			//*/
						wrapper.classList.add('is-desktop');
					}



		})(); //END SEAF
	</script>


<!-- START WP_FOOTER() -->
	<?php wp_footer(); ?>
<!-- END WP_FOOTER() -->
</body>
</html>