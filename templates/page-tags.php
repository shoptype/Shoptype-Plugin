<?php
/*
 * Template name: Shoptype Products by tag template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
get_header();

try {
	$vendorId = 0;
	$vendorName = $vendorDescription = $vendorUrl = "";
	$tag = get_query_var( 'sttag' );
	$firstLoad = 20;
	
	$pluginUrl = plugin_dir_url( __FILE__ );
	
	$response = wp_remote_get("https://backend.shoptype.com/platforms/$stPlatformId/products?tags=$tag&count=$firstLoad");
  	$result = wp_remote_retrieve_body( $response );
 
	if( !empty( $result ) ) {
		$st_tag_products = json_decode($result);
		add_filter('pre_get_document_title', function() use ($tag) {
			return "$tag products";
		});
		add_action( 'wp_head', function() use ($tag) {
			echo "<meta name='description' content='All Products with tag {$tag}'>";
			echo "<meta property='og:title' content='{$tag} products' />";
			echo "<meta property='og:description' content='All Products with tag {$tag}' />";
			echo "<meta property='og:image' content='{$st_tag_products[0]->primaryImageSrc->imageSrc}' />";
		},1 );
	}
}
catch(Exception $e) {
}

?>
	<div class="products-container" tags="<?php echo $tag ?>" loadmore="true">
			<div class="product-container single-product" style="display: none;">
	            <div class="product-image">
	                <a href="" class="am-product-link">
	                    <img class="am-product-image" src="" loading="lazy" alt="">
	                </a>
	                <div class="market-product-price am-product-price"></div>
	            </div>
	            <div class="product-info">
	                <p class="am-product-title product-title"></p>
	                <p class="am-product-vendor brand-title"></p>
	            </div>
	        </div>
		<?php foreach($st_tag_products->products as $key=>$product): ?>
	        <div class="product-container single-product" id="<?php echo $product->id ?>">
	            <div class="product-image">
	                <a href="/products/<?php echo $product->id ?>" class="am-product-link">
	                    <img class="am-product-image" src="<?php echo $product->primaryImageSrc->imageSrc ?>" loading="lazy" alt="">
	                </a>
	                <div class="market-product-price am-product-price"><?php echo "{$stCurrency[$product->variants[0]->discountedPriceAsMoney->currency]} {$product->variants[0]->discountedPriceAsMoney->amount}" ?></div>
	            </div>
	            <div class="product-info">
	                <p class="am-product-title product-title"><?php echo $product->title ?></p>
	                <p class="am-product-vendor brand-title"><?php echo $product->vendorName ?></p>
	            </div>
	        </div>
	    <?php endforeach; ?>
    </div>
    <script type="text/javascript">
    	offset = <?php echo $firstLoad ?>;
    </script>
<?php
get_footer();

