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
} 
	
get_header(null);
?>

	<div class="st-success">
	<div class="div-block-21">
<?php
	if (isset($st_checkout->payment) && $st_checkout->payment->status == "success") {
?>
	<h2 class="st-success-heading">Checkout Successful!</h2>
	<div class="st-success-txt">Thank you for shopping with us! <br>You’ll be notified about your order status and tracking by email.</div>
<?php
	}elseif(isset($st_checkout->id)){
?>
	<h2 class="st-success-heading">Checkout not complete!</h2>
	<div class="st-success-txt">You can try to complete this order by clicking <a href="<?php echo "/checkout/{$st_checkout->id}"; ?>">here</a></div>		
<?php
	}else{
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
		echo '<h2 class="st-success-heading">Checkout not found!</h2></div>';
	}
	if(isset($st_checkoutt->id)){
?>
	</div>
		<div class="st-success-details">
			<?php foreach($st_checkout->order_details_per_vendor as $vendorId=>$items): ?>
				<?php foreach($items->cart_lines as $key=>$product): ?>
					<div class="st-success-product">
						<div class="st-success-prod-img-box"><img src="<?php echo "{$product->image_src}" ?>" loading="lazy" alt="" class="st-success-prod-ing"></div>
						<div class="st-success-desc">
						  <div class="st-success-prod-details"><?php echo "{$product->name}"; if(isset($product->variant_name_value)){echo "- {$product->variant_name_value->title}";} echo "<br/>{$product->quantity} x {$prodCurrency}{$product->price->amount}"; ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
	</div>
<?php } ?>
	<script type="text/javascript">
		var ignoreEvents = true;
		if(wc_cart_fragments_params){
			sessionStorage[wc_cart_fragments_params["fragment_name"]] = null;
		}
	</script>

<?php
get_footer();