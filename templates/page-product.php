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
try {
	$vendorId = 0;
	$vendorName = $vendorDescription = $vendorUrl = "";
	$productId = get_query_var('product');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/platforms/$stPlatformId/products?productIds=$productId");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	$pluginUrl = plugin_dir_url(__FILE__);
	curl_close($ch);
	if (!empty($result)) {
		$st_products = json_decode($result);
		$st_product = $st_products->products[0];
		$commission = $st_product->variants[0]->discountedPriceAsMoney->amount * $st_product->productCommission->percentage / 100;
		$prodCurrency = $stCurrency[$st_product->currency];
		$vendorId = $st_product->catalogId;

		try {
			$groupSlug = preg_replace('~[^\pL\d]+~u', '-', $st_brand->name);
			$groupSlug = preg_replace('~[^-\w]+~', '', $groupSlug);
			$groupSlug = strtolower($groupSlug);
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
		$vendorch = curl_init();
		curl_setopt($vendorch, CURLOPT_URL, "https://backend.shoptype.com/platforms/$stPlatformId/vendors?vendorId=$vendorId");
		curl_setopt($vendorch, CURLOPT_RETURNTRANSFER, true);
		$vendorChResponse = curl_exec($vendorch);
		curl_close($vendorch);
		if (!empty($vendorChResponse)) {
			$vendorDetails = json_decode($vendorChResponse);
			$vendor = $vendorDetails[0];
			$vendorName = $vendor->name;
			$vendorUrl = str_replace("{{brandId}}", $vendorId, $brandUrl);
		}
	}
} catch (Exception $e) {
}
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
																<div class="product-option-text">
																	<?php
																	echo '<h4>' . $optionName->name . '</h4>';

																	?>
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
														<a class="btn btn-standard am-product-add-cart-btn" href="javascript:void()" role="button" onclick="addToCart(this,false)" variantid="<?php echo $st_product->variants[0]->id ?>" productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</a>
													</div>
												</div>
												<button type="button" class="btn btn-standard cosell-btn am-cosell-btn" onclick="showCosell('<?php echo $st_product->id ?>')">Cosell and earn upto <?php echo "$prodCurrency" . number_format($commission, 2) ?></button>
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
	sendUserEvent();
	jQuery(function($) {

		$(document).ready(function() {
			//-- Click on QUANTITY
			$(".btn-minus").on("click", function() {
				var now = $(".add-box > div > input").val();
				if ($.isNumeric(now)) {
					if (parseInt(now) - 1 > 0) {
						now--;
					}
					$(".add-box > div > input").val(now);
				} else {
					$(".add-box > div > input").val("1");
				}
			})
			$(".btn-plus").on("click", function() {
				var now = $(".add-box > div > input").val();
				if ($.isNumeric(now)) {
					$(".add-box > div > input").val(parseInt(now) + 1);
				} else {
					$(".add-box > div > input").val("1");
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
	const product = <?php echo $result; ?>;

	var productjson = product.products;
	productjson = productjson[0];
	productjson = productjson['variants'];

	var json = {};

	function varientChang() {
		var varients = document.getElementsByClassName("product-option-select");
		for (var i = 0; i < varients.length; i++) {
					json[varients[i].getAttribute('id')] = varients[i].value;
		}
		for (var key in productjson) {
			var obj1 = productjson[key]['variantNameValue'];
			if (JSON.stringify(obj1) === JSON.stringify(json)) {
				var varientid = productjson[key]['id'];
				var productprice = productjson[key]['discountedPrice'];
				var varientquntity=productjson[key]['quantity'];
				console.log(varientquntity);

				if (productjson[key].hasOwnProperty('primaryImageSrc')) {
					var imagesrc = productjson[key]['primaryImageSrc'];
					imagesrc = imagesrc['imageSrc']

					var productimage = document.getElementsByClassName("am-product-image");
					for (var i = 0; i < productimage.length; i++) {
						productimage[i].src(imagesrc);
					}
				}
				var addtocart = document.getElementsByClassName("am-product-add-cart-btn");
				for (var i = 0; i < addtocart.length; i++) {
					addtocart[i].setAttribute("variantid", varientid);
				}
				document.getElementById("productprice").innerHTML = productprice;

			}
		}


	}
	varientChang();
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
