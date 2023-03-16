<?php
/*
 * Template name: Shoptype My Account
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stDefaultCurrency;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;

$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'cartCss', $path.'/css/st-myaccount.css' );

get_header(null);

$st_token = $_COOKIE["stToken"];

$args = array(
	'body'				=> '{}',
	'headers'		 => array(
		"Content-Type"=> "application/json",
		"Authorization"=> $st_token,
		"X-Shoptype-PlatformId" => $stPlatformId,
	)
);
$result = wp_remote_get( "{$stBackendUrl}/me", $args );

if ( ! is_wp_error( $result ) ) {
	$body = wp_remote_retrieve_body( $result );
	$st_profile = json_decode($body);
	if(!isset($st_profile->profilePicture) || $st_profile->profilePicture==null){
		$st_profile->profilePicture = $path.'/images/profile.jpg';
	}
}

$result = wp_remote_get( "{$stBackendUrl}/cosellers/{$st_profile->_id}/purchase-orders", $args );

if ( ! is_wp_error( $result ) ) {
	$body = wp_remote_retrieve_body( $result );
	$st_orders = json_decode($body);

}

$result = wp_remote_get( "{$stBackendUrl}/coseller-dashboard?viewType=cosellerView&currency={$stDefaultCurrency}", $args );

if ( ! is_wp_error( $result ) ) {
	$body = wp_remote_retrieve_body( $result );
	$st_kpis = json_decode($body);
}
?>
<div class="container">
	<div class="st-account-details">
		<div class="st-coseller">
			<div><img src="<?php echo $st_profile->profilePicture ?>" loading="lazy" id="st-user-img" alt="" class="st-order-profile-img"></div>
			<div class="st-coseller-info-div">
				<div class="st-order-title-div">
					<div class="st-order-title">Name:</div>
					<div id="st-user-name" class="st-order-title-txt"><?php echo  $st_profile->name ?></div>
				</div>
				<div class="st-order-title-div">
					<div class="st-order-title">Email:</div>
					<div id="st-user-email" class="st-order-title-txt"><?php echo $st_profile->email ?></div>
				</div>
			</div>
		</div>
		<div class="st-coseller-info">
			<div class="st-coseller-kpi">
				<div class="st-coseller-kpi-name">Total Earnings</div>
				<div id="st-coseller-earning" class="st-coseller-kpi-data"><?php echo "{$stCurrency[$stDefaultCurrency]} ".number_format($st_kpis->total_commissions,2) ?></div>
			</div>
			<div class="st-coseller-kpi">
				<div class="st-coseller-kpi-name">Clicks</div>
				<div id="st-coseller-clicks" class="st-coseller-kpi-data"><?php echo $st_kpis->total_clicks ?></div>
			</div>
			<div class="st-coseller-kpi">
				<div class="st-coseller-kpi-name">Publishes</div>
				<div id="st-coseller-publish" class="st-coseller-kpi-data"><?php echo $st_kpis->total_publishes ?></div>
			</div>
			<div class="st-coseller-kpi">
				<div class="st-coseller-kpi-name">Currency</div>
				<div id="st-coseller-curr" class="st-coseller-kpi-data"><?php echo $st_kpis->currency ?></div>
			</div>
		</div>
	</div>
	<div class="st-orders">
		<?php foreach($st_orders->data as $key=>$order): ?>
		<div class="st-order">
			<div class="st-order-header">
				<div class="st-order-col">
					<div>Order Placed</div>
					<div class="st-order-date"><?php $date=date_create($order->created_at); echo date_format($date,"d-M-Y") ?></div>
				</div>
				<div class="st-order-col">
					<div>Order Total</div>
					<div class="st-order-total"><?php echo "{$stCurrency[$order->total->currency]} ". number_format($order->total->amount,2) ?></div>
				</div>
				<div class="st-order-col-full">
					<div>Shipping Details</div>
					<div class="st-order-address"><?php echo $order->shoptypeOrderId ?></div>
				</div>
				<div class="st-order-col">
					<div>Order Number</div>
					<div class="st-order-no"><?php echo $order->external_order_id ?></div>
				</div>
			</div>
			<div class="st-order-product">
				<div class="st-order-product-info">
					<?php foreach($order->cart_lines as $key2=>$product): ?>
					<div class="div-block-7">
						<div class="st-order-vendor"><?php echo $product->vendor_name ?></div>
						<div class="st-order-product-div">
							<div><img src="<?php echo $product->image_src ?>" loading="lazy" alt="" class="st-order-product-img"></div>
							<div class="st-order-product-details">
								<div class="st-order-product-name"><?php echo $product->name ?></div>
								<div class="st-order-product-cost"><?php echo "{$stCurrency[$product->price->currency]} ". number_format($product->price->amount,2) ?></div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
					<div class="st-order-details">
						<div class="st-order-status"><?php $date=date_create($order->history[0]->updated_at); echo "{$order->history[0]->status_to} on ".date_format($date,"d-M-Y") ?></div>
						<div class="st-order-cosell">Cosell</div>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<button onclick="stLogout()">
		Logout
	</button>
</div>
<script>
	function stLogout(){
		STUtils.setCookie("stToken","",-1);
		window.location.href = "<?php echo wp_logout_url("/");?>";
	}
	
</script>
<?php
get_footer();