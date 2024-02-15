<?php
/*
 * Template name: Shoptype Cart
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
$cartId = get_query_var( 'cart' );

if(empty($cartId)||$cartId=="main"){
  $cartstr = stripslashes($_COOKIE["carts"]);
  $cartsParsed = json_decode($cartstr);
  $cartId = $cartsParsed->shoptypeCart;
}
try {
  $args = array(
    'headers' => array(
      "X-Shoptype-Api-Key" =>$stApiKey,
      "X-Shoptype-PlatformId" =>$stPlatformId,
    "origin" => "https://".$_SERVER['HTTP_HOST']
      ));
  $response = wp_remote_get("{$stBackendUrl}/cart/$cartId",$args);
  $result = wp_remote_retrieve_body( $response );

  if( !empty( $result ) ) {
    $st_cart = json_decode($result);
    $prodCurrency = $stCurrency[$st_cart->sub_total->currency];
  }
}
catch(Exception $e) {
  echo "Cart not found";
}
wp_enqueue_style( 'new-market', $path . '/css/st-cart.css' );
wp_enqueue_script('triggerUserEvent','https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js');
get_header(null);
?>
  <div class="st-cart">
    <div class="st-cart-head">
     <div class="cart-checkout" bis_skin_checked="1">
  <div class="cart-back" bis_skin_checked="1" href="#" onclick="history.back()">
    <span><img src="http://flowsides-com.ibrave.host/wp-content/uploads/2023/07/Back.png"></span>
  </div>
  
</div>
    </div>
    <div class="st-cart-main">
      <div class="st-cart-products">
    <?php if(isset($st_cart->cart_lines)){ ?>
        <?php foreach($st_cart->cart_lines as $key=>$value): ?>
          <div id="st-cart-product" class="st-cart-product">
            <div class="st-cart-product-details">
              <div class="st-cart-product-img-div"><img src="<?php echo $value->image_src ?>" loading="lazy" alt="" class="st-cart-product-img"></div>
              <div class="st-cart-product-sum">
                <h2 class="st-cart-product-title"><?php echo $value->name ?></h2>
        <div class="st-cart-product-tot-price"><?php echo $prodCurrency.($value->price->amount*$value->quantity) ?></div>
        <?php if(!(count((array)$value->variant_name_value)<=1 && reset($value->variant_name_value)=="Default Title")){ ?>
                <div class="product-varition-cart">
                    <?php foreach($value->variant_name_value as $varKey=>$varValue): ?>
                      <div id="st-cart-product-var" class="st-cart-product-var">
                        <div class="st-cart-product-var-title"><?php echo $varKey ?>:</div>
                        <div class="st-cart-product-var-val"><?php echo $varValue ?></div>
                      </div>
                    <?php endforeach; ?>
                </div>
        <?php } ?>
                <div class="st-cart-product-pricing">
              <div class="st-cart-product-price"><?php echo $prodCurrency.$value->price->amount ?></div>
              <input type="number" pid="<?php echo $value->product_id ?>" vid="<?php echo $value->product_variant_id ?>" vname='<?php echo json_encode($value->variant_name_value) ?>' name="<?php echo $value->product_id."_qty" ?>" class="st-cart-product-qty" value="<?php echo $value->quantity ?>" onchange="cartUpdateProductQuant(this)">
            </div>
              </div>
            </div>
            
            <div class="st-cart-product-remove" onclick="removeProduct(this)"><img src="http://flowsides-com.ibrave.host/wp-content/uploads/2023/07/Remove-1.svg" loading="lazy" alt=""></div>
          </div>
        <?php endforeach; ?>
    <?php } ?>
      </div>
      <div class="st-cart-details">
        <div class="st-cart-top">
          <h3 class="st-cart-sum-title">CART TOTALS</h3>
        </div>
        <div>
          <div class="st-cart-details-title">
            <div class="st-cart-subtitle">Subtotal</div>
            <div class="st-cart-subtotal"><?php echo $prodCurrency.$st_cart->sub_total->amount ?></div>
          </div>
          <div class="st-cart-shipping">
            <div class="st-cart-subtitle">Shipping</div>
            <div class="st-shipping-details">
              <div class="st-shipping-cost">Calculated at Checkout</div>
              <div class="st-shipping-add"></div>
            </div>
          </div>
          <div class="st-cart-details-title">
            <div class="st-cart-subtitle">Total</div>
            <div class="st-cart-total"><?php echo $prodCurrency.$st_cart->sub_total->amount ?></div>
          </div>
        </div>
        <div class="st-cart-checkout-btn" onclick="checkout()">
          <div class="st-cart-checkout-btn-txt">Proceed to Checkout</div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
  const st_cartId = "<?php echo $st_cart->id ?>";
  const myCurrency = "<?php echo $prodCurrency ?>";
  var ignoreEvents = true;
  var modalCheckout = false;
  function cartUpdateProductQuant(qtyInput){
    var productId = qtyInput.getAttribute("pid");
    var variantId = qtyInput.getAttribute("vid");
    var variantName = JSON.parse(qtyInput.getAttribute("vname"));
    var quantity = parseInt(qtyInput.value);
    shoptype_UI.stShowLoader();
    st_platform.updateCart(productId, variantId, variantName, quantity)
      .then(cartJson => {
    shoptype_UI.stHideLoader();
        if(quantity==0){
          qtyInput.parentElement.parentElement.parentElement.parentElement.remove();
        }else{
          var parent = qtyInput.parentElement.parentElement;
          var prodVal = parent.querySelector(".st-cart-product-tot-price").innerHTML.replace(myCurrency,"");
          prodVal = parseFloat(prodVal);
          parent.querySelector(".st-cart-product-tot-price").innerHTML = myCurrency + (quantity*prodVal);
        }
        var totQuant = 0;
        var totSum = myCurrency+"0";
        if(cartJson.sub_total){
          totQuant = cartJson.total_quantity;
          totSum = myCurrency+cartJson.sub_total.amount;
        }
        let shoptypeCartCountChanged =new CustomEvent('shoptypeCartCountChanged', {'detail': {
          "count": totQuant
        }});
        document.dispatchEvent(shoptypeCartCountChanged);
        document.querySelector(".st-cart-subtotal").innerHTML = totSum;
        document.querySelector(".st-cart-total").innerHTML = totSum;
          shoptype_UI.stHideLoader();
      });
  }

  function checkout(){
    shoptype_UI.stShowLoader();
    st_platform.createCheckout((checkoutJson)=>{
    console.info(checkoutJson);
      if(checkoutJson.message || (!checkoutJson.checkout_id)){
          shoptype_UI.stHideLoader();
      if(!checkoutJson.message){checkoutJson.message="Oops! we are unable to create the checkout at the moment";}
          ShoptypeUI.showError(checkoutJson.message);
        }else if(checkout.external_url){
          let childWindow = null;
          let st_redirect_uri = checkout.redirect_uri;
          if(st_hostDomain && st_hostDomain!=""){
            let st_checkoutUrl = new URL(st_redirect_uri);
            st_redirect_uri = st_checkoutUrl.href.replace(st_checkoutUrl.host,st_hostDomain)
          }

          if(stCheckoutType == "newWindow"){
            childWindow = window.open(st_redirect_uri);
          }else{
            window.location.href = st_redirect_uri;           
          }
        }else{
          window.location.href = "/checkout/" + checkoutJson.checkout_id; 
        }
    })
  }
  function removeProduct(removeBtn){
    var quantity = removeBtn.parentElement.querySelector(".st-cart-product-qty");
    quantity.value = 0;
    cartUpdateProductQuant(quantity)
  }

</script>
<style type="text/css">
.st-cart-head h1,h3.st-cart-sum-title{padding-bottom:20px;padding-top:20px;text-align:center}
.st-cart-details,.st-cart-products,h3.st-cart-sum-title{box-shadow:0 0 20px 0 rgba(0,0,0,.1)}
.btn-minus,.btn-plus{border-color:#f58620;color:#fff}
.st-cart .st-cart-product-pricing .st-cart-product-price,.st-cart-details::after,.st-cart-details::before{display:none}
.st-cart-head h1{font-family:Montserrat;font-style:normal;font-weight:400;font-size:22px!important;line-height:104.9%;text-transform:uppercase;background:linear-gradient(180deg,#f58620 0,#fec547 100%);-webkit-background-clip:text;background-clip:text;text-fill-color:transparent}
.st-cart-product-tot-price{padding-left:0;color:#16161d;font-size:22px;font-family:Roboto Slab;font-style:normal;font-weight:400;line-height:150%}
.st-cart-product-var-title,.st-cart-product-var-val{font-family:Montserrat;font-style:normal;font-weight:500;font-size:14px;line-height:104.9%;text-transform:uppercase}
h2.st-cart-product-title{background-clip:text;text-fill-color:transparent;color:#16161d;font-size:16px;font-family:Roboto Slab;font-style:normal;font-weight:300;line-height:normal}
.st-cart-product-var-title{display:flex;align-items:center;color:#5c5c5c}
.st-cart-product-var-val{text-fill-color:transparent;border:1px solid #0d2b24;padding:5px;margin-left:5px}
div#st-cart-product-var{margin-bottom:10px}
.st-cart-product{margin:10px 0px;position:relative}
.st-cart-product-details{justify-content:flex-start; gap:20px;min-height:220px;align-content:center;align-items:flex-start;padding:20px;border-width:1px!important}
.am-add-cart-quantity{border-radius:0;border-radius:none}
.st-cart-checkout-btn-txt,.st-cart-product-pricing input{font-family:Roboto Slab;font-style:normal;line-height:normal;border-radius:30px}
.st-cart-checkout-btn-txt{cursor:pointer;color:#fff;text-align:center;font-size:16px;font-weight:400;background:#16161d;margin: 0px -20px;border-radius: 0px 0px 20px 20px;width: calc(100% + 40px);}
.st-cart-subtotal,.st-cart-total,.st-shipping-add,.st-shipping-cost,.st-shipping-details{color:#16161d;font-size:14px;font-family:Roboto Slab;font-style:normal;font-weight:400;line-height:150%}
h3.st-cart-sum-title{align-items:center}
div#st-cart-product{border-radius:20px;background:#f4f4f4}
.st-cart-product-remove{top:20px;right:20px}
.st-cart-details{margin:15px 0 40px 0px;justify-content: flex-start;padding:0;border-radius:20px;background:#fff;height:100%;}
.product-varition-cart{gap:20px;display:flex;margin-top:10px}
.st-cart-product-pricing{padding-top:10px;display:block}
.cart-checkout{min-width:81%;margin-top:20px;margin-bottom:20px}
.cart-back{cursor:pointer}
body{background-image:url(https://flowsides.com/wp-content/uploads/2023/06/BG-Art-2.png)}
.st-cart-products{border-radius:20px;background:#fff;padding:10px 20px;min-width:50%;max-width:100%;}
.st-cart-product-pricing input{color:#16161d;font-size:16px;font-weight:700;border:1px solid #16161d;background:#fff;margin-left:0}
.st-cart-subtitle,h3.st-cart-sum-title{font-family:Roboto Slab;font-style:normal;line-height:150%}
img.st-cart-product-img{height:auto;margin-top:10px;border-radius:20px;border:5px solid #fff}
h3.st-cart-sum-title{margin:0;border-radius:20px 20px 0 0;background:#16161d;color:#fff;font-size:14px;font-weight:400;border-bottom:none}
.st-cart-subtitle{color:#464646;font-size:16px;font-weight:300}
.st-cart-checkout-btn,.st-cart-details-title,.st-cart-shipping{padding-left:20px;padding-right:20px}
.st-cart-main {gap: 20px;}
</style>
<?php
get_footer();