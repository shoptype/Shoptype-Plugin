<?php
/*
 * Template name: Brand Detail template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
try {
	$brandId = $_GET['id'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/platforms/$stPlatformId/vendors?vendorId=$brandId");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	if( !empty( $result ) ) {
		$st_brands = json_decode($result);
		$st_brand = $st_brands[0];
	}
}
catch(Exception $e) {
}

get_header(null, [ 'brand' =>  $st_brand]);
?>
<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main">
		<!-- PDP Custom page -->
		<div class="page-wrapper">
			<div class="outer-container">
				<!-- ============== PAGE CONTENT ============== -->
				<!-- brand header section -->
				<div class="am-brand-display-container">
					<div class="brand-header">
						<div class="brand-logo brand-image-block">
							<img src="<?php echo $st_brand->logo ?>" loading="lazy" alt="" class="brand-image am-brand-logo">
						</div>
						<div class="brand-details brand-info">
							<div class="info-title">
								<h1 class="brand-name am-brand-name"><?php echo $st_brand->name ?></h1>
								<!-- <a href="javascript:void()" class="btn btn-standard">follow</a> -->
							</div>
							<div class="brand-speciality">
								<!-- <p><span>Available in:</span> Global Shipping</p> -->
								<p><span>Specializes in:</span> <span class="am-brand-categories"><?php echo join(", ",$st_brand->productCategories); ?></span></p>
							</div>
							<div class="brand-about">
								<h4>ABOUT US</h4>
								<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Asperiores ratione voluptas ex quo doloremque possimus, provident ab voluptatem dolorem quibusdam eum ullam perferendis praesentium ipsa. Maxime quis optio veritatis placeat?</p>
							</div>
						</div>
					</div>



<div class="wp-block-kadence-rowlayout alignnone">
	<div id="kt-layout-id_57c52b-1b" class="kt-row-layout-inner kt-layout-id_57c52b-1b">
		<div class="kt-row-column-wrap kt-has-1-columns kt-gutter-default kt-v-gutter-default kt-row-valign-top kt-row-layout-equal kt-tab-layout-inherit kt-m-colapse-left-to-right kt-mobile-layout-row">
			<div class="wp-block-kadence-column inner-column-1 kadence-column_5d2256-83">
			<div class="kt-inside-inner-col">
			<h2 class="kt-adv-heading_2ea420-ff wp-block-kadence-advancedheading" data-kb-block="kb-adv-heading_2ea420-ff">Brand&#8217;s Products</h2>
			<div count="4" imageSize="250x0" vendorId="<?php echo $st_brand->id ?>" removeTemplate class="products-container grid-two-by-two" >
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
			</div>
			</div>
		</div>
	</div>
</div>







					<!-- ends brand header section -->
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
				<!-- ============== ENDS PAGE CONTENT ============== -->
			</div>
		</div>
		<!-- End PDP Custom page -->
	</main><!-- #main -->
</div><!-- #primary -->
<script>
	let currentBrandId = currentUrl.searchParams.get("id");
	document.addEventListener('marketLoaded', function (e) {
		initMarket();
		loadBrand(currentBrandId, function(brand){
			console.info(brand);
		},
		function(e){
			console.info("error: " + e)
		});
	}, false);


	jQuery(function($){

		document.addEventListener('amProductsLoadFailed', function (e) {
			$(".products-container").append("<h5>No Products found</h5>");
		}, false);

		$(document).ready(function(){
		});
	});
</script>
<?php
get_footer();
