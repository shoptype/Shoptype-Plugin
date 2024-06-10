<?php
$args = wp_parse_args(
    $args,
    array(
        'my_data' => array(
            'st_product' => null, // default value
        )
    )
);

$st_product = $args['st_product'];
?>

<style>
.product-details-tab { font-weight: 400;}
#show-more{align-items:center;justify-content:center;font-size:24px;cursor:pointer;color:#0d2b24;transform:rotate(-90deg);}
#show-more:hover{color:#fff;background:#00000050;}
.xzoom-thumbs.single-product-image-list {flex-direction: row;flex-wrap: wrap;}
.single-product-image-list {display: flex;gap: 10px;margin-top: 15px;margin-bottom: 15px;}
#show-more, .xzoom-thumbs {display: flex;}
.xzoom-thumbs a:not(:nth-child(-n+3)) {display: none;}
#scroll-container {border: 3px solid black;	border-radius: 5px;	overflow: hidden;	display: flex; background: #00000080;}
#scroll-text {display: flex; flex: 0 0 100%; font: 400 16px/22px sans-serif;
	-moz-transform: translateX(100%);
	-webkit-transform: translateX(100%);
	transform: translateX(100%);
	-moz-animation: my-animation 15s linear infinite;
	-webkit-animation: my-animation 15s linear infinite;
	animation: my-animation 15s linear infinite;
}

