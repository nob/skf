<?php
/*
* Plugin Name: qTranslate-wp_nav_menu 
* Plugin URI: 
* Description: WP's wp_nav_menu() customization using qTranslate's qtrans_convertURL() function. 
* Author: Nob Y.
* Author URI: http://jointanet.com
* Version: 1.0.0
* */

/**
 * qtwnm_custom()
 *
 * A filter function, which is hooked on 'wp_nav_menu' filter hook.
 */
add_filter('wp_nav_menu', 'qtwnm_custom');
function qtwnm_custom($nav_menu_html) {
    $str = '';

    $pattern = "%https?\:\/\/.+\/en\/\"%";
    $replace = qtrans_convertURL('', 'en') . '"';;
    $str = preg_replace($pattern, $replace, $nav_menu_html);

    $pattern = "%https?\:\/\/.+\/ja\/\"%";
    $replace = qtrans_convertURL() . '"';;
    $str = preg_replace($pattern, $replace, $str);

    return $str;
}
