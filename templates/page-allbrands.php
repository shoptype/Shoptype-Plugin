<?php
/* Template Name:Brands Listing Template */
get_header(); ?>
<!-- ===================================== -->
<div class="main-content m-body">
	<!-- ================= PRODUCT SECTION 01 ================= -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="section-header">
						<h1 class="section-title">2022 Brands</h1>
					</div>
					<!-- ===== best selling products for desktop ===== -->
					<?php echo do_shortcode( '[awake_brands for_listing="1" per_row="12" slider="0" container_classes="brand-wrapper" brand_classes="single-brand"]' ); ?>
					<!-- ===== end best selling products for desktop ===== -->
				</div>
			</div>
		</div>
	</div>
	<!-- ================= END PRODUCT SECTION 01 ================= -->

<!-- ===================================== -->
<?php get_footer(); ?>
