<?php
/* Template Name:Products Home */
get_header(); ?>

<!-- ===================================== -->
<div class="main-content m-body">
	<!-- ================= PRODUCT SECTION 01 ================= -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="section-header">
						<h1 class="section-title">Wine</h1>
						<a href="<?php echo home_url('all-products'); ?>?tags=wine" data-tag="wine" class="btn-blueRounded tag-redirection">See All</a>
					</div>
					<!-- ===== best selling products for desktop ===== -->
					<?php echo do_shortcode( '[awake_products for_listing="1" slider="0" tags="wine" product_classes="product-container single-product"]' ); ?>
					<!-- ===== end best selling products for desktop ===== -->
				</div>
			</div>
		</div>
	</div>
	<!-- ================= END PRODUCT SECTION 01 ================= -->
</div>
<!-- ===================================== -->
<?php  get_footer(); ?>
