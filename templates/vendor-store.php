<?php
/*
 * Template name: Shoptype vendor store
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
try {
	$vendorName = urldecode(get_query_var('st_store'));

	$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/vendors?name={$vendorName}");
	$resultVendor     = wp_remote_retrieve_body( $response );
	
	$args= array(
	  'search' => $vendorName, // or login or nicename in this example
	  'search_fields' => array('user_login','user_nicename','display_name')
	);
	$user_query = new WP_User_Query($args);
	$user = $user_query->get_results();
	
	$args = array(
		'author'        =>  $user[0]->id,
		'orderby'       =>  'post_date',
		'order'         =>  'ASC',
		'posts_per_page' => 5
    );
	$shop_posts = get_posts( $args );
	$args = array(
		'orderby'       =>  'post_date',
		'order'         =>  'ASC',
		'posts_per_page' => 5
    );
	$all_posts = get_posts( $args );
	$posts = array_merge($shop_posts, $all_posts);

	if (!empty($resultVendor)) {
		$st_vendors = json_decode($resultVendor);
		$st_vendor = $st_vendors[0];

		add_filter('pre_get_document_title', function () use ($st_product) {
			return $st_vendor->name;
		});

		add_action('wp_head', function () use ($st_product) {
			$description = substr($st_product->description, 0, 160);
			echo "<meta name='description' content='Shop form $st_vendor->name'>";
			echo "<meta property='og:title' content='$st_vendor->name' />";
			echo "<meta property='og:description' content='Shop form $st_vendor->name' />";
			echo "<meta property='og:image' content='{$st_vendor->logo}' />";
		}, 1);

		$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?vendorId={$st_vendor->id}&count=15&imgSize=400x0");

  		$result = wp_remote_retrieve_body( $response );
  		$resultProducts     = wp_remote_retrieve_body( $response );
  		if (!empty($resultProducts)) {
  			$st_products = json_decode($resultProducts);
			$st_products = $st_products->products;
  		}
	}
} catch (Exception $e) {
}

get_header();
?>

<style>
h1, h2, h3, h4 {
    font-family: serif;
}
.st-shop-main {
    margin: 90px 0px 0px;
	background: #EEF0EA;
}
.st-shop-top {
    display: flex;
    width: 100%;
    justify-content: center;
    padding: 0px 0px 200px;
	background-size: cover;
	background-position: center;
	position:relative;
	z-index:1;
}
div.st-shop-logo {
    width: 400px;
    height: 400px;
}
.st-shop-bio h4 {
    color: #fff;
}
.st-shop-name h1 {
    color: #fff;
}
.st-shop-display {
    padding: 70px 50px;
}
img.st-shop-logo {
    height: 100%;
	object-fit: contain;
}
.st-shop-product-img{
    width: 100%;
    aspect-ratio: 1;
}
.st-shop-product-img img{
    height: 100%;
    object-fit: cover;
}
.st-shop-content {
    margin: auto;
    max-width: 1240px;
	background: #fff;
	padding: 0px 5px;
}
.st-shop-product-list {
	margin-top:-150px;
    display: flex;
    justify-content: space-around;
	margin-bottom:20px;
	flex-wrap: wrap;
}
.st-shop-product-list-3 {
    display: flex;
    justify-content: space-around;
	margin-bottom:20px;
	flex-wrap: wrap;
}
div.st-shop-product {
    height: 300px;
    background: #fff;
    width: 230px;
	padding: 2px;
}
a.st-shop-product {
	position:relative;
	z-index:2;	
}
.st-shop-blog-content a {
    display: flex;
}
.st-shop-blog{
	margin: 10px 10px 40px;
}
.st-shop-blog-img img {
    width: 100%;
    aspect-ratio: 1.5;
    object-fit: cover;
}
.st-shop-blog-img, .st-shop-blog-details {
    flex: 1 1 50%;
}
.st-shop-blog-details {
	padding:20px;
	background: #F4F4F4;
}
.st-shop-stories {
    display: flex;
}
.st-shop-stories-inner {
    flex: 1 1 calc(100% - 250px);
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}
.st-shop-blog2 {
    width: calc(50% - 10px);
    margin-bottom: 20px;
}
.st-shop-editorial {
    margin: 10px 10px 30px;
}
.st-shop-ad {
    flex: 1 1 260px;
    padding: 0px 0px 20px 10px;
    overflow: hidden;
}
.st-shop-ad-holder {
    width: 100%;
    height: 100%;
    background: #e1e1e1;
    display: flex;
    justify-content: center;
    align-items: center;
}
.st-shop-ad1{
	margin: 10px 10px 40px;
	height:250px;
}
.st-shop-blog-details h4 {
    height: 48px;
    font-weight: 400;
    line-height: 24px;
    font-size: 20px;
}
.st-shop-blog-details div {
    color: #333;
}
.st-shop-blog-excerpt {
   overflow: hidden;
   display: -webkit-box;
   -webkit-line-clamp: 2; /* number of lines to show */
           line-clamp: 2; 
   -webkit-box-orient: vertical;
}
@media only screen and (max-width: 768px) {
	.st-shop-blog-content a {
		flex-direction: column;
	}
	.st-shop-stories {
		flex-direction: column;
	}
	.st-shop-top {
		justify-content: center;
		flex-direction: column;
    	align-items: center;
	}
	a.st-shop-product {
		width: calc(50% - 20px);
	}
	div.st-shop-product {
		width:100%;
		height:auto;
	}
}
@media only screen and (max-width: 768px) {
}
</style>

