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
global $stBackendUrl;

$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'cartCss', $path.'/css/st-cart.css' );
wp_enqueue_style( 'stripeCss', $path.'/css/stripe.css' );
wp_enqueue_style( 'authnetCss', $path.'/css/authnet.css' );
wp_enqueue_script('triggerUserEvent','https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js');
wp_enqueue_script('st-payment-handlers',$path."/js/shoptype-payment.js");
wp_enqueue_script('stripe',"https://js.stripe.com/v3/");
wp_enqueue_script('razorpay',"https://checkout.razorpay.com/v1/checkout.js");
$checkoutId = get_query_var( 'checkout' );

wp_enqueue_script( 'jquery_min', '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
wp_enqueue_script( 'jquery_validate', '//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js');
get_header();

if($checkoutId == "new"){
	$productId = $_GET["productid"];
	$variantId = $_GET["variantid"];
	$args = array(
		'body'        => '{}',
		'headers'     => array(
			"Content-Type"=> "application/json",
			"X-Shoptype-Api-Key" => $stApiKey,
			"X-Shoptype-PlatformId" => $stPlatformId,
			"origin" => "https://".$_SERVER['HTTP_HOST']
		)
	);
	$result = wp_remote_post( "{$stBackendUrl}/cart", $args );

	if ( is_wp_error( $result ) ) {
		$error_message = $result->get_error_message();
		echo "Something went wrong: $error_message";
	}
	else {
		$body = wp_remote_retrieve_body( $result );
		$st_cart = json_decode($body);
		$data = array(
			"product_id" => $productId,
			"product_variant_id" => $variantId,
			"quantity" => 1
		);
		$args = array(
			"body"        => json_encode($data),
			"headers"     => array(
					"Content-Type"=> "application/json",
					"X-Shoptype-Api-Key"=>$stApiKey,
					"X-Shoptype-PlatformId" =>$stPlatformId,
					"origin" => "https://".$_SERVER['HTTP_HOST']
				)
		);
			
		$result = wp_remote_post( "{$stBackendUrl}/cart/{$st_cart->id}/add", $args );
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		}
		else {
			$body = wp_remote_retrieve_body( $result );
			$new_cart = json_decode($body);
			$st_checkout = new stdClass();
			$st_checkout->order_details_per_vendor = new stdClass();
			$vendorId = $new_cart->cart_lines[0]->vendor_id;
			$st_checkout->order_details_per_vendor->$vendorId = $new_cart;
			$st_checkout->sub_total = $new_cart->sub_total;
			$st_checkout->total = $new_cart->sub_total;
			$prodCurrency = $stCurrency[$st_checkout->total->currency];
		}
	}
}else{
	try {
		$args = array(
			'headers'     => array(
				"X-Shoptype-Api-Key" => $stApiKey,
				"origin" => "https://".$_SERVER['HTTP_HOST'],
			)
		);
		$response = wp_remote_get("{$stBackendUrl}/checkout/$checkoutId/",$args);
		$result = wp_remote_retrieve_body( $response );
		$http_code = wp_remote_retrieve_response_code( $response );
		
		if( !empty( $result ) ) {
			$st_checkout = json_decode($result);
			$prodCurrency = $stCurrency[$st_checkout->total->currency];
			if(isset($st_checkout->shipping_address)){
				$parts = explode(" ", $st_checkout->shipping_address->name);
				$st_checkout->shipping_address->lastname = array_pop($parts);
				$st_checkout->shipping_address->firstname = implode(" ", $parts);
			}
			if(isset($st_checkout->billing_address)){
				$parts = explode(" ", $st_checkout->billing_address->name);
				$st_checkout->billing_address->lastname = array_pop($parts);
				$st_checkout->billing_address->firstname = implode(" ", $parts);
			}
		}else{
			echo "<h1>Cart not found</h1>";
			echo $response->get_error_message();
		}
	}
	catch(Exception $e) {
		echo "<h1>Cart not found</h1>";
	}
}
if(isset($st_checkout)){
?>
	<script>var modalCheckout=false;</script>
	<style>
		.st-chkout-billing-fullname{margin-right:-10px;}
		input.st-chkout-billing-fld-val::placeholder, select.st-chkout-billing-fld-val, label {font:14px sans-serif; opacity:0.8;}
		input.st-chkout-billing-fld-val, select.st-chkout-billing-fld-val {font: 16px/50px sans-serif;background: #EEE;height: 51px;}
		.st-chkout-billing-title { background: #000; margin: 0px -20px 20px; text-align: center; color: #FFF; font: 23px sans-serif;}
		.st-chkout-billing { border: solid 1px #eee; margin-right: 20px;}
		select.st-chkout-shipping-select {border: solid 1px #aaa;}
		.st-chkout-btn-txt, .stripe-pay-btn{color: #FFF !important;background: #000 !important;font: bold 24px sans-serif;}
		.st-chkout-sum {font: 20px/30px sans-serif;}
		form.stripe-payment-form span, form.stripe-payment-form input.field {font: 16px/40px sans-serif;color: #000;}
		.st-chkout-main {margin-bottom:80px;}
		div#payment-container-main { margin-top: 20px;}
		.st-pay-title {width: 100%;}
		.st-pay-title h2 {text-align: center;font: bold 28px sans-serif;background: #000;color: #fff;margin: 0px 0px 0px;padding: 10px 0px;}
		.st-pay-options {background: #000;padding: 10px;display: flex;justify-content: center;}
		.st-pay-options img {height: 40px;margin: 0px 5px;}
	</style>
	<div class="st-chkout-top">
		<h1 class="st-chkout-title">Checkout</h1>
		<div class="st-chkout-coupon" style="display:none;">
			<div class="st-chkout-coupon-title">Have a coupon?</div>
			<div class="st-chkout-coupon-code">Click here to enter your code</div>
		</div>
	</div>
	<div class="st-chkout-main">
		<div class="st-chkout-billing">
			<form id="shippingAddressForm">
				<div class="st-chkout-billing-title">DELIVERY DETAILS</div>
				<div class="st-chkout-billing-fld st-chkout-billing-fullname">
					<div class="st-chkout-billing-fname">
						<input type="text" name="fname" class="st-chkout-billing-fld-val" placeholder='First Name *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->firstname;} ?>" onchange="updateAddress()" required>
					</div>
					<div class="st-chkout-billing-fname">
						<input type="text" name="lname" class="st-chkout-billing-fld-val" placeholder='Last Name *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->lastname;} ?>" onchange="updateAddress()" required>
					</div>
				</div>
				<div class="st-chkout-billing-fld" style="display: none;">
					<input type="text" name="lastName" class="st-chkout-billing-fld-val">
				</div>
				<div class="st-chkout-billing-fld  st-chkout-billing-fullname">
					<div class="st-chkout-billing-fname">				
						<select name="country" class="st-chkout-billing-fld-val" id="st-chkout-country" placeholder='Country *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->country;} ?>"	required onchange="updateAddress()">
							 <option value="">Select Country</option>
						</select>
					</div>

					<div class="st-chkout-billing-fname">
						<select name="state" class="st-chkout-billing-fld-val" id="st-chkout-state" placeholder='State *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->state;} ?>"	required onchange="updateAddress()">
							 <option value="">Select state</option>
						</select>
					</div >
				</div >
				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="address" class="st-chkout-billing-fld-val" placeholder='Street Address: Line 1 *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->street1;} ?>"	required onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="address2" class="st-chkout-billing-fld-val" placeholder='Street Address: Line 2' onchange="updateAddress()">
					</div>
				</div>
				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="city" class="st-chkout-billing-fld-val" placeholder='Town / City *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->city;} ?>"	required onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="pincode" class="st-chkout-billing-fld-val" placeholder='ZIP Code/PIN Code *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->postalCode;} ?>"	required onchange="updateAddress()">
					</div>	
				</div>

				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="phone" class="st-chkout-billing-fld-val" placeholder='Phone *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->phone;} ?>"	onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="email" class="st-chkout-billing-fld-val" placeholder='Email Address *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->email;} ?>"	required onchange="updateAddress()">
					</div>
				</div>
			</form>
		<div>
			<input type="checkbox" id="billingDifferent" name="billingDifferent" value="true" onchange="billingSelectChanged()" <?php if(isset($st_checkout->billing_address)&&(!$st_checkout->is_shipping_billing)){echo "checked";} ?>>
  			<label for="vehicle1"> Billing address is different</label>
		</div>
		<form id="billingAddressForm" style="display:none;">
				<div class="st-chkout-billing-title">BILLING DETAILS</div>
				<div class="st-chkout-billing-fld st-chkout-billing-fullname">
					<div class="st-chkout-billing-fname">
						<input type="text" name="fname" class="st-chkout-billing-fld-val" placeholder='First Name *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->firstname;} ?>" onchange="updateAddress()" required>
					</div>
					<div class="st-chkout-billing-fname">
						<input type="text" name="lname" class="st-chkout-billing-fld-val" placeholder='Last Name *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->lastname;} ?>" onchange="updateAddress()" required>
					</div>
				</div>

				<div class="st-chkout-billing-fld  st-chkout-billing-fullname">
					<div class="st-chkout-billing-fname">				
						<select name="country" class="st-chkout-billing-fld-val" id="st-chkout-country" placeholder='Country *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->country;} ?>"	required onchange="updateAddress()">
							 <option value="">Select Country</option>
						</select>
					</div>

					<div class="st-chkout-billing-fname">
						<select name="state" class="st-chkout-billing-fld-val" id="st-chkout-state" placeholder='State *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->state;} ?>"	required onchange="updateAddress()">
							 <option value="">Select state</option>
						</select>
					</div >
				</div >
				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="address" class="st-chkout-billing-fld-val" placeholder='Street Address: Line 1 *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->street1;} ?>"	required onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="address2" class="st-chkout-billing-fld-val" placeholder='Street Address: Line 2' onchange="updateAddress()">
					</div>
				</div>
				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="city" class="st-chkout-billing-fld-val" placeholder='Town / City *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->city;} ?>"	required onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="pincode" class="st-chkout-billing-fld-val" placeholder='ZIP Code/PIN Code *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->postalCode;} ?>"	required onchange="updateAddress()">
					</div>	
				</div>

				<div class="st-chkout-billing-fld">
					<div class="st-chkout-billing-flds">
						<input type="text" name="phone" class="st-chkout-billing-fld-val" placeholder='Phone *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->phone;} ?>"	onchange="updateAddress()">
					</div>
					<div class="st-chkout-billing-flds">
						<input type="text" name="email" class="st-chkout-billing-fld-val" placeholder='Email Address *' value="<?php if(isset($st_checkout->shipping_address)){echo $st_checkout->shipping_address->email;} ?>"	required onchange="updateAddress()">
					</div>
				</div>
		</form>
		</div>

		<div class="st-chkout-sum">
			<div class="st-chkout-products">
				<div id="st-chkout-products-list" class="st-chkout-products-list">
					<?php foreach($st_checkout->order_details_per_vendor as $vendorId=>$items): ?>
						<div class="st-chkout-vendor-cart">
						<select name="shippingOption" orderId="<?php echo $vendorId ?>" class="st-chkout-billing-fld-val st-chkout-shipping-select" id="st-shipping-<?php echo $vendorId ?>" value=""	required onchange="onShippingChanged(this)">
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
								<div class="st-chkout-product-title"><?php echo "{$product->name}" ?>
								<?php if(isset($product->variant_name_value) && !(count((array)$product->variant_name_value)<=1 && reset($product->variant_name_value)=="Default Title")){ ?>
									<?php echo " - "; ?>
									<?php foreach($product->variant_name_value as $varKey=>$varValue){
											echo "{$varKey}:{$varValue}, ";
									} ?>
								<?php } ?>
								<span class="st-chkout-product-qty"> x <?php echo $product->quantity ?></span></div>
							</div>
							<div class="st-chkout-product-tot"><?php echo $prodCurrency.number_format((float)($product->quantity*$product->price->amount), 2, '.', '')?></div>
						</div>
						<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>



			<div class="st-chkout-details">
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">SUBTOTAL</div>
					<div class="st-chkout-cost"><?php echo $prodCurrency.number_format((float)$st_checkout->sub_total->amount, 2, '.', '') ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">SHIPPING</div>
					<div class="st-chkout-shipping-tot"><?php echo $prodCurrency.number_format((float)$st_checkout->shipping->amount, 2, '.', '') ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">TAX</div>
					<div id="st-chkout-tax-tot" class="st-chkout-shipping-tot"><?php echo $prodCurrency.number_format((float)$st_checkout->taxes->amount, 2, '.', '') ?></div>
				</div>
				<div class="st-chkout-tot-row">
					<div class="st-chkout-tot-title">TOTAL</div>
					<div class="st-chkout-tot-cost"><?php echo $prodCurrency.number_format((float)$st_checkout->total->amount, 2, '.', '') ?></div>
				</div>
			</div>
			<div id="payment-container-main" style="display:none">
				<div class="st-pay-title"><h2>Payment Info</h2></div>
				<div class="st-pay-options">
					<img src="<?php echo st_locate_file("images/Visa.png"); ?>" alt="Visa" class="st-pay-option" />
					<img src="<?php echo st_locate_file("images/Mastercard.png"); ?>" alt="Master" class="st-pay-option" />
					<img src="<?php echo st_locate_file("images/Amex.png"); ?>" alt="Amex" class="st-pay-option" />
					<img src="<?php echo st_locate_file("images/Stripe.png"); ?>" alt="Amex" class="st-pay-option" />
				</div>
				<div id="payment-container">
				</div>	
			</div>

			<div class="st-chkout-btn" onclick="showPayment()">
				<div class="st-chkout-btn-txt">Pay Now</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var ignoreEvents = true;
		const testCheckout = <?php echo json_encode( $st_checkout ); ?>;
		let st_checkoutId = "<?php echo $checkoutId ?>";
		const new_cart_id = "<?php if(isset($st_cart)){echo $st_cart->id;} ?>";
		const st_currSymb = "<?php echo $prodCurrency ?>";

		function checkoutSetCountry(){
			STUtils.countries()
				.then(countriesJson => {
					var billingForm = document.getElementById("billingAddressForm");
					var shippingForm = document.getElementById("shippingAddressForm");
					addCountriesTo(shippingForm.querySelector("#st-chkout-country"), countriesJson, shippingForm.querySelector("#st-chkout-state"));
					addCountriesTo(billingForm.querySelector("#st-chkout-country"), countriesJson, billingForm.querySelector("#st-chkout-state"));
				});
		}

		function addCountriesTo(countryField,countriesJson,stateField){
			let selectedCntry =	countryField.getAttribute("value");
			let selectedVal = null;
			let usOption = null;
			for (var i = 0; i < countriesJson.data.length; i++) {
				var option = document.createElement("option");
				option.text = countriesJson.data[i].name;
				option.value = countriesJson.data[i].iso2;
				if(countriesJson.data[i].iso2 == "US"){
					usOption = option;
				}
				if(selectedCntry==countriesJson.data[i].name){
					option.setAttribute("selected","");
				}
				countryField.add(option);
			}
			countryField.removeChild(usOption);
			countryField.insertBefore(usOption, countryField.options[1]);
			countryField.addEventListener('change', () => {
				if(countryField.value && countryField.value != ""){
					STUtils.states(countryField.value)
						.then(statesJson => {
							addStatesTo(stateField,statesJson);
						});
				}
			});
			countryField.dispatchEvent(new Event('change'));
		}

		function addStatesTo(stateField, statesJson){
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
		}

		function onShippingChanged(shippingSelect){
			shoptype_UI.stShowLoader();
			let orderKey = shippingSelect.getAttribute("orderId");
			let shippingBody = {
				"method_key_per_vendor": {
					[orderKey]: {
						method_key: shippingSelect.options[shippingSelect.selectedIndex].value
					}
				}
			};
			document.querySelector(".st-chkout-btn").style.display="";
			document.getElementById("payment-container").innerHTML="";
			st_platform.updateShipping(st_checkoutId,shippingBody).then(checkoutJson=>{
				updateStCheckout(checkoutJson);
				shoptype_UI.stHideLoader();
			});
		}

		function updateStCheckout(checkout){
			Object.keys(checkout.order_details_per_vendor).forEach(key => {
				let vendorCart = checkout.order_details_per_vendor[key];
				let cartShipSelect = document.getElementById("st-shipping-"+key);
				removeOptions(cartShipSelect);
				for (var i = 0; i < vendorCart.shipping_options.length; i++) {
					var shipOption = vendorCart.shipping_options[i];
					var option = document.createElement("option");
					option.text = shipOption.method_title;
					option.value = shipOption.method_key;
					cartShipSelect.add(option);
					if(vendorCart.shipping_selected.method_key==shipOption.method_key){
						option.setAttribute("selected","");
					}
				}
				if(!checkout.requires_shipping && vendorCart.shipping_options.length==0){
					var option = document.createElement("option");
					option.text = "Shipping Not Required";
					option.value = "No Shipping";
					option.setAttribute("selected","");
					cartShipSelect.add(option);
				}

			});
			document.querySelector(".st-chkout-cost").innerHTML = st_currSymb+checkout.sub_total.amount.toFixed(2);
			document.querySelector(".st-chkout-shipping-tot").innerHTML = st_currSymb+checkout.shipping.amount.toFixed(2);
			document.querySelector("#st-chkout-tax-tot").innerHTML = st_currSymb+checkout.taxes.amount.toFixed(2);
			document.querySelector(".st-chkout-tot-cost").innerHTML = st_currSymb+checkout.total.amount.toFixed(2);
		}

		function removeOptions(selectElement) {
			 var L = selectElement.options.length - 1;
			 for(i = L; i >= 0; i--) {
					selectElement.remove(i);
			 }
		}

		function updateAddress(){
			var addressForm = document.getElementById("shippingAddressForm");
			if(!addressForm.checkValidity()){
				return;
			}
			shoptype_UI.stShowLoader();

			let checkoutBody = {};
			checkoutBody.shipping_address = getAddressFromForm(addressForm);

			if(document.getElementById("billingDifferent").checked){
				checkoutBody.is_shipping_billing = false;
				checkoutBody.billing_address = getAddressFromForm(document.getElementById("billingAddressForm"));
			}else{
				checkoutBody.is_shipping_billing = true;
				checkoutBody.billing_address = checkoutBody.shipping_address;
			}
			

			st_platform.updateAddress(st_checkoutId,checkoutBody)
				.then(checkoutJson => {
					shoptype_UI.stHideLoader();
					if(checkoutJson.error){
						ShoptypeUI.showError(checkoutJson.message);
						var shippingSelect = document.querySelector(".st-chkout-products").getElementsByClassName("st-chkout-billing-fld-val");
						for (let i = 0; i < shippingSelect.length; i++) {
							removeOptions(shippingSelect[i]);
							var option = document.createElement("option");
							option.text = checkoutJson.message;
							option.value = "";
							shippingSelect[i].add(option);
						}
					}else{
						updateStCheckout(checkoutJson);
						shoptype_UI.stHideLoader();
					}
				});
		}

		function getAddressFromForm(addressForm){
			let countrySelect = addressForm.querySelector("#st-chkout-country");
			let stateSelect = addressForm.querySelector("#st-chkout-state");
			let shipping_address = {
				"name": addressForm.querySelector('[name="fname"]').value + " " +addressForm.querySelector('[name="lname"]').value,
				"phone": addressForm.querySelector('[name="phone"]').value,
				"email": addressForm.querySelector('[name="email"]').value,
				"street1": addressForm.querySelector('[name="address"]').value+ " " + addressForm.querySelector('[name="address2"]').value,
				"city": addressForm.querySelector('[name="city"]').value,
				"country": countrySelect.options[countrySelect.selectedIndex].text,
				"countryCode": countrySelect.value,
				"state": stateSelect.options[stateSelect.selectedIndex].text,
				"stateCode": stateSelect.value,
				"postalCode": addressForm.querySelector('[name="pincode"]').value
			};
			return shipping_address;
		}

		function billingSelectChanged(){
			if(document.getElementById("billingDifferent").checked){
				document.getElementById("billingAddressForm").style.display="";
			}else{
				document.getElementById("billingAddressForm").style.display="none";
			}
			updateAddress();
		}
		
		function showPayment(){
			var shippingSelect = document.querySelector(".st-chkout-products").getElementsByClassName("st-chkout-billing-fld-val");
			if(!$("#shippingAddressForm").valid()){
				ShoptypeUI.showError("Please enter a valid Shipping Address");
				return;
			}
			for (let i = 0; i < shippingSelect.length; i++) {
				if(shippingSelect[i].value == null ||shippingSelect[i].value == "" ){
					ShoptypeUI.showError("Shipping method not selected");
					return;
				}
			}
			try{
				initSTPayment(st_checkoutId, STUtils.backendUrl, st_platform.apiKey, onPaymentReturn);
				document.querySelector(".st-chkout-btn").style.display="none";
				document.querySelector("#payment-container").style.display="";
				document.querySelector("#payment-container-main").style.display="";
			}catch(e){
				ShoptypeUI.showError(e.message);
			}
		}

		function onPaymentReturn(payload){
			switch(payload.status){
			case "failed":
				ShoptypeUI.showError(payload.message);
				document.querySelector(".st-chkout-btn").style.display="";
				document.querySelector("#payment-container").style.display="none";
				document.querySelector("#payment-container-main").style.display="none";
				break;
			case "closed":
				document.querySelector(".st-chkout-btn").style.display="";
				document.querySelector("#payment-container").style.display="none";
				document.querySelector("#payment-container-main").style.display="none";
				break;
			case "success":
				STUtils.setCookie("carts","",0)
				window.location.href = `/${st_settings.baseUrl}checkout-success/`+st_checkoutId;
				break;
			default:
				document.querySelector(".st-chkout-btn").style.display="";
				document.querySelector("#payment-container").style.display="none";
				document.querySelector("#payment-container-main").style.display="none";
				break;
			}
		}
		
		function createCheckout(cartId){
			if(st_checkoutId == "new"){
				var currentUrl = new URL(window.location);
				let tid = currentUrl.searchParams.get("tid");
				STUser.sendUserEvent(tid, "<?php echo $stPlatformId ?>");
				st_platform.createCheckout(data=>{
					st_checkoutId = data.checkout_id;
				},cartId);	
			}
		}

		if(st_platform){
			checkoutSetCountry();
			createCheckout(new_cart_id);
		}else{
			document.addEventListener("stPlatformCreated", ()=>{
				checkoutSetCountry();
				setTimeout(()=>{createCheckout(new_cart_id);}, 10);
			});
		}
		billingSelectChanged();
		
		$(document).ready(function(){
			$("#shippingAddressForm").validate({
				rules:{ 
					fname: {required:true},
					lname: {required:true},
					country: {required:true},
					state: {required:true},
					address: {required:true},
					city: {required:true},
					pincode: {required:true},
					phone: {required:true,minlength:10,maxlength:10},
					email: {required:true,email: true}
				},
				messages: {
					email: {
						required: "Please enter email address",
						email: "Please enter valid email address"
					},
					fname: {required:"Please enter your first name"},
					lname: {required:"Please enter your last name"},
					country: {required:"Please select the country to ship to"},
					state: {required:"Please select the state"},
					address: {required:"Please enter the address to ship to"},
					city: {required:"Please enter the city to ship to"},
					pincode: {required:"Please enter the zip code of the shipping address"},
					phone: {
						required:"Please enter your phone number",
						minlength:"Enter your 10 digit phone number",
						maxlength:"Enter your 10 digit phone number"
					},
				},
				errorClass: "address_error"  
			})
		});
	</script>
<style>
input.st-chkout-billing-fld-val.address_error,select.st-chkout-billing-fld-val.address_error {border: solid 1px red;}
label.address_error {color: red;margin-top:-30px}
.st-chkout-sum:before,.st-chkout-sum:after {display: none;}
.cart-back {margin-top: 20px;margin-bottom: 20px;background: transparent;}
.st-chkout-top {margin: auto;}
.cart-text h2,.checkout-text h2 {font-family: 'Colfax';font-style: normal;font-weight: 400;font-size: 24px;line-height: 104.9%;color: #0D2B24;opacity: 0.5;}
.cart-checkout {max-width: 81%;min-width: 90%;margin-left: auto;margin-right: auto;}
.cart-back {margin-left: 8%;cursor: pointer;}
body {background: rgba(248, 248, 255, 1);}
.st-chkout-billing-title {display: flex;margin-top: -20px;margin-left: -20px;margin-right: -20px;border-radius: 30px 30px 0px 0px;background: #000000;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);justify-content: center;align-items: center;margin-bottom: 20px;}
.st-chkout-billing-title h3 {padding: 10px !important;margin: 0px;color: #ffffff;text-align: center;font-family: Josefin Sans;font-size: 22px;font-style: normal;font-weight: 300;line-height: 150%;text-transform: uppercase;}
.st-chkout-billing-fld-name {display: none;}
input.st-chkout-billing-fld-val,select.st-chkout-billing-fld-val {padding-left: 20px;height: 45px;border-radius: 30px;border: 1px solid #E9E9F4;color: #03003F;font-family: Josefin Sans;font-size: 15px;font-style: normal;font-weight: 300;line-height: 100%;}
input.st-chkout-billing-fld-val::placeholder,select.st-chkout-billing-fld-val::placeholder {color: #03003F;}
.custom-arrow {position: relative;top: -32px;left: 94%;transform: translateY(-50%);color: red;font-size: 16px;pointer-events: none;}
.st-chkout-main {gap: 40px;}
.st-chkout-billing {margin-top: 20px;border-radius: 30px;background: #FFF;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);}
.note-bottom {display: flex;justify-content: space-between;align-content: center;}
.note-bottom * {color: #03003F;font-family: Josefin Sans;font-size: 15px;font-style: normal;font-weight: 300;line-height: 100%;}
.st-chkout-main {gap: 40px;}
.st-chkout-billing {margin-top: 20px;border-radius: 30px;background: #FFF;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);}
.note-bottom {display: flex;justify-content: space-between;align-content: center;}
.note-bottom * {color: #03003F;font-family: Josefin Sans;font-size: 15px;font-style: normal;font-weight: 300;line-height: 100%;}
.st-chkout-main {margin-bottom: 50px;}
.st-chkout-sum {border-radius: 30px;background: #FFF;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.05);}
.checkout-summry h3 {padding: 20px;color: #ffffff;text-align: center;font-family: Josefin Sans;font-size: 22px;font-style: normal;font-weight: 300;line-height: 150%;text-transform: uppercase;}
.checkout-summry {margin-left: -20px;margin-right: -20px;margin-top: -20px;border-radius: 30px 30px 0px 0px;background: #000000;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);}
.st-chkout-products-title,.st-chkout-products-tot,.st-chkout-product-title,.st-chkout-product-tot,.st-chkout-tot-title,.st-chkout-cost,.st-chkout-shipping-tot,.st-chkout-tot-cost {color: #000000;font-family: Josefin Sans;font-size: 15px;font-style: normal;font-weight: 300;line-height: 150%;}
div#st-chkout-product {border-color: #FF0A53;}
div#st-chkout-products-list * {border-color: #03003F;}
.st-chkout-btn {position: absolute;bottom: 0px;right: 0px;border-radius: 0px 0px 30px 30px;background: #03003F;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);}
.st-chkout-btn-txt {padding: 20px !important;color: #ffffff;text-align: center;font-family: Josefin Sans;font-size: 22px;font-style: normal;font-weight: 300;line-height: 150%;text-transform: uppercase;border-radius: 0px 0px 30px 30px;background: #000000;box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.10);}
form#billingAddressForm {margin-top: 100px;}
.st-chkout-billing-title {color: #ffffff;text-align: center;font-family: Josefin Sans;font-size: 22px;font-style: normal;font-weight: 300;line-height: 150%;text-transform: uppercase;}
h1.st-chkout-title {margin-top: 2%;margin-bottom: 2%;}
select.st-chkout-billing-fld-val {background-image: none !important;}
@media only screen and (max-width: 768px) {
  .st-chkout-billing {padding-right: 0px;}
}
input::placeholder,select::placeholder,select *,select {color: #000 !important;}
</style>
<?php
}
get_footer();