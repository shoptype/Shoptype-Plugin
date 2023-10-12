<?php
/*
Plugin Name:  Shoptype
Plugin URI:	
Description:  Integrate shoptype directly into your network with native login, checkout, market, product features and native integrations with budypress social features. 
Version:	  2.1.3
Author:	 	  shoptype 
Author URI:   https://www.shoptype.com
License:	  GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  https://www.shoptype.com
Domain Path:  /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
//include required files

/* Initialisation of the shopype JS and adding the cart+profile buttons to the header */
define( 'ST__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ST__PLUGIN_URL', plugin_dir_url( __FILE__ ) );


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

	wp_enqueue_script( 'shoptype_js', ST__PLUGIN_URL . 'js/shoptype.js');
	wp_enqueue_script( 'shoptype_ui_js', ST__PLUGIN_URL . 'js/shoptype_ui.js');
	wp_enqueue_script( 'awakeMarket_js', ST__PLUGIN_URL . 'js/AwakeMarket.js');
	wp_enqueue_style( 'shoptype_css', ST__PLUGIN_URL . 'css/shoptype.css');
	wp_enqueue_style( 'awake-prod-style', ST__PLUGIN_URL . 'css/awake-prod-style.css' );
	wp_enqueue_style( 'awake-prod-media-style', ST__PLUGIN_URL . 'css/awake-prod-media-style.css' );
	echo "<awakeMarket productpage='$productUrl' brandPage='$brandUrl'></awakeMarket>"; 
	?>
	<div class="st-cosell-btn">
	  <a href="#" class="div-block w-inline-block" onclick="shoptype_UI.showCosell(null)"><img src="<?php echo st_locate_file("images/cosell.png"); ?>" style="width:42px;" loading="lazy" alt=""></a>
	  <div class="st-cosell-details">
		<div class="st-cosell-txt">Share &amp; Earn</div>
	  </div>
	</div>
	<?php
};
add_action('wp_head', 'shoptype_header');

// Using 'bp-api-request' as a dependency.
function example_enqueue_script() {
  wp_enqueue_script( 'my-script-handle', 'url-to/my-script.js', array( 'bp-api-request' ) );
}
add_action( 'bp_enqueue_scripts', 'example_enqueue_script' );

//Add Product Route
add_action('init', function(){
	global $shoptypeUrlBase;
	add_rewrite_rule( $shoptypeUrlBase.'products/([a-z0-9\-]+)[/]?$', 'index.php?stproduct=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)/products/([a-z0-9\-]+)[/]?$', 'index.php?stproduct=$matches[2]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)/cart/([a-z0-9\-]+)[/]?$', 'index.php?cart=$matches[2]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)/checkout/([a-z0-9\-]+)[/]?$', 'index.php?checkout=$matches[2]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)/checkout-success/(.+)[/]?$', 'index.php?success_chkout=$matches[2]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)/cosell/(.+)[/]?$', 'index.php?cosell_link=$matches[2]&shop_name=$matches[1]', 'top' );	
	add_rewrite_rule( $shoptypeUrlBase.'brands/([a-z0-9\-]+)[/]?$', 'index.php?brand=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'cart/([a-z0-9\-]+)[/]?$', 'index.php?cart=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'checkout/([a-z0-9\-]+)[/]?$', 'index.php?checkout=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop-wizard/(.+)[/]?$', 'index.php?stwizard=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'shop/(.+)[/]?$', 'index.php?shop=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'collections/(.+)[/]?$', 'index.php?collection=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'tags/(.+)[/]?$', 'index.php?sttag=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'checkout-success/(.+)[/]?$', 'index.php?success_chkout=$matches[1]', 'top' );
	add_rewrite_rule( $shoptypeUrlBase.'cosell/(.+)[/]?$', 'index.php?cosell_link=$matches[1]', 'top' );

});

add_filter( 'query_vars', function( $query_vars ) {
	$query_vars[] = 'stproduct';
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
	$query_vars[] = 'stwizard';
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
	$query_vars[] = 'sttag';
	return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
	$query_vars[] = 'success_chkout';
	return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
	$query_vars[] = 'cosell_link';
	return $query_vars;
} );
add_filter( 'query_vars', function( $query_vars ) {
	$query_vars[] = 'shop_name';
	return $query_vars;
} );
/**
 * Works much like <a href="http://codex.wordpress.org/Function_Reference/locate_template" target="_blank">locate_template</a>, except it takes a string instead of an array of templates, we only need to load one.
 * @param string $template_name
 * @param boolean $load
 * @uses locate_template()
 * @return string
 */
