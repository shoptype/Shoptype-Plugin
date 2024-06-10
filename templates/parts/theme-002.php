<?php
/*
 * Template name: my-shop-templet-002
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */

global $stCurrency;

$mini_store = $data['mini_store'];
$cover=(empty($mini_store->attributes->BG_img)) ? st_locate_file('images/shop-banner.jpg') : $mini_store->attributes->BG_img ;
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
                <a class="send-message" href="/members/me/messages/compose/?r=<?php echo $st_user_products->user_nicename ?>" data-next="" title="Send a private message to <?php echo $st_user_products->user_name ?>.">Ask a question</a>
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
					  <i class="fas fa-search" aria-hidden="true"></i>
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
a.owner-name,h3.store-name,p.store-bio{font-family:Popine,sans-serif}.elementor-14070 .elementor-element.elementor-element-f1d40e2>.elementor-widget-container{width:50px;height:50px;border-radius:50%;z-index:999;position:relative;background:#fff;padding:14px}.directory .bp-docs-container,.wrapper{margin:-105px auto 0!important}img.store-banner{height:348px;border-radius:8px;position:unset}.owner-info h3{display:none}.st-header-container #inner-element .store-info{display:flex;margin:auto;padding:0;justify-content:flex-end;flex-direction:column}h3.store-name{padding:0!important;font-style:normal;font-weight:700;font-size:40px;line-height:110%;display:flex;align-items:center;text-align:center;color:#1e1e1e;margin:0}#search-shops input[type=text],p.store-bio{font-style:normal;font-weight:400;color:#1e1e1e}p.store-bio{margin-bottom:0!important;font-size:14px;line-height:160%;display:flex;align-items:center;padding-bottom:0!important;padding-top:8px}.owner-info{display:flex;justify-content:center;align-items:center;align-content:center}img.avatar.avatar-90.photo{width:32px;height:32px;margin:0;padding:0}a.owner-name{font-style:normal;font-weight:600;font-size:18px;line-height:110%;display:flex;align-items:center;color:#1e1e1e;margin:0!important;padding-left:16px;padding-right:16px}.store-icon{border-radius:50%;width:128px;height:128px;left:0;top:0;background:#fff;border:.761905px solid rgba(0,0,0,.1)}.owner-content{overflow:hidden;display:flex;flex-direction:column;align-items:center;padding:48px 0 80px;background:#fff}#search-shops .search-container{display:flex;align-items:center;gap:10px;width:413px;height:50px;background:#fff}#search-shops input[type=text]{flex:1;padding:10px;border:none;font-family:popin;font-size:16px;line-height:160%;opacity:.5}#search-shops button[type=submit]{display:flex;justify-content:center;align-items:center;width:50px;height:50px;background-color:transparent;border:none;cursor:pointer}#search-shops button[type=submit] i{font-size:20px;color:#1e1e1e}#search-shops input[type=text]::placeholder{font-family:popin;font-style:normal;font-weight:400;font-size:16px;line-height:160%;display:flex;align-items:center;color:#1e1e1e;opacity:.5}form#search-shops{border:none}div#main{overflow:visible}.store-filters.table{display:flex;justify-content:space-between;align-items:center;align-content:center;margin-bottom:40px}h2.st-loop-product__title,p.st-result-count{font-weight:400;align-items:center;color:#1e1e1e;font-style:normal;font-family:Popine,sans-serif;display:flex}p.st-result-count{font-size:16px;line-height:160%;text-align:center;margin:0!important;padding:0;float:none}ul.products.columns-4{display:flex;flex-wrap:wrap;justify-content:space-evenly}.st-product-outher{width:280px;background:#fff;border-radius:8px;padding:10px}h2.st-loop-product__title{margin-top:12px!important;font-size:18px;line-height:120%}bdi{margin-top:15px;font-style:normal;font-weight:500;font-size:16px;line-height:130%;display:flex;align-items:center;color:#075ade;font-family:Popine,sans-serif}.loop-product-image{align-content:center;display:flex;height:219px;background:#fff;border-radius:8px;justify-content:center;align-items:center;max-height:219px!important}img.attachment-st-product-archive.size-st-product-archive.wp-post-image{align-items:center;width:auto;height:176px;margin:auto;text-align:center;display:flex;align-content:center}@media only screen and (max-width:767px){.store-container{display:flex;flex-direction:column;align-items:center}.store-icon{margin-bottom:20px}.store-info-container{margin-left:0;display:flex;flex-direction:column;align-items:center}.st-header-container #inner-element .store-info{display:flex;flex-direction:column;justify-content:center;text-align:center;align-items:center;width:100%}h3.store-name{text-align:center;font-size:28px}.st-header-container{height:auto}.st-header-container #inner-element{margin-bottom:0;width:100%}ul.products.columns-4{justify-content:center}p.store-bio{font-size:14px}.owner-info{display:flex;flex-direction:row;align-items:center}.owner-info .store-icon{margin-right:12px}a.owner-name{font-size:16px}img.avatar.avatar-90.photo{width:24px;height:24px}.show-owner-widget.widget{flex-direction:column}.owner-content{display:flex;flex-direction:column;align-items:center;padding:20px;gap:32px}.show-owner-widget{margin-top:20px}.search-container{max-width:85%}.search-container input[type=text]{width:100%}form#search-shops{max-width:90%}}@media only screen and (min-width:768px){a.send-message,a.visit-profile{font-style:normal;font-weight:700;font-size:16px;line-height:20px;text-align:center;display:flex;padding:14px 24px}.store-container{justify-content:center;display:flex;flex-direction:column;align-items:center;padding:45px 30px;position:relative;display:flex;flex-direction:column;align-items:center;gap:32px;width:100%;background:#f8f5ec;border-radius:16px;top:-90px;max-height:480px;margin-right:40px}.main-owner-container{display:flex;flex-direction:row}.owner-content{position:unset;width:calc(100% - 320px)}.st-header-container #inner-element:before{display:none!important}.profile-buttons{display:flex;gap:5px;margin-top:46px}a.visit-profile{align-items:center;color:#fff;flex-direction:row;align-items:flex-start;background:#f99a42;border-radius:50px}a.send-message{align-items:center;color:#1e1e1e;flex-direction:row;align-items:flex-start;gap:8px;border:1px solid #1e1e1e;border-radius:50px;text-decoration:none}}@media screen and (max-width:1300px) and (min-width:770px){.shop-main{padding-right:30px;padding-left:30px}}</style>