/* for Firefox */
@-moz-keyframes my-animation {<?php
$args = wp_parse_args(
    $args,
    array(
        'my_data' => array(
            'st_product' => null, // default value
        )
    )
);

$st_product = $my_data['st_product'];
$resultProduct = $my_data['resultProduct'];
$prodCurrency = $my_data['prodCurrency'];
$vendor = $my_data['vendor'];
wp_enqueue_script( 'jquery-core', '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
wp_enqueue_script( "daterangepicker_js", "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js");
wp_enqueue_style( "daterangepicker_css", "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css");
?>
<style>
.single-product-image-main{max-height:300px}
.single-product-image-list{display:flex;gap:10px;margin-top:15px;margin-bottom:15px}
.xactive{border:1px solid var(--primary-color)}
.single-product-image{width:calc(50% - 20px);display:flex;flex:1 1 calc(50% - 20px);aspect-ratio:1/1;flex-direction:column;margin-right:20px}
.single-product-image-container{display:flex;aspect-ratio:1/1;clear:both;width:100% !important;min-height: auto !important;}
img.xzoom{max-height:100%;width:100%;height:100% margin: auto;object-fit:contain}
.outer-container{max-width:100%!important;margin:0 auto !important}
.option-container{margin:0 10px;display:flex;flex-direction:column;flex:1 1 100%;min-width:calc(33% - 20px)}
form.single-option{display:flex}
select.product-option-select{width:100%;font-size:18px;height:32px}
.options-container .single-option .custom-select{width:100%}
.main-product-price{margin-top:0;margin-bottom:21px;font-size:32px;font-weight:500;font-family:sans-serif}
h1.main-product-title{font-size:30px;margin:0 0 20px;line-height:30px}
@media screen and (max-width:775px){
	.details-box{flex-direction:column}
	.single-product-image{width:100%;flex:1 1 100%;margin-right:0}
	.product-info{flex:1 1 100%;margin:0}
}
</style>
<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main">
		<!-- PDP Custom page -->
		<div class="page-wrapper">
			<div class="outer-container">
				<div class="am-product-display-container">
					<!-- product details section -->
					<div class="row">
						<div class="col-md-12">
							<div class="product-section">
								<div class="details-box">
									<!-- product slider -->
									<div class="xzoom-container single-product-image" >
										<div class="single-product-image-container"><img src="<?php echo $st_product->primaryImageSrc->imageSrc ?>" xoriginal="<?php echo $st_product->primaryImageSrc->imageSrc ?>" id="xzoom-default single-product-image-main" class="xzoom " alt="" /></div>
										<div class="xzoom-thumbs single-product-image-list">
										
											<?php
											echo "<a href='{$st_product->primaryImageSrc->imageSrc}'><img src='{$st_product->primaryImageSrc->imageSrc}' class='xzoom-gallery' alt='' width='80'/></a>";
											if(isset($st_product->secondaryImageSrc)){
												foreach ($st_product->secondaryImageSrc as $img) {
													$imgIcon = "https://images.shoptype.com/unsafe/100x0/".urlencode($img->imageSrc);
													echo "<a href='{$img->imageSrc}'><img src='{$imgIcon}' class='xzoom-gallery' alt='' width='80'/></a>";
												}
											}
											?>
										</div>
									</div>
									<!-- ends product slider -->
									<!-- product details -->
									<div class="product-info">
										<h1 class="main-product-title"><?php echo $st_product->title ?></h1>
										<h5 class="am-product-vendor"><a href="<?php echo str_replace("{{brandId}}", $st_product->catalogId, $brandUrl); ?>"><?php echo $st_product->vendorName ?></a></h5>
										<h4 class="main-product-price">
											<?php
												$show_full_price = 'style="display: none;"';
												if($st_product->variants[0]->discountedPriceAsMoney->amount < $st_product->variants[0]->priceAsMoney->amount){
													$show_full_price = "";
												} 
											?>
											<span class="currency-symbol price-old" <?php echo $show_full_price ?>><?php echo $prodCurrency ?></span><span <?php echo $show_full_price ?> class="price-old" id="old-productprice"><?php echo number_format($st_product->variants[0]->price, 2) ?></span>
											<span class="currency-symbol"><?php echo $prodCurrency ?></span><span id="productprice"><?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?></span>
										</h4>
										<div class="options-container">
												<form method="post" class="single-option" id="varientform">
													<?php if (count($st_product->options) > 0) {
														foreach ($st_product->options as $optionName) {
															if ($optionName->name != 'title') { ?>
																<div class="option-container">
																	<div class="product-option-text">
																		<h4> <?php echo "$optionName->name "; ?></h4>
																	</div>
																	<div class="custom-select">
																		<div class="form-group">
																			<select name="<?php echo $optionName->name ?>" id="<?php echo $optionName->name ?>" class="form-control product-option-select" onchange="variantChanged()">
																				<?php foreach ($optionName->values as $optionValue) {
																					echo '<option value="' . $optionValue . '">' . $optionValue . '</option>';
																				}
																				?>
																			</select>
																		</div>
																	</div>
																</div>
													<?php }
														}
													} ?>
												</form>
										</div>
										<div class="add-box">
											<?php
												$show_quant = "";
												if(substr( $st_product->variants[0]->sku, 0, 8 ) === "booking-"){
													$show_quant = 'style="display:none"';
													$last = date_create(date("Y-m-d"));
													date_add($last,date_interval_create_from_date_string("80 days"));
													$bookingResponse = wp_remote_get("https://app.yucatano.com/wp-json/st-bookings/v1/availablity/{$st_product->variants[0]->sku}?from=".date('Y-m-d')."&to=".date_format($last,'Y-m-d'));
													$booking_data = wp_remote_retrieve_body( $bookingResponse );
													$meta_data = json_encode(array(
															"from_date"=>date("Y-m-d"),
															"to_date"=>date("Y-m-d"),
															"number_of_days"=>"1",
														));
												?>
													
													<div class="st_date_select_top">
														<div class="st_date_select_title">
															TELL US YOUR DATES: 
														</div>
														<input class="st_date_select" type="text" name="daterange" value=" <?php echo date("m/d/Y")." - ".date("m/d/Y") ?>" isInvalidDate="checkAvailable"/>
													</div>
												<?php
												}
											?>
											<div <?php echo $show_quant ?> >Quantity : </div>
											<div <?php echo $show_quant ?> >
												<div class="btn-minus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)<1?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)-1">-</div>
												<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1" max="<?php echo $st_product->variants[0]->quantity ?>"/>
												<div class="btn-plus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)>(document.getElementById('quantity').max)?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)+1">+</div>
											</div>
										</div>
										<div class="product-button-container">
										<div class="product-button-container-main">
										<div class="addToCart-container">
											<div class="addButton-container">
												<button id="add-to-cart-btn" 
														class="btn btn-standard am-product-add-cart-btn" 
														role="button" 
														onclick="shoptype_UI.addToCart(this,false)" 
														variantid="<?php echo $st_product->variants[0]->id ?>" 
														variantName='<?php echo json_encode($st_product->variants[0]->variantNameValue) ?>' 
														productid="<?php echo $st_product->id ?>" 
														vendorid="<?php echo $st_product->catalogId ?>"
														<?php if(isset($meta_data)){echo "meta_data='$meta_data'";}?>
														quantityselect=".am-add-cart-quantity" >add to cart</button>
												<a id="goto-cart-btn" class="btn btn-standard am-product-add-cart-btn" href="/<?php echo $shoptypeUrlBase; ?>cart/main" style="display:none">
													Goto Cart
												</a>
											</div>
										</div>
										<div>
											<?php 
											if(is_user_logged_in())
											{
												$user = wp_get_current_user();
												$user = get_userdata( $user->ID  );
												$iscoseller=in_array( 'coseller', (array) $user->roles); 
											}
											else
											{
												$iscoseller=false;
											}
											if(get_option('manage_coseller')== 1 || $iscoseller || current_user_can( 'manage_options' )) 
											{?>
												<button type="button" class="btn btn-standard cosell-btn am-cosell-btn ">
													Share and earn upto <?php echo "{$prodCurrency}{$commission}" ?>
												</button>
											<?php 
											} ?>
											</div>
										
									</div>
									<!-- ends product details -->
								</div>
								<div class="product-return">
									<p>
										<div style="color:#FF0A53;margin-top:20px;">
										<?php									
											if(isset($meta_fields["Promotions"])) : ?>
												<div id="scroll-container">
													<div id="scroll-text"><?php echo $meta_fields["Promotions"] ?></div>
												</div>
											<?php endif; ?>
										</div>	
									</p>
									<!--<p>
										<a style="color:#FF0A53;margin-top:20px;" href="/cosell/"> Learn more about Share & Earn </a>
									</p> -->
								</div>	
							</div>
						</div>
					</div>
					<!-- #start -->
					<div class="row">


					</div>
					
					<div class="row-collection">
					</div>

					<div class="row-coseller" style="background:#F7F7F7">
					</div>
					<div class="row-blogs">
					</div>
					<div class="product-details-tab">
					<div class="row">
						<div class="col-md-12">
							<div class="custom-tabs">
								<ul class="tabs product-details-tabs">
									<li class="product-details-tab active" rel="description">Details</li>
									<!-- <li class="product-details-tab" rel="shipping">Shipping</li>
									<li class="product-details-tab" rel="additional_information">Additional Information</li>
									<li class="product-details-tab" rel="reviews">Reviews</li> -->
								</ul>
								<div class="tab_container product-details-content">
									<h3 class="d_active tab_drawer_heading product-details-tab" rel="description">Details</h3>
									<div id="description" class="tab_content am-product-description active">
										<div><?php 

												$description = do_shortcode(wpautop($st_product->description));
												echo '<div class="product-description">' . $description . '</div>';
											?></div>
									</div>
									<!-- #tab1 -->
									<!-- <h3 class="tab_drawer_heading product-details-tab" rel="shipping">Shipping</h3>
									<div id="shipping" class="tab_content am-product-description">
										<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
									</div> -->
									<!-- #tab2 -->
									<!-- <h3 class="tab_drawer_heading product-details-tab" rel="additional_information">Additional Information</h3>
									<div id="additional_information" class="tab_content am-product-description">
										<p>Nulla eleifend felis vitae velit tristique imperdiet. Etiam nec imperdiet elit. Pellentesque sem lorem, scelerisque sed facilisis sed, vestibulum sit amet eros.</p>
									</div> -->
									<!-- #tab3 -->
									<!-- <h3 class="tab_drawer_heading product-details-tab" rel="reviews">Reviews</h3>
									<div id="reviews" class="tab_content am-product-description">
										<p>Integer ultrices lacus sit amet lorem viverra consequat. Vivamus lacinia interdum sapien non faucibus. Maecenas bibendum, lectus at ultrices viverra, elit magna egestas magna, a adipiscing mauris justo nec eros.</p>
									</div> -->
									<!-- #tab4 -->
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="row-blogs">
						<div class="col-md-12">

						</div>
					</div>
					<div class="row-blogs">
				
						<?php echo do_shortcode('[elementor-template id="12818"]'); ?>

					</div>
					<!-- #end -->
				</div>
			</div>
		</div>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->
<script>
	jQuery(function($) {
		$(document).ready(function() {
			//-- Click on QUANTITY
			
			$(".tab_content").hide();
			$(".tab_content:first").show();

			/* if in tab mode */
			$("ul.tabs li").click(function() {

				$(".tab_content").hide();
				var activeTab = $(this).attr("rel");
				$("#" + activeTab).fadeIn();

				$("ul.tabs li").removeClass("active");
				$(this).addClass("active");

				$(".tab_drawer_heading").removeClass("d_active");
				$(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

			});
			/* if in drawer mode */
			$(".tab_drawer_heading").click(function() {

				$(".tab_content").hide();
				var d_activeTab = $(this).attr("rel");
				$("#" + d_activeTab).fadeIn();

				$(".tab_drawer_heading").removeClass("d_active");
				$(this).addClass("d_active");

				$("ul.tabs li").removeClass("active");
				$("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
			});
		});
	});
</script>
<script id='browser-js-extra'>
	const product = <?php echo $resultProduct; ?>;
	var bookings_data = <?php echo $booking_data; ?>;
	var variantsJson = product.products[0].variants;
	var productImages = {};
	
	if(!bookings_data){
		bookings_data={};
	}
	
	productImages[product.products[0].primaryImageSrc.id] = product.products[0].primaryImageSrc.imageSrc;
	if(product.products[0].secondaryImageSrc){
		for (var i = 0; i < product.products[0].secondaryImageSrc.length; i++) {
			productImages[product.products[0].secondaryImageSrc[i].id] = product.products[0].secondaryImageSrc[i].imageSrc;
		}
	}
	var json = {};
	var variantquntity;

	function variantChanged() {
		var variantSelected = false;
		var varients = document.getElementsByClassName("product-option-select");
		var addtocartbtn = document.querySelector(".am-product-add-cart-btn");
		var addtocart = document.querySelector(".addToCart-container");
		for (var i = 0; i < varients.length; i++) {
			json[varients[i].getAttribute('id')] = varients[i].value;
		}
		for (var key in variantsJson) {
			var obj1 = variantsJson[key]['variantNameValue'];
			if (isVariantSame(obj1,json)||Object.keys(json)==0) {
				variantSelected = true;
				var productprice = variantsJson[key]['discountedPrice'];
				var productCommission = variantsJson[key].discountedPriceAsMoney.amount * product.products[0].productCommission.percentage / 100;
				variantquntity=variantsJson[key]['quantity'];
				document.getElementById("quantity").max = variantquntity;
				if(variantquntity<=0)
				{
					variantSoldOut(addtocart,addtocartbtn);
				}else{
					variantAvailable(addtocart,addtocartbtn);
				}
				
				if(variantsJson[key].imageIds && productImages[variantsJson[key].imageIds[0]]){
				   document.querySelector(".single-product-image-container img").src = productImages[variantsJson[key].imageIds[0]];
				}
				
				if (variantsJson[key].hasOwnProperty('primaryImageSrc')) {
					var imagesrc = variantsJson[key]['primaryImageSrc'];
					imagesrc = imagesrc['imageSrc']

					var productimage = document.querySelector(".am-product-image");
					for (var i = 0; i < productimage.length; i++) {
						productimage.src(imagesrc);
					}
				}
				addtocartbtn.setAttribute("variantid", variantsJson[key]['id']);
				addtocartbtn.setAttribute("variantname", JSON.stringify(variantsJson[key]['variantNameValue']));
				document.getElementById("productprice").innerHTML = productprice;
				var cosellBtn = document.querySelector(".am-cosell-btn");
				if(cosellBtn){
					cosellBtn.innerHTML = cosellBtn.innerHTML.replace(/(\d*\.)?\d+/g,productCommission.toFixed(2));
				}
			}
		}
		if(!variantSelected){
			variantSoldOut(addtocart,addtocartbtn);
			addtocartbtn.setAttribute("variantid", "soldout");
		}
		updateCartStatus();
	}

	function variantSoldOut(container, button){
		container.style.opacity='60%';
		document.getElementById("quantity").max =1;
		document.getElementById("quantity").disabled = true;
		button.style.pointerEvents = "none";
		if (!document.getElementById("soldOut")) {
			jQuery("<p id='soldOut' style='color:red;font-weight:600;text-align: center;margin-bottom:20px;font-size:18px'> Sold Out</p>").insertAfter(".addToCart-container");
		}
	}
	function updateCartStatus(){
		var addToCartBtn = document.getElementById("add-to-cart-btn");
		var goToCartBtn = document.getElementById("goto-cart-btn");
		var variantId = addToCartBtn.getAttribute("variantid");
		var variantName = addToCartBtn.getAttribute("variantname");
		var incart = false;
		st_platform.getCart().then(x=>{
			x.cart_lines.forEach(y=>{
				if(y.product_id==product.products[0].id && y.product_variant_id==variantId && JSON.stringify(y.variant_name_value)==variantName){
					incart=true;
				}
			})
			if(incart){
				addToCartBtn.style.display="none";
				goToCartBtn.style.display="";
			}else{
				addToCartBtn.style.display="";
				goToCartBtn.style.display="none";
			}
		});
	}
	function variantAvailable(container, button){
		container.style.opacity='';
		document.getElementById("quantity").disabled = false;
		button.style.pointerEvents = "";
		if (document.getElementById("soldOut")){
			document.getElementById('soldOut').remove();
		} 
	}

	function isVariantSame(variant1, variant2){
		if(variant1 && variant2){
			return Object.keys(variant1).every(key=>variant2.hasOwnProperty(key)&&variant2[key]===variant1[key]);
		}
		return false;
	}
	document.addEventListener("cartQuantityChanged", (e)=>{
		updateCartStatus();
	});
	variantChanged();
	window.onload = variantChanged;
	
	function checkAvailable(date){
		console.info(date);
	}
</script>

<script id='browser-js-extra1'>
	jQuery(document).ready(function($) {
		$(function() {
		  $('input[name="daterange"]').daterangepicker({
			autoApply: true,
			opens: 'center',
			minDate: new Date(),
			showDropdowns: false,
			  
			isInvalidDate: x=>{
				var check_b = bookings_data.find(y=>{return y.booking_date == x.format('YYYY-MM-DD')});
				if (check_b && check_b.quantity>0){
					return false;
				}else{
					return true;
				}
		  	}
		  }, function(start, end, label) {
			fetch(`https://app.yucatano.com/wp-json/st-bookings/v1/availablity/${product.products[0].variants[0].sku}?from=`+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD'))
				.then(x=>x.json())
				.then(y=>{
					var addtocartbtn = document.querySelector(".am-product-add-cart-btn");
					var time_difference = end._d.getTime() - start._d.getTime();
					var days_difference = Math.round(time_difference / (1000 * 60 * 60 * 24));
					document.querySelector("#quantity").value = days_difference;
					var meta_data = {
						from_date:start.format('YYYY-MM-DD'),
						to_date:end.format('YYYY-MM-DD'),
						number_of_days:days_difference.toString(),
					};
					addtocartbtn.setAttribute("meta_data", JSON.stringify(meta_data));
				});
		  });
			jQuery(".xzoom, .xzoom-gallery").xzoom({
				tint: '#333',
				Xoffset: 15
			});
		});
	});

</script>
<style type="text/css">
.btn-plus,.product-button-container,.xzoom-thumbs,input#quantity{display:flex}
.product-description,.product-info .am-product-vendor a{font-family:Barlow;line-height:normal;text-transform:uppercase}
.row{padding-right:0!important;padding-left:0!important;max-width:1440px!important}
.xzoom-thumbs.single-product-image-list{flex-direction:column}
.xzoom-container.single-product-image{gap:25px;display:flex;flex-direction:row-reverse;display:flex;flex-direction:column;background:0 0}
.single-product-image-container{margin-top:10px!important}
.xzoom-thumbs.single-product-image-list a img{width:70px;height:70px;aspect-ratio:auto}
img.xzoom-gallery.xactive{border:5px solid #f1c042}
.product-info{padding-left:40px}
.h1.am-product-title h1.am-product-title{color:#03003f!important;font-family:Josefin Sans;font-size:40px;font-style:normal;font-weight:300;line-height:normal}
.product-info .am-product-vendor a{margin-bottom:20px;color:#ffce00;font-size:15px;font-style:normal;font-weight:600;letter-spacing:.6px;text-decoration-line:underline}
span#productprice,span.currency-symbol{color:#0e132e;font-family:Barlow;font-size:36px;font-style:normal;font-weight:300;line-height:36px}
h4.am-product-price{margin-top:25px!important}
.product-option-text h4{font-family:Colfax;font-style:normal;font-weight:400!important;font-size:17px!important;line-height:19px!important;color:#0d2b24!important}
.product-option-text{margin-bottom:10px}
.add-button-container{background:#0d2b24!important}
li.product-details-tab.active{padding-bottom:20px!important;min-height:52px!important;font-family:Prata;font-style:normal;font-weight:400;font-size:36px;line-height:49px;color:#16161d!important;margin-bottom:20px;border-bottom:none!important}
.product-description{border:none!important;color:#0e132e;font-style:normal;font-weight:300}
.product-description p{font-family:Prata;font-style:normal;font-weight:400;font-size:16px;line-height:25px!important;letter-spacing:.03em;color:#14463a!important}
select.product-option-select,select.product-option-select *{border:1px solid #fff!important;border-radius:20px!important;background:#ffce00!important;min-height:100%;line-height:210%;width:auto;color:#0e132e!important;font-family:Barlow;font-size:16px;font-style:normal;font-weight:400;line-height:normal;height:auto}
.add-box .btn-plus,.quntity p{font-family:Josefin Sans;font-style:normal}
.single-brand,.single-product,.single-product-image-container img,.xzoom-source img,.xzoom-thumbs.single-product-image-list img,img.xzoom-gallery.xactive{background:0 0}
.btn-minus,input#quantity{background:#fff;color:#0e132e;font-family:Barlow;font-style:normal;font-weight:600}
div#primary{background:0 0/cover no-repeat #f8f8ff}
.am-product-display-container{padding-bottom:40px;padding-top:40px}
.xzoom-thumbs.single-product-image-list{flex-direction:row;flex-wrap:wrap}
button#show-more{transform:rotate(-90deg);border:none;color:#fff}
.quntity p{color:#03003f;font-size:14px;font-weight:400;line-height:normal;text-transform:uppercase}
.add-box .btn-minus{border-radius:15px 0 0 15px;border:1px solid #0e132e;line-height:100%}
.add-box input#quantity{padding:0;border:1px solid #0e132e!important;margin:0;border-radius:0;height:auto;justify-content:center;text-align:center;font-size:16px;line-height:normal}
a#goto-cart-btn,button.btn.btn-standard.am-product-add-cart-btn{border-radius:20px;background:#0e132e;color:#fff;height:50px;margin-bottom:20px;font: 400 14px / 32px Gotham;}
.options-container .single-option .product-option-text h4,.product-details-content h3,button.btn.btn-standard.cosell-btn.am-cosell-btn{color:#0e132e;font-family:Barlow;font-style:normal;line-height:normal;text-transform:uppercase}
.product-details-content h3{margin-bottom:25px!important;box-shadow:none!important;border:none!important;text-align:center;font-size:32px;font-weight:300}
@media only screen and (max-width:768px){
	div#description{margin-top:-40px}
	.product-details-content h3{margin-bottom:25px!important;box-shadow:none!important;border:none!important;color:#0e132e;text-align:center;font-family:Barlow;font-size:32px;font-style:normal;font-weight:300;line-height:normal;text-transform:uppercase}
	.product-details-tab{padding-top:0;padding-bottom:0!important;box-shadow:none}
	.product-return{flex-direction:column;justify-content:center;align-items:center;margin-top:10px;margin-bottom:-50px}
	.details-box{display:flex;justify-content:space-between;align-items:flex-start;margin-top:-20px}
}
@media (min-width:768px){
	button.btn.btn-standard.cosell-btn.am-cosell-btn{max-width:300px}
	.addToCart-container{min-width:220px;float:left;margin-right:25px margin-bottom:0px!important}
	.am-product-display-container{max-width:1240px}
}
.add-box .btn-plus{cursor:pointer;color:#ffce00;border-radius:0 15px 15px 0;border:1px solid #0e132e;background:#0e132e;font-size:35px;font-weight:400;line-height:normal;justify-content:center;align-items:center;padding:0 15px}
button.btn.btn-standard.cosell-btn.am-cosell-btn{border-radius:20px;background:#ffce00;height:50px;padding-right:20px;padding-left:20px;font: 400 14px / 32px Gotham;}
div#description *{color:#0e132e!important}
@media only screen and (max-width:767px){
	.product-list .single-brand,.product-list .single-product{min-width:44%!important}
}
.single-product-image-container{border-radius:15px;border:1px solid #dadff4;background:#fff;padding-top:30px!important;padding-bottom:30px}
.xzoom-thumbs.single-product-image-list a{border-radius:15px!important;background:#fff}
h1.main-product-title{color:#03003f;font-family:Barlow;font-size:24px;font-style:normal;font-weight:300;line-height:normal}
.options-container .single-option .product-option-text h4{font-size:16px;font-weight:400}
.options-container .single-option{flex-direction:row;overflow:hidden}
.product-button-container{margin-top:40px;justify-content:center;margin-bottom:40px}
.addToCart-container{margin-bottom:0}
div#description{background:#eff0fa;margin-top:20px;border-radius:20px;box-shadow:0 0 20px 0 rgba(0,0,0,.15) inset;padding:20px}
.product-button-container-main{display: flex;gap: 35px;justify-content: center;flex-wrap: wrap;}
.detail-container h1,.detail-container h2,.detail-container h3{color:#f0f8ff;font-family:Barlow;font-size:28px!important;font-style:normal;font-weight:300;line-height:normal;margin-bottom:20px;margin-top:20px}
.detail-container h2{font-size:22px!important;}
.detail-container h3{font-size:18px!important;  text-align: left;}
.st_date_select_top input.st_date_select{
	width: 250px;
    max-width: 100%;
}
table td, table th {
	padding: 0px;
}
.st_date_select_title {
    //line-height: 40px;
    //margin-right: 20px;
}
th.next.available, th.next.available:hover,th.prev.available, th.prev.available:hover {
    background: #000;
}
.daterangepicker .calendar-table .next span,.daterangepicker .calendar-table .prev span {
    border: solid 2px #FFECA9;
	border-width: 0 2px 2px 0;
}
.daterangepicker .drp-calendar{
	max-width: 100% !important;		
}
.table-condensed th {
    font: 600 13px / 16px Gotham !important;
    text-transform: uppercase;
}
.right {
    float: none;
    display: table-cell;
    width: 100%;
}
.table-condensed th.month {
    font: 600 16px / 25px Gotham !important;
}
td.active.start-date, td.active.start-date:hover {
    border-left: solid 4px #358995;
    background: #A9F5FF !important;
    color: #000;
}
td.in-range, td.in-range:hover {
    background: #A9F5FF !important;
}
td.active.end-date, td.active.end-date:hover {
    border-right: solid 4px #358995;
	background: #A9F5FF !important;
    color: #000;
}
.daterangepicker td.disabled, .daterangepicker option.disabled {
    color: #555;
    cursor: not-allowed;
    text-decoration: line-through;
    background: #f4f4f4 !important;;
    border: solid 3px #fff;
}
.daterangepicker .calendar-table th, .daterangepicker .calendar-table td {
	background: #ffffff00;
}
.st_date_select_title {
    font: 400 16px / 40px sans-serif;
    margin-right: 20px;
}
.price-old{
	color: #aaa !important;
    text-decoration: line-through;
}

@media (min-width: 564px){
	.daterangepicker .drp-calendar.left .calendar-table {
    	padding-right: 7px;
	}
}

</style>
  from { -moz-transform: translateX(100%); }
  to { -moz-transform: translateX(-100%); }
}

/* for Chrome */
@-webkit-keyframes my-animation {
  from { -webkit-transform: translateX(100%); }
  to { -webkit-transform: translateX(-100%); }
}

@keyframes my-animation {
  from { -moz-transform: translateX(100%); -webkit-transform: translateX(100%); transform: translateX(100%);}
  to { -moz-transform: translateX(-100%); -webkit-transform: translateX(-100%); transform: translateX(-100%);
}
</style>
<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main-product">
		<!-- PDP Custom page -->
		<div class="page-wrapper">
			<div class="outer-container">
				<div class="am-product-display-container">
					<!-- product details section -->
					<div class="row">
						<div class="col-md-12">
							<div class="product-section">
								<div class="details-box">
									<!-- product slider -->
									<div class="xzoom-container single-product-image" >
										<div class="single-product-image-container"><img src="<?php echo $st_product->primaryImageSrc->imageSrc ?>" xoriginal="<?php echo $st_product->primaryImageSrc->imageSrc ?>" id="xzoom-default single-product-image-main" class="xzoom " alt="" /></div>
										<div class="xzoom-thumbs single-product-image-list">
										
											<?php
											$imgIcon = "https://images.shoptype.com/unsafe/100x0/".urlencode($st_product->primaryImageSrc->imageSrc);
											$imgListStr = '"'.$imgIcon.'"';
											echo "<a href='{$st_product->primaryImageSrc->imageSrc}'><img src='{$imgIcon}' class='xzoom-gallery' alt='' width='80' height='80'/></a>";
											if(isset($st_product->secondaryImageSrc)){
												foreach ($st_product->secondaryImageSrc as $img) {
													$imgIcon = "https://images.shoptype.com/unsafe/100x0/".urlencode($img->imageSrc);
													$imgListStr = $imgListStr.',"'.$imgIcon.'"';
													echo "<a href='{$img->imageSrc}'><img src='{$imgIcon}' class='xzoom-gallery' alt='' width='80' height='80'/></a>";
												}
											}
											?>
										</div>
									</div>
									<!-- ends product slider -->
									<!-- product details -->
									<div class="product-info">
										<h5 class="am-product-vendor"><a href="<?php echo str_replace("{{brandId}}", $st_product->catalogId, $brandUrl); ?>"><?php echo $st_product->vendorName ?></a></h5>
										<h1 class="am-product-title"><?php echo $st_product->title ?></h1>
										<script type="application/ld+json">
										{
										  "@context": "https://schema.org/",
										  "@type": "Product",
										  "name": <?php echo json_encode($st_product->title) ?>,
										  "image": [<?php echo $imgListStr ?>],
										  "description": <?php echo json_encode($st_product->description) ?>,
										  "sku": "<?php echo $st_product->id ?>",
										  "mpn": "<?php echo $st_product->sourceId ?>",
										  "brand": {
											"@type": "Brand",
											"name": "<?php echo $st_product->vendorName ?>"
										  },
										  "offers": {
											"@type": "Offer",
											"url": "<?php echo "https://".$domain.str_replace("{{productId}}",$st_product->id, $trimmed_productUrl) ?>",
											"priceCurrency": "<?php echo $st_product->variants[0]->discountedPriceAsMoney->currency ?>",
											"price": <?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?>,
											"priceValidUntil": "2024-11-20",
											"itemCondition": "https://schema.org/NewCondition",
											"availability": "https://schema.org/InStock"
										  }
										}
										</script>
										<h4 class="am-product-price"><span class="currency-symbol"><?php echo $prodCurrency ?></span><span id="productprice"><?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?></span></h4>
										<div class="options-container">
											<div class="single-option">
												<form method="post" class="single-option" id="varientform">
													<?php if (count($st_product->options) > 0) {
														foreach ($st_product->options as $optionName) {
															if ($optionName->name != 'title') { ?>
																<div>
																	<div class="product-option-text">
																		<h4> <?php echo "$optionName->name "; ?></h4>
																	</div>
																	<div class="custom-select">
																		<div class="form-group">
																			<select name="<?php echo $optionName->name ?>" id="<?php echo $optionName->name ?>" class="form-control product-option-select" onchange="varientChang()">
																				<?php foreach ($optionName->values as $optionValue) {
																					echo '<option value="' . htmlspecialchars($optionValue) . '">' . $optionValue . '</option>';
																				}
																				?>
																			</select>
																		</div>
																	</div>
																</div>
													<?php }
														}
													} ?>
												</form>
											</div>
										</div>
												<div class="addToCart-container">
													<div class="add-box">
														<span>
															Quantity
														</span>
														<div>
															<div class="btn-minus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)<1?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)-1">-</div>
															<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1" max=""/>
															<div class="btn-plus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)>(document.getElementById('quantity').max)?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)+1">+</div>
														</div>
													</div>
													<div class="addButton-container">
														<button class="btn btn-standard am-product-add-cart-btn" role="button" onclick="shoptype_UI.addToCart(this,false)" variantid="<?php echo $st_product->variants[0]->id ?>" variantName='<?php echo json_encode($st_product->variants[0]->variantNameValue) ?>' productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</button>

<?php 
                                                    if(is_user_logged_in())
                                                    {
                                                    $user = wp_get_current_user();
                                                    $user = get_userdata( $user->ID  );
                                                    $iscoseller=in_array( 'coseller', (array) $user->roles); 
                                                    }
                                                    else
                                                    {
                                                        $iscoseller=false;
                                                    }
                                                if(get_option('manage_coseller')== 1 || $iscoseller || current_user_can( 'manage_options' )) 
                                                    {?>
                                                <button type="button" class="btn btn-standard cosell-btn am-cosell-btn" onclick="shoptype_UI.showCosell(null)" >Cosell and Earn</button>

												<?php } ?>

													</div>
												</div>
												<?php if(isset($meta_fields["Promo Offers"]) && !empty($meta_fields["Promo Offers"])){ ?>
													<div id="scroll-container">
													  <div id="scroll-text"><?php echo $meta_fields["Promo Offers"] ?><div>
													</div>
												<?php } ?>
										
										
									</div>
									<!-- ends product details -->

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End PDP Custom page -->
	</main><!-- #main -->
</div><!-- #primary -->
	<div class="product-details-tab outer-container">
		<div class="row">
			<div class="col-md-12">
				<div class="custom-tabs">
					<ul class="tabs product-details-tabs">
						<li class="product-details-tab active" rel="description">Product Details</li>
						<?php if(isset($meta_fields["Description"])){ ?>
							<li class="product-details-tab" rel="additional_information">About <?php echo $vendorName ?></li>
						<?php } ?>
						<?php if(isset($meta_fields["Shipping Policy"])){ ?>
							<li class="product-details-tab" rel="shipping">Shipping Policy</li>
						<?php } ?>
						<?php if(isset($meta_fields["Return & Refund Policy"])){ ?>
							<li class="product-details-tab" rel="returns">Return &amp; Refund Policy</li>
						<?php } ?>
					</ul>
					<div class="tab_container product-details-content">
						<h3 class="d_active tab_drawer_heading product-details-tab" rel="description">Product Details</h3>
						<div id="description" class="tab_content am-product-description">
							<div><?php 
								$description = do_shortcode(wpautop($st_product->description));
								echo '<div class="product-description">' . $description . '</div>';
								?>
							</div>
						</div>
						<!-- #tab1 -->
						<?php if(isset($meta_fields["Description"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="additional_information">Additional Information</h3>
							<div id="additional_information" class="tab_content am-product-description">
								<p><? echo $meta_fields["Description"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab2 -->
						<?php if(isset($meta_fields["Shipping Policy"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="shipping">Shipping Policy</h3>
							<div id="shipping" class="tab_content am-product-description">
								<p><? echo $meta_fields["Shipping Policy"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab3 -->
						<?php if(isset($meta_fields["Return & Refund Policy"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="returns">Return &amp; Refund Policy</h3>
							<div id="returns" class="tab_content am-product-description">
								<p><? echo $meta_fields["Return & Refund Policy"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab4 -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row-blogs outer-container">
		<div class="col-md-12">
			<div class="custom-tabs">
				<div>
					<ul class="tabs product-details-tabs">
						<li class="product-details-tab active" rel="description">You may also like</li>
					</ul>
				</div>	
			</div>
		</div>
	</div>
	<div class="row-blogs outer-container">
		<div count="8" imageSize="400x0" class="products-container" inStock="true">
			<div class="product-container single-product" style="display: none;" id="st-product-select-template">
				<a href="demo/awake/pdp/?product-id={{productId}}" class="am-product-link">
					<div class="product-image">
						<div class="am-product-img-div">
							<div class="sold-out" style="display:none;">Sold Out</div>
							<div class="on-sale" style="display:none;">Sale</div>
							<img class="am-product-image" src="" loading="lazy" alt="">
						</div>
					</div>
					<div class="product-info">
						<p class="am-product-title product-title">Product Title</p>
						<p class="am-product-vendor brand-title">Brand Title</p>
						<div class="market-product-price am-product-price">$ 00.00</div>
					</div>
				</a>
			</div>
		</div>
	</div>

<!-- PDP PAGE = PLUS/MINUS -->
<script>
	jQuery(function($) {

		$(document).ready(function() {
			//-- Click on QUANTITY
			
			$(".tab_content").hide();
			$(".tab_content:first").show();

			/* if in tab mode */
			$("ul.tabs li").click(function() {

				$(".tab_content").hide();
				var activeTab = $(this).attr("rel");
				$("#" + activeTab).fadeIn();

				$("ul.tabs li").removeClass("active");
				$(this).addClass("active");

				$(".tab_drawer_heading").removeClass("d_active");
				$(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

			});
			/* if in drawer mode */
			$(".tab_drawer_heading").click(function() {

				$(".tab_content").hide();
				var d_activeTab = $(this).attr("rel");
				$("#" + d_activeTab).fadeIn();

				$(".tab_drawer_heading").removeClass("d_active");
				$(this).addClass("d_active");

				$("ul.tabs li").removeClass("active");
				$("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
			});
		});
	});
			

</script>
<script>
	const product = <?php echo $resultProduct; ?>;

	var variantsJson = product.products[0].variants;
	var productImages = {};
	productImages[product.products[0].primaryImageSrc.id] = product.products[0].primaryImageSrc.imageSrc;
	if(product.products[0].secondaryImageSrc){
		for (var i = 0; i < product.products[0].secondaryImageSrc.length; i++) {
			productImages[product.products[0].secondaryImageSrc[i].id] = product.products[0].secondaryImageSrc[i].imageSrc;
		}
	}
	
	var json = {};
	var variantquntity;

	function varientChang() {
		var variantSelected = false;
		var varients = document.getElementsByClassName("product-option-select");
		var addtocartbtn = document.querySelector(".am-product-add-cart-btn");
		var addtocart = document.querySelector(".addToCart-container");
		for (var i = 0; i < varients.length; i++) {
			json[varients[i].getAttribute('id')] = varients[i].value;
		}
		$(".onadd-box > div > input").val(1);
		for (var key in variantsJson) {
			var obj1 = variantsJson[key]['variantNameValue'];
			if (isVariantSame(obj1,json)||Object.keys(json)==0) {
				variantSelected = true;
				var productprice = variantsJson[key]['discountedPrice'];
				var productCommission = variantsJson[key].discountedPriceAsMoney.amount * product.products[0].productCommission.percentage / 100;
				variantquntity=variantsJson[key]['quantity'];
				document.getElementById("quantity").max =variantquntity;
				jQuery("#quantity").prop('max',variantquntity);
				if(variantquntity<=0)
				{
					variantSoldOut(addtocart,addtocartbtn);
				}else{
					variantAvailable(addtocart,addtocartbtn);
				}
				
				if(variantsJson[key].imageIds && productImages[variantsJson[key].imageIds[0]]){
				   document.querySelector(".single-product-image-container img").src = productImages[variantsJson[key].imageIds[0]];
				}
				
				var cosellBtn = document.querySelector(".am-cosell-btn");
				cosellBtn.innerHTML = cosellBtn.innerHTML.replace(/(\d*\.)?\d+/g,productCommission.toFixed(2));
				addtocartbtn.setAttribute("variantid", variantsJson[key]['id']);
				addtocartbtn.setAttribute("variantname", JSON.stringify(variantsJson[key]['variantNameValue']));
				document.getElementById("productprice").innerHTML = productprice;
			}
		}
		if(!variantSelected){
			variantSoldOut(addtocart,addtocartbtn);
			addtocartbtn.setAttribute("variantid", "soldout");
		}
	}

	function variantSoldOut(container, button){
		container.style.opacity='60%';
		document.getElementById("quantity").max =1;
		document.getElementById("quantity").disabled = true;
		button.style.pointerEvents = "none";
		if (!document.getElementById("soldOut")) {
			jQuery("<p id='soldOut' style='color:red;font-weight:600;text-align: center;margin-bottom:20px;font-size:18px'> Sold Out</p>").insertAfter(".addToCart-container");
		}
	}

	function variantAvailable(container, button){
		container.style.opacity='';
		document.getElementById("quantity").disabled = false;
		button.style.pointerEvents = "";
		if (document.getElementById("soldOut")){
			document.getElementById('soldOut').remove();
		} 
	}

	function isVariantSame(variant1, variant2){
		return Object.keys(variant1).every(key=>variant2.hasOwnProperty(key)&&variant2[key]===variant1[key]);
	}
	window.onload = varientChang;
</script>


<script type="text/javascript" src="<?php echo untrailingslashit( plugin_dir_url( __FILE__ ) ) ;?>/js/imageslider.js"></script>
<script>
	jQuery(".xzoom, .xzoom-gallery").xzoom({
		tint: '#333',
		Xoffset: 15
	});
</script>

<script>
// Get the container element
const container = document.querySelector('.xzoom-thumbs.single-product-image-list');

// Create a down arrow button element
const downArrowBtn = document.createElement('button');
downArrowBtn.setAttribute('id', 'show-more');
downArrowBtn.innerHTML = '&#x25BC;';

// Add the down arrow button element to the container element
container.appendChild(downArrowBtn);

const showMoreButton = document.getElementById("show-more");
const thumbLinks = document.querySelectorAll(".xzoom-thumbs a");

showMoreButton.addEventListener("click", () => {
  thumbLinks.forEach(link => link.style.display = "inline-block");
  showMoreButton.style.display = "none";
});

if (thumbLinks.length > 3) {
  const showMoreArrow =document.getElementById("show-more");  showMoreArrow.addEventListener("click", () => {
    thumbLinks.forEach(link => link.style.display = "inline-block");
    showMoreArrow.style.display = "none";
  });
}

</script>