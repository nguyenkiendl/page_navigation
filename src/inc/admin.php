<?php

function add_page_admin()
{
	add_menu_page(
		'Page navigation',
		'Page navigation',
		'manage_options',
		'page-navigation',
		'func_page_navigation',
		'dashicons-ellipsis',
		'2'
	);
}

add_action('admin_menu', 'add_page_admin');


function func_page_navigation(){
	$types = array(
		'page-navi' => 'Page Navi',
		'load-more' => 'Loadmore'
	);

	if (get_option('pnav_type') === false) {
		add_option('pnav_type', 'page-navi');
	}

	$taxonomies = get_taxonomies( array('public' => true ), 'names', 'and' );
	unset( $taxonomies['post_format'] );
	$taxonomies['is-home'] = 'home';
	$pnav_taxonomies = array();
	if (get_option('pnav_taxonomies') === false) {
		foreach ($taxonomies as $k => $v) {
			$pnav_taxonomies[] = $k;
		}
		//add_option
		add_option('pnav_taxonomies', $pnav_taxonomies);
	}
	if(isset($_POST['submit'])) {
		$v_type = !empty($_POST['pnav_type']) ? $_POST['pnav_type'] : 'page-navi';
		update_option('pnav_type', $v_type);
		$v_tax = !empty($_POST['pnav_taxonomies']) ? $_POST['pnav_taxonomies'] : [];
		update_option('pnav_taxonomies', $v_tax);
	}
	$pnav_type = get_option('pnav_type');
	$pnav_taxonomies = get_option('pnav_taxonomies');

	include( PNAVI_DIR . '/views/page_navigation_view.php' );
}
add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ )) . 'page-navigation.php', 'plugin_settings' );

function plugin_settings( $links ) {
   	$settings_link = '<a href="'.esc_url( get_page_url() ).'">'.__('Settings', 'pnav').'</a>';
	array_unshift( $links, $settings_link ); 
	return $links; 
}