<?php
/*
 * Template name: Product Detail template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */

function add_custom_meta_des(){
	echo '<meta name="description" content="test" />';
	echo '<meta name="description" content="test" />';
}


global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
try {
	$productId = $_GET['id'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/platforms/$stPlatformId/products?productIds=$productId");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	if( !empty( $result ) ) {
		$st_products = json_decode($result);
		$st_product = $st_products->products[0];
		$commission = $st_product->variants[0]->discountedPriceAsMoney->amount * $st_product->productCommission->percentage / 100;
		$prodCurrency = $stCurrency[$st_product->currency];

		add_filter('pre_get_document_title', function() use ($st_product) {
			return $st_product->title;
		});
		
		add_action( 'wp_head', function() use ($st_product) {
			$description = substr($st_product->description,0,160);
			echo "<meta name='description' content='$description'>";
			echo "<meta property='og:title' content='$st_product->title' />";
			echo "<meta property='og:description' content='$st_product->description' />";
			echo "<meta property='og:image' content='{$st_product->primaryImageSrc->imageSrc}' />";
		},1 );
	}
}
catch(Exception $e) {
}

get_header(null);
?>
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
									<div class="product-slider product-image-slider" style="overflow:hidden;">
										<img src="<?php echo $st_product->primaryImageSrc->imageSrc ?>" class="am-product-image am-product-other-image am-product-main-image" alt=""/>
										<?php
											foreach ($st_product->secondaryImageSrc as $img){
											  echo "<img src='{$img->imageSrc}' class='am-product-image am-product-other-image am-product-main-image' alt=''/>";
											}
										?>
									</div>
									<!-- ends product slider -->
									<!-- product details -->
									<div class="product-info">
										<h1 class="am-product-title"><?php echo $st_product->title ?></h1>
										<h5 class="am-product-vendor"><a href="<?php echo str_replace("{{brandId}}",$st_product->catalogId,$brandUrl); ?>"><?php echo $st_product->vendorName ?></a></h5>
										<h4 class="am-product-price"><span class="currency-symbol"><?php echo $prodCurrency ?></span><?php echo number_format($st_product->variants[0]->discountedPrice,2) ?></h4>
										<div class="options-container" style="display: none;">
											<div class="single-option">
												<div class="product-option-text">
													<h4>size</h4>
												</div>
												<div class="custom-select">
													<div class="form-group">
														<select class="form-control product-option-select">
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="addToCart-container">
											<div class="add-box">
												<div>
													<div class="btn-minus">-</div>
													<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1"/>
													<div class="btn-plus">+</div>
												</div>
											</div>
											<div class="addButton-container">
												<a class="btn btn-standard am-product-add-cart-btn" href="javascript:void()" role="button"  onclick="addToCart(this)" variantid="<?php echo $st_product->variants[0]->id ?>" productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</a>
											</div>
										</div>
										<button type="button" class="btn btn-standard cosell-btn am-cosell-btn" onclick="showCosell('<?php echo $st_product->id ?>')">Cosell and earn upto <?php echo "$prodCurrency".number_format($commission,2) ?></button>
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
				
				<div class="wp-block-kadence-rowlayout alignnone"><div id="kt-layout-id_a4d92d-16" class="kt-row-layout-inner kt-layout-id_a4d92d-16"><div class="kt-row-column-wrap kt-has-2-columns kt-gutter-none kt-v-gutter-default kt-row-valign-top kt-row-layout-left-golden kt-tab-layout-inherit kt-m-colapse-left-to-right kt-mobile-layout-row">
				<div class="wp-block-kadence-column inner-column-1 kadence-column_8c25f3-ec"><div class="kt-inside-inner-col">
				<h2 class="kt-adv-heading_ae42d3-d6 wp-block-kadence-advancedheading" data-kb-block="kb-adv-heading_ae42d3-d6">Related Products</h2>
				</div></div>
				<div class="wp-block-kadence-column inner-column-2 kadence-column_28a730-ab"><div class="kt-inside-inner-col">
				<div class="wp-block-kadence-advancedbtn kt-btn-align-right kt-btn-tablet-align-inherit kt-btn-mobile-align-inherit kt-btns-wrap kt-btns_b0010a-ce kt-force-btn-fullwidth"><div class="kt-btn-wrap kt-btn-wrap-0"><a class="kt-button button kt-btn-0-action kt-btn-size-standard kt-btn-style-basic kt-btn-svg-show-always kt-btn-has-text-true kt-btn-has-svg-false" href="https://us.awake.market/marketplace/"><span class="kt-btn-inner-text">MARKETPLACE</span></a></div></div>
				</div></div>
				</div></div></div>

				<div count="4" imagesize="250x0" vendorId="<?php echo $st_product->catalogId ?>" removetemplate class="products-container grid-two-by-two">
					<div class="product-container single-product " style="display: none">
						<a href="<?php>'$productUrl'?>/?product-id={{productId}}" class="am-product-link">
							<div class="product-image">
								<img class="am-product-image" src="product-image.png" alt="">
								<div class="market-product-price am-product-price">$ 48.00</div>
							</div>
							<div class="product-content">
								<p class="am-product-vendor">Brand Name</p>
								<h4 class="am-product-title">Product name</h4>
							</div>
						</a>
					</div>
				</div>
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
		<!-- End PDP Custom page -->
	</main><!-- #main -->
</div><!-- #primary -->

<!-- PDP PAGE = PLUS/MINUS -->
<script>
	sendUserEvent();
	jQuery(function($){
		$(document).ready(function(){
			//-- Click on QUANTITY
			$(".btn-minus").on("click",function(){
				var now = $(".add-box > div > input").val();
				if ($.isNumeric(now)){
					if (parseInt(now) -1 > 0){ now--;}
					$(".add-box > div > input").val(now);
				}else{
					$(".add-box > div > input").val("1");
				}
			})            
			$(".btn-plus").on("click",function(){
				var now = $(".add-box > div > input").val();
				if ($.isNumeric(now)){
					$(".add-box > div > input").val(parseInt(now)+1);
				}else{
					$(".add-box > div > input").val("1");
				}
			}); 

			$(".tab_content").hide();
			$(".tab_content:first").show();

			/* if in tab mode */
			$("ul.tabs li").click(function() {

				$(".tab_content").hide();
				var activeTab = $(this).attr("rel"); 
				$("#"+activeTab).fadeIn();		

				$("ul.tabs li").removeClass("active");
				$(this).addClass("active");

				$(".tab_drawer_heading").removeClass("d_active");
				$(".tab_drawer_heading[rel^='"+activeTab+"']").addClass("d_active");

			});
			/* if in drawer mode */
			$(".tab_drawer_heading").click(function() {

				$(".tab_content").hide();
				var d_activeTab = $(this).attr("rel"); 
				$("#"+d_activeTab).fadeIn();

				$(".tab_drawer_heading").removeClass("d_active");
				$(this).addClass("d_active");

				$("ul.tabs li").removeClass("active");
				$("ul.tabs li[rel^='"+d_activeTab+"']").addClass("active");
			});
			$(".product-image-slider").slick({
					dots: true,
					arrows: true
				});
			document.querySelector(".product-slider").style.overflow="";
		});
	});
</script>
<?php
get_footer();
