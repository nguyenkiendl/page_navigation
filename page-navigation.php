<?php
/**
 * @package Page Navigation
 * @version 1.0.0
 */
/**
 * Plugin Name:     Page Navigation
 * Plugin Slug:     page-navigation
 * Plugin URI:      https://github.com/nguyenkiendl/page_navigation
 * Description:     pagination plugin or see more with auto ajax
 * Author:          nguyenkiendl
 * Author URI:      https://nguyenkiendl.com
 * Version:         1.0.0
 * License:         GPLv2 or later
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'PNAVI_DIR', plugin_dir_path( __FILE__) );
define( 'PNAVI_URL', plugin_dir_url( __FILE__) );
define( 'PNAVI_VERSION', '1.0.0');

if ( is_admin() ) {
    require_once( PNAVI_DIR . 'class.page-navigation-admin.php');
    new Page_Navigation_Admin();
}

register_activation_hook( __FILE__, array( 'Page_Navigation', 'plugin_activation') );
register_deactivation_hook( __FILE__, array( 'Page_Navigation', 'plugin_deactivation') );

session_start();

require_once( PNAVI_DIR . 'class.page-navigation.php' );
Page_Navigation::init();




// add_action( 'all', 'th_show_all_hooks' );
    
// function th_show_all_hooks( $tag ) {
//     if(!(is_admin())){ // Display Hooks in front end pages only
//         $debug_tags = array();
//         global $debug_tags;
//         if ( in_array( $tag, $debug_tags ) ) {
//             return;
//         }
//         echo "<pre>" . $tag . "</pre>";
//         $debug_tags[] = $tag;
//     }
// }
if (! function_exists('page_naviagtion')) {
    function page_naviagtion($custom_query=false)
    {
        return Page_Navigation::get_page_naviagtion($custom_query);
    }
}