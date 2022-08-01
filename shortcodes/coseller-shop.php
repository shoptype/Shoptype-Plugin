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

//short code [st-user-shop shop_id='email']
function st_user_shop($atts) {
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

$cover=(empty($st_user_products->cover)) ? '' : $st_user_products->cover ;
$avatar=(isset($user->ID))? get_avatar_url( $user->ID ) : $path.'/images/shop-profile.jpg';
$user_name=(isset($user->ID))? $user->display_name : 'Shoptype user';
$shop_name=(empty($st_user_products->shop_name))? $user_name.'- store' : $st_user_products->shop_name;

$shop_page='/shop/'.$st_shop['shop_id'];





    ?>
    <div class="table st-store-item" style="opacity: 1;">
       
<div class="table-cell st-store-desc">
<a href="<?php echo $shop_page?>" class="st-about-store">
 <h3><?php echo $shop_name ?></h3>
<span class="icon-wrap">
<span class="st-side-icon"></span>
</span>
</a>
<div class="st-about-owner">
<div class="table">
<div class="table-cell owner-avatar">
<a href="<?php echo $shop_page?>"><img alt="" src="<?php echo $avatar ?>" class="avatar avatar-40 photo" height="40" width="40" loading="lazy"></a>
</div>
<div class="table-cell owner-name">
<span>Shop Owner</span>
<a href="<?php echo $shop_page?>"><?php echo $user_name ?></a>
</div>
</div>
</div>
</div>
<div class="table-cell st-store-products">
    <?php
    //show no product found on 0 peoducts
    if($st_user_products->count==0)
    {
        echo '<div><h3>No product found</h3></div>';
    }
    $count_product=0;
    

     foreach($st_user_products->products as $key=>$value): ?>
<a href="<?php echo "/products/{$value->id}/?tid={$value->tid}" ?>">
<img width="135" height="150" src="<?php echo $value->primaryImageSrc->imageSrc ?>"  alt="" loading="lazy"> </a>


<?php 
$count_product++;
if($count_product==13){ break;} //show only 13 product per shop

endforeach; 


if($st_user_products->count > 13)
{
?>
<div class="st-product-count">
<img src="">
<a href="<?php echo $shop_page?>" class="overlay">
<div class="table">
<div class="table-cell">
<span class="number"><?php echo $st_user_products->count ?></span>
<span class="text">items</span>
</div>
</div>
</a>
<?php } ?>
</div>

</div>
</div>

    <style>
 .st-store-item {
    
    margin:auto;
    margin-top: 30px;
    max-width: 80vw;
    min-width: 80vw;
    width: 100%;
    margin-bottom: 30px;
    padding: 0;
    opacity: 0;
    -webkit-transition: all .15s ease-in-out;
    -moz-transition: all .15s ease-in-out;
    -o-transition: all .15s ease-in-out;
    transition: all .15s ease-in-out;
    background-color: #f9f8f3;
    display: table;
    display: flex
}
 .st-store-item .st-store-desc {
    min-width: 140px;
    position: relative;
    width: 140px;
    background-color: #fff;
    border: 1px solid #e1e1e1;
}
.st-store-item .st-about-store {
        margin-top: 50px;

    border-bottom: 47px solid transparent;
    text-align: center;
    padding-left: 10px;
    padding-right: 10px;
    display: block;
}
.st-store-item .st-about-store img {
    border-radius: 50%;
    width: 45px;
    height: 45px;
    margin-bottom: 20%;
    vertical-align: middle;
    margin-top: 30%;
}
.st-store-item .st-about-store h3 {
    margin-top: 0px;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
    margin-bottom: 8%;
}
.st-about-store .icon-wrap {
    display: inline-block;
    width: 26px;
    height: 24px;
    line-height: 0;
        padding-bottom: 33%;
}
.st-about-store .icon-wrap .st-side-icon
{
        background-color: #e7753f;
        -webkit-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    transform: rotate(180deg);
    background-color: #54ae68;
    width: 26px;
    display: inline-block;
   
       
    height: 2px;
    position: relative;
    top: 10px;
    -webkit-transition: -webkit-transform .3s;
    transition: transform .3s;
}
.st-about-store .st-side-icon:before
{
    top: 0;
    transform: translateX(0) translateY(0) rotate(-45deg);
    width: 50%;
    background-color: #54ae68;

    -webkit-transform-origin: top left;
    -ms-transform-origin: top left;
    transform-origin: top left;
    -webkit-transition: -webkit-transform .3s,width .3s,top .3s;
    transition: transform .3s,width .3s,top .3s;
    position: absolute;
    display: block;
    left: 0;
    height: 2px;
    content: ' ';
}
.st-about-store .st-side-icon:after
{
    bottom: 0;
    transform: translateX(0) translateY(0) rotate(45deg);
    width: 50%;
    background-color: #54ae68;
    transform-origin: bottom left;
    transform-origin: bottom left;
    -webkit-transition: -webkit-transform .3s,width .3s,bottom .3s;
    transition: transform .3s,width .3s,bottom .3s;
    position: absolute;
    display: block;
    left: 0;
    height: 2px;
    content: ' '
}

.st-store-item .st-about-owner {
    height: 47px;
    width: 100%;
    position: absolute;
    bottom: 0;
    top: calc(100% - 47px);
    left: 0;
    background-color: #fafafa;
    border-top: 1px solid #e1e1e1;
    padding: 8px;
    display: block;
}
.st-about-owner .owner-avatar, .st-seller-shop-desc .owner-avatar {
    display: table-cell;
    vertical-align: middle;
    padding-right: 12px;
    width: 42px;
}
.st-about-owner .table {
    display: table;
    width: 100%;
}
.st-about-owner .table-cell
{
    display: table-cell;
    vertical-align: middle;
}
.st-store-item .st-about-owner img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}
.st-about-owner .owner-name a {
    white-space: nowrap;
    width: 80px;
    text-overflow: ellipsis;
    overflow: hidden;
    font-size: 13px;
    line-height: 1;
    display: block;
    margin-top: 2px;
}
.st-store-item .st-about-owner span {
    font-size: 10px;
    color: rgba(0,0,0,.6);
    display: block;
    line-height: 1;
}
 .st-store-item .st-store-products {
    margin-bottom: 15px;
    padding-top: 15px;
    padding-left: 5px;
    display: table-cell;
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -moz-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;
}

