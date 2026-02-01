<?php ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">

<meta name="robots" content= "index, follow">
<meta name="author" content="Lenka Krejčová Barre Academy">
<meta name="keywords" lang="cs" content="studio barre, pilates, barre, praha, prague marina, lenka, krejcova, studio, lenky, krejčové, lenka krejčová, barre reformer, total barre,">

<link rel="icon"	href="<?php echo get_template_directory_uri(); ?>/assets/favicon.ico" sizes="any"><!-- 32×32 -->
<link rel="icon"	href="<?php echo get_template_directory_uri(); ?>/assets/icon.svg" type="image/svg+xml">
<link rel="apple-touch-icon"	href="<?php echo get_template_directory_uri(); ?>/assets/apple-touch-icon.png"><!-- 180×180 -->
<link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.webmanifest">

<!-- START WP_HEAD() -->
<?php wp_head(); ?>
<!-- END WP_HEAD() -->

<!--[if lt IE]>
<style></style>
<![endif]-->


<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css?v12-12-2025.01" />


<style>
	/* CRITICAL CSS */
	@supports (--custom:property) {
		[style*="--aspect-ratio"] { position: relative; padding-bottom: 0; }
		[style*="--aspect-ratio"]::before { padding-bottom: calc(100% / (var(--aspect-ratio))); display: block; content: ""; width: 100%;}
		[style*="--aspect-ratio"] > :first-child:not(.play-button) { position: absolute; top: 0; left: 0; height: 100%; } 
	}
</style>


</head>
<body class="" data-theme="">

	<script> document.body.classList.add("js"); </script>


	
	<div class="wrapper loading " id="wrapper-id" >
		<div class="header" id="headerID">
			<div class="hotspot reveal-menu-on-hover" id="navigation-hotspot">&nbsp;</div>
			<?php include('template-parts/navigation-top.php'); ?>
		</div>


		