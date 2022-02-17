<?php

/**
* Get the Awake products
*
* @author Jay Pagnis
*/
function renderAwakeProducts($atts = []){
    ob_start(); ?>
    <style media="screen">
    .brand-image img {
        width: 70%;
    }
    .products-container {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    </style>
    <?php
    // We need to handle this since desktop and mobile have different layouts.
    if(!wp_is_mobile()) {
        $outerClass = "d-sm-block d-none";
        $wrapperClass = "product-wrapper";
        $showSliderDiv = false;
        $perRow = $atts['per_row'];
    }
    else {
        $outerClass = "m-product-container d-block d-sm-none";
        $wrapperClass = "";
        $showSliderDiv = true;
        $perRow = 5;
    }
    $vendorId = $tags = "";
    // Need to add other attributes here such as is_slider, slides_to_show
    if(isset($atts['vendor_id']) && !empty($atts['vendor_id'])) $vendorId = $atts['vendor_id'];
    if(isset($atts['tags']) && !empty($atts['tags'])) $tags = "tags=".$atts['tags'];
    $totalRows = 1; ?>
    <div class="<?php echo $outerClass; ?>"> <!-- Outer Section -->
        <?php if(!$showSliderDiv) : ?><div class="<?php echo $wrapperClass; ?>"><?php endif; ?> <!-- Wrapper Section -->
            <?php for($i=1;$i<=$totalRows;$i++) { ?>
                <div count="<?php echo $perRow; ?>" imageSize="150x150" vendorid="<?php echo $vendorId; ?>" <?php echo $tags; ?> class="products-container <?php echo ($showSliderDiv ? "m-product" : ""); ?>">
                    <?php if($showSliderDiv) : ?><div><?php endif; ?>
                        <div class="<?php echo $atts['product_classes']; ?>">
                            <div class="product-image">
                                <a href="demo/awake/pdp/?product-id={{productId}}" class="am-product-link">
                                    <img class="am-product-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                                </a>
                            </div>
                            <div class="product-info">
                                <p class="am-product-title product-title">Product Title</p>
                                <p class="am-product-vendor brand-title">Brand Title</p>
                            </div>
                        </div>
                    <?php if($showSliderDiv) : ?></div><?php endif; ?>
                </div>
            <?php } ?>
        <?php if(!$showSliderDiv) : ?></div><?php endif; ?> <!-- End Wrapper Section -->
    </div> <!-- End Outer Section -->
    <?php return ob_get_clean();
}
add_shortcode('awake_products', 'renderAwakeProducts');
?>
