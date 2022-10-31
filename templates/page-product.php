<?php
/*
 * Template name: Product Detail template
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

		try {
			if(isset($st_brand)){
				$groupSlug = preg_replace('~[^\pL\d]+~u', '-', $st_brand->name);
				$groupSlug = preg_replace('~[^-\w]+~', '', $groupSlug);
				$groupSlug = strtolower($groupSlug);
			}
		} catch (Exception $ex) {
		}

		add_filter('pre_get_document_title', function () use ($st_product) {
			return $st_product->title;
		});

		add_action('wp_head', function () use ($st_product) {
			$description = substr($st_product->description, 0, 160);
			echo "<meta name='description' content='$description'>";
			echo "<meta property='og:title' content='$st_product->title' />";
			echo "<meta property='og:description' content='$st_product->description' />";
			echo "<meta property='og:image' content='{$st_product->primaryImageSrc->imageSrc}' />";
		}, 1);
	}
	// Get vendor details
	if (!empty($vendorId)) {
		$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/vendors?vendorId=$vendorId");
  		$result = wp_remote_retrieve_body( $response );
 
		if (!empty($vendorChResponse)) {
			$vendorDetails = json_decode($vendorChResponse);
			$vendor = $vendorDetails[0];
			$vendorName = $vendor->name;
			$vendorUrl = str_replace("{{brandId}}", $vendorId, $brandUrl);
		}
	}
} catch (Exception $e) {
}
wp_enqueue_script( 'jquery_min', '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
get_header();
?>
<style>
.single-product-image-main
{
	max-height: 300px;

}
.single-product-image-list
{
	display: flex;
	gap:10px;
	margin-top:15px;
	margin-bottom: 15px;
}	
.xactive
{
	border:1px solid var(--primary-color);
}
.single-product-image-container
{
	min-height: 320px;
	clear: both;
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
											foreach ($st_product->secondaryImageSrc as $img) {
												echo "<a href='{$img->imageSrc}'><img src='{$img->imageSrc}' class='xzoom-gallery' alt='' width='80'/></a>";
											}
											?>
										</div>
									</div>
									<!-- ends product slider -->
									<!-- product details -->
									<div class="product-info">
										<h1 class="am-product-title"><?php echo $st_product->title ?></h1>
										<h5 class="am-product-vendor"><a href="<?php echo str_replace("{{brandId}}", $st_product->catalogId, $brandUrl); ?>"><?php echo $st_product->vendorName ?></a></h5>
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
										</div>
												<div class="addToCart-container">
													<div class="add-box">
														<div>
															<div class="btn-minus">-</div>
															<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1" />
															<div class="btn-plus">+</div>
														</div>
													</div>
													<div class="addButton-container">
														<button class="btn btn-standard am-product-add-cart-btn" role="button" onclick="shoptype_UI.addToCart(this,false)" variantid="<?php echo $st_product->variants[0]->id ?>" variantName='<?php echo json_encode($st_product->variants[0]->variantNameValue) ?>' productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</button>
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
					</div>
					<!-- ends product description section -->
				</div>

				<div>
					<h2>Related Products</h2>
				</div>

				<div count="4" imagesize="250x0" vendorId="<?php echo $st_product->catalogId ?>" removetemplate class="products-container grid-two-by-two">
					<div class="product-container single-product " style="display: none">
						<a href="" class="am-product-link">
							<div class="product-image">
								<img class="am-product-image" src="product-image.png" alt="">
								<div class="market-product-price am-product-price">$ 00.00</div>
							</div>
							<div class="product-content">
								<p class="am-product-vendor">Brand Name</p>
								<h4 class="am-product-title">Product name</h4>
							</div>
						</a>
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
			$(".btn-minus").on("click", function() {
				var now = $(".onadd-box > div > input").val();
				if ($.isNumeric(now)) {
					if (parseInt(now) - 1 > 0) {
						now--;
					}
					$(".add-box > div > input").val(now);
				} else {
					$(".add-box > div > input").val("1");
				}
			})
				jQuery(".btn-plus").on("click", function() {
				var now = jQuery(".add-box > div > input").val();
				if (jQuery.isNumeric(now)) {
					if(parseInt(now)<variantquntity){
						console.log(now); jQuery(".add-box > div > input").val(parseInt(now) + 1);
					}
				} else {
					jQuery(".add-box > div > input").val("1");
				}
			});

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
			if (isVariantSame(obj1,json)) {
				variantSelected = true;
				var productprice = variantsJson[key]['discountedPrice'];
				var productCommission = variantsJson[key].discountedPriceAsMoney.amount * product.products[0].productCommission.percentage / 100;
				variantquntity=variantsJson[key]['quantity'];
				document.getElementById("quantity").max =variantquntity;

				if(variantquntity<=0)
				{
					variantSoldOut(addtocart,addtocartbtn);
				}else{
					variantAvailable(addtocart,addtocartbtn);
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

<?php
get_footer();
