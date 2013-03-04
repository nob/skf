<?php
/**
 * Homepage Shop Panel
 */

	/**
 	* The Variables
 	*
 	* Setup default variables, overriding them if the "Theme Options" have been saved.
 	*/

	global $woocommerce, $post;

	$settings = array(
					'homepage_number_of_products' => 8,
					'homepage_shop_area_title' => 'Product Showcase'
					);

	$settings = woo_get_dynamic_values( $settings );

?>
			<section id="home-shop" class="home-section fix">

				<header>
					<?php if ( '' != $settings['homepage_shop_area_title'] ) { ?>
						<h1><?php echo $settings['homepage_shop_area_title']; ?></h1>
					<?php } ?>
				</header>

    			<ul class="products">

					<?php
					$number_of_products = $settings['homepage_number_of_products'];
					$args = array(
						'post_type' => 'product',
						'posts_per_page' => intval( $number_of_products ),
						'meta_query' => array( array(
							'key' => '_featured',
							'value' => 'yes'
							))
					);

					$first_or_last = 'first';
					$loop = new WP_Query( $args );
					$query_count = $loop->post_count;
					$count = 0;
					?>

					<?php

					while ( $loop->have_posts() ) : $loop->the_post(); $count++; ?>

							<?php
								if ( function_exists( 'get_product' ) ) {
									$_product = get_product( $loop->post->ID );
								} else { 
									$_product = &new WC_Product( $loop->post->ID );
								}
							?>

							<li>
								<div class="product item">

									<?php woocommerce_show_product_sale_flash( $post, $_product ); ?>

									<?php if (has_post_thumbnail( $loop->post->ID )) { ?>
										<a href="<?php echo esc_url( get_permalink( $loop->post->ID ) ); ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>">
											<?php echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); ?>
										</a>
									<?php }
										else {
											echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
										}
									?>

									<div class="fix"></div>

									<h3><a href="<?php echo esc_url( get_permalink( $loop->post->ID ) ); ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>"><?php echo get_the_title(); ?></a></h3>

									<span class="price"><?php echo $_product->get_price_html(); ?></span>

									<div class="fix"></div>

									<?php woocommerce_template_loop_add_to_cart( $loop->post, $_product ); ?>
									<a class="btn btn-details" href="<?php echo esc_url( get_permalink( $loop->post->ID ) ); ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>"><?php _e('View Details' ,'woothemes'); ?></a>

									<div class="fix"></div>

								</div><!--/.product-->
							</li>
							<?php if ( $count %4 == 0 ): ?><li class="fix"></li><?php endif; ?>
					<?php endwhile; ?>

				</ul><!--/ul.recent-->

    		</section>

    		<?php wp_reset_postdata(); ?>