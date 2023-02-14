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
      <h1>Cart</h1>
    </div>
    <div class="st-cart-main">
      <div class="st-cart-products">
        <?php foreach($st_cart->cart_lines as $key=>$value): ?>
          <div id="st-cart-product" class="st-cart-product">
            <div class="st-cart-product-details">
              <div class="st-cart-product-img-div"><img src="<?php echo $value->image_src ?>" loading="lazy" alt="" class="st-cart-product-img"></div>
              <div class="st-cart-product-sum">
                <h2 class="st-cart-product-title"><?php echo $value->name ?></h2>
                <div>
                    <?php foreach($value->variant_name_value as $varKey=>$varValue): ?>
                      <div id="st-cart-product-var" class="st-cart-product-var">
                        <div class="st-cart-product-var-title"><?php echo $varKey ?>:</div>
                        <div class="st-cart-product-var-val"><?php echo $varValue ?></div>
                      </div>
                    <?php endforeach; ?>
                </div>
              </div>
            </div>
            <div class="st-cart-product-pricing">
              <div class="st-cart-product-price"><?php echo $prodCurrency.$value->price->amount ?></div>
              <input type="number" pid="<?php echo $value->product_id ?>" vid="<?php echo $value->product_variant_id ?>" vname='<?php echo json_encode($value->variant_name_value) ?>' name="<?php echo $value->product_id."_qty" ?>" class="st-cart-product-qty" value="<?php echo $value->quantity ?>" onchange="cartUpdateProductQuant(this)">
              <div class="st-cart-product-tot-price"><?php echo $prodCurrency.($value->price->amount*$value->quantity) ?></div>
            </div>
            <div class="st-cart-product-remove" onclick="removeProduct(this)"><img src="<?php echo $path ?>/images/delete.png" loading="lazy" alt=""></div>
          </div>
        <?php endforeach; ?>

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
        if(quantity==0){
          qtyInput.parentElement.parentElement.remove();
        }else{
          var parent = qtyInput.parentElement;
          var prodVal = parent.querySelector(".st-cart-product-price").innerHTML.replace(myCurrency,"");
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
      if(checkoutJson.message){
          shoptype_UI.stHideLoader();
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
<?php
get_footer();