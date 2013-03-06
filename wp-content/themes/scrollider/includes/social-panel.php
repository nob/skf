<?php

//Setup default variables, overriding them if the "Theme Options" have been saved.
$settings = array(
				'feed_url' => '',
				'connect_rss' => '',
				'connect_twitter' => '',
				'connect_facebook' => '',
				'connect_youtube' => '',
				'connect_flickr' => '',
				'connect_linkedin' => '',
				'connect_delicious' => '',
				'connect_rss' => '',
				'connect_googleplus' => ''
				);
$settings = woo_get_dynamic_values( $settings );

?>

<div id="social-panel" class="social">
		<?php if ( $settings['connect_rss' ] == 'true' ) { ?>
		<a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>

		<?php } if ( $settings['connect_twitter' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="<?php echo esc_attr( __( 'Follow me on Twitter', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_facebook' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="<?php echo esc_attr( __( 'My Facebook profile', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_youtube' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="<?php echo esc_attr( __( 'View my Youtube channel', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_flickr' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="<?php echo esc_attr( __( 'My Flickr photostream', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_linkedin' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="<?php echo esc_attr( __( 'My LinkedIn profile', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_delicious' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="<?php echo esc_attr( __( 'My Declicious profile', 'woothemes' ) ); ?>"></a>

		<?php } if ( $settings['connect_googleplus' ] != '' ) { ?>
		<a href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="<?php echo esc_attr( __( 'My Google+ profile', 'woothemes' ) ); ?>"></a>

		<?php } ?>
</div><!-- /.social -->