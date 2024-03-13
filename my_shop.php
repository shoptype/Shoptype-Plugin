<?php
/**
 * Add custom sub-tab on groups page.
 */
function buddyboss_my_shop_tab() {
	wp_enqueue_style( 'my-shop-css', plugin_dir_url( __FILE__ ) . '/css/st-my-shop.css' );
	// Avoid fatal errors when plugin is not available.
	if ( ! function_exists( 'bp_core_new_nav_item' ) ||
		 ! function_exists( 'bp_loggedin_user_domain' ) ||
		 empty( bp_displayed_user_id() ) ) {
		return;
	  }

	  global $bp;

	  bp_core_new_nav_item(
		array(
		  'name'                => esc_html__( 'My Shop', 'shoptype' ),
		  'slug'                => 'my-shop',
		  'screen_function'     => 'my_shop_screen',
		  'position'            => 100,
		  'parent_url'          => bp_displayed_user_domain() . '/my-shop/',
		  'parent_slug'         => $bp->profile->slug,
		)
	  );
}

add_action( 'bp_setup_nav', 'buddyboss_my_shop_tab' );

/**
 * Set template for new tab.
 */
function my_shop_screen() {
	// Add title and content here - last is to call the members plugin.php template.
	if( bp_displayed_user_id() !== get_current_user_id()){
		$displayUser = get_userdata(bp_displayed_user_id());
		wp_redirect( "/shop/".$displayUser->user_login, 302 );
	}else{
		add_action( 'bp_template_title', 'my_shop_tab_title' );
		add_action( 'bp_template_content', 'my_shop_tab_content' );
		bp_core_load_template( 'buddypress/members/single/plugins' );
	}
}

/**
 * Set title for My Shop.
 */
function my_shop_tab_title() {
	echo esc_html__( 'My Shop', 'shoptype' );
}

/**
 * Display content of My Shop.
 */
function my_shop_tab_content() {
	st_locate_template("parts/myShop_edit.php", true);
}

/**
 * Add user menu for My Shop.
 *
 * @return void
 */
function buddyboss_add_my_shop_menu() {

	// Bail, if anything goes wrong.
	if ( ! function_exists( 'bp_loggedin_user_domain' ) ) {
		return;
	}

	printf(
		"<li class='logout-link'><a href='%s'>%s</a></li>",
		trailingslashit( bp_loggedin_user_domain() ) . 'my-shop/',
		esc_html__( 'My Shop', 'default' )
	);
}

add_action( 'buddyboss_theme_after_bb_profile_menu', 'buddyboss_add_my_shop_menu' );


