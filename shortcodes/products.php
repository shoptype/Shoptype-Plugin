<?php

/**
* Get the Awake products
*
* @author Jay Pagnis
*/
function renderAwakeProducts($atts = []){
    ob_start(); ?>
    <?php

    $perRow = $atts['per_row'];
    $vendorId = $tags = $loadmore = $my_shop = "";
    $imgSize = "600x0";

    // Need to add other attributes here such as is_slider, slides_to_show
    if(isset($atts['vendor_id']) && !empty($atts['vendor_id'])) $vendorId = $atts['vendor_id'];
    if(isset($atts['imgSize']) && !empty($atts['imgSize'])) $imgSize = $atts['imgSize'];
    if(isset($atts['tags']) && !empty($atts['tags'])) $tags = "tags=".$atts['tags'];
    if(isset($atts['loadmore']) && !empty($atts['loadmore'])) $loadmore = "loadmore=".$atts['loadmore'];
    if(isset($atts['my_shop']) && !empty($atts['my_shop'])) $my_shop = "myshop=".$atts['my_shop']; 
    if(isset($atts['offset']) && !empty($atts['offset'])) $offset = "offset=".$atts['offset'];
    if(isset($atts['orderBy']) && !empty($atts['orderBy'])) $orderBy = "orderBy=".$atts['orderBy'];
    if(isset($atts['sortBy']) && !empty($atts['sortBy'])) $sortBy = "sortBy=".$atts['sortBy'];
    if(isset($atts['collection']) && !empty($atts['collection'])) $collection = "collection_id=".$atts['collection'];
    if(isset($atts['instock']) && !empty($atts['instock'])) $inStock = "inStock=".$atts['instock'];?>

    <div count="<?php echo $perRow; ?>" <?php echo "$my_shop $offset $sortBy $orderBy $collection $inStock"; ?> imageSize="<?php echo $imgSize; ?>" vendorid="<?php echo $vendorId; ?>" <?php echo "$tags $loadmore"; ?> class="products-container">
        <div class="product-container single-product" style="display: none;" id="st-product-select-template">
            <a href="demo/awake/pdp/?product-id={{productId}}" class="am-product-link">
                <div class="product-image">
                    <div class="am-product-img-div">
                        <div class="sold-out" style="display:none;">Sold Out</div>
                        <div class="on-sale" style="display:none;">Sale</div>
                        <img class="am-product-image" src="" loading="lazy" alt="">
                    </div>
                </div>
                <div class="product-info">
                    <p class="am-product-title product-title">Product Title</p>
                    <p class="am-product-vendor brand-title">Brand Title</p>
                    <div class="market-product-price am-product-price">$ 00.00</div>
                </div>
            </a>
        </div>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('awake_products', 'renderAwakeProducts');
?>