<div class="st-shop-main">
	<div class="st-shop-top" style="background-image:url('<?php echo st_locate_file("images/store-bg.png") ?>')">
		<div class="st-shop-logo"><img class="st-shop-logo" src="<?php echo $st_vendor->logo ?>"></div>
		<div class="st-shop-display">
			<div class="st-shop-name"><h1><?php echo $st_vendor->name ?></h1></div>
			<div class="st-shop-bio"><h4><?php echo $st_vendor->name ?></h4></div>
		</div>
	</div>
	<div class="st-shop-content">
		<div class="st-shop-product-list">
			<?php for ($x = 0; $x < 5; $x++){ ?>
				<a class="st-shop-product" href="./products/<?php echo $st_products[$x]->id ?>">
					<div class="st-shop-product">
						<div class="st-shop-product-img">
							<img src="<?php echo $st_products[$x]->primaryImageSrc->imageSrc ?>">
						</div>
						<div class="st-shop-product-title">
							<div>
								<?php echo $st_products[$x]->title ?>
							</div>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>
		<div class="st-shop-blog">
			<div class="st-shop-sub-head">
				<h4>Blog Article / About Us</h4>
			</div>
			<div class="st-shop-blog-content">
				<a href="<?php echo get_permalink( $posts[0]->ID ) ?>">
					<div class="st-shop-blog-img">
						<img src="<?php echo get_the_post_thumbnail_url($posts[0]->ID) ?>">
					</div>
					<div class="st-shop-blog-details">
						<h3>
							<?php echo $posts[0]->post_title ?>
						</h3>
						<div>
							<?php echo get_the_excerpt($posts[0]->ID) ?>
						</div>
					</div>
				</a>
			</div>
		</div>
		<div class="st-shop-ad1">
			<div class="st-shop-ad-holder">
				Ad
			</div>
		</div>
		<div class="st-shop-product-list-2">
			<div class="st-shop-sub-head">
			</div>
			<div class="st-shop-product-list-3">
				<?php for ($x = 5; $x < 10; $x++){ ?>
					<a class="st-shop-product" href="./products/<?php echo $st_products[$x]->id ?>">
						<div class="st-shop-product">
							<div class="st-shop-product-img">
								<img src="<?php echo $st_products[$x]->primaryImageSrc->imageSrc ?>">
							</div>
							<div class="st-shop-product-title">
								<div>
									<?php echo $st_products[$x]->title ?>
								</div>
							</div>
						</div>
					</a>
				<?php } ?>
			</div>
		</div>
		<div class="st-shop-coseller-list">
		
		</div>
		<div class="st-shop-editorial">
			<div class="st-shop-sub-head">
				<h4>Freshly Brewed Editorials</h4>
			</div>
			<div class="st-shop-stories">
				<div class="st-shop-stories-inner">
				<?php for ($y = 1; $y < 5; $y++){ ?>
					<div class="st-shop-blog2">
						<a href="<?php echo get_permalink( $posts[$y]->ID ) ?>">
							<div class="st-shop-blog-img">
								<img src="<?php echo get_the_post_thumbnail_url($posts[$y]->ID) ?>">
							</div>
							<div class="st-shop-blog-details">
								<h4>
									<?php echo $posts[$y]->post_title ?>
								</h4>
								<div class="st-shop-blog-excerpt">
									<?php echo get_the_excerpt($posts[$y]->ID) ?>
								</div>
							</div>
						</a>
					</div>

				<?php } ?>			
				</div>
				<div class="st-shop-ad">
					<div class="st-shop-ad-holder">
						Ad
					</div>
				</div>
			</div>
		</div>
		<div class="st-shop-product-list-2">
			<div class="st-shop-sub-head">
			</div>
			<div class="st-shop-product-list-3">
				<?php for ($x = 10; $x < 15; $x++){ ?>
					<a class="st-shop-product" href="./products/<?php echo $st_products[$x]->id ?>">
						<div class="st-shop-product">
							<div class="st-shop-product-img">
								<img src="<?php echo $st_products[$x]->primaryImageSrc->imageSrc ?>">
							</div>
							<div class="st-shop-product-title">
								<div>
									<?php echo $st_products[$x]->title ?>
								</div>
							</div>
						</div>
					</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer('shop');