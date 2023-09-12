<?php
/*
 * Template name: Shoptype Product Detail template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
try {
	
	$vendorId = 0;
	$vendorName = $vendorDescription = $vendorUrl = "";
	$productId = get_query_var('stproduct');
	$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?productIds=$productId");
	$resultProduct     = wp_remote_retrieve_body( $response );
	$pluginUrl = plugin_dir_url(__FILE__);
	
	if (!empty($resultProduct)) {
		$st_products = json_decode($resultProduct);
		$st_product = $st_products->products[0];
		$commission = $st_product->variants[0]->discountedPriceAsMoney->amount * $st_product->productCommission->percentage / 100;
		$prodCurrency = $stCurrency[$st_product->currency];
		$vendorId = $st_product->catalogId;

		add_filter('pre_get_document_title', function () use ($st_product) {
			return $st_product->title;
		});

		add_filter( 'pre_get_document_title', function( $title ) use ($st_product){
			$description = addslashes(substr(strip_tags($st_product->description), 0, 250));
			echo "<title>$st_product->title</title>";
			echo "<meta name='description' content=\"".substr($description, 0, 160)."\">";
			echo "<meta property='og:title' content='$st_product->title' >";
			echo "<meta property='og:description' content=\"".$description."\" >";
			echo "<meta property='og:image' content='{$st_product->primaryImageSrc->imageSrc}' >";
			echo '<meta property="og:type" content="product">';
			echo "<meta property='twitter:title' content='$st_product->title' >";
			echo "<meta property='twitter:card' content=\"".$description."\" >";
			echo "<meta property='twitter:image' content='{$st_product->primaryImageSrc->imageSrc}' >";
			return $st_product->title;
		}, 20, 1 );
	}
} catch (Exception $e) {
}
wp_enqueue_script( 'jquery_min', '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
get_header();
?>
<style>
.single-product-image-main{max-height:300px}
.single-product-image-list{display:flex;gap:10px;margin-top:15px;margin-bottom:15px}
.xactive{border:1px solid var(--primary-color)}
.single-product-image{width:calc(50% - 20px);display:flex;flex:1 1 calc(50% - 20px);aspect-ratio:1/1;flex-direction:column;margin-right:20px}
.single-product-image-container{display:flex;aspect-ratio:1/1;clear:both;width:100%}
img.xzoom{max-height:100%;width:100%;height:100% margin: auto;object-fit:contain}
.outer-container{max-width:100%!important;margin:0 20px!important}
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
													echo "<a href='{$img->imageSrc}'><img src='{$img->imageSrc}' class='xzoom-gallery' alt='' width='80'/></a>";
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
										<h4 class="main-product-price"><span class="currency-symbol"><?php echo $prodCurrency ?></span><span id="productprice"><?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?></span></h4>
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
											<div>Quantity : </div>
											<div>
												<div class="btn-minus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)<1?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)-1">-</div>
												<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1" max=""/>
												<div class="btn-plus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)>(document.getElementById('quantity').max)?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)+1">+</div>
											</div>
										</div>
										<div class="addToCart-container">
											<div class="addButton-container">
												<button id="add-to-cart-btn" class="btn btn-standard am-product-add-cart-btn" role="button" onclick="shoptype_UI.addToCart(this,false)" variantid="<?php echo $st_product->variants[0]->id ?>" variantName='<?php echo json_encode($st_product->variants[0]->variantNameValue) ?>' productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</button>
												<a id="goto-cart-btn" class="btn btn-standard am-product-add-cart-btn" href="/cart/main" style="display:none">
													Goto Cart
												</a>
											</div>
										</div>

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
										<button type="button" class="btn btn-standard cosell-btn am-cosell-btn" onclick="shoptype_UI.showCosell('<?php echo $st_product->id ?>')">Cosell and earn upto <?php echo "$prodCurrency" . number_format($commission, 2) ?></button>

												<?php } ?>
												<!-- <div class="product-spec">
											<h4>specs</h4>
											<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae commodi dolorem voluptate quisquam, quasi illo iste mollitia ex maiores facilis reprehenderit ipsa quod veritatis. Animi, eaque ipsa! Nihil, mollitia nisi?</p>
										</div> -->
										<div class="custom-tabs">
											<ul class="tabs product-details-tabs">
												<li class="product-details-tab active" rel="description">Description</li>
												<!-- <li class="product-details-tab" rel="shipping">Shipping</li>
												<li class="product-details-tab" rel="additional_information">Additional Information</li>
												<li class="product-details-tab" rel="reviews">Reviews</li> -->
											</ul>
											<div class="tab_container product-details-content">
												<h3 class="d_active tab_drawer_heading product-details-tab" rel="description">Description</h3>
												<div id="description" class="tab_content am-product-description">
													<p><?php echo $st_product->description ?></p>
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
									<!-- ends product details -->
								</div>
							</div>
						</div>
					</div>
					<!-- ends product details section -->
					<!-- product description section -->
					<div class="row">
						<div class="col-md-12">

						</div>
					</div>
					<!-- ends product description section -->
				</div>
				<div>
					<div>
						<h3 style="margin: 30px 0px 10px;font-family:sans-serif">Related Products</h3>
					</div>
					<div count="8" imageSize="600x0" class="products-container" vendorId="<?php echo $st_product->catalogId ?>"  sortBy="createdAt" orderBy="desc" id="t-product-search-results" >
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
					<div class="st-market-link">
						<a href="<?php global $marketUrl; echo $marketUrl; ?>"  class="st-market-link">View All Products</a>
					</div>
				</div>
			</div>
		</div>
		<!-- End PDP Custom page -->
	</main><!-- #main -->
</div><!-- #primary -->

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
	window.onload = variantChanged;
</script>

<script>
	jQuery(".xzoom, .xzoom-gallery").xzoom({
		tint: '#333',
		Xoffset: 15
	});
</script>

<?php
get_footer();
