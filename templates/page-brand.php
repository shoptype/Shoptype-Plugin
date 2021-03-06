<?php /* Template Name: Brand Detail Template */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
$st_brand = [];
try {
	$brandId = get_query_var( 'brand' );
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "{$stBackendUrl}/platforms/$stPlatformId/vendors?vendorId=$brandId");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	if( !empty( $result ) ) {
		$st_brands = json_decode($result);
		$st_brand = $st_brands[0];
		$productCategories = join(", ",$st_brand->productCategories);
		$store = $st_brand->store;
		$groupSlug = preg_replace('~[^\pL\d]+~u', '-', $st_brand->name);
		$groupSlug = preg_replace('~[^-\w]+~', '', $groupSlug);
		$groupSlug = strtolower($groupSlug);
		$group_id = groups_get_id( $groupSlug );
		$group = groups_get_group( array( 'group_id' => (int) $group_id ) );
		$user_id = get_current_user_id();
		$isInGroup = groups_is_user_member( $user_id, $group_id );
	}
}
catch(Exception $e) {
}
// wp_enqueue_style( 'awake-prod-style', get_template_directory_uri() . '/css/awake-prod-style.css' );
// wp_enqueue_style( 'awake-prod-media-style', get_template_directory_uri() . '/css/awake-prod-media-style.css' );
get_header(); ?>
<?php
?>
<!-- ===================================== -->
<div class="main-content">
	<!-- ================= COSELLER DETAILS SECTION FOR DESKTOP ================= -->
	<div class="coseller-info-section d-none d-sm-block">
		<!-- -------------- coseller intro section -------------- -->
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="coseller-profile">
						<div class="brand-image-container">
							<img style="width:100%;" src="<?php echo $st_brand->logo ?>" loading="lazy" alt="" class="brand-image am-brand-logo">
							<div class="follow-link"><a href="javascript:void()" class="btn-blueRounded">Follow</a></div>
						</div>
						<div class="intro-box">
							<h1 class="brand-name am-brand-name"><?php echo $st_brand->name; ?></h1>
							<!-- <p class="brand-statue">Status:<span>Brand Elite</span></p> -->
							<!-- <p class="brand-since">Member since:<span>28 April 2020</span></p> -->
							<!-- <p class="brand-fame">Brand Fame:<span>10K</span></p> -->
							<p class="brand-location">Located in:<span><?php echo (!empty($store->countryState) ? $store->countryState : "Not Specified"); ?></span></p>
							<p class="brand-speciality am-brand-categories">Specialises in:<span><?php echo (!empty($productCategories) ? $productCategories : "Not Specified"); ?></span></p>
							<?php if(!empty($group->description)) : ?>
								<div class="coseller-bio">
									<p class="bio-heading">About:</p>
									<p class="bio-desc">
										<?php echo$group->description; ?>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- -------------- end coseller intro section -------------- -->
		<!-- -------------- preferences section -------------- -->
		<!-- <div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="preferences-container">
						<h2>Brand Key Tags</h2>
						<ul class="preferences-list">
							<li><a href="#">Fashion</a></li>
							<li><a href="#">Wine & Spirits</a></li>
							<li><a href="#">Vegan Diet</a></li>
							<li><a href="#">Home Decor</a></li>
							<li><a href="#">Lifestyle</a></li>
							<li><a href="#">Products for Women</a></li>
							<li><a href="#">Photography</a></li>
							<li><a href="#">Travel</a></li>
							<li><a href="#">Action Figures / Collectibles</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div> -->
		<!-- -------------- end preferences section -------------- -->
		<!-- -------------- brands section -------------- -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="section-header">
							<h1 class="section-title">Products by <?php echo $st_brand->name; ?></h1>
							<a href="<?php echo home_url('all-products'); ?>" class="btn-blueRounded">See All</a>
						</div>
						<?php echo do_shortcode( '[awake_products per_row="12" slider="0" vendor_id="'.$st_brand->id.'" product_classes="product-container  single-product"]' ); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- -------------- end brands section -------------- -->
		<!-- -------------- content section -------------- -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="section-header">
							<h1 class="section-title">Blogs related to <?php echo $st_brand->name; ?></h1>
							<a href="javascript:void()" class="btn-blueRounded">See All</a>
						</div>
						<div class="blogs-container">
							<div class="row">
								<?php echo do_shortcode("[awake_editors_picks display_layout=\"1\" search=\"{$st_brand->name}\"]"); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- -------------- end content section -------------- -->
	</div>
	<!-- ================= END COSELLER DETAILS SECTION FOR DESKTOP ================= -->
</div>
<!-- ===================================== -->


<?php get_footer(); ?>
