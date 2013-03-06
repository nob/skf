<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page' );
}
?>
<?php

// Register widgetized areas

if ( ! function_exists( 'the_widgets_init' ) ) {
	function the_widgets_init() {
	    if ( ! function_exists( 'register_sidebar' ) )
	        return;
	
	    register_sidebar(array( 'name' => __( 'Primary', 'woothemes' ),'id' => 'primary','description' => "Normal full width sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));   
	    register_sidebar(array( 'name' => __( 'Footer 1', 'woothemes' ),'id' => 'footer-1', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Footer 2', 'woothemes' ),'id' => 'footer-2', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Footer 3', 'woothemes' ),'id' => 'footer-3', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Footer 4', 'woothemes' ),'id' => 'footer-4', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Homepage Left', 'woothemes' ),'id' => 'homepage-1', 'description' => "Widgetized Homepage", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Homepage Center', 'woothemes' ),'id' => 'homepage-2', 'description' => "Widgetized Homepage", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Homepage Right', 'woothemes' ),'id' => 'homepage-3', 'description' => "Widgetized Homepage", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Homepage Full', 'woothemes' ),'id' => 'homepage-full', 'description' => "Widgetized Homepage", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array( 'name' => __( 'Single Full', 'woothemes' ),'id' => 'single-full', 'description' => "Full area under your content", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	}
}

add_action( 'init', 'the_widgets_init' );
    
?>