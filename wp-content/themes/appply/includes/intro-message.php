<?php 
$settings = array(
				'homepage_intro_message' => ''
			);
					
$settings = woo_get_dynamic_values( $settings );
?>
<?php if ( '' != $settings['homepage_intro_message'] ) { ?>
<section id="intro-message" class="home-section">
	<header>
		<h1><?php echo stripslashes( nl2br( do_shortcode( $settings['homepage_intro_message'] ) ) ); ?></h1>
	</header>
</section><!--/#intro-message-->
<?php } ?>