<?php
/**
 * Page navigation
 */

class Page_Navigation
{
	private static $initiated = false;
	private static $taxonomies = array();
	private static $pnav_taxonomies = array();
	private static $pnav_type = '';
	private static $next_link = '';
	private static $next_data_file = '';
	private static $next_data_url = '';
	private static $custom_query = false;


	public static function init()
	{
		self::$pnav_type = get_option('pnav_type');
		if (! self::$initiated) {
			self::init_hooks();
		}
		if (count(self::$taxonomies) == 0) {
			self::$taxonomies = get_taxonomies( array('public' => true ), 'names', 'and' );
			unset(self::$taxonomies['post_format'] );
	    	self::$taxonomies['is-home'] = 'home';
		}
		if (count(self::$pnav_taxonomies) == 0) {
			if (count(self::$taxonomies) > 0) {
			    foreach (self::$taxonomies as $k => $v) {
			        self::$pnav_taxonomies[] = $k;
			    }
		    }
		}
		self::$next_data_file = PNAVI_DIR . 'nextdata.html'; 
		self::$next_data_url = PNAVI_URL . 'nextdata.html'.'?='.time(); 
	}
	/**
	 * init hooks
	 */
	private static function init_hooks()
	{
		self::$initiated = true;
		
		add_action('loop_end', array( 'Page_Navigation', 'page_naviagtion_default'));
		add_action('pre_get_posts', array( 'Page_Navigation', 'custom_pre_get_posts'));
		if (self::$pnav_type == 'load-more') {
			add_action('loop_start', array( 'Page_Navigation', 'add_ob_start'));
			//add_action('loop_end', array( 'Page_Navigation', 'add_ob_end'));
			add_action('wp_enqueue_scripts', array( 'Page_Navigation', 'scripts_loadmore') );
			add_action('wp_ajax_loadmore', array( 'Page_Navigation', 'pnav_loadmore_ajax_handler') ); // wp_ajax_{action}
            add_action('wp_ajax_nopriv_loadmore', array( 'Page_Navigation', 'pnav_loadmore_ajax_handler') ); // wp_ajax_nopriv_{action}
		}
	}

	/**
	 * custom pre get posts page in template
	 */
	public function custom_pre_get_posts($query)
	{
		if( is_admin() )
	    {
	        return $query;
	    }
	    if (!is_page_template() || (!is_front_page() && is_home())) return;
    	if (get_query_var('paged')) $paged = (int) get_query_var('paged');
	    elseif (get_query_var('page')) $paged = (int) get_query_var('page');
	    else $paged = 1;
	    //echo $paged;
    	if ($paged>1) {
    		if ($query->get('paged') != $paged) {
	    		$query->set('paged', $paged);
	    		$query->set('is_paged', true);
	    	}
    	}
	}

	 /**
	 * page navitagion function
	 */