function st_locate_template( $template_name, $load=false, $the_args = array() ) {
	//First we check if there are overriding tempates in the child or parent theme
	$located = locate_template(array('shoptype/'.$template_name));
	if( !$located ){
		// finally get the plugin from Shoptype if no others exist
		$located = apply_filters('em_locate_template_default', $located, $template_name, $load, $the_args);
		if ( !$located && file_exists(ST__PLUGIN_DIR.'templates/'.$template_name) ) {
			$located = ST__PLUGIN_DIR.'templates/'.$template_name;
		}
	}
	$located = apply_filters('st_locate_template', $located, $template_name, $load, $the_args);
	if( $located && $load ){
		$the_args = apply_filters('st_locate_template_args_'.$template_name, $the_args, $located);
		if( is_array($the_args) ) extract($the_args);
		include($located);
	}
	return $located;
}

function st_locate_file($filename){
	$fileUrl = get_template_directory_uri().'/shoptype/'.$filename;
	$located = locate_template(array('shoptype/'.$filename));
	if( !$located ){
		$fileUrl = ST__PLUGIN_URL.$filename;
	}
	return $fileUrl;
}

function my_headers() {
    header("Access-Control-Allow-Origin: *");
}
add_action( 'send_headers', 'my_headers' );
add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'stproduct' ) == false || get_query_var( 'stproduct' ) == '' ) {
		return $template;
	}

	wp_enqueue_script( 'image-slider', ST__PLUGIN_URL . 'templates/js/imageslider.js' );
	return st_locate_template('page-product.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'brand' ) == false || get_query_var( 'brand' ) == '' ) {
		return $template;
	}
	return st_locate_template('page-brand.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'cart' ) == false || get_query_var( 'cart' ) == '' ) {
		return $template;
	}
	wp_enqueue_style( 'new-market', ST__PLUGIN_URL . '/css/st-cart.css' );
	wp_enqueue_script('triggerUserEvent','https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js');
	return st_locate_template('cart.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'checkout' ) == false || get_query_var( 'checkout' ) == '' ) {
		return $template;
	}
	wp_enqueue_style( 'cartCss', ST__PLUGIN_URL.'/css/st-cart.css' );
	wp_enqueue_style( 'stripeCss', ST__PLUGIN_URL.'/css/stripe.css' );
	wp_enqueue_style( 'authnetCss', ST__PLUGIN_URL.'/css/authnet.css' );
	wp_enqueue_script('triggerUserEvent','https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js');
	wp_enqueue_script('st-payment-handlers',ST__PLUGIN_URL."/js/shoptype-payment.js");
	wp_enqueue_script('stripe',"https://js.stripe.com/v3/");
	wp_enqueue_script('razorpay',"https://checkout.razorpay.com/v1/checkout.js");
	return st_locate_template('checkout.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'stwizard' ) == false || get_query_var( 'stwizard' ) == '' ) {
		return $template;
	}
	return st_locate_template('myshop-wizard.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'shop' ) == false || get_query_var( 'shop' ) == '' ) {
		return $template;
	}
	$user_id = getUserIdByUrl(get_query_var( 'shop' ));
	if(!can_have_myshop($user_id)){
		return st_locate_template("myshop_enable.php");
	}
	$field_id = xprofile_get_field_id_from_name( 'st_shop_url');
	$shop_theme = xprofile_get_field_data( 'st_shop_theme' , $user_id );
	
	if( !$shop_theme ){
		$shop_theme="page-myshop.php";
	}else{
		$shop_theme = "$shop_theme.php";
	}

	return st_locate_template($shop_theme);
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'collection' ) == false || get_query_var( 'collection' ) == '' ) {
		return $template;
	}
	return st_locate_template('page-collection.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'sttag' ) == false || get_query_var( 'sttag' ) == '' ) {
		return $template;
	}
	return st_locate_template('page-tags.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'success_chkout' ) == false || get_query_var( 'success_chkout' ) == '' ) {
		return $template;
	}
	wp_enqueue_style( 'new-market', ST__PLUGIN_URL . '/css/st-cart.css' );
	wp_enqueue_script('triggerUserEvent','https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js');

	return st_locate_template('checkout-success.php');
} );

add_action( 'template_include', function( $template ) {
	if ( get_query_var( 'cosell_link' ) == false || get_query_var( 'cosell_link' ) == '' ) {
		return $template;
	}
	
	return st_locate_template('coseller-share.php');
} );

