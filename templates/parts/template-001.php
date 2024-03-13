<?php
/*
 * Template name: my-shop-templet-01
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */

global $stCurrency;


$mini_store = $args['mini_store'];

add_action('wp_head', function () use ($st_user_products) {
      $description = substr($st_user_products->shop_bio, 0, 160);
      echo "<meta name='description' content='$description'>";
      echo "<meta property='og:title' content='$st_user_products->shop_name' />";
      echo "<meta property='og:description' content='$st_user_products->shop_bio' />";
      echo "<meta property='og:image' content='{$st_user_products->avatar}' />";
    }, 1);
   
wp_enqueue_style( 'my-shop-css', st_locate_file('css/st-my-shop.css' ));

ob_start();
get_header('shop');
$header = ob_get_clean();
$header = preg_replace('#<title>(.*?)<\/title>#', "<title>$mini_store->name</title>", $header);
echo $header;


$cover=(empty($mini_store->attributes->BG_img)) ? st_locate_file('images/shop-banner.jpg') : $mini_store->attributes->BG_img ;

$user_name= $mini_store->attributes->username;
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
        <div id="inner-element">
          <div class="store-container">
            <div class="store-brand-container">
              <div class="store-icon">
                <a href="<?php echo $mini_store->domain ?>">
                  <img src="<?php echo $mini_store->attributes->profile_img ?>" alt="" class="store-icon-image">
                </a>
              </div>
            </div>
            <div class="store-info-container">
              <div class="store-info">
                <h3 class="store-name"><?php echo $mini_store->name ?></h3>
                <p class="store-bio"><?php echo $mini_store->attributes->bio ?></p>
              </div>
              <div class="show-owner-widget widget">
                <div class="owner-info">
                  <h3>Shop Owner</h3>
                  <div class="inner-avatar-wrap owner-avatar author-follow">
                    <a class="boss-avatar-container" href="/members/<?php echo $mini_store->attributes->username ?>">
                      <img alt="" src="<?php echo $mini_store->attributes->user_img ?>" class="avatar avatar-90 photo" height="90" width="90" loading="lazy">
                    </a>
                  </div>
                  <a href="/members/<?php echo $mini_store->attributes->user_nickname ?>" class="owner-name"><?php echo $mini_store->attributes->username ?></a>
                  <div class="shop-rating"></div>
                </div>
                <a class="send-message" href="/members/me/messages/compose/?r=<?php echo $mini_store->attributes->user_nickname ?>" data-next="" title="Send a private message to <?php echo $mini_store->attributes->username ?>.">Ask me a question</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="owner-content">
	<div class="shop-main">
      <div class="woocommerce-notices-wrapper"></div>
		<ul class="products columns-4">
		  <?php 
			if(count($mini_store->product_details)==0){
				echo '<div><h3>No product found on this store</h3></div>';
			}
			foreach($mini_store->product_details as $value): ?>
			<li class="product type-product">
				<div class="st-product-outher">
				<div class="st-product-inner">
					<div class="loop-product-image">
						 <a class="image-link" href="<?php echo "/products/{$value->id}/?tid={$mini_store->tid}" ?>">
							<img width="297" height="330" src="<?php echo $value->primaryImageSrc->imageSrc ?>" class="attachment-st-product-archive size-st-product-archive wp-post-image" sizes="(max-width: 297px) 100vw, 297px"> 
						</a>
					</div>

					<a class="product-details" href="<?php echo "/products/{$value->id}/?tid={$mini_store->tid}" ?>">
						<h2 class="st-loop-product__title"><?php echo $value->title ?></h2>
						<span class="price">
							<span class="woocommerce-Price-amount amount">
								<bdi><span class="st-Price-currencySymbol"><?php echo "{$value->variants[0]->discountedPriceAsMoney->currency}" ?></span><?php echo "{$value->variants[0]->discountedPriceAsMoney->amount}" ?></bdi>
							</span>
						</span>
					</a>
				</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
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
img.store-banner{height: 348px;border-radius: 8px;position: unset}
.st-store-banner-wrap{margin: 0;padding: 0;width: 100%;position: unset}
.store-container{margin-bottom: 60px;margin-top: 30px;display: flex;flex-direction: row}
.store-info-container{margin-left: 30px;min-width:100%}
.owner-info h3{display: none}
.st-header-container #inner-element .store-info{width: 100%;padding: 0;position: unset}
h3.store-name{padding: 0 !important;font-family: Popine, sans-serif;font-style: normal;font-weight: 700;font-size: 40px;line-height: 110%;display: flex;align-items: center;text-align: center;color: #1e1e1e;margin: 0}
p.store-bio{margin-bottom: 0 !important;font-style: normal;font-weight: 400;font-size: 14px;line-height: 160%;display: flex;align-items: center;color: #1e1e1e;font-family: Popine, sans-serif;padding-bottom: 0 !important;padding-top: 8px}
.owner-info{display: flex;justify-content: center;align-items: center;align-content: center}
.show-owner-widget.widget{margin-top: 1px;display: flex;justify-content: center;align-items: center}
img.avatar.avatar-90.photo{width: 32px;height: 32px;margin: 0;padding: 0}
a.owner-name{font-style: normal;font-weight: 600;font-size: 18px;line-height: 110%;display: flex;align-items: center;color: #1e1e1e;margin: 0 !important;padding-left: 16px;padding-right: 16px;font-family: Popine, sans-serif}
a.send-message{font-family: Popine, sans-serif;font-style: normal;font-weight: 600;font-size: 18px;line-height: 110%;display: flex;align-items: center;text-decoration-line: underline;color: #f99a42}
.owner-content{overflow: hidden;display: flex;flex-direction: column;align-items: center;padding: 48px 0 80px;gap: 72px;min-width: 100vw;background: #f8f5ec;width: 100vw !important;position: relative;left: 50% !important;right: 50% !important;margin-left: -50vw !important;margin-right: -50vw !important}
p.st-result-count{float: right}
div#page{overflow: hidden}
.shop-main{max-width: 1288px;width: 100%}
#search-shops .search-container{display: flex;align-items: center;gap: 10px;width: 413px;height: 50px;background: #fff}
#search-shops input[type="text"]{flex: 1;padding: 10px;border: none;font-family: popin;font-style: normal;font-weight: 400;font-size: 16px;line-height: 160%;color: #1e1e1e;opacity: 0.5}
#search-shops button[type="submit"]{display: flex;justify-content: center;align-items: center;width: 50px;height: 50px;background-color: transparent;border: none;cursor: pointer}
#search-shops button[type="submit"] i{font-size: 20px;color: #1e1e1e}
#search-shops input[type="text"]::placeholder{font-family: popin;font-style: normal;font-weight: 400;font-size: 16px;line-height: 160%;display: flex;align-items: center;color: #1e1e1e;opacity: 0.5}
form#search-shops{border: none}
div#main{overflow: visible}
.store-filters.table{display: flex;justify-content: space-between;align-items: center;align-content: center;margin-bottom: 40px}
p.st-result-count{font-family: Popine, sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 160%;display: flex;align-items: center;text-align: center;color: #1e1e1e;margin: 0 !important;padding: 0;float: none}
ul.products.columns-4{display: flex;flex-wrap: wrap;justify-content: start;gap: 20px}
.st-product-outher{width: 305px;padding:10px;border-radius: 8px}
h2.st-loop-product__title{margin-top: 12px !important;font-style: normal;font-weight: 400;font-size: 18px;line-height: 120%;display: flex;align-items: center;color: #1e1e1e;font-family: Popine, sans-serif}
bdi{margin-top: 15px;font-style: normal;font-weight: 500;font-size: 16px;line-height: 130%;display: flex;align-items: center;color: #075ade;font-family: Popine, sans-serif}
.loop-product-image{align-content: center;display: flex;width: 100%;height: 219px;background: #fff;border-radius: 8px;justify-content: center;align-items: center;max-height: 219px !important}
img.attachment-st-product-archive.size-st-product-archive.wp-post-image{align-items: center;width: auto !important;height: 176px;margin: auto;text-align: center;display: flex;align-content: center}
@media only screen and (max-width: 767px){
	.store-container{display: flex;flex-direction: column;align-items: center}
	.store-icon{margin-bottom: 20px}
	.store-info-container{margin-left: 0;display: flex;flex-direction: column;align-items: center}
	.show-owner-widget{margin-top: 20px}
	.show-owner-widget.widget{flex-direction: column}
	.st-header-container #inner-element .store-info{display: flex;flex-direction: column;justify-content: center;text-align: center;width: 100%;align-items: center}
	.owner-content{display: flex;flex-direction: column;align-items: center;padding: 20px;gap: 32px;background: #f8f5ec;max-width: 100%}
	form#search-shops{max-width: 70%}
	.search-container{max-width: 85%}
	ul.products.columns-4{justify-content: center}
}
@media screen and (max-width: 1300px) and (min-width: 770px){
	.shop-main{padding-right: 30px;padding-left: 30px}
}
@media only screen and (max-width: 767px){
	h3.store-name{display: block;white-space: normal;white-space: wrap;text-align: center;word-break: break-all}
}

 </style>
