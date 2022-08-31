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

$userName = urldecode(get_query_var( 'shop' ));

try {
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
 
  $response = wp_remote_get("https://$domain/wp-json/shoptype/v1/shop/".$userName."?count=100");
  $result = wp_remote_retrieve_body( $response );
 
  if( !empty( $result ) ) {
    $st_user_products = json_decode($result);
  }else{
  }
}
catch(Exception $e) {

}
add_action('wp_head', function () use ($st_product) {
      $description = substr($st_user_products->shop_bio, 0, 160);
      echo "<meta name='description' content='$description'>";
      echo "<meta property='og:title' content='$st_user_products->shop_name' />";
      echo "<meta property='og:description' content='$st_user_products->shop_bio' />";
      echo "<meta property='og:image' content='{$st_user_products->primaryImageSrc->avatar}' />";
    }, 1);

switch ($st_user_products->theme) {
  case "theme-02":
  //echo "<link href='".$path ."/css/st-my-shop-fw.css'/>";
  wp_enqueue_style( 'my-shop-css-2', $path . '/css/st-my-shop-fw.css' );
    break;
  default:
  //echo "<link href='".$path ."/css/st-my-shop.css'/>";
    wp_enqueue_style( 'my-shop-css', $path . '/css/st-my-shop.css' );
}

get_header(null);
$user = get_user_by( 'login',$userName );
if(!isset($user->ID))
{
  $user = get_user_by( 'email',$userName );
}

$cover=(empty($st_user_products->cover)) ? $path.'/images/shop-banner.jpg' : $st_user_products->cover ;

$user_name= $user->display_name;
$shop_name=(empty($st_user_products->shop_name))? $user_name.' store' : $st_user_products->shop_name;
$shop_bio = xprofile_get_field_data( 'st_shop_bio' , $user->id );

?>
  <div class='wrapper' id='main'>
    <div id="content">
    <div class="st-myshop-head">
     <div class="st-header-container">
      <div class="st-store-grid st-store-header">
<div id="banner-wrap">
<div class="st-store-banner-wrap"><img src='<?php echo $cover ?>' alt="" class="store-banner"></div>
<div id="inner-element">
<div class="store-brand">
<div class="store-icon-img">
  <a href="/shop/<?php echo $user_name ?>"><img src="<?php echo $st_user_products->avatar ?>" alt="" class="store-icon"></a>
</div>

</div>
<div class="store-info">
<h3><?php echo $st_user_products->shop_name ?></h3>

<p><?php echo $st_user_products->shop_bio ?></p>
</div>
</div>
</div>
</div>
     </div>

<div class="owner-content">
<div class="shop-sidebar">
<aside class="show-owner-widget widget">
<div class="owner-info">
<h3>Shop Owner</h3>
<div class="inner-avatar-wrap owner-avatar author-follow">
<a class="boss-avatar-container" href="/members/<?php echo $userName ?>">
<img alt="" src="<?php echo $st_user_products->user_avatar ?>" class="avatar avatar-90 photo" height="90" width="90" loading="lazy"> </a>
</div>
<a href="/members/<?php echo $userName ?>" class="owner-name"><?php echo $st_user_products->user_name ?></a>
<div class="shop-rating">
</div>
</div>
<a class="send-message" href="/members/me/messages/compose/?r=<?php echo $userName ?>" data-next="" title="Send a private message to <?php echo $st_user_products->user_name ?>.">Ask a question</a>
</aside>



</div>
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

<a class="product-details" href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<h2 class="st-loop-product__title"><?php echo $value->title ?></h2>
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

<a class="product-details" href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
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

get_footer();