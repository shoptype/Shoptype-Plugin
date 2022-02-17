<?php
/* Template Name: Shop Template */
get_header(); ?>

<div class="main-content m-body"> <!--m-body is base class for the mobile body -->
	<div class="shop-tab-container">
		<div class="shop-tabs">
			<nav>
				<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active" id="nav-products-tab" data-toggle="tab" href="#nav-products" role="tab" aria-controls="nav-products" aria-selected="true">Products</a>
					<a class="nav-item nav-link" id="nav-brands-tab" data-toggle="tab" href="#nav-brands" role="tab" aria-controls="nav-brands" aria-selected="false">Brands</a>
				</div>
			</nav>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="tab-content" id="nav-tabContent">
					<!-- ----------------- product tab for mobile and desktop ----------------- -->
					<div class="tab-pane fade show active" id="nav-products" role="tabpanel" aria-labelledby="nav-products-tab">
						<?php require_once('inc/products-home.inc.php'); ?>
					</div>
					<!-- ----------------- end product tab for mobile and desktop ----------------- -->
					<!-- ----------------- brands tab for mobile and desktop ----------------- -->
					<div class="tab-pane fade" id="nav-brands" role="tabpanel" aria-labelledby="nav-brands-tab">
						<?php require_once('inc/brands-home.inc.php'); ?>
					</div>
					<!-- ----------------- brands tab for mobile and desktop ----------------- -->
				</div>
			</div>
		</div>
	</div>


<?php get_footer(); ?>
