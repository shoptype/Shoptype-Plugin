<?php

/**
 * Add custom sub-tab on groups page.
 */
function buddyboss_st_dashboard_tab() {
	wp_enqueue_style( 'st-dashboard-css', plugin_dir_url( __FILE__ ) . '/css/st-dashboard.css' );
	// Avoid fatal errors when plugin is not available.
	if ( ! function_exists( 'bp_core_new_nav_item' ) ||
		 ! function_exists( 'bp_loggedin_user_domain' ) ||
		 empty( bp_displayed_user_id() ) ) {
		return;

	  }

	  global $bp;

	  bp_core_new_nav_item(
		array(
		  'name'                => esc_html__( 'Dashboard', 'shoptype' ),
		  'slug'                => 'st-dashboard',
		  'screen_function'     => 'st_dashboard_screen',
		  'position'            => 100,
		  'parent_url'          => bp_displayed_user_domain() . '/my-shop/',
		  'parent_slug'         => $bp->profile->slug,
		)
	  );
}

add_action( 'bp_setup_nav', 'buddyboss_st_dashboard_tab' );

/**
 * Set template for new tab.
 */
function st_dashboard_screen() {
	// Add title and content here - last is to call the members plugin.php template.
	add_action( 'bp_template_title', 'st_dashboard_tab_title' );
	add_action( 'bp_template_content', 'st_dashboard_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );
}

/**
 * Set title for St Dashboard.
 */
function st_dashboard_tab_title() {
	echo esc_html__( 'Dashboard', 'shoptype' );
}

/**
 * Display content of St Dashboard.
 */
function st_dashboard_tab_content() {
	//echo st_locate_template("parts/st-account.php");
	get_template_part(st_locate_template("parts/st-account.php"));
}

/**
 * Add user menu for St Dashboard.
 *
 * @return void
 */
function buddyboss_add_st_dashboard_menu() {

	// Bail, if anything goes wrong.
	if ( ! function_exists( 'bp_loggedin_user_domain' ) ) {
		return;
	}

	printf(
		"<li class='logout-link'><a href='%s'>%s</a></li>",
		trailingslashit( bp_loggedin_user_domain() ) . 'st-dashboard/',
		esc_html__( 'Dashboard', 'default' )
	);
}

add_action( 'buddyboss_theme_after_bb_profile_menu', 'buddyboss_add_st_dashboard_menu' );

