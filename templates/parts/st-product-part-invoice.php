<?php
$st_product = $data['st_product'];
$resultProduct = $data['resultProduct'];
$prodCurrency = $data['prodCurrency'];
$vendor = $data['vendor'];
wp_enqueue_script( 'qrcode_js', ST__PLUGIN_URL . 'js/qrcode.min.js');
?>
<style>
	.st-invoice{
		display:flex;
		margin: 80px 10px;
		justify-content: center;
	}
	.st-invoice-description{
		display:flex;
	}
	.st-invoice-description{
		display:flex;
		margin: 10px 10px 20px;
		justify-content: space-between;
	}
	.st-invoice-description-txt {
		width: 140px;
		display: flex;
		align-items: center;
	}
	input.st-invoice-input {
		height: 30px;
		max-width: 100%;
	}
	input.st-invoice-amount {
		height: 30px;
		max-width: 100%;
		padding: 2px;
		text-align: right;
	}
	.st-invoice-btn button{
		float:right;
		margin-right:10px;
	}
	.st-invoice-img-div {
		width: 80px;
	}
	.st-invoice-details {
		max-width: calc(100% - 80px);
		width: 600px;
	}
	div#qrcode {
		position: absolute;
		display: flex;
		top: 100px;
		right: 0px;
		bottom: 0px;
		left: 0px;
		z-index: 9;
		padding: 30px;
		background: #ffffff90;
		align-items: center;
		justify-content: center;
		flex-direction: column-reverse;
	}
	a#checkout-link {
		border: solid 1px;
		padding: 5px 10px;
		margin: 20px 10px;
		background:#fff;
	}
	@media only screen and (max-width: 600px) {
		.st-invoice{
			flex-direction: column;
		}
	}
</style>
<div class="st-invoice">
	<div class="st-invoice-img-div">
		<img class="st-invoice-img" style="height:80px;" src="<?php echo $st_product->primaryImageSrc->imageSrc ?>">
	</div>
	<div class="st-invoice-details">
		<div class="st-invoice-description">
			<div class="st-invoice-description-txt">Description: </div>
			<input class="st-invoice-input" name="description" type="text" >
		</div>
		<div class="st-invoice-description" style="display:flex">
			<div class="st-invoice-description-txt">Amount: </div>
			<input class="st-invoice-amount" name="amount" type="number" value="0" min="0">
		</div>	
		<div class="st-invoice-btn">
			<button onclick="generateQRCode()">
				Generate Invoice
			</button>
		</div>
	</div>
	<div id="qrcode" style="display:none">
		<a id="checkout-link" href="">Goto Checkout</a>
	</div>
</div>
<script>
	function validate(){
		if(document.querySelector(".st-invoice-input").value.trim()==""){
			ShoptypeUI.showError("Description cannot be empty");
			return false;
		}
		if(document.querySelector(".st-invoice-amount").value<=0){
			ShoptypeUI.showError("Amount must be greater than 0");
			return false;
		}
		  
		return true;
	}
	function geterateInvoice(){
		var quant = document.querySelector(".st-invoice-amount").value * (1/ <?php echo $st_product->variants[0]->discountedPriceAsMoney->amount ?>);
		var metadata = JSON.stringify({"Description": document.querySelector(".st-invoice-input").value});
		window.location.href = `/checkout/new/?productid=<?php echo $st_product->id ?>&variantid=<?php echo $st_product->variants[0]->id ?>&quantity=${quant}&metadata=${metadata}`;
	
	}
	function generateQRCode(){
		shoptype_UI.stShowLoader();
		var quant = document.querySelector(".st-invoice-amount").value * (1/ <?php echo $st_product->variants[0]->discountedPriceAsMoney->amount ?>);
		var metadata = {"Description": document.querySelector(".st-invoice-input").value};
		var cart = st_platform.createCart().then(cart=>{
			st_platform.addToCart( "<?php echo $st_product->id ?>", "<?php echo $st_product->variants[0]->id ?>", <?php echo json_encode($st_product->variants[0]->variantNameValue) ?>, quant, metadata, cart.id)
				.then(cartJson=>{
				if(cartJson.cart_lines){
					createCheckout(cartJson.id);
					//ShoptypeUI.showSuccess( ShoptypeUI.pluralize(quantity, "item") + " added to cart")
				}else{
					ShoptypeUI.showError(cartJson.message);
				}
			});
		});
	}
	
	function createCheckout(cartId){
		st_platform.createCheckout(checkout=>{
			shoptype_UI.stHideLoader();
			var domain = window.location.protocol+"//"+window.location.hostname;
			var checkoutUrl = domain+`/checkout/${checkout.checkout_id}`;
			document.querySelector("#qrcode").style.display="";
			document.querySelector("#checkout-link").href=checkoutUrl;
			var qrcode = new QRCode("qrcode", {
				text: checkoutUrl,
				width: 300,
				height: 300,
				colorDark : "#000000",
				colorLight : "#ffffff",
				correctLevel : QRCode.CorrectLevel.H
			});
			//window.location.href = `/checkout/${checkout.checkout_id}`;
			
		},cartId);
	}
</script>