	public static function get_page_naviagtion($custom_query=false)
	{
	    global $wp_query;
	    if ( !$custom_query ) { $custom_query = $wp_query; }
	    $_SESSION['custom_query'] = $custom_query;
	    
	    if (get_query_var('paged')) $paged = (int) get_query_var('paged');
	    elseif (get_query_var('page')) $paged = (int) get_query_var('page');
	    else $paged = 1;

	    //echo $paged;
	    //echo "<pre>";
	   // var_dump($custom_query);

	    if ($custom_query->max_num_pages <= 1) {
	        return;
	    }
	    $max = absint($custom_query->max_num_pages);

	    if (self::$pnav_type == 'page-navi') {
	    	return self::get_page_type_pagenumber($paged, $max);
	    }	else {
	    	return self::get_page_type_loadmore($paged, $max);
	    }

	}
	/**
	 * pagination with style pagenumber
	 */
	public static function get_page_type_pagenumber($paged, $max)
	{
		$range = 6;
	    $i=2;
	    
	    if ($range>=$max) {
	        $range = $max-1;
	    } else {
	        if ($paged >= $range) {
	            $f = 2;
	            $e = 2;
	            if($paged==$max) {
	                $e=-1;
	                $f=4;
	            } elseif($paged+1==$max) {
	                $e=0;
	                $f=3;
	            } elseif($paged+2==$max) {
	                $e=1;
	                $f=2;
	            }
	            $i = $paged - $f;
	            $range = $paged + $e;
	        }
	    }
	    ob_start();
	    ?>
	    <style type="text/css">
	        .nav-pagination {
	            display: block;
	            text-align: center;
	            position: relative;
	        }
	        .page-number {
	            display: inline-block;
	            padding-left: 0;
	            margin: 15px 0 20px;
	            border-radius: 4px;
	        }
	        .page-number>li {
	            display: inherit;
	            margin: 0 3px 10px;
	        }
	        .page-number>li a.page-link {
	            border: 1px solid #ccc;
	            border-radius: 1px;
	            display: inline-block;
	            font-size: 14px;
	            color: #999;
	            padding: 5px 15px;
	            margin: 0;
	            text-decoration: none;
	        }
	        .page-number>li.active a, .page-number>li a:hover{
	            background: #d32f2f;
	            color: #fff;
	            border: 1px solid #d32f2f;
	        }
	    </style>
	    <div class="nav-pagination">
	        <ul class="page-number">
	            <!-- prev -->
	            <?php if (get_previous_posts_link()) : ?>
	                <li>
	                    <a class="page-link" href="<?php echo esc_url( get_pagenum_link($paged-1) ) ?>"> ‹ </a>
	                </li>
	            <?php endif; ?>

	            <!-- page one -->
	            <?php $active = (1==$paged) ? 'class="active"' : ''; ?>
	            <li <?php echo $active ?>>
	                <a class="page-link" href="<?php echo esc_url( get_pagenum_link(1) ) ?>"> 1 </a>
	            </li>

	            <!-- dots begin -->
	            <?php if ($i>2) : ?>
	                <li><span class="dots">...</span></li>
	            <?php endif; ?>

	            <!-- number center -->
	            <?php for ($i; $i <= $range; $i++) : 
	                $active = ($i==$paged) ? 'class="active"' : '';
	                ?>
	                <li <?php echo $active ?>>
	                    <a class="page-link" href="<?php echo esc_url( get_pagenum_link($i) ) ?>"><?php echo $i ?></a>
	                </li>
	            <?php endfor; ?>

	            <!-- dots end --> 
	            <?php if ($max-$range>=2) : ?>
	                <li><span class="dots">...</span></li>
	            <?php endif; ?>

	            <!-- page end -->
	            <?php if ($max) : 
	                $active = ($max==$paged) ? 'class="active"' : ''; ?>
	                <li <?php echo $active ?>>
	                    <a class="page-link" href="<?php echo esc_url( get_pagenum_link($max) ) ?>"><?php echo $max ?></a>
	                </li>
	            <?php endif; ?>

	            <!-- next -->
	            <?php if (get_next_posts_link()) : ?>
	                <li>
	                    <a class="page-link" href="<?php echo esc_url( get_pagenum_link($paged+1) ) ?>"> › </a>
	                </li>
	            <?php endif; ?>
	        </ul>
	    </div>
	    <?php
	    $content = ob_get_contents();
	    ob_end_clean();

	    return $content;
	}
	/**
	 * pagination with style loadmore
	 */
	public function get_page_type_loadmore($paged, $max)
	{
		$content = ob_get_contents();
        ob_end_flush();
	    self::save_file(self::$next_data_file, $content);
        echo '<span class="pointer-append"></span>';
		ob_start();
		?>
			<style type="text/css">
				.wrap-loadmore {
				    position: relative;
				    text-align: center;
				    display: block;
				}
				.btn-loadmore {
					background: #d32f2f;
				    border: none;
				    box-sizing: border-box;
				    color: #fff;
				    font-size: 14px;
				    padding: 12px;
				    text-shadow: none;
				    cursor: pointer;
				    display: inline-block;
				    text-decoration: none;
				    white-space: nowrap;
				    margin: 15px 0 20px;
				    min-width: 180px;
				}
			</style>
			<div class="wrap-loadmore">
				<button class="btn-loadmore">Load more</button>
			</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	/**
	 * save content to file
	 */
	public static function save_file($path_file, $content)
	{
		if (file_exists($path_file)) {
	        file_put_contents($path_file, $content);
	    }
	}
	/**
	 * scritps loadmore
	 */
	public function scripts_loadmore()
	{
		global $wp, $wp_query;
		wp_register_script('pnav_loadmore', plugin_dir_url( __FILE__) . 'assets/js/pnav-loadmore.js', array('jquery'), PNAVI_VERSION, true );

		if (get_query_var('paged')) $paged = (int) get_query_var('paged');
	    elseif (get_query_var('page')) $paged = (int) get_query_var('page');
	    else $paged = 1;
		wp_localize_script( 'pnav_loadmore', 'pnav_loadmore_params', array(
			'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
			'current_page' => $paged,
			'current_url' => home_url( $wp->request ),
			'max_page' => $_SESSION['custom_query']->max_num_pages
		) );
	 	wp_enqueue_script( 'pnav_loadmore' );
	}
	/**
	 * AJAX loadmore handler
	 */
	public function pnav_loadmore_ajax_handler()
	{
		global $wp;
		$args = json_decode( stripslashes( $_POST['query'] ), true );
        $paged = $_POST['page'] + 1;
        $next_link = $_POST['current_url'] .'/page/'.$paged.'/';
		$res_quest = file_get_contents($next_link, "r" );
		if ($res_quest) {
			readfile(self::$next_data_url);
		}
		exit; 
	}
	public function add_ob_start()
	{
		ob_start();
	}

	/**
	 * add page navigation to end loop with default setting
	 */
	public static function page_naviagtion_default()
	{
	    $page_naviagtion = '';
	    $taxonomy = !empty(get_queried_object()->taxonomy) ? get_queried_object()->taxonomy : '';
	    $taxonomies = get_option('pnav_taxonomies');
	    if ( in_array($taxonomy, $taxonomies) || (in_array('is-home', $taxonomies) && is_front_page() && is_home()) ) {
	        $page_naviagtion = self::get_page_naviagtion();
	    }
	    echo $page_naviagtion;
	}

	/**
	 * Run when plugin activation
	 */
	
	public static function plugin_activation()
	{
	    add_option('pnav_type', 'page-navi');
	    add_option('pnav_taxonomies', self::$pnav_taxonomies);
	}
	/**
	 * Run when plugin deactivation
	 */
	public static function plugin_deactivation()
	{
        delete_option('pnav_type');
        delete_option('pnav_taxonomies');
	}
}