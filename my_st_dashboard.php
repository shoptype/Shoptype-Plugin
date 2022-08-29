<?php

/**
 * Add custom sub-tab on groups page.
 */
function buddyboss_st_dashboard_tab() {
	wp_enqueue_script( 'my-script-handle', '/url-to/my-script.js', array( 'bp-api-request' ) );
	$path = dirname(plugin_dir_url( __FILE__ ));
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
		  'name'                => esc_html__( 'Dashboard', 'default' ),
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
	echo esc_html__( '', 'default' );
}

/**
 * Display content of St Dashboard.
 */
function st_dashboard_tab_content() {
	global $stPlatformId;
	global $stDefaultCurrency;
	global $stCurrency;
	global $stBackendUrl;
	$stToken = $_COOKIE['stToken'];
	$count = 10;
	$offset = $_GET['offset'];
	$offset = empty($offset)?0:$offset;
	$next = $offset+$count;
	$prev = $offset-$count<0?0:$offset-$count;
	$currencyStr = $stCurrency[$stDefaultCurrency];
	try {
		
		$args = array(
			'headers' => array(
			  'authorization' => $stToken
			  ));
		$response = wp_remote_get("{$stBackendUrl}/coseller-dashboard?viewType=cosellerProductView&currency=$stDefaultCurrency&count={$count}&offset={$offset}",$args);
		$result = wp_remote_retrieve_body( $response );
		
		
		if( !empty( $result ) ) {
			$cosellerDash = json_decode($result);
		}
		$args = array(
			'headers' => array(
			  'authorization' => $stToken
			  ));
		$response = wp_remote_get("{$stBackendUrl}/coseller-dashboard?viewType=cosellerView&currency=$stDefaultCurrency",$args);
		$result = wp_remote_retrieve_body( $response );
		
		
		if( !empty( $result ) ) {
			$cosellerKpi = json_decode($result);
		}
	}
	catch(Exception $e) {
		echo "Cart not found";
	}
	?>
	<div class="st-cosell-links">
    <div class="st-redirect">
      <div class="st-redirect-txt">To view earnings across all market networks, please visit:</div>
      <div class="st-redirect-btn-div">
        <a href="https://app.shoptype.com/" class="st-redirect-btn w-inline-block"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>/images/Shoptype-Logo-White-1.png" loading="lazy" alt="" class="st-redirect-btn-image">
          <div class="st-redirect-btn-title">Visit Shoptype</div>
        </a>
        <div class="st-redirect-btn-txt">(Redirects to Shoptype. Opens in new tab)</div>
      </div>
    </div>
    <div class="st-coseller-db">
      <div class="st-coseller-db-data">
        <div class="st-coseller-kpi-div">
          <div class="div-block-137">
            <div class="st-coseller-kpi">
              <div class="st-coseller-kpi-txt">Total Earnings</div>
              <div id="st-coseller-kpi-val-tot-earning" class="st-coseller-kpi-val"><?php echo $currencyStr.$cosellerKpi->total_commissions ?></div>
            </div>
            <div class="st-coseller-kpi">
              <div class="st-coseller-kpi-txt">Clicks</div>
              <div id="st-coseller-kpi-val-tot-click" class="st-coseller-kpi-val"><?php echo $cosellerKpi->total_clicks ?></div>
            </div>
            <div class="st-coseller-kpi">
              <div class="st-coseller-kpi-txt">Publishes</div>
              <div id="st-coseller-kpi-val-tot-publish" class="st-coseller-kpi-val"><?php echo $cosellerKpi->total_publishes ?></div>
            </div>
            <div class="st-coseller-kpi">
              <div class="st-coseller-kpi-txt">Currency</div>
              <div id="st-coseller-kpi-val-currency" class="st-coseller-kpi-val"><?php echo $stDefaultCurrency ?></div>
            </div>
          </div>
          <div class="st-coseller-kpi-products">
            <div>
              <h3 class="st-coseller-products-title">Products Published</h3>
            </div>
            <div class="st-coseller-products-list">
            	<?php foreach($cosellerDash as $x=>$product): ?>
				  <div class="st-coseller-product">
					<div class="st-coseller-product-div">
					  <div class="st-coseller-product-details">
						<div class="st-coseller-product-img-div"><img src="<?php echo $product->image_url ?>" loading="lazy" alt="" class="st-coseller-product-img"></div>
						<div class="st-coseller-product-desc">
						  <div class="st-coseller-product-name"><?php echo $product->title ?></div>
						  <div class="st-coseller-product-vendor"><?php echo $product->vendorName ?></div>
						</div>
					  </div>
					  <div class="st-coseller-product-kpi-copy">
						<div class="st-coseller-kpi-block">
						  <div class="st-coseller-kpis">
							<div class="st-coseller-kpi-txt1">Direct</div>
							<div class="st-coseller-kpi-val1"><?php echo $currencyStr.$product->total_commissions_direct_sales ?></div>
						  </div>
						  <div class="st-coseller-kpis">
							<div class="st-coseller-kpi-txt1">Influenced</div>
							<div class="st-coseller-kpi-val1"><?php echo $currencyStr.$product->total_commissions_influenced_sales ?></div>
						  </div>
						</div>
						<div class="st-coseller-kpis-blk">
						  <div class="st-coseller-kpi-txt">Total Earnings</div>
						  <div class="st-coseller-kpi-val st-product-tot-earnings"><?php echo $currencyStr.$product->total_commissions ?></div>
						</div>
					  </div>
					</div>
					<div class="st-coseller-kpis-list">
					  <div class="st-coseller-product-kpi">
						<div class="st-coseller-kpi-txt">Product Price</div>
						<div class="st-coseller-kpi-val"><?php echo $currencyStr.$product->price ?></div>
					  </div>
					  <div class="st-coseller-product-kpi">
						<div class="st-coseller-kpi-txt">Clicks</div>
						<div class="st-coseller-kpi-val"><?php echo $product->total_clicks ?></div>
					  </div>
					  <div class="st-coseller-product-kpi">
						<div class="st-coseller-kpi-txt">Publishes</div>
						<div class="st-coseller-kpi-val"><?php echo $product->total_publishes ?></div>
					  </div>
					  <div class="st-coseller-product-kpi">
						<div class="st-coseller-nudge-btn" onclick="showCosell('<?php echo $product->productId ?>')">Nudge</div>
					  </div>
					</div>
				  </div>
				<?php endforeach; ?>
              	<div style="display: flex; justify-content: space-between;">
				  <a href="?offset=<?php echo $prev ?>">Prev</a>
				  <?php if(count($cosellerDash)==10){?>
					<a href="?offset=<?php echo $next ?>">Next</a>
				  <?php } ?>
				</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
	
</script>
<?php
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

