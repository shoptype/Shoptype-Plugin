<?php /* Template Name: Shoptype Brand Detail Template */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
get_header();
$st_brand = [];
try {
	$brandId = get_query_var( 'brand' );
	if (preg_match('/^\{?[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}\}?$/', $brandId)) {
		$brandSearch = "vendorId=$brandId";
	} else {
		$brandSearch = "text=$brandId";
	}
	$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/vendors?$brandSearch");
	$result = wp_remote_retrieve_body( $response );

	if( !empty( $result ) ) {
		$st_brands = json_decode($result);
		$st_brand = $st_brands[0];
		if(isset($st_brand->vendor_meta_data)){
			$meta_fields = array();
			foreach ($st_brand->vendor_meta_data as $meta_data) {
			  $meta_fields[$meta_data->key] = $meta_data->value;
			}
		}
		if(isset($st_brand->productCategories)){
			$productCategories = join(", ",$st_brand->productCategories);
		}
		$store = $st_brand->store;
		if( function_exists('groups_get_id')) {
			$groupSlug = preg_replace('~[^\pL\d]+~u', '-', $st_brand->name);
			$groupSlug = preg_replace('~[^-\w]+~', '', $groupSlug);
			$groupSlug = strtolower($groupSlug);
			$group_id = groups_get_id( $groupSlug );
			$group = groups_get_group( array( 'group_id' => (int) $group_id ) );
			$user_id = get_current_user_id();
			$isInGroup = groups_is_user_member( $user_id, $group_id );
		}
	}
}
catch(Exception $e) {
}

 ?>
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
							<?php if(isset($group)){?><div class="follow-link"><a href="javascript:void()" class="btn-blueRounded">Follow</a></div> <?php } ?>
						</div>
						<div class="intro-box">
							<h1 class="brand-name am-brand-name"><?php echo $st_brand->name; ?></h1>
							<!-- <p class="brand-statue">Status:<span>Brand Elite</span></p> -->
							<!-- <p class="brand-since">Member since:<span>28 April 2020</span></p> -->
							<!-- <p class="brand-fame">Brand Fame:<span>10K</span></p> -->
							<p class="brand-location">Located in:<span><?php echo (!empty($store->countryState) ? $store->countryState : "Not Specified"); ?></span></p>
							<?php if(isset($meta_fields["Speciality"])) : ?>
								<div class="coseller-bio">
									<p class="bio-heading">Specialises in:</p>
									<p class="bio-desc">
										<?php echo $meta_fields["Speciality"]; ?>
									</p>
								</div>
							<?php endif; ?>
							<?php if(isset($meta_fields["Description"])) : ?>
								<div class="coseller-bio">
									<p class="bio-heading">About:</p>
									<p class="bio-desc">
										<?php echo $meta_fields["Description"]; ?>
									</p>
								</div>
							<?php endif; ?>
							<?php if(isset($meta_fields["Return & Refund Policy"])) : ?>
								<div class="coseller-bio">
									<p class="bio-heading">Return &amp; Refund Policy:</p>
									<p class="bio-desc">
										<?php echo $meta_fields["Return & Refund Policy"]; ?>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="section-header">
							<h1 class="section-title">Products by <?php echo $st_brand->name; ?></h1>
							<a href="<?php global $marketUrl; echo $marketUrl; ?>" class="btn-blueRounded">See All</a>
						</div>
						<?php echo do_shortcode( '[awake_products per_row="20" vendor_id="'.$st_brand->id.'" loadmore="true" product_classes="product-container  single-product"]' ); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- -------------- end brands section -------------- -->

	</div>
	<!-- ================= END COSELLER DETAILS SECTION FOR DESKTOP ================= -->
</div>
<!-- ===================================== -->


<?php get_footer(); ?>
