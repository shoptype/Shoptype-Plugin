<?php
/*
 * Template name: Shoptype Checkout template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'cartCss', $path.'/css/st-cart.css' );
wp_enqueue_style( 'stripeCss', $path.'/css/stripe.css' );
wp_enqueue_style( 'authnetCss', $path.'/css/authnet.css' );
wp_enqueue_script('triggerUserEvent','https://shoptype-scripts.s3.amazonaws.com/triggerUserEvent.js');
wp_enqueue_script('st-payment-handlers',"https://shoptype-scripts.s3.amazonaws.com/payment_js/st-payment-handlers-bundle.js");
wp_enqueue_script('stripe',"https://js.stripe.com/v3/");
wp_enqueue_script('razorpay',"https://checkout.razorpay.com/v1/checkout.js");
$checkoutId = get_query_var( 'checkout' );

try {
	$headers = array(
		"X-Shoptype-Api-Key: ".$stApiKey,
		"X-Shoptype-PlatformId: ".$stPlatformId
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/checkout/$checkoutId");
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

get_header(null);
?>
	<div class="st-chkout-top">
		<h1 class="st-chkout-title">Checkout</h1>
		<div class="st-chkout-coupon" style="display:none;">
			<div class="st-chkout-coupon-title">Have a coupon?</div>
			<div class="st-chkout-coupon-code">Click here to enter your code</div>
		</div>
	</div>
	<div class="st-chkout-main">
		<div class="st-chkout-billing">
		<form id="addressForm">
			<div class="st-chkout-billing-title">BILLING DETAILS</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Name *</div>
				<input type="text" name="name" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->name ?>" onchange="updateAddress()" required>
			</div>
			<div class="st-chkout-billing-fld" style="display: none;">
				<div class="st-chkout-billing-fld-name">Last Name</div>
				<input type="text" name="lastName" class="st-chkout-billing-fld-val">
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Street Address *</div>
				<input type="text" name="address" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->street1 ?>"	required onchange="updateAddress()">
				<input type="text" name="address2" class="st-chkout-billing-fld-val" onchange="updateAddress()">
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Town / City *</div>
				<input type="text" name="city" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->city ?>"	required onchange="updateAddress()">
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">State *</div>
				<select name="state" class="st-chkout-billing-fld-val" id="st-chkout-state" value="<?php echo $st_checkout->shipping_address->state ?>"	required onchange="updateAddress()">
					 <option value="">Select state</option>
				</select>
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Country *</div>
				<select name="country" class="st-chkout-billing-fld-val" id="st-chkout-country" value="<?php echo $st_checkout->shipping_address->country ?>"	required onchange="updateAddress()">
					 <option value="">Select Country</option>
				</select>
			</div>			
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">PIN *</div>
				<input type="text" name="pincode" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->postalCode ?>"	required onchange="updateAddress()">
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Phone</div>
				<input type="text" name="phone" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->phone ?>"	onchange="updateAddress()">
			</div>
			<div class="st-chkout-billing-fld">
				<div class="st-chkout-billing-fld-name">Email Address *</div>
				<input type="text" name="email" class="st-chkout-billing-fld-val" value="<?php echo $st_checkout->shipping_address->email ?>"	required onchange="updateAddress()">
			</div>
		</form>
		</div>

		<div class="st-chkout-sum">
			<div class="st-chkout-products">
				<div id="st-chkout-products-list" class="st-chkout-products-list">
					<?php foreach($st_checkout->order_details_per_vendor as $vendorId=>$items): ?>
						<select name="shippingOption" orderId="<?php echo $vendorId ?>" class="st-chkout-billing-fld-val" id="st-shipping-<?php echo $vendorId ?>" value=""	required onchange="onShippingChanged(this)">
						<?php if(!empty($items->shipping_options)) : ?>
							<?php foreach($items->shipping_options as $key=>$shipping_options): ?>
								 <option value="<?php echo $shipping_options->method_key ?>" <?php if($shipping_options->method_key == $items->shipping_selected->method_key){echo "selected";} ?> ><?php echo $shipping_options->method_title ?></option>
							<?php endforeach; ?>
						<?php else: ?>
								 <option value="" >Enter address to get shipping options</option>
						<?php endif; ?>
						</select>
						<div class="st-chkout-products-head">
							<div class="st-chkout-products-title">PRODUCT</div>
							<div class="st-chkout-products-tot">SUBTOTAL</div>
						</div>
						<?php foreach($items->cart_lines as $key=>$product): ?>
						<div id="st-chkout-product" class="st-chkout-product">
							<div class="div-block-18">
								<div class="st-chkout-product-title"><?php echo "{$product->name} - {$product->variant_name_value->title}" ?> <span class="st-chkout-product-qty">x <?php echo $product->quantity ?></span></div>
							</div>
							<div class="st-chkout-product-tot"><?php echo $prodCurrency.($product->quantity*$product->price->amount) ?></div>
						</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</div>


			<div class="st-chkout-details">
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">SUBTOTAL</div>
					<div class="st-chkout-cost"><?php echo $prodCurrency.$st_checkout->sub_total->amount ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">SHIPPING</div>
					<div class="st-chkout-shipping-tot"><?php echo $prodCurrency.$st_checkout->shipping->amount ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">TAX</div>
					<div class="st-chkout-shipping-tot"><?php echo $prodCurrency.$st_checkout->taxes->amount ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">TOTAL</div>
					<div class="st-chkout-tot-cost"><?php echo $prodCurrency.$st_checkout->total->amount ?></div>
				</div>
			</div>
			<div id="payment-container"></div>
			<div class="st-chkout-btn" onclick="showPayment()">
				<div class="st-chkout-btn-txt">PLACE ORDER</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		const st_checkoutId = "<?php echo $st_checkout->id ?>";
		const st_currSymb = "<?php echo $prodCurrency ?>";
		function checkoutSetCountry(){
			fetch(st_backend + "/countries")
			.then(response => response.json())
			.then(countriesJson => {
				let countryField = document.getElementById("st-chkout-country");
				let selectedCntry =	countryField.getAttribute("value");
				let selectedVal = null;
				for (var i = 0; i < countriesJson.data.length; i++) {
					var option = document.createElement("option");
					option.text = countriesJson.data[i].name;
					option.value = countriesJson.data[i].iso2;
					if(selectedCntry==countriesJson.data[i].name){
						option.setAttribute("selected","");
					}
					countryField.add(option);
				}
				countryField.addEventListener('change', () => {
					if(countryField.value && countryField.value != ""){
						fetch(st_backend + "/states/" + countryField.value)
						.then(response => response.json())
						.then(statesJson => {
							let stateField = document.getElementById("st-chkout-state");
							let selectedState =	stateField.getAttribute("value");
							for (var i = stateField.options.length-1; i > 0; i--) {
								stateField.options[i] = null;
							}
							for (var i = 0; i < statesJson.data.length; i++) {
								var option = document.createElement("option");
								option.text = statesJson.data[i].name;
								option.value = statesJson.data[i].state_code;
								if(selectedState==statesJson.data[i].name){
									option.setAttribute("selected","");
								}
								stateField.add(option);
							}
						});
					}
				});
        		countryField.dispatchEvent(new Event('change'));

			});
		}

		function onShippingChanged(shippingSelect){
			stShowLoader();
			let orderKey = shippingSelect.getAttribute("orderId");
			let shippingBody = `{
				"method_key_per_vendor": {
				"${orderKey}": {
						"method_key": "${shippingSelect.options[shippingSelect.selectedIndex].value}"
					}
				}
			}`;
			headerOptions.method = "put";
			headerOptions.body = shippingBody;
			document.querySelector(".st-chkout-btn").style.display="";
			document.getElementById("payment-container").innerHTML="";
			fetch(st_backend + `/checkout/${st_checkoutId}/shipping-method`, headerOptions)
				.then(response => response.json())
				.then(checkoutJson => {
					updateStCheckout(checkoutJson);
					stHideLoader();
				});
		}

		function updateStCheckout(checkout){
			Object.keys(checkout.order_details_per_vendor).forEach(key => {
				let vendorCart = checkout.order_details_per_vendor[key];
				let cartShipSelect = document.getElementById("st-shipping-"+key);
				removeOptions(cartShipSelect);
				for (var i = 0; i < vendorCart.shipping_options.length; i++) {
					var option = document.createElement("option");
					var shipOption = vendorCart.shipping_options[i];
					option.text = shipOption.method_title;
					option.value = shipOption.method_key;
					cartShipSelect.add(option);
					if(vendorCart.shipping_selected.method_key==shipOption.method_key){
						option.setAttribute("selected","");
					}
				}
			});
			document.querySelector(".st-chkout-cost").innerHTML = st_currSymb+checkout.sub_total.amount;
			document.querySelector(".st-chkout-shipping-tot").innerHTML = st_currSymb+checkout.shipping.amount;
			document.querySelector(".st-chkout-tot-cost").innerHTML = st_currSymb+checkout.total.amount;
		}

		function removeOptions(selectElement) {
			 var L = selectElement.options.length - 1;
			 for(i = L; i >= 0; i--) {
					selectElement.remove(i);
			 }
		}

		function updateAddress(){
			var addressForm = document.getElementById("addressForm");
			if(!addressForm.checkValidity()){
				return;
			}
			headerOptions.method = "put";
			let countrySelect = document.getElementById("st-chkout-country");
			let stateSelect = document.getElementById("st-chkout-state");
			let checkoutBody = {
				"shipping_address": {
					"name": addressForm.querySelector('[name="name"]').value,
					"phone": addressForm.querySelector('[name="phone"]').value,
					"email": addressForm.querySelector('[name="email"]').value,
					"street1": addressForm.querySelector('[name="address"]').value+ " " + addressForm.querySelector('[name="address2"]').value,
					"city": addressForm.querySelector('[name="city"]').value,
					"country": countrySelect.options[countrySelect.selectedIndex].text,
					"countryCode": countrySelect.value,
					"state": stateSelect.options[stateSelect.selectedIndex].text,
					"stateCode": stateSelect.value,
					"postalCode": addressForm.querySelector('[name="pincode"]').value
				},
				"is_shipping_billing": true
			};
			checkoutBody.billing_address = checkoutBody.shipping_address
			headerOptions.body = JSON.stringify(checkoutBody);
			fetch(st_backend + `/checkout/${st_checkoutId}/address`, headerOptions)
				.then(response => response.json())
				.then(checkoutJson => {
					stHideLoader();
					if(checkoutJson.error){
						st_showError(checkoutJson.error);
					}else{
						updateStCheckout(checkoutJson);
						stHideLoader();
					}
				});
		}
		
		function showPayment(){
			initSTPayment(st_checkoutId, st_backend, headerOptions.headers["X-Shoptype-Api-Key"], onPaymentReturn);
			document.querySelector(".st-chkout-btn").style.display="none";
			document.querySelector("#payment-container").style.display="";
		}

		function onPaymentReturn(payload){
			switch(payload.status){
			case "failed":
				alert(payload.message);
				document.querySelector(".st-chkout-btn").style.display="";
				document.querySelector("#payment-container").style.display="none";
				break;
			case "closed":
				document.querySelector(".st-chkout-btn").style.display="";
				document.querySelector("#payment-container").style.display="none";
				break;
			case "success":
					window.location.href = "/chechout-success/"+st_checkoutId;
				break;
			}
		}

		checkoutSetCountry();
	</script>

<?php
get_footer();