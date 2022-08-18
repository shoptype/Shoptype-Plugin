<?php
// ..............................
// Create shortcode for coseller shop list
// ..............................
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
$path = dirname(plugin_dir_url( __FILE__ ));

//short code [st-user-shop-box shop_id='email']
function st_user_shop_box($atts) {
    $default = array(
        'shop_id' => '#',
    );
    //getting value from shortcode
    $st_shop = shortcode_atts($default, $atts);
    
$path = dirname(plugin_dir_url( __FILE__ ));
if(empty($userName)){
}
try {
  $ch = curl_init();
  $urlparts = parse_url(home_url());
  $domain = $urlparts['host'];
  curl_setopt($ch, CURLOPT_URL, "https://$domain/wp-json/shoptype/v1/shop/".$st_shop['shop_id']);
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

//getting wordpress user by user email address
$user = get_user_by( 'email', $st_shop['shop_id'] );

if(!isset($user->ID))
{
  $user = get_user_by( 'login',$st_shop['shop_id']  );

}

$cover=(empty($st_user_products->cover)) ? '' : $st_user_products->cover ;
$avatar=(isset($user->ID))? get_avatar_url( $user->ID ) : $path.'/images/shop-profile.jpg';
if (str_contains($avatar, 'gravatar.com')) { 
   $avatar= $path.'/images/shop-profile.jpg';
}
$user_name= $user->display_name;
$shop_name=(empty($st_user_products->shop_name))? $user_name.'- store' : $st_user_products->shop_name;

$shop_page='/shop/'.$st_shop['shop_id'];  
?>
    <div class="st-feat_seller">
<div class="inner-wrap">
<div class="avatar">

<a href="<?php echo $shop_page ?>"><img alt="" src="<?php echo $avatar ?>" class="avatar avatar-50 photo" height="50" width="50" loading="lazy"></a>
</div>
<div class="shop-details">
<div class="name">
<a href="<?php echo $shop_page ?>"><?php echo $user_name ?></a>
</div>
<div class="details">

</div>


<div class="table seller-shop-desc">
<div class="table-cell owner-avatar">
<a href="<?php echo $shop_page ?>">
<img src="<?php echo $cover ?>" alt="" class="store-icon" style="max-width:100%;"></a>
</div>
<div class="table-cell owner-name">
<span>Shop</span>
<a href="<?php echo $shop_page ?>"><?php echo $shop_name ?></a>
</div>
</div>
</div>
</div>
<div class="seller-shop-products">

    
    <?php
    //show no product found on 0 peoducts
    if($st_user_products->count==0)
    {
        echo '<div><h3>No product found</h3></div>';
    }
    $count_product=0;
    

     foreach($st_user_products->products as $key=>$value): ?>
        <a href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<img width="135" height="150" src="<?php echo $value->primaryImageSrc->imageSrc ?>"  class="attachment-bm-store-archive size-bm-store-archive wp-post-image" alt="" loading="lazy" sizes="(max-width: 135px) 100vw, 135px"> </a>



<?php 
$count_product++;
if($count_product==3){ break;} //show only 13 product per shop

endforeach; 


if($st_user_products->count > 3)
{
?>
<a href="<?php echo $shop_page?>" class="overlay">

<div class="product-count">
<div class="table prodcount" style='background:#ffffff'>
<div class="table-cell">
<span class="number"><?php echo $st_user_products->count ?></span>
<span class="text">items</span>
</div>
</div>
</div>
</a>


<?php } ?>
</div>
</div>

    <style>
.prodcount
		{
			min-height: 65px;
    background: #ffffff !important;
    justify-content: center;
    display: flex !important;
		}
 .st-feat_seller {
    width: 33.3333%;
    padding: 0 15px;
    float: left;
    margin-bottom: 30px;
}
.st-feat_seller .inner-wrap {
    background-color: #fff;
    border: 1px solid #e1e1e1;
    padding: 25px 25px 30px;
    position: relative;
    min-height: 273px;
}
.st-feat_seller .avatar {
    float: left;
    width: 50px;
    border-radius: 50%;
}
.st-feat_seller .shop-details {
    margin-left: 75px;
}
.st-feat_seller .shop-details .name {
    margin-top: 8px;
}
.st-feat_seller .shop-details .name a {
    font-size: 20px;
    color: #333;
    font-weight: 600;
}
.st-feat_seller .shop-details .details {
    margin-top: 15px;
    margin-bottom: 25px;
}
.table {
    display: table;
    width: 100%;
}
.st-feat_seller .seller-shop-desc .owner-avatar {
    padding-right: 12px;
    width: 42px;
}
.table-cell {
    display: table-cell;
    vertical-align: middle;
}
.seller-shop-desc .owner-avatar a img {
    width: 30px;
    display: block;
    max-width: none!important;
    width: 30px;
    height: 30px;
    border-radius: 50%;
}
.entry-summary .about-owner span, .seller-shop-desc .owner-name span, .store-item .about-owner span {
    font-size: 10px;
    color: rgba(0,0,0,.6);
    display: block;
    line-height: 1;
}
 .seller-shop-desc .owner-name a {
    line-height: 1;
    display: block;
    margin-top: 2px;
}
.st-feat_seller>.seller-shop-products {
    margin-left: -1px;
    margin-right: -1px;
}
.seller-shop-products>a, .seller-shop-products>div {
    width: 25%;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 25%;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
.seller-shop-products>a
{
    display: block;
    float: left;
    padding: 1px;
    -webkit-transition: all .15s ease-in-out;
    -moz-transition: all .15s ease-in-out;
    -o-transition: all .15s ease-in-out;
    transition: all .15s ease-in-out;
}
.seller-shop-products>img
{
max-width: 100%;
    height: auto;
}
.table-cell {
    display: table-cell;
    vertical-align: middle;
}
.seller-shop-products .product-count .number
{
        font-size: 28px;
    color: #54ae68;
    line-height: 1.2;
    display: block;
}
.product-count .text {
    display: block;
    color: rgba(0,0,0,.6);
}
.seller-shop-products .product-count .table, .store-products .product-count .table {
    height: 100%;
    background-color: #f9f9f9;
}
.seller-shop-products .product-count .overlay, .store-products .product-count .overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    text-align: center;
    padding: 5px;
}
.seller-shop-products .product-count, .store-products .product-count, article.store-item .store-desc, article.store-item .store-products>a {
    position: relative;
}
.seller-shop-products>a, .seller-shop-products>div {
    width: 25%;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 25%;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
.seller-shop-products>a, .seller-shop-products>div {
    width: 25%;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 25%;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
.seller-shop-products .product-count, .store-products .product-count, article.store-item .store-desc, article.store-item .store-products>a {
    position: relative;
}
@media screen and (max-width: 667px)
{
.st-feat_seller {
    width: 100%;
}
.seller-shop-products {
    width: 100%;
    display: block;
}
.seller-shop-products>a, .seller-shop-products>div {
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 33.3333%;
    -ms-flex: 0 0 33.3333%;
    flex: 0 0 33.3333%;
    max-width: 33.3333%;
}
.seller-shop-products>div
{
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 33.3333%;
    -ms-flex: 0 0 33.3333%;
    flex: 0 0 33.3333%;
    max-width: 33.3333%;
}
}

    </style>

    <?php



    return '';
}
add_shortcode('st-user-shop-box', 'st_user_shop_box');