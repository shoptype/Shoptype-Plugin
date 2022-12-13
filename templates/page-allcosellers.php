<?php
/* Template Name: Shoptype Cosellers Listing Page */
get_header() ?>

<div class="main-content m-body">
	<!-- ================= COSELLERS SECTION ================= -->
	<div class="section small-section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="section-header">
						<h1 class="section-title">All Cosellers</h1>
					</div>
					<?php echo do_shortcode('[awake_cosellers per_row="-1" for_listing="1" slider="0" outer_class="cosellers-list-container"]'); ?>
				</div>
			</div>

			<div class="pagination">
				<ul> <!--pages or li are comes from javascript --> </ul>
			</div>

		</div>
	</div>
	<!-- ================= END COSELLERS SECTION ================= -->


</div>

<?php get_footer(); ?>
