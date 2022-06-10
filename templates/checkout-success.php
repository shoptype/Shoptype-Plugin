<?php
/*
 * Template name: Shoptype Checkout Success template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'cartCss', $path.'/css/st-cart.css' );
$checkoutId = get_query_var( 'success_chkout' );

try {
	$headers = array(
		"X-Shoptype-Api-Key: ".$stApiKey,
		"X-Shoptype-PlatformId: ".$stPlatformId
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "{$stBackendUrl}/checkout/$checkoutId");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);

	curl_close($ch);

	if( !empty( $result ) ) {
		$st_checkout = json_decode($result);
		$prodCurrency = $stCurrency[$st_checkout->total->currency];
	}
}
catch(Exception $e) {
	echo "Cart not found";
}

if (isset($_COOKIE['carts'])) {
    unset($_COOKIE['carts']); 
    setcookie('carts', null, -1, '/'); 
} 
	
get_header(null);
?>

	<div class="st-success">
	<div class="div-block-21">
	<h2 class="st-success-heading">Checkout Successful!</h2>
	<div class="st-success-txt">Thank you for shopping with us! <br>You’ll be notified about your order status and tracking by email.</div>
	</div>
		<div class="st-success-details">
			<?php foreach($st_checkout->order_details_per_vendor as $vendorId=>$items): ?>
				<?php foreach($items->cart_lines as $key=>$product): ?>
					<div class="st-success-product">
						<div class="st-success-prod-img-box"><img src="<?php echo "{$product->image_src}" ?>" loading="lazy" alt="" class="st-success-prod-ing"></div>
						<div class="st-success-desc">
						  <div class="st-success-prod-details"><?php echo "{$product->name} - {$product->variant_name_value->title}<br/>{$product->quantity} x {$prodCurrency}{$product->price->amount}" ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
	</div>



<?php
get_footer();