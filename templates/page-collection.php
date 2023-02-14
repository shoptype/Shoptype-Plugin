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
  <style type="text/css">
    .collections-products-container{
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
    }
  </style>
  <div>
    <div class="st-myshop-head">
      <div class="st-myshop-bg-top" style="background-image:url('<?php echo $st_collection->preview_image_src ?>')" ></div>
      <div class="st-myshop-top">
        <h1 class="st-myshop-title"><?php echo $st_collection->name ?></h1>
      </div>
    </div>
    <div class="collections-products-container">
      <?php foreach($st_collection->product_details as $key=>$value): 
          $max_price=$value->variants->discountedPriceAsMoney;
          $min_price=$value->variants->discountedPriceAsMoney;
          $value->{"sale"} = false;
          $value->{"soldout"} = true;
          foreach($value->variants as $variantKey=>$variantValue):
            if($max_price->amount < $variantValue->discountedPriceAsMoney->amount){
              $max_price=$variantValue->discountedPriceAsMoney;
            }else if($min_price->amount > $variantValue->discountedPriceAsMoney->amount){
              $min_price=$variantValue->discountedPriceAsMoney;
            }
            if($variantValue->priceAsMoney->amount > $variantValue->discountedPriceAsMoney->amount){
              $value->{"sale"} = true;
            }
            if($variantValue->quantity>0){
              $value->{"soldout"} = false;
            }
          endforeach;
      ?>
        <div class="product-container single-product">
          <a href="<?php echo "/products/{$value->id}" ?>" class="am-product-link">
            <div class="product-image">
              <div class="am-product-img-div">
                <?php if($value->soldout){ ?>
                  <div class="sold-out" style="display:none;">Sold Out</div>
                <?php }elseif ($value->sale) { ?>
                  <div class="on-sale" style="display:none;">Sale</div>
                <?php }?>
                <img class="am-product-image" src="<?php echo $value->primaryImageSrc->imageSrc ?>" loading="lazy" alt="">
              </div>
            </div>
            <div class="product-info">
              <p class="am-product-title product-title"><?php echo $value->title ?></p>
              <p class="am-product-vendor brand-title"><?php echo $value->vendorName ?></p>
              <div class="market-product-price am-product-price"><?php echo "{$stCurrency[$value->variants[0]->discountedPriceAsMoney->currency]} {$value->variants[0]->discountedPriceAsMoney->amount}" ?></div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  
    <div class="st-market-link">
      <a href="<?php global $marketUrl; echo $marketUrl; ?>"><h3>View All Products</h3></a>
    </div>
  </div>

<?php
get_footer();