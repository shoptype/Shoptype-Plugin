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
	$args = array(
		'headers' => array(
		  "X-Shoptype-Api-Key" =>$stApiKey,
		  "X-Shoptype-PlatformId" =>$stPlatformId,
		  "origin" => "https://".$_SERVER['HTTP_HOST']
		  ));
	  $response = wp_remote_get("{$stBackendUrl}/checkout/$checkoutId",$args);
	  $result = wp_remote_retrieve_body( $response );

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

	<div class="st-success-chkout">
	<div class="div-block-21">
<?php
	if (isset($st_checkout->payment) && $st_checkout->payment->status == "success") {
?>
	<h2 class="st-success-heading" id="chk_heading">Checkout Successful!</h2>
	<div class="st-success-txt" id="chk_txt">Thank you for shopping with us! <br>You’ll be notified about your order status and tracking by email.</div>
<?php
	}elseif(isset($st_checkout->id)){
?>
	<h2 class="st-success-heading" id="chk_heading">Processing your Checkout!</h2>
	<div class="st-success-txt" id="chk_txt">We are just checking we have everything in order this will just take a couple of seconds</div>
	<div class="st-success-txt" id="chk_failed" style="display:none">You can try to complete this order by clicking <a href="<?php echo "/checkout/{$st_checkout->id}"; ?>">here</a></div>
		<script type="text/javascript">
			var chkCount = 0;
			var chkoutId = "<?php echo $st_checkout->id; ?>";
			function updateCheckout(){
				st_platform.checkout(chkoutId).then(chkout=>{
					if(chkout.payment.status==="success"){
						document.getElementById("chk_heading").innerHTML = "Checkout Successful!";
						document.getElementById("chk_txt").innerHTML = "Thank you for shopping with us! <br>You’ll be notified about your order status and tracking by email.";
					}else{
						chkCount++;
						if(chkCount<10){
							setTimeout(function(){updateCheckout();},500);
						}else{
							document.getElementById("chk_heading").innerHTML = "Payment Failed!";
							document.getElementById("chk_txt").style.display = "none";
							document.getElementById("chk_failed").style.display = "";
						}
					}
				});
			}
			document.addEventListener("ShoptypeUILoaded", ()=>{
				updateCheckout();
			});
		</script>
	
<?php
	}else{
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
		echo '<h2 class="st-success-heading">Checkout not found!</h2></div></div>';
	}
	if(isset($st_checkout->id)){
?>
	</div>
		<div class="st-success-details">
			<?php foreach($st_checkout->order_details_per_vendor as $vendorId=>$items): ?>
				<?php foreach($items->cart_lines as $key=>$product): ?>
					<div class="st-success-product">
						<div class="st-success-prod-img-box"><img src="<?php echo "{$product->image_src}" ?>" loading="lazy" alt="" class="st-success-prod-ing"></div>
						<div class="st-success-desc">
						  <div class="st-success-prod-details"><?php 
						  		echo "{$product->name}<br/>"; 
						  		if(isset($product->variant_name_value)){
						  			foreach($product->variant_name_value as $varKey=>$varValue){
										echo "{$varKey}:{$varValue},<br/>";
									}
						  		} 
						  		echo "<br/>{$product->quantity} x {$prodCurrency}{$product->price->amount}"; ?></div>
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