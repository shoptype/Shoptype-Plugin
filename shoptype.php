<?php
/*
Plugin Name:  Shoptype
Plugin URI:    
Description:  Integrate shoptype directly into your network with native login, checkout, market, product features and native integrations with budypress social features. 
Version:      1.3.1
Author:       shoptype 
Author URI:   https://www.shoptype.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  https://www.shoptype.com
Domain Path:  /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/* Initialisation of the shopype JS and adding the cart+profile buttons to the header */

function shoptype_header(){
	global $stApiKey;
	global $stPlatformId;
	global $stRefcode;
	global $productUrl;
	global $brandUrl;
	global $siteUrl;
	global $siteName;
	global $loginUrl;
	global $cartCountMatch;

	$siteUrl = get_site_url();
	$siteName = get_bloginfo('name');
	
  echo '<script src="https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@2.7.8/shoptype.js"></script>';
  echo "<awakesetup apikey='$stApiKey' refcode='$stRefcode' cartcountmatch='$cartCountMatch' platformid='$stPlatformId' loginUrl='$loginUrl'></awakesetup>";
  echo "<awakeMarket platformid='$stPlatformId' productpage='$productUrl' brandPage='$brandUrl'></awakeMarket>";
  echo '<script src="https://cdn.jsdelivr.net/gh/shoptype/Awake-Market-JS@1.6/awakeMarket.min.js"></script>';
};

//ST login modal script
function shoptype_login_modal(){
	echo '<script type="text/javascript">
		const openModal = () => {
			stLoginHandler.openSTLoginModal(
				{
				name: "<?php global $siteName; echo $siteName; ?>",
				url: "<?php global $siteUrl; echo $siteUrl; ?>",
				rid: "<?php global $stRefcode; echo $stRefcode; ?>",
				},
				(appRes) => {
					switch (appRes.app.event) {
						case "form rendered":
						  break;
						case "modal opened":
						  break;
						case "modal closed":
						  break;
						case "modal closed by user":
						  break;
						case "login success":
						  stLoginHandler.closeSTLoginModal();
						  window.location.search += "&token="+appRes.user.token;
						  break;
						case "login failed":
						  break;
						case "sign-up success":
						  stLoginHandler.closeSTLoginModal();
						  window.location.search += "&token="+appRes.user.token;
						  break;
						case "sign-up failed":
							break;
					  }
				}
			);
		};
	</script>';
	}
	add_action('wp_head', 'shoptype_login_modal');
	
//Add Product Route
add_action('init', function(){
    add_rewrite_rule( 'products/([a-z0-9\-]+)[/]?$', 'index.php?product=$matches[1]', 'top' );
	add_rewrite_rule( 'brands/([a-z0-9\-]+)[/]?$', 'index.php?brand=$matches[1]', 'top' );
	add_rewrite_rule( 'cart/([a-z0-9\-]+)[/]?$', 'index.php?cart=$matches[1]', 'top' );
	add_rewrite_rule( 'checkout/([a-z0-9\-]+)[/]?$', 'index.php?checkout=$matches[1]', 'top' );
	add_rewrite_rule( 'shop/(.+)[/]?$', 'index.php?shop=$matches[1]', 'top' );
	add_rewrite_rule( 'collections/(.+)[/]?$', 'index.php?collection=$matches[1]', 'top' );
	add_rewrite_rule( 'tags/(.+)[/]?$', 'index.php?tag=$matches[1]', 'top' );
});

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'product';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'brand';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'cart';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'checkout';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'shop';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'collection';
    return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'tag';
    return $query_vars;
} );

add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'product' ) == false || get_query_var( 'product' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/page-product.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'brand' ) == false || get_query_var( 'brand' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/page-brand.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'cart' ) == false || get_query_var( 'cart' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/cart.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'checkout' ) == false || get_query_var( 'checkout' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/checkout.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'shop' ) == false || get_query_var( 'shop' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/page-myshop.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'collection' ) == false || get_query_var( 'collection' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/page-collection.php';
} );
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'tag' ) == false || get_query_var( 'tag' ) == '' ) {
        return $template;
    }
    return plugin_dir_path( __FILE__ ) . '/templates/page-tags.php';
} );

