<?php
/*
 * Template name: Shoptype My Shop
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
$path = dirname(plugin_dir_url( __FILE__ ));

$userName = get_query_var( 'shop' );
//$the_user = get_user_by('login', $userName);
if(empty($userName)){
}
try {
  $ch = curl_init();
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
  curl_setopt($ch, CURLOPT_URL, "https://$domain/wp-json/shoptype/v1/shop/".$userName);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  if( !empty( $result ) ) {
    $st_user_products = json_decode($result);
  }else{
  }
}
catch(Exception $e) {
  echo "Cart not found";
}

get_header(null);
$user = get_user_by( 'email',$userName );


$cover=(empty($st_user_products->cover)) ? '' : $st_user_products->cover ;
$avatar=(isset($user->ID))? get_avatar_url( $user->ID ) : $st_user_products->avatar;
$user_name=(isset($user->ID))? $user->display_name : 'Shoptype user';
$shop_name=(empty($st_user_products->shop_name))? $user_name.' store' : $st_user_products->shop_name;

?>
  <div class='wrapper' id='main'>
    <div id="content">
    <div class="st-myshop-head">
      <div class="st-shop-header-name"><?php echo $shop_name ?></div>

     <div class="st-header-container">
      <div class="st-store-grid st-store-header">
<div id="banner-wrap">
<div class="st-store-banner-wrap"><img src='<?php echo $cover ?>' alt="" class="store-banner"></div>
<div id="inner-element">
<div class="store-brand">
<div class="store-icon-img">
<img src="<?php echo $avatar ?>" alt="" class="store-icon"> </div>

</div>
<div class="store-info">
<h3><?php echo $shop_name ?></h3>

<p></p>
</div>
</div>
</div>
</div>
     </div>

<div class="owner-content">
<!-- <div class="shop-sidebar">
<aside class="show-owner-widget widget">
<div class="owner-info">
<h3>Shop Owner</h3>

<div class="inner-avatar-wrap owner-avatar author-follow">
<a class="boss-avatar-container" href="#">
<img alt="" src="<?php echo $avatar ?>" class="avatar avatar-90 photo" height="90" width="90" loading="lazy"> </a>
</div>
<a href="#" class="owner-name"><?php echo $user_name ?></a>
<div class="shop-rating">
</div>

</div>

</aside>



</div> -->

<div class="shop-main-area">
<div class="woocommerce-notices-wrapper"></div><div class="store-filters table">
<form id="search-shops" role="search" action="" method="get" class="table-cell page-search">
<input type="text" name="st_store_search" placeholder="Search in this shop" value="">
<input type="submit" alt="Search" value="Search">
</form>

</div>
<?php
if(isset($_GET['st_store_search']))
{
$searckey=$_GET['st_store_search'];
}
else
{
  $searckey='';
}

 if(empty($searckey))
{
?>
<p class="st-result-count">
Showing all <?php echo $st_user_products->count ?> results</p>
<ul class="products columns-4">
  <?php 
 if($st_user_products->count==0)
    {
        echo '<div><h3>No product found on this store</h3></div>';
    }
  foreach($st_user_products->products as $key=>$value): ?>
<li class="product type-product">
<div class="st-product-outher">
<div class="st-product-inner">
<div class="loop-product-image">
 <a class="image-link" href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<img width="297" height="330" src="<?php echo $value->primaryImageSrc->imageSrc ?>" class="attachment-st-product-archive size-st-product-archive wp-post-image" sizes="(max-width: 297px) 100vw, 297px"> </a>

</div>

<a href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<h2 class="st-loop-product__title"><?php echo $value->title ?></h2>
<h3 class="st-myshop-prod-vendor"><?php echo $value->vendorName ?></h3>
<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="st-Price-currencySymbol"><?php echo "{$value->variants[0]->discountedPriceAsMoney->currency}" ?></span><?php echo "{$value->variants[0]->discountedPriceAsMoney->amount}" ?></bdi></span></span>
</a>
</div>
</div>
</li>
<?php endforeach; ?>

</ul>
<?php }
else
{ ?>
  <p class="st-result-count">
Showing serch results for <?php echo $searckey ?></p>
<ul class="products columns-4">
  <?php foreach($st_user_products->products as $key=>$value): 
if(stripos($value->title,$searckey) === false)
 {
  
  continue;  }
    ?>

<li class="product type-product">
<div class="st-product-outher">
<div class="st-product-inner">
<div class="loop-product-image">
 <a class="image-link" href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<img width="297" height="330" src="<?php echo $value->primaryImageSrc->imageSrc ?>" class="attachment-st-product-archive size-st-product-archive wp-post-image" sizes="(max-width: 297px) 100vw, 297px"> </a>

</div>

<a href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<h2 class="st-loop-product__title"><?php echo $value->title ?></h2>
<h3 class="st-myshop-prod-vendor"><?php echo $value->vendorName ?></h3>
<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="st-Price-currencySymbol"><?php echo "{$value->variants[0]->discountedPriceAsMoney->currency}" ?></span><?php echo "{$value->variants[0]->discountedPriceAsMoney->amount}" ?></bdi></span></span>
</a>
</div>
</div>
</li>
<?php endforeach; ?>

</ul>
<?php } ?>
</div>

</div>
</div>
</div>
</div>



  
<?php
function pagemyshop_enqueue_style() {
    wp_enqueue_style( 'my-shop-css', plugin_dir_url( __FILE__ ) . '/css/st-my-shop.css' );
}

add_action( 'wp_enqueue_scripts', 'pagemyshop_enqueue_style' );

get_footer();