function getUserIdByUrl( $store_url ) {
	$the_user = get_user_by('login', $store_url);
	if(isset($the_user->id)){
		return $the_user->id;
	}else{
		$field_id = xprofile_get_field_id_from_name( 'st_shop_url');
		global $wpdb;
		$bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

		$query = $wpdb->prepare(
			"SELECT user_id,user_login,user_nicename,user_email,display_name " .
			"FROM $bp_table B, $wpdb->users U " .
			"WHERE B.user_id = U.ID " .
			"AND B.field_id = %d " .
			"AND B.value = %s"
		   , $field_id
		   , $store_url
		);
		$get_desired = $wpdb->get_results($query);
		
		return  $get_desired[0]->user_id;
	}
}

//Ensure that user is logged in
function st_ensure_user_loggedin(){
	global $stBackendUrl;

	if(!is_user_logged_in()){
		redirect_to_login($temp);
	}

	$st_token = $_COOKIE["stToken"];
	$args = array(
		'headers'=> array(
			"Authorization"=> $st_token
		)
	);
	$result = wp_remote_get( "{$stBackendUrl}/me", $args );
	if ( ! is_wp_error( $result ) ) {
		$body = wp_remote_retrieve_body( $result );
		$st_profile = json_decode($body);
		return $st_profile;
	}else{
		redirect_to_login($temp);
	}
}

function redirect_to_login($temp){
	global $wp;
	global $loginUrl;
	$current_url = home_url( $wp->request );
	wp_redirect( "{$loginUrl}?redirectUrl=" . urlencode($current_url) . "&$temp", 302 );
    exit();
}

//Add Custom Var to Groups
add_action( 'bp_groups_admin_meta_boxes', 'bpgcp_add_admin_metabox' );
function bpgcp_add_admin_metabox() {	
	add_meta_box( 
		'bp_channel_products', // Meta box ID 
		'Channel Products', // Meta box title
		'bpgcp_render_admin_metabox', // Meta box callback function
		get_current_screen()->id, // Screen on which the metabox is displayed. In our case, the value is toplevel_page_bp-groups
		'side', // Where the meta box is displayed
		'core' // Meta box priority
	);
}
function bpgcp_render_admin_metabox() {
	$group_id = intval( $_GET['gid'] );
	$channel_products = groups_get_groupmeta( $group_id, 'channel_products' );
	?>

	<div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
		<fieldset>
			<legend>Products Collection ID</legend>
			<label>
				<input type="text" name="channel_products" value='<?php echo $channel_products  ?>' >
			</label>
		</fieldset>
	</div>

	<?php
}

add_action( 'groups_group_details_edited', 'bpgcp_save_metabox_fields' );
add_action( 'bp_group_admin_edit_after', 'bpgcp_save_metabox_fields' );
function bpgcp_save_metabox_fields( $group_id ) {
	$channel_products = $_POST['channel_products'];
	groups_update_groupmeta( $group_id, 'channel_products', $channel_products );
}

//Set width and height for cover images
function your_theme_xprofile_cover_image( $settings = array() ) {
    $settings['width']  = 2000;
    $settings['height'] = 600;
 
    return $settings;
}
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'your_theme_xprofile_cover_image', 11, 1 );

function bpex_hide_profile_menu_tabs() {
	if( bp_is_active( 'xprofile' ) ) :
		bp_core_remove_nav_item( 'settings' );
		bp_core_remove_nav_item( 'activity' );
		bp_core_remove_nav_item( 'groups' );
	endif; 
}
add_action( 'bp_setup_nav', 'bpex_hide_profile_menu_tabs', 15 );