//Shoptype login handler
function login_load_js_script() {
	wp_enqueue_script( 'js-file', plugin_dir_url(__FILE__) . 'js/st-login-handler.min.js');
}

add_action('wp_enqueue_scripts', 'login_load_js_script');
add_action('wp_head', 'shoptype_header');
add_action('wp_head', 'shoptypeLogout');

//Enqueue Product and brand page css

function theme_scripts() {
	wp_enqueue_style( 'awake-prod-style', plugin_dir_url(__FILE__) . 'css/awake-prod-style.css' );
	wp_enqueue_style( 'awake-prod-media-style', plugin_dir_url(__FILE__) . 'css/awake-prod-media-style.css' );
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

//Initialise the market
function awakenthemarket(){
	echo '<script>awakenTheMarket()</script>';
}
add_action('wp_footer', 'awakenthemarket');

/* Shoptype login */
function shoptype_login(){
	$token = $_GET['token'];
	if (is_user_logged_in()) {return;}
	if( empty( $token ) ) {return;}
	
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://backend.shoptype.com/me');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   "Authorization: {$token}"
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		curl_close($ch);
	}
	catch(Exception $e) {
		return false;
	}
	if( empty( $result ) ) {return false;}
	$st_user = json_decode($result);

	$user = get_user_by( 'email', "{$st_user->email}" ); 

	if ( empty( $user ) ) {
		$user_id = wp_insert_user( array(
			'user_login' => $st_user->email,
			'user_pass' => $token,
			'user_email' => $st_user->email,
			'first_name' => $st_user->name,
			'display_name' => $st_user->name,
			'role' => 'subscriber',
			'show_admin_bar_front' => false

		));
		$wp_user = wp_set_current_user($user_id, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		global $current_user;
		$current_user = $wp_user;
	}else{
		$wp_user = wp_set_current_user($user->ID, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		global $current_user;
		$current_user = $wp_user;
		do_action( 'wp_login', $wp_user->user_login, $wp_user );
	}
	
	global $wp;
	$url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	header("location: $url");
};

//Redirect users to home after logout
add_action('wp_logout','ST_redirect_after_logout');
function ST_redirect_after_logout(){
         wp_redirect( '/' );
         exit();
}

add_action('get_header', 'shoptype_login');





/* Shoptype logout */
function shoptypeLogout(){
	if ( !is_user_logged_in() ) {
		unset( $_COOKIE["stToken"] );
		setcookie( "stToken", '', time() - ( 15 * 60 ) );
		echo '<script>setCookie("stToken",null,0);sessionStorage.removeItem("token");sessionStorage.removeItem("userId");</script>';
	}
}


add_action( 'wp_logout','ST_logout' );
function ST_logout() { 
	?>
<script type="text/javascript">shoptypeLogout();</script>;
<?php
	wp_safe_redirect( home_url() );
    exit();
}


/*Adding shoptype products to search results*/
function search_filter($query) {
    if ( is_search() ) {
		$post = new stdClass();
		$post->ID = 113;
		$wp_post = new WP_Post( $post );
        array_push( $query->posts, $wp_post );
    }
}
add_action('posts_search','search_filter');    

function have_posts_override(){
    if ( is_search() ) {
		global $stPlatformId;
        global $wp_query;
		$q = $wp_query->query_vars;
		$searchTxt="";
		foreach ((array)$q['search_terms'] as $term) {
			$searchTxt .= "$term%20";
		}
		$url = "https://backend.shoptype.com/platforms/$stPlatformId/products?count=20&text=$searchTxt";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		if( !empty( $result ) ) {
			$st_products = json_decode($result);
			for (end($st_products->products); key($st_products->products)!==null; prev($st_products->products)){
				$stProduct = current($st_products->products);
				$post = new stdClass();
				$post->ID = $stProduct->id;
				$post->post_author = 1;
				$post->post_date = current_time( 'mysql' );
				$post->post_date_gmt = current_time( 'mysql', 1 );
				$post->post_title = $stProduct->title;
				$post->post_content = $stProduct->description;
				$post->post_status = 'publish';
				$post->comment_status = 'closed';
				$post->ping_status = 'closed';
				$post->post_name = "products/{$stProduct->id}"; // append random number to avoid clash
				$post->post_type = 'post';
				$post->filter = 'raw';
				$wp_post = new WP_Post( $post );
				array_unshift($wp_query->posts, $wp_post); 
			}
		}
    }
}
//add condition to not override admin pagination
if (! is_admin() ) {
    add_action( 'found_posts', 'have_posts_override' );

} 

/*Add page templates*/

class PageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new PageTemplater();
		} 

		return self::$instance;

	} 

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);


		// Add your templates to this array.
		$this->templates = array(
			'templates/page-product.php' => 'Product Details Page',
			'templates/page-brand.php' => 'Brand Details Page',
			'templates/st-login.php' => 'Shoptype Login Page',
			'templates/page-all-groups.php' => 'All Groups Page',
			'templates/page-allbrands.php' => 'All Brands Page',
			'templates/page-allcosellers.php' => 'All Cosellers Page',
			'templates/page-allproducts.php' => 'All Products Page',
			'templates/page-coseller.php' => 'Coseller Details Page',
			'templates/page-products-home.php' => 'Products Home Page',
			'templates/page-shop.php' => 'Shop Page',
			'templates/st-profile.php' => 'Shoptype Profile Page',
			'templates/cart.php' => 'Shoptype Cart Page',
		);
			
	} 

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

} 
add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );
//Create hook for creating page with shoptype shortcode
function add_shop_page() {
    // Create Page object
    $shop_page = array(
      'post_title'    => wp_strip_all_tags('Marketplace'),
      'post_content'  => '[awake_products per_row="6"]',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'     => 'page',
    );

    // Insert the Page into the database
    wp_insert_post( $shop_page );
}

