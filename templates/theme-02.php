<?php
/*
 * Template name: my-shop-templt
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
  $ch = curl_init();
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
  curl_setopt($ch, CURLOPT_URL, "https://$domain/wp-json/shoptype/v1/shop/".$userName."?count=100");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  if( !empty( $result ) ) {
    $st_user_products = json_decode($result);
  }else{
  }
}
catch(Exception $e) {

}
add_action('wp_head', function () use ($st_user_products) {
      $description = substr($st_user_products->shop_bio, 0, 160);
      echo "<meta name='description' content='$st_user_products->shop_bio'/>";
      echo "<meta property='og:title' content='$st_user_products->shop_name' />";
      echo "<meta property='og:description' content='$st_user_products->shop_bio' />";
      echo "<meta property='og:image' content='{$st_user_products->avatar}' />";
    }, 0);

    wp_enqueue_style( 'my-shop-css', $path . '/css/st-my-shop.css' );

add_filter('pre_get_document_title', 'custom_title');
function custom_title($title) {
     return 'Test New Title';
}

ob_start();
get_header('shop');
$header = ob_get_clean();
$header = preg_replace('#<title>(.*?)<\/title>#', "<title>$st_user_products->shop_name</title>", $header);
echo $header;

$user = get_user_by( 'login',$userName );
if(!isset($user->ID))
{
  $user = get_user_by( 'email',$userName );
}

$cover=(empty($st_user_products->cover)) ? st_locate_file('images/shop-banner.jpg') : $st_user_products->cover ;

$user_name= $user->display_name;
$shop_name=(empty($st_user_products->shop_name))? $user_name.' store' : $st_user_products->shop_name;
$shop_bio = xprofile_get_field_data( 'st_shop_bio' , $user->id );

?>
  <div class='wrapper my-shop-wrapper' id='main'>
    <div id="content-main">
    <div class="st-myshop-head">
  <div class="st-header-container">
    <div class="st-store-grid st-store-header">
      <div id="banner-wrap">
        <div class="st-store-banner-wrap">
          <img src='<?php echo $cover ?>' alt="" class="store-banner">
        </div>
        <div class="main-owner-container">
        <div id="inner-element">
          <div class="store-container">
            <div class="store-brand-container">
              <div class="store-icon">
                <a href="/shop/<?php echo $st_user_products->shop_url ?>">
                  <img src="<?php echo $st_user_products->avatar ?>" alt="" class="store-icon-image">
                </a>
              </div>
            </div>
            <div class="store-info-container">
              <div class="store-info">
                <h3 class="store-name"><?php echo $st_user_products->shop_name ?></h3>
                <p class="store-bio"><?php echo $st_user_products->shop_bio ?></p>
              </div>
              <div class="show-owner-widget widget">
                <div class="owner-info">
                  <h3>Shop Owner</h3>
                  <div class="inner-avatar-wrap owner-avatar author-follow">
                    <a class="boss-avatar-container" href="/members/<?php echo $st_user_products->user_nicename ?>">
                      <img alt="" src="<?php echo $st_user_products->user_avatar ?>" class="avatar avatar-90 photo" height="90" width="90" loading="lazy">
                    </a>
                  </div>
                  <a href="/members/<?php echo $st_user_products->user_nicename ?>" class="owner-name"><?php echo $st_user_products->user_name ?></a>
                  <div class="shop-rating"></div>
                </div>
                <div class="profile-buttons">
                  <a href="/members/<?php echo $st_user_products->user_nicename ?>" class="visit-profile">Visit Profile</a>
                  <div class="shop-rating"></div>
					<?php if ( bp_is_active( 'messages' ) ){ ?>
                	<a class="send-message" href="/members/me/messages/compose/?r=<?php echo $st_user_products->user_nicename ?>" data-next="" title="Send a private message to <?php echo $st_user_products->user_name ?>.">Ask a question</a>
					<?php } ?>
              </div>
              </div>
            </div>
          </div>
        </div>
        <div class="owner-content">
      <div class="shop-main">
      <div class="woocommerce-notices-wrapper"></div>

		<div class="store-filters table">
			<div>
				<form id="search-shops" role="search" action="" method="get" class="table-cell page-search">
				  <div class="search-container">
					<input type="text" name="st_store_search" placeholder="Search in this shop" value="">
					<button type="submit" aria-label="search">
					  <img src="<?php echo $path ?>/images/search.svg">
					</button>
				  </div>
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
			<div>
				<p class="st-result-count">
				Showing all <?php echo $st_user_products->count ?> results</p>
			</div>
		</div>

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
		  <div><p class="st-result-count">
		Showing serch results for <?php echo $searckey ?></p></div>
		</div>
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
</div>



</div>
</div>
<script>
	function changeSocial(){
		document.querySelector("a.elementor-social-icon-facebook").href="<?php echo $st_user_products->facebook; ?>";
		document.querySelector("a.elementor-social-icon-twitter").href="<?php echo $st_user_products->twitter; ?>";
		document.querySelector("a.elementor-social-icon-youtube").href="<?php echo $st_user_products->youtube; ?>";
		document.querySelector("a.elementor-social-icon-instagram").href="<?php echo $st_user_products->instagram; ?>";	
		document.querySelector("a.elementor-social-icon-linkedin").style.display="none";	
	}
	
	window.addEventListener("DOMContentLoaded", (event) => {
		changeSocial();
	});
</script>

<style>
img.store-banner {height: 348px;position: unset;}
.st-store-banner-wrap {margin: 0px;padding: 0px;width: 100%;position: unset;}
div#banner-wrap {width: 100%;min-width: 100%;}
.store-container {margin: 30px 0px 60px 20px;display: flex;flex-direction: row;}
.owner-info h3 {display: none;}
.main-owner-container {max-width: 1240px;margin: auto;}
.st-header-container #inner-element .store-info {margin: 10px 0px 20px !important; display:flex;flex-direction:column;justify-content:center;width: 100%;}
h3.store-name {padding: 0px !important;font-family: "Popine", sans-serif;font-style: normal;font-weight: 700;font-size: 36px;line-height: 110%;display: flex;align-items: center;text-align: center;color: #1E1E1E;margin: 0px;overflow-wrap: anywhere;}
 p.store-bio {margin-bottom: 0px !important;font-style: normal;font-weight: 400;font-size: 14px;line-height: 160%;display: flex;align-items: center;color: #1E1E1E;font-family: "Popine", sans-serif;padding-bottom: 0px !important;padding-top: 8px;}
.owner-info {display: flex;justify-content: center;align-items: center;align-content: center;}
.show-owner-widget.widget {margin-top: 1.px;display: flex;justify-content: center;align-items: center;}
.inner-avatar-wrap.owner-avatar.author-follow {max-width: 32px;max-height: 32px;}
img.avatar.avatar-90.photo {width: 32px;height: 32px;margin: 0px;padding: 0px;}
a.owner-name {font-style: normal;font-weight: 600;font-size: 18px;line-height: 110%;display: flex;align-items: center;color: #1E1E1E;margin: 0px !important;padding-left: 16px;padding-right: 16px;font-family: "Popine", sans-serif;}
a.send-message {font-family: "Popine", sans-serif;font-style: normal;font-weight: 600;font-size: 18px;line-height: 110%;display: flex;align-items: center;text-decoration-line: underline;color: #F99A42;}
.store-icon {border-radius: 50%;width: 128px;height: 128px;left: 0px;top: 0px;background: #FFFFFF;border: 0.761905px solid rgba(0, 0, 0, 0.1);}
.store-icon {width: 128px;height: 128px;left: 0px;top: 0px;background: #FFFFFF;border: 0.761905px solid rgba(0, 0, 0, 0.1);}
.store-icon {max-width: 128px;max-height: 128px;left: 0px;top: 0px;background: #FFFFFF;border: 0.761905px solid rgba(0, 0, 0, 0.1);}
div#page {overflow: hidden;}
.shop-main {max-width: 1288px;width: 100%;}
#search-shops .search-container {display: flex;align-items: center;gap: 10px;width: 125%;height: 50px;background: #FFFFFF;}
#search-shops button[type="submit"] {display: flex;justify-content: center;align-items: center;width: 50px;height: 50px;background-color: transparent;border: none;cursor: pointer;}
#search-shops button[type="submit"] i {font-size: 20px;color: #1E1E1E;}
form#search-shops {border: none;}
div#main {overflow: visible;}
form#search-shops {float: left;width: fit-content;}
.owner-content {overflow: hidden;display: flex;flex-direction: column;align-items: center;padding: 48px 0px 80px;background: #fff;}
.st-header-container #inner-element {width: 320px;}
div#page {overflow: hidden;}
.shop-main {max-width: 1288px;width: 100%;}
#search-shops .search-container {display: flex;align-items: center;gap: 10px;width: 413px;height: 50px;background: #FFFFFF;}
#search-shops button[type="submit"] {display: flex;justify-content: center;align-items: center;width: 50px;height: 50px;background-color: transparent;border: none;cursor: pointer;}
#search-shops button[type="submit"] i {font-size: 20px;color: #1E1E1E;}
#search-shops input[type="text"]::placeholder {font-family: popin;font-size: 16px;color: #1E1E1E;opacity: 0.8;}
form#search-shops {border: none;}
div#main {overflow: visible;}
.store-filters.table {display: flex;justify-content: space-between;align-items: center;align-content: center;margin-bottom: 40px;}
p.st-result-count {font-family: "Popine", sans-serif;font-size: 16px;color: #1E1E1E;margin: 0px 10px;}
ul.products.columns-4 {display: flex;flex-wrap: wrap;justify-content: space-evenly;}
.loop-product-image {background: #FFFFFF;border-radius: 8px;}
.st-product-outher {width: 280px;background: #FFFFFF;border-radius: 8px;padding: 10px;}
li.product.type-product {margin: 0px 0px 40px;background: transparent; display:flex}
a.product-details {padding-top: 12px;background: transparent indianred;background-color: transparent;}
h2.st-loop-product__title {margin-top: 12px !important;font-style: normal;font-weight: 400;font-size: 18px;line-height: 120%;display: flex;align-items: center;color: #1E1E1E;font-family: "Popine", sans-serif;}
bdi {margin-top: 15px;font-style: normal;font-weight: 500;font-size: 16px;line-height: 130%;display: flex;align-items: center;color: #075ADE;font-family: "Popine", sans-serif;}
.st-product-outher {background: transparent;}
.loop-product-image {align-content: center;display: flex;height: 219px;background: #FFFFFF;border-radius: 8px;justify-content: center;align-items: center;max-height: 219px !important;}
img.attachment-st-product-archive.size-st-product-archive.wp-post-image {align-items: center;width: auto;height: 176px;margin: auto;text-align: center;display: flex;align-content: center;}
.shop-main-area .st-loop-product__title, ul.products li.product h3 {height: auto;}
.powerdbytext {font-style: normal;font-weight: 500;font-size: 16px;line-height: 160%;display: flex;align-items: center;color: #1E1E1E;opacity: 0.3;font-family: "Popine", sans-serif;}
.my-shop-post {background:#F8F5EC;}
.my-shop-post, .full-width {margin-left: calc(50% - 50vw);width: 100vw;}
@media only screen and (max-width: 767px) {
    .store-container {display: flex;flex-direction: column;align-items: center;}
    .store-icon {margin-bottom: 20px;}
    .store-info-container {display: flex;flex-direction: column;align-items: center;}
    .owner-info {display: flex;flex-direction: row;align-items: center;}
    .show-owner-widget {margin-top: 20px;}
    .show-owner-widget.widget {flex-direction: column;}
    h3.store-name {text-align: center;}
    .st-header-container {height: auto;}
    .st-header-container #inner-element {margin-bottom: 0px; width: 100%;}
    .owner-content {display: flex;flex-direction: column;align-items: center;padding: 20px;gap: 32px;}
    form#search-shops {max-width: 90%;}
    .search-container {max-width: 85%;}
    ul.products.columns-4 {justify-content: center;}
}
@media only screen and (min-width: 768px) {
    .store-container {justify-content: center;display: flex;flex-direction: column;align-items: center;padding: 32px 24px;gap: 32px;position: relative;width: 100%;top: -77px;background: #F8F5EC;border-radius: 16px;}
    .st-store-banner-wrap {width: 100vw !important;position: relative !important;left: 50% !important;right: 50% !important;margin-left: -50vw !important;margin-right: -50vw !important;}
    img.store-banner {min-height: 403px !important;}
    .store-info-container {display: flex;flex-direction: column;justify-content: center;align-content: center;}
    .show-owner-widget.widget {flex-direction: column;}
    .store-info {display: flex;justify-content: center;align-items: center;}
    h3.store-name {text-align: center;}
    .store-info {display: flex;justify-content: center;align-items: center;align-content: center;}
    .store-info {margin-left: 25% !important;}
    .powerdbytext {text-align: center;display: flex;justify-content: center;}
    .shop-main {width: 100%;background: transparent;}
    .main-owner-container {display: flex;flex-direction: row;}
    .store-container:before {display: none;}
    .owner-content {position: unset;width: calc(100% - 320px);}
    ul.products.columns-4 {gap: 0px;}
    .st-header-container #inner-element:before {display: none !important;}
    .profile-buttons {display: flex;gap: 5px;margin-top: 46px;}
    a.visit-profile {font-style: normal;font-weight: 700;font-size: 16px;line-height: 20px;display: flex;align-items: center;text-align: center;color: #FFFFFF;display: flex;flex-direction: row;align-items: flex-start;padding: 14px 24px;background: #F99A42;border-radius: 50px;}
    a.send-message {font-style: normal;font-weight: 700;font-size: 16px;line-height: 20px;display: flex;align-items: center;text-align: center;color: #1E1E1E;display: flex;flex-direction: row;align-items: flex-start;padding: 14px 24px;gap: 8px;border: 1px solid #1E1E1E;border-radius: 50px;text-decoration: none;}
}
@media screen and (max-width: 1300px) and (min-width: 770px) {
    .shop-main {padding-right: 30px;padding-left: 30px;}
}

@media only screen and (max-width: 767px) {
    h3.store-name {display: block;white-space: normal;white-space: wrap;text-align: center;word-break: break-all;}
}

</style>

<?php
get_footer('shop');