<?php
/*
 * Template name: Shoptype Collections Page
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;
$path = dirname(plugin_dir_url( __FILE__ ));
$collection_id = get_query_var( 'collection' );

try {
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
  
  $response = wp_remote_get("{$stBackendUrl}/platforms/{$stPlatformId}/collections/{$collection_id}");
  $result = wp_remote_retrieve_body( $response );
 
  if( !empty( $result ) ) {
    $st_collection = json_decode($result);
  }else{
  }
}
catch(Exception $e) {
  echo "Cart not found";
}

get_header(null);
?>
  <div>
    <div class="st-myshop-head">
      <div class="st-myshop-bg-top" style="background-image:url('<?php echo $st_collection->preview_image_src ?>')" ></div>
      <div class="st-myshop-top">
        <h1 class="st-myshop-title"><?php echo $st_collection->name ?></h1>
      </div>
    </div>
    <div class="st-myshop-prods">
        <?php foreach($st_collection->product_details as $key=>$value): ?>
          <a href="<?php echo "/products/{$value->id}" ?>" class="st-myshop-prod">
            <div><img src="<?php echo $value->primaryImageSrc->imageSrc ?>" loading="lazy" alt="" class="st-myshop-prod-img"></div>
            <div class="st-myshop-prod-price"><?php echo "{$stCurrency[$value->variants[0]->discountedPriceAsMoney->currency]} {$value->variants[0]->discountedPriceAsMoney->amount}" ?></div>
            <div class="st-myshop-prod-details">
              <div class="st-myshop-prod-name"><?php echo $value->title ?></div>
              <div class="st-myshop-prod-vendor"><?php echo $value->vendorName ?></div>
            </div>
          </a>
        <?php endforeach; ?>
    </div>
    <div class="st-market-link">
      <a href="<?php global $marketUrl; echo $marketUrl; ?>"><h3>View All Products</h3></a>
    </div>
  </div>

<?php
get_footer();