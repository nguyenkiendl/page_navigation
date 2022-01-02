<?php
/**
 * Admin page navigation
 */
class Page_Navigation_Admin
{
	private $initiated = false;
	private $taxonomies = array();
	private $pnav_taxonomies = array();
	public function __construct()
	{
		$this->init();

	}

	private function init()
	{
		if (!$this->initiated) {
			$this->init_hooks();
		}

		
	}
	/**
	 * init hooks
	 */
	private function init_hooks()
	{
		$this->initiated = true;
		add_action( 'admin_menu', array( $this, 'add_page_admin'));
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path(__FILE__) . 'page-navigation.php'), array( $this, 'plugin_settings' ));
	}
	/**
	 * add page admin settings
	 */
	public function add_page_admin()
	{
		add_menu_page(
			'Page navigation',
			'Page navigation',
			'manage_options',
			'page-navigation',
			array( $this, 'func_page_navigation'),
			'dashicons-ellipsis',
			'2'
		);
	}
	/**
	 * action saving data settings
	 */
	public function func_page_navigation(){
		$types = array(
			'page-navi' => 'Page Navi',
			'load-more' => 'Loadmore'
		);
		if (count($this->taxonomies) == 0) {
			$this->taxonomies = get_taxonomies( array('public' => true ), 'names', 'and' );
			unset($this->taxonomies['post_format'] );
	    	$this->taxonomies['is-home'] = 'home';
		}
		if (count($this->pnav_taxonomies) == 0) {
			if (count($this->taxonomies) > 0) {
			    foreach ($this->taxonomies as $k => $v) {
			        $this->pnav_taxonomies[] = $k;
			    }
		    }
		}
		$taxonomies = $this->taxonomies;
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
	/**
	 * action plugin settings links
	 */
	public function plugin_settings( $actions ) {
        $links = array(
            '<a href="' . admin_url( 'admin.php?page=page-navigation' ) . '">'. __('Settings', 'pnav') .'</a>',
        );
        $actions = array_merge( $actions, $links );
        return $actions;
    }

}