//Initialise the market
function awakenthemarket(){
	global $stApiKey;
	global $stPlatformId; 
	global $cartCountMatch;
	global $shoptypeUrlBase;
	global $productUrl;
	global $loginUrl;
	global $myAccountUrl;?>
<script>
	let st_platform = undefined;
	let st_settings ={
		'apikey':'<?php echo $stApiKey ?>',
		'platformId':'<?php echo $stPlatformId ?>',
		'cartCount':'<?php echo $cartCountMatch ?>',
		'productUrl':'<?php echo $productUrl ?>',
		'loginUrl':'<?php echo $loginUrl ?>',
		'myAccountUrl':'<?php echo $myAccountUrl ?>',
		'siteUrl' : '<?php echo get_option('siteurl'); ?>',
		'baseUrl' : '<?php echo $shoptypeUrlBase; ?>'
	}

	if(typeof STUtils !== 'undefined'){
		st_platform = new STPlatform(st_settings.platformId, st_settings.apikey);
	}else{
		document.addEventListener("ShoptypeJsLoaded", ()=>{
			st_platform = new STPlatform(st_settings.platformId, st_settings.apikey);
		});
	}

	if(typeof shoptype_UI !== 'undefined'){
		loadShoptypeJs();
	}else{
		document.addEventListener("ShoptypeUILoaded", ()=>{
			loadShoptypeJs();
		});
	}
	
	function loadShoptypeJs(){
		createCartMenu();
		updateLoginMenu();
		shoptype_UI.setProductUrl(st_settings.productUrl);
		shoptype_UI.setLoginUrl(st_settings.loginUrl);
		shoptype_UI.setPlatform(st_settings.platformId);
		shoptype_UI.setCosellWidget(st_settings.baseUrl);
		document.addEventListener("cartQuantityChanged", (e)=>{
			var cartCounts = document.querySelectorAll(st_settings.cartCount);
			if(cartCounts.length>0){
				cartCounts.forEach((x)=>{
					x.innerHTML = e.detail.count;
				});
			}
		});
		if(typeof awakenTheMarket === 'function'){
			awakenTheMarket();
		}
		else{
			document.addEventListener('marketLoaded', function(){awakenTheMarket()});
		}
	}
	
	function createCartMenu(){
		var cartMenus = document.querySelectorAll(".st-cart-menu a");
		if(cartMenus.length>0){
		   cartMenus.forEach((x)=>{
			  x.innerHTML="<img src='<?php echo ST__PLUGIN_URL . 'images/shopping-cart.png' ?>'><span>0</span>"; 
		   });
		}
	}
	
	function updateLoginMenu(){
		if(shoptype_UI.user){
			var loginMenu =  document.getElementsByClassName("st-login-menu");
			for (var i = 0; i < loginMenu.length; i++) {
				loginMenu[i].querySelector("a").href=st_settings.myAccountUrl;
				loginMenu[i].querySelector("a").title="My Account";
				loginMenu[i].querySelector("a").innerHTML="My Account";
			}
		}
	}
</script>

<?php
}
add_action('wp_head', 'awakenthemarket');

/* Shoptype login */
function shoptype_login(){
	if(isset($_GET['token'])){
		$token = $_GET['token'];
		if ( empty($token)) {return;}
	}else{
		return;
	}
	global $stBackendUrl;
	if (is_user_logged_in()) {
		return;
	}
	if( empty( $token ) ) {return;}
	setcookie( "stToken", $token, time() + ( 150000 * 60 ) );
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "{$stBackendUrl}/me");
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
		$parts = explode(" ", $st_user->name);
		if(count($parts) > 1) {
			$lastname = array_pop($parts);
			$firstname = implode(" ", $parts);
		}
		else{
			$firstname = $name;
			$lastname = " ";
		}
		$user_id = wp_insert_user( array(
			'user_login' => $st_user->email,
			'user_pass' => $token,
			'user_email' => $st_user->email,
			'first_name' => $firstname,
			'last_name' => $lastname,
			'display_name' => $st_user->name,
			'role' => 'subscriber',
			'show_admin_bar_front' => false
		));
		$wp_user = wp_set_current_user($user_id, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		do_action( 'wp_login', $wp_user->user_login, $wp_user );
	}else{
		$wp_user = wp_set_current_user($user->ID, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		do_action( 'wp_login', $wp_user->user_login, $wp_user );
	}
};

//Redirect users to home after logout
add_action('wp_logout','ST_redirect_after_logout');
function ST_redirect_after_logout(){
		 wp_redirect( '/' );
		 exit();
}

add_action('init', 'shoptype_login');

add_action( 'wp_logout','ST_logout' );
function ST_logout() { 
	?>
	<script type="text/javascript">sessionStorage.clear();</script>;
	<?php
	unset( $_COOKIE["stToken"] );
	setcookie( "stToken", '', time() - ( 15 * 60 ) );
	echo "Logout user";
	wp_safe_redirect( home_url() );
	exit();
}

/**
 * Register a custom Form
**/
function edit_post_form() {
	define('BP_DEFAULT_COMPONENT', 'profile' );
	if(function_exists("bp_new_simple_blog_post_form")){
		$post_id = 0;
		if ( get_query_var('post_id') ) {
			$post_id = get_query_var('post_id');
		}
		$settings = array(
			'post_type'             => 'post',
			'post_author'           =>  bp_loggedin_user_id(),
			'post_status'           => 'draft',
			'current_user_can_post' =>  is_user_logged_in(),
			'show_categories'       => true,
			'allow_upload'          => true,
			'post_id'				=> $post_id
		);

		$form = bp_new_simple_blog_post_form( 'edit_form', $settings );
		//create a Form Instance and register it	
	}
}

