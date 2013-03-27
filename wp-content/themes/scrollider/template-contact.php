<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Template Name: Contact Form
 *
 * The contact form page template displays the a
 * simple contact form in your website's content area.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
global $woo_options;
get_header();
 
$nameError = '';
$emailError = '';
$commentError = '';

//If the form is submitted
if( isset( $_POST['submitted'] ) ) {

	//Check to see if the honeypot captcha field was filled in
	if( trim( $_POST['checking'] ) !== '' ) {
		$captchaError = true;
	} else {

		//Check to make sure that the name field is not empty
		if( trim( $_POST['contactName'] ) === '' ) {
			$nameError =  __( 'You forgot to enter your name.', 'woothemes' );
			$hasError = true;
		} else {
			$name = trim( $_POST['contactName'] );
		}

		//Check to make sure sure that a valid email address is submitted
		if( trim( $_POST['email'] ) === '' )  {
			$emailError = __( 'You forgot to enter your email address.', 'woothemes' );
			$hasError = true;
		} else if ( ! eregi( "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email'] ) ) ) {
			$emailError = __( 'You entered an invalid email address.', 'woothemes' );
			$hasError = true;
		} else {
			$email = trim( $_POST['email'] );
		}

		//Check to make sure comments were entered
		if( trim( $_POST['comments'] ) === '' ) {
			$commentError = __( 'You forgot to enter your comments.', 'woothemes' );
			$hasError = true;
		} else {
			$comments = stripslashes( trim( $_POST['comments'] ) );
		}

		//If there is no error, send the email
		if( ! isset( $hasError ) ) {

			$emailTo = get_option( 'woo_contactform_email' );
			$subject = __( 'Contact Form Submission from ', 'woothemes' ).$name;
			$sendCopy = trim( $_POST['sendCopy'] );
			$body = __( "Name: $name \n\nEmail: $email \n\nComments: $comments", 'woothemes' );
			$headers = __( 'From: ', 'woothemes') . "$name <$email>" . "\r\n" . __( 'Reply-To: ', 'woothemes' ) . $email;

			wp_mail( $emailTo, $subject, $body, $headers );

			if( $sendCopy == true ) {
				$subject = __( 'You emailed ', 'woothemes' ) . get_bloginfo( 'title' );
				$headers = __( 'From: ', 'woothemes' ) . "$name <$emailTo>";
				wp_mail( $email, $subject, $body, $headers );
			}

			$emailSent = true;

		}
	}
}
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery(document).ready(function() {
	jQuery( 'form#contactForm').submit(function() {
		jQuery( 'form#contactForm .error').remove();
		var hasError = false;
		jQuery( '.requiredField').each(function() {
			if(jQuery.trim(jQuery(this).val()) == '') {
				var labelText = jQuery(this).prev( 'label').text();
				jQuery(this).parent().append( '<span class="error"><?php _e( 'You forgot to enter your', 'woothemes' ); ?> '+labelText+'.</span>' );
				jQuery(this).addClass( 'inputError' );
				hasError = true;
			} else if(jQuery(this).hasClass( 'email')) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test(jQuery.trim(jQuery(this).val()))) {
					var labelText = jQuery(this).prev( 'label').text();
					jQuery(this).parent().append( '<span class="error"><?php _e( 'You entered an invalid', 'woothemes' ); ?> '+labelText+'.</span>' );
					jQuery(this).addClass( 'inputError' );
					hasError = true;
				}
			}
		});
		if(!hasError) {
			var formInput = jQuery(this).serialize();
			jQuery.post(jQuery(this).attr( 'action'),formInput, function(data){
				jQuery( 'form#contactForm').slideUp( "fast", function() {
					jQuery(this).before( '<p class="tick"><?php _e( '<strong>Thanks!</strong> Your email was successfully sent.', 'woothemes' ); ?></p>' );
				});
			});
		}

		return false;

	});
});
//-->!]]>
</script>

    <div id="content" class="col-full">
    	
    	<?php woo_main_before(); ?>
    
		<section id="main" class="col-left">
	        <h1><?php the_title(); ?></h1>

            <article id="contact-page" class="page type-page">

	            <div class="article-inner">

	            <?php if( isset( $emailSent ) && $emailSent == true ) { ?>

	                <p class="info"><?php _e( 'Your email was successfully sent.', 'woothemes' ); ?></p>

	            <?php } else { ?>

	                <?php if ( have_posts() ) { ?>

	                <?php while ( have_posts() ) { the_post(); ?>
	                
	                		<header>
	                		</header>

	                        <section class="entry">
		                        <?php the_content(); ?>
	                			
	                			<div class="location-twitter fix">
	    							<?php if ( isset( $woo_options['woo_contact_panel'] ) && $woo_options['woo_contact_panel'] == 'true' ) { ?>
							    	<section id="office-location"<?php if ( ( isset( $woo_options['woo_contact_twitter'] ) && $woo_options['woo_contact_twitter'] != '' ) || ( isset($woo_options['woo_contact_subscribe_and_connect']) && $woo_options['woo_contact_subscribe_and_connect'] == 'true' ) ) { ?> class="col-left"<?php } ?>>
										<?php if (isset($woo_options['woo_contact_title'])) { ?><h3><?php echo esc_html( $woo_options['woo_contact_title'] ); ?></h3><?php } ?>
										<ul>
											<?php if (isset($woo_options['woo_contact_title']) && $woo_options['woo_contact_title'] != '' ) { ?><li><?php echo nl2br( esc_html( $woo_options['woo_contact_address'] ) ); ?></li><?php } ?>
											<?php if (isset($woo_options['woo_contact_number']) && $woo_options['woo_contact_number'] != '' ) { ?><li><?php _e('Tel:','woothemes'); ?> <?php echo esc_html( $woo_options['woo_contact_number'] ); ?></li><?php } ?>
											<?php if (isset($woo_options['woo_contact_fax']) && $woo_options['woo_contact_fax'] != '' ) { ?><li><?php _e('Fax:','woothemes'); ?> <?php echo esc_html( $woo_options['woo_contact_fax'] ); ?></li><?php } ?>
											<?php if (isset($woo_options['woo_contactform_email']) && $woo_options['woo_contactform_email'] != '' ) { ?><li><?php _e('Email:','woothemes'); ?> <a href="mailto:<?php echo esc_attr( $woo_options['woo_contactform_email'] ); ?>"><?php echo esc_html( $woo_options['woo_contactform_email'] ); ?></a></li><?php } ?>
										</ul>
							    	</section>
							    	<?php } ?>
							    	<div class="contact-social<?php if ( ( isset( $woo_options['woo_contact_panel'] ) && $woo_options['woo_contact_panel'] == 'true' ) && ( ( isset( $woo_options['woo_contact_twitter'] ) && $woo_options['woo_contact_twitter'] != '' ) || ( isset($woo_options['woo_contact_subscribe_and_connect']) && $woo_options['woo_contact_subscribe_and_connect'] == 'true' ) ) ) { ?> col-right<?php } ?>">
							    	
							    		<?php if ( isset( $woo_options['woo_contact_twitter'] ) && $woo_options['woo_contact_twitter'] != '' ) { ?>
							    		<section id="twitter">
							    			<h3>Twitter</h3>
							    			<ul id="twitter_update_list_123"><li></li></ul>
							    			<?php echo woo_twitter_script(123, $woo_options['woo_contact_twitter'],1); ?>
							    		</section>
							    		<?php } ?>
							    		<?php if ( isset($woo_options['woo_contact_subscribe_and_connect']) && $woo_options['woo_contact_subscribe_and_connect'] == 'true' ) { woo_subscribe_connect(); } ?>
							    	
							    	</div>
							    	
							    	</div><!-- /.location-twitter -->
		                        
	                        </section>
	                        
	                        <?php if ( isset($woo_options['woo_contactform_map_coords']) && $woo_options['woo_contactform_map_coords'] != '' ) { $geocoords = $woo_options['woo_contactform_map_coords']; }  else { $geocoords = ''; } ?>
	                		<?php if ($geocoords != '') { ?>
	                		<?php woo_maps_contact_output("geocoords=$geocoords"); ?>
	                		<?php //echo do_shortcode( '[hr]' ); ?>
	                		<?php } ?>

	                    <?php if( isset( $hasError ) || isset( $captchaError ) ) { ?>
	                        <p class="alert"><?php _e( 'There was an error submitting the form.', 'woothemes' ); ?></p>
	                    <?php } ?>

	                    <?php if ( get_option( 'woo_contactform_email' ) == '' ) { ?>
	                        <?php echo do_shortcode( '[box type="alert"]' . __( 'E-mail has not been setup properly. Please add your contact e-mail!', 'woothemes' ) . '[/box]' );  ?>
	                    <?php } ?>


	                    <form action="<?php the_permalink(); ?>" id="contactForm" method="post">

	                        <ol class="forms">
	                            <li><label for="contactName">お名前<?php //_e( 'Name', 'woothemes' ); ?></label>
	                                <input type="text" name="contactName" id="contactName" value="<?php if( isset( $_POST['contactName'] ) ) { echo esc_attr( $_POST['contactName'] ); } ?>" class="txt requiredField" />
	                                <?php if($nameError != '') { ?>
	                                    <span class="error"><?php echo $nameError;?></span>
	                                <?php } ?>
	                            </li>

	                            <li><label for="email">Eメールアドレス<?php //_e( 'Email', 'woothemes' ); ?></label>
	                                <input type="text" name="email" id="email" value="<?php if( isset( $_POST['email'] ) ) { echo esc_attr( $_POST['email'] ); } ?>" class="txt requiredField email" />
	                                <?php if($emailError != '') { ?>
	                                    <span class="error"><?php echo $emailError;?></span>
	                                <?php } ?>
	                            </li>

	                            <li class="textarea"><label for="commentsText">お問い合わせ内容<?php //_e( 'Message', 'woothemes' ); ?></label>
	                                <textarea name="comments" id="commentsText" rows="8" cols="30" class="requiredField"><?php if( isset( $_POST['comments'] ) ) { echo esc_textarea( $_POST['comments'] ); } ?></textarea>
	                                <?php if( $commentError != '' ) { ?>
	                                    <span class="error"><?php echo $commentError; ?></span>
	                                <?php } ?>
	                            </li>
                                <li class="textarea">
                            <h3>個人情報保護方針</h3>
                            <textarea cols="110" rows="10" disabled>
株式会社エスケーファイン（以下、弊社）は、お客さまに安心と信頼と有益な情報を提供し、さらにご満足いただくため、「個人情報保護法」を遵守し、個人情報を適切に取り扱います。

１．個人情報の定義

個人情報は「氏名」「住所」「電話番号」「Eメールアドレス」などに加えて、個人を特定できる「写真・画像データ」等、特定の個人を識別できる情報と定義します。また、直接個人を特定できなくても、いくつかの情報を結びつけることで、個人を識別できうる情報も個人情報として定義します。

２．個人情報の取得と利用目的

弊社はお客さまの個人情報を適正な手段以外で取得することはいたしません。個人情報をお預かりする場合には、事前にその利用目的と取り扱い責任者を明示して、合意いただくか、取得後速やかにその利用目的を本人に通知し、または、公表するものとします。

お客さまの情報は電子化されている、されていないにかかわらず、限られた担当者のみが合意された目的にのみ利用します。担当者は業務上必要な時以外にはお客さまの情報を利用いたしません。

Ⅰ.　弊社サービスをご利用の方の個人情報の利用目的

    弊社又は弊社が提供する各サービスに関していただいたお問い合わせに関する内容確認、調査、又はご返信時の参照情報として
    弊社が提供する各サービスの障害情報、メンテナンス情報等技術的なサポートに関する情報又は新サービス、新商品、機能改善等お客様に有用と思われる情報の告知の送付のため
    弊社が提供する各サービス及びそれに関連するサービスのご提供及び弊社サービス利用者の管理のため
    各サービスの提供に当たりお客様本人からあらかじめ同意を得ている場合に、個人情報を第三者に提供するため
    その他、各サービスの提供に当たり利用目的を公表の上、同意をいただいた利用目的のため
                         
※なお、各サービスの利用に際しては、お客様の意思によって利用される個人情報と連動した機能の取り扱いには格別のご注意をいただきますようお願いいたします

３．適切な情報セキュリティ対策の実施

「個人情報取り扱い管理者」を配置するとともに、不正アクセス、紛失、改ざんおよび、漏洩などの予防に対する適切な対策を行います。電子化されていない個人情報（会員申込書、伝票など）も適切に保管します。

４．教育と監査

従業者で個人情報を取り扱う者へ定期的に「個人情報取り扱い」に関する教育を行います。さらに、定期的に個人情報が適切に取り扱われているか、監査を行います。

５．個人情報の第三者提供について

弊社では、代理店契約に基づき運営しているサービスがあります。これらのサービスでお預かりした個人情報は、弊社と同様の取り扱いをします。上記以外の第三者への提供は、個人情報保護法に定められた例外を除いて、提供いたしません。

６．個人情報の取り扱いに関するお問い合わせ等の窓口

弊社が保有しているお客さまの個人情報に対するお問い合わせ等は下記の窓口までご連絡ください。

「株式会社エスケーファイン個人情報お問い合わせ窓口」
                            </textarea>
                                </li>
	                            <li class="inline"><input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if( isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] == true ) { echo ' checked="checked"'; } ?> /><label for="sendCopy">このEメールのコピーを自身に送信する<?php //_e( 'Send a copy of this email to yourself', 'woothemes' ); ?></label></li>
	                            <li class="screenReader"><label for="checking" class="screenReader"><?php _e( 'If you want to submit this form, do not enter anything in this field', 'woothemes' ); ?></label><input type="text" name="checking" id="checking" class="screenReader" value="<?php if( isset( $_POST['checking'] ) ) { echo esc_attr( $_POST['checking'] ); } ?>" /></li>
	                            <li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><input class="submit button" type="submit" value="送信<?php //esc_attr_e( 'Submit', 'woothemes' ); ?>" /></li>
	                        </ol>
	                    </form>


	                    <?php
	                    		} // End WHILE Loop
	                    	}
	                    }
	                    ?>
	            </div><!--.article-inner-->
            </article><!-- /#contact-page -->
		</section><!-- /#main -->
		
		<?php woo_main_after(); ?>

        <?php get_sidebar(); ?>

    </div><!-- /#content -->

<?php get_footer(); ?>
