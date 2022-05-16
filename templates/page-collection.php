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
$path = dirname(plugin_dir_url( __FILE__ ));
$collection_id = get_query_var( 'collection' );

try {
  $ch = curl_init();
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
  curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/platforms/{$stPlatformId}/collections/{$collection_id}");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
  
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
  </div>

<?php
get_footer();