register_activation_hook(__FILE__, 'add_shop_page');

// Add post state to the Shop page

add_filter( 'display_post_states', 'ecs_add_post_state', 10, 2 );

function ecs_add_post_state( $post_states, $post ) {

	if( $post->post_name == 'marketplace' ) {
		$post_states[] = ' Marketplace page';
	}

	return $post_states;
}
//Admin notice for buddypress requirement
/*	if ( ! class_exists( 'BuddyPress' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Shoptype requires BuddyPress to be installed and active. You can download %s here.', 'Shoptype' ), '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">BuddyPress</a>' ) . '</strong></p></div>';
		} );
		return;
}*/
//Add custom Search templet templates/search.php
add_filter('template_include','custom_search_template', 10, 3);

function custom_search_template($template){
    global $wp_query;
    if (!$wp_query->is_search)
        return $template;
    
	$search_template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/search.php';
    return $search_template;

} 

define( 'ST__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ST__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once(ST__PLUGIN_DIR.'/shortcodes/products.php');
require_once(ST__PLUGIN_DIR.'/shortcodes/cosellers.php');
require_once(ST__PLUGIN_DIR.'/shortcodes/brands.php');
require_once(ST__PLUGIN_DIR.'/shortcodes/communities.php');
require_once(ST__PLUGIN_DIR.'/shortcodes/editors_picks.php');
require_once(ST__PLUGIN_DIR.'/admin_settings.php');
require_once(ST__PLUGIN_DIR.'/my_shop.php');
require_once(ST__PLUGIN_DIR.'/my_st_dashboard.php');
require_once(ST__PLUGIN_DIR.'/shortcodes/collections.php');
