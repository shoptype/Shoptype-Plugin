<?php
get_header();
$cosellerId = 0;
/* Template Name: Coseller Profile BKP Template */
if(isset($_GET) && !empty($_GET) && isset($_GET['id']) && !empty($_GET['id'])) :
	$cosellerId = $_GET['id'];
	$cosellerData = get_userdata($cosellerId);
	if(!empty($cosellerData)) $displayName = $cosellerData->display_name;
	$contentCreated = count_user_posts($cosellerId);
	$showBrandsDiv = false;
	// Get groups
	$groupArgs = array(
		'group_type' => array('brand'),
		'user_id' => $cosellerId
	);
	if ( bp_has_groups( $groupArgs ) ) {
		$showBrandsDiv = true;
	}
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
							<div class="coseller-image-container">
								<img src="<?php echo get_avatar_url($cosellerId); ?>" alt="">
								<?php //echo $mybutton = bp_add_friend_button( $cosellerId ); ?>
								<?php //do_action('button_here'); ?>
								<div class="follow-link"><a href="javascript:void()">Follow</a></div>
							</div>
							<div class="intro-box">
								<h1 class="coseller-name"><?php echo $displayName; ?></h1>
								<p class="coseller-statue">Status:<span>Elite Coseller</span></p>
								<p class="coseller-since">Member since:<span>28 April 2020</span></p>
								<p class="coseller-earnings">Cosell earnings:<span>1-3k USD per month</span></p>
								<!-- <p class="coseller-location">Located in:<span>Not Specified</span></p> -->
								<p class="coseller-speciality">Specialises in:<span>Sommelier</span></p>
								<div class="coseller-bio">
									<p class="bio-heading">Personal Bio:</p>
									<p class="bio-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse ipsum massa, auctor sit amet congue eu, egestas a ipsum. Suspendisse porttitor magna luctus lacus tempus malesuada. Sed placerat turpis non odio vulputate elementum. Nullam rhoncus lacus tellus, at viverra tortor consequat ut. Sed ligula diam, laoreet sit amet cursus et.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- -------------- end coseller intro section -------------- -->
			<!-- -------------- preferences section -------------- -->
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="preferences-container">
							<h2>Preferences</h2>
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
			</div>
			<!-- -------------- end preferences section -------------- -->
			<!-- -------------- brands section -------------- -->
			<?php if($showBrandsDiv) : ?>
				<div class="section">
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
								<div class="section-header">
									<h1 class="section-title">Brands Followed</h1>
									<a href="<?php echo home_url('all-brands'); ?>">See All</a>
								</div>
								<!-- slick slider -->
								<?php //echo do_shortcode( '[awake_brands per_row="8" slider="1" container_classes="dBrandSlider" brand_classes="single-brand"]' ); ?>
								<div class="groupsSlider">
									<?php while ( bp_groups() ) : bp_the_group();
						                $groupId = bp_get_group_id();
						                $coverImgUrl = get_template_directory_uri()."/img/communities-full-image.jpg";
						                $groupCoverImage = bp_attachments_get_attachment('url', array(
						                    'object_dir' => 'groups',
						                    'item_id' => bp_get_group_id(),
						                ));
						                if(!empty($groupCoverImage)) $coverImgUrl = $groupCoverImage; ?>
										<div>
											<div class="single-brand">
												<a href="<?php bp_group_permalink(); ?>">
													<img src="<?php echo $coverImgUrl; ?>" alt="">
												</a>
												<h4><?php bp_group_name(); ?></h4>
											</div>
										</div>
									<?php endwhile; ?>
								</div>
								<!-- end slick slider -->
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<!-- -------------- end brands section -------------- -->
			<!-- -------------- content section -------------- -->
			<?php if($contentCreated > 0) : ?>
				<div class="section">
					<div class="container">
						<div class="row">
		                    <div class="col-sm-12">
		    					<div class="section-header">
		    						<h1 class="section-title">Content Created</h1>
		    						<a href="javascript:void(0);">See All</a>
		    					</div>
		    					<div class="blogs-container">
		    						<div class="row">
		    							<?php echo do_shortcode('[awake_editors_picks for_author="'.$cosellerId.'" display_layout="1"]'); ?>
		    						</div>
		    					</div>
		    				</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<!-- -------------- end content section -------------- -->
		</div>
		<!-- ================= END COSELLER DETAILS SECTION FOR DESKTOP ================= -->
	</div>
	<!-- ===================================== -->
<?php else: ?>
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h3>No data found.</h3>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php get_footer(); ?>