.st-store-item .st-store-products .table-cell {
    display: table-cell;
    vertical-align: middle;
}
 .st-store-item:after
{
    content: "";
    display: table;
    clear: both
}
.st-store-item .ststore-products > a
{
    width: 14.2857%;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 14.2857%;
    flex: 0 0 14.2857%;
    -ms-flex: 0 0 14.08%;
    display: block;
    float: left;
    padding: 1px;
    -webkit-transition: all .15s ease-in-out;
    -moz-transition: all .15s ease-in-out;
    -o-transition: all .15s ease-in-out;
    transition: all .15s ease-in-out;
    position: relative;
}
.st-store-item .st-store-products a
{
    background-color: #fff;
    display: flex;
    justify-content: center;
        min-width: 150px;
        min-height: 150px;
}
.st-store-item .st-store-products a img
{
    display: flex;
    margin:auto;
    
    max-width: 150px;
    width: 100%;
    max-height: 150px;
       
}
.st-store-item .st-store-products .st-product-count
{
    position: relative;
}
 .st-store-products .st-product-count .overlay {
    background: #ffffff;
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    text-align: center;
  
}
 .st-store-products .st-product-count .table {
    height: 100%;
    background-color: #f9f9f9;
    width: 100%;
    display: table;
}
.st-store-products .st-product-count .number {
    font-size: 28px;
    color: #54ae68;
    line-height: 1.2;
    display: block;
    }
    .st-store-products .st-product-count .text {
    color: rgba(0,0,0,.6);
}
.st-store-item .table-cell {
   
    vertical-align: middle;
}
.st-product-count img
{
    min-width: 135px;
    min-height: 150px;
}
.store-item .store-desc {
    width: 140px;
    background-color: #fff;
    border: 1px solid #e1e1e1;
    position: relative;
}

    </style>

    <?php



    return '';
}
add_shortcode('st-user-shop', 'st_user_shop');