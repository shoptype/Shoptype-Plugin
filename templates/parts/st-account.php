<?php
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stDefaultCurrency;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;

wp_enqueue_style( 'cartCss', st_locate_file('css/st-myaccount.css') );

$st_token = $_COOKIE["stToken"];
$args = array( 
			'headers' => array( 
				'Authorization' => $st_token,
			) 
		);

$st_profile = st_ensure_user_loggedin();
if(!isset($st_profile->profilePicture) || $st_profile->profilePicture==null){
	$st_profile->profilePicture = st_locate_file('images/profile.jpg');
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
<style>
.container { max-width: 1240px; margin: auto; padding: 40px 0px;}
</style>
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
	<div class="st-redirect">
      <div class="st-redirect-txt">To withdraw earnings across all market networks, please visit:</div>
      <div class="st-redirect-btn-div">
        <a href="https://app.shoptype.com/" class="st-redirect-btn w-inline-block" target="_blank"><img src="<?php echo st_locate_file("images/Shoptype-Logo-White-1.png") ?>" loading="lazy" alt="" class="st-redirect-btn-image">
          <div class="st-redirect-btn-title">Visit Shoptype</div>
        </a>
        <div class="st-redirect-btn-txt">(Redirects to Shoptype. Opens in new tab)</div>
      </div>
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