add_action( 'bp_init', 'edit_post_form', 4 );//register a form

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
			'templates/st-login.php' => 'Shoptype Login Page',
			'templates/st-signup.php' => 'Shoptype Signup Page',
			'templates/page-allbrands.php' => 'All Brands Page',
			'templates/page-allcosellers.php' => 'All Cosellers Page',
			'templates/page-coseller.php' => 'Coseller Details Page',
			'templates/page-products-home.php' => 'Products Home Page',
			'templates/cart.php' => 'Shoptype Cart Page',
			'templates/my-account.php' => 'Shoptype MyAccount Page',
			'templates/my-shop-template.php' => 'my-shop-templet',
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
	  'post_title'	=> wp_strip_all_tags('Marketplace'),
	  'post_content'  => '[awake_products per_row="6"]',
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'	 => 'page',
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
// 	if ( ! class_exists( 'BuddyPress' ) ) {
// 		add_action( 'admin_notices', function() {
// 			echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Shoptype requires BuddyPress to be installed and active. You can download %s here.', 'Shoptype' ), '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">BuddyPress</a>' ) . '</strong></p></div>';
// 		} );
// 		return;
// }
//Add custom Search templet templates/search.php
add_filter('template_include','custom_search_template', 10, 3);

function custom_search_template($template){
	global $wp_query;
	global $addProductsInSearch;
	if (!($wp_query->is_search && $addProductsInSearch )){
		return $template;	
	}
	
	$search_template = untrailingslashit( plugin_dir_path( __FILE__ ) . '/templates/search.php');
	return $search_template;

} 

//Add snipet to manage cosell user
add_action('admin_menu', 'register_cosell_user', 20 );
 
function register_cosell_user() {
	add_submenu_page(
		'shoptype_settings',
		'Manage Cosell Users',
		'Manage Cosell Users',
		'manage_options',
		'my-admin-slug',
		'manage_cosell_users' );
}


function manage_cosell_users() {
	global $stRefcode;
	include (ST__PLUGIN_DIR.'shortcodes/coselluserlist.php');
	echo '<div class="wrap">';
		echo '<h2>Manage Cosell Users</h2><div style="margin-top:50px;margin-bottom:50px">';
		if($stRefcode=='')
		{
			 $stRefcode=0;
		}
		coselluserlists($stRefcode);
	echo '</div></div>';
} 

//Add snipet to manage cosell user
add_action('admin_menu', 'add_myshop_dashboard', 20 );
 
function add_myshop_dashboard() {
	if ( class_exists( 'BuddyPress' ) ) {
		add_submenu_page(
			'shoptype_settings',
			'My Shop Dashboard',
			'My Shop Dashboard',
			'manage_options',
			'my-shop-slug',
			'show_myshop_dashboard' );
	}
}


function show_myshop_dashboard() {
	global $stRefcode;
	include (ST__PLUGIN_DIR.'templates/myshop-dashboard.php');
} 
add_action('admin_menu', 'add_myshop_details', 20 );
 
function add_myshop_details() {
	if ( class_exists( 'BuddyPress' ) ) {
		add_submenu_page(
			null,
			'My Shop Details',
			'My Shop Details',
			'manage_options',
			'my-shop-details',
			'show_myshop_details' );
	}
}


function show_myshop_details() {
	global $stRefcode;
	include (ST__PLUGIN_DIR.'templates/myshop-details.php');
} 

//add coseller role
function coseller_new_role() {  
 
	//add the new user roles
	add_role(
		'coseller',
		'Coseller',
		array(
			'read'			=> true,
			'delete_posts'	=> false
		)
	);

	add_role(
		'channel_champion',
		'Channel Champion',
		array(
			'read'					=> true,
			'edit_posts'			=> true,
			'edit_published_posts'	=> true,
			'upload_files'			=> true,
			'publish_posts'			=> true,
			'delete_published_posts'=> true,
			'delete_posts'			=> true
		)
	);
	
	add_role(
		'myshop_owner',
		'Myshop Owner',
		array(
			'read'					=> true,
			'edit_posts'			=> true,
			'edit_published_posts'	=> true,
			'upload_files'			=> true,
			'publish_posts'			=> true,
			'delete_published_posts'=> true,
			'delete_posts'			=> true
		)
	);
 
}
add_action('admin_init', 'coseller_new_role');

//define hooks for ajax

 require_once(ST__PLUGIN_DIR.'/shortcodes/products.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/cosellers.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/brands.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/communities.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/editors_picks.php');
 require_once(ST__PLUGIN_DIR.'/admin_settings.php');
 require_once(ST__PLUGIN_DIR.'/my_shop.php');
 require_once(ST__PLUGIN_DIR.'/my_st_dashboard.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/collections.php');
 require_once(ST__PLUGIN_DIR.'/shortcodes/coseller-shop.php');

