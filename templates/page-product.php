<?php
/*
 * Template name: Shoptype Product Detail template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
global $productUrl;

$trimmed_productUrl = str_replace("tid={{tid}}", "", $productUrl);
$trimmed_productUrl = rtrim($trimmed_productUrl,"?");
$urlparts = wp_parse_url(home_url());
$domain = $urlparts['host'];
try {
	$vendorId = 0;
	$vendorName = $vendorDescription = $vendorUrl = "";
	$productId = get_query_var('stproduct');
	$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?productIds=$productId");
	$resultProduct     = wp_remote_retrieve_body( $response );
	
	if (!empty($resultProduct)) {
		$st_products = json_decode($resultProduct);
		if(!isset($st_products->products[0])){
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( 404 );
			exit();
		}
		$st_product = $st_products->products[0];
		$commission = $st_product->variants[0]->discountedPriceAsMoney->amount * $st_product->productCommission->percentage / 100;
		$prodCurrency = $stCurrency[$st_product->currency];
		$vendorId = $st_product->catalogId;

		add_filter('pre_get_document_title', function () use ($st_product) {
			return $st_product->title;
		});

		add_action('wp_head', function () use ($st_product) {
			$description = substr($st_product->description, 0, 160);
			echo "<meta name='description' content='$description'>";
			echo "<meta property='og:title' content='$st_product->title' />";
			echo "<meta property='og:description' content='".substr(strip_tags($st_product->description),0,250)."' />";
			echo "<meta property='og:image' content='{$st_product->primaryImageSrc->imageSrc}' />";
		}, 1);
	}
	// Get vendor details
	if (!empty($vendorId)) {
		$response2 = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/vendors?vendorId=$vendorId");
  		$vendorChResponse = wp_remote_retrieve_body( $response2 );
 
		if (!empty($vendorChResponse)) {
			$vendorDetails = json_decode($vendorChResponse);
			$vendor = $vendorDetails[0];
			if(isset($vendor->vendor_meta_data)){
				$meta_fields = array();
				foreach ($vendor->vendor_meta_data as $meta_data) {
				  $meta_fields[$meta_data->key] = $meta_data->value;
				}
			}
			$vendorName = $vendor->name;
			$vendorUrl = str_replace("{{brandId}}", $vendorId, $brandUrl);
		}
	}
} catch (Exception $e) {
}
wp_enqueue_script( 'jquery_min', '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
get_header();

get_template_part( 
    'templates/parts/st-product-part.php', 
    null, 
    array( 
        'my_data' => array(
            'st_product' => $st_product,
            'commission' => $commission,
            'prodCurrency' => $prodCurrency,
            'vendor' => $vendor,
        )
    )
);
?>

<?php
get_footer();