<?php
/**
* Get the Awake brands
*
* @author Jay Pagnis
*/
function renderAwakeBrands($atts = []){
    ob_start();

    $loadMore = "";
    if( isset($atts["loadmore"]) ) {$loadMore = "loadmore='".$atts["loadmore"]."'";} ?>

    <div count="<?php echo $atts['per_row']; ?>" imageSize="300x300" class="brands-container <?php echo $atts['container_classes']; ?>" <?php echo $loadMore; ?> >
        <div class="brand-container single-brand <?php echo $atts['brand_classes']; ?>" style="display: none;">
                <div class="product-image">
                    <a href="demo/awake/bdp/?brand-id={{brandId}}" class="am-brand-link">
                        <img class="am-brand-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                    </a>
                </div>
                <div class="product-info">
                    <p class="product-title am-brand-name">Brand Name</p>
                </div>
        </div>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('awake_brands', 'renderAwakeBrands');

// ..............................
// Render Latest Brands
// ..............................
function renderLatestBrands($atts = []){
    ob_start();
    $removeTemplate = "removeTemplate";
    $loadMore = "";
    if( isset($atts["loadmore"]) )
    $loadMore = "loadmore='".$atts["loadmore"]."'"; ?>
    <div count="<?php echo $atts['per_row']; ?>" imageSize="300x300" <?php echo $removeTemplate;?> class="brands-container <?php echo $atts['container_classes']; ?>" <?php echo $loadMore;?>>
        <div class="brand-container single-brand <?php echo $atts['brand_classes']; ?>">
            <div>
                <div class="brand-image-container">
                    <img class="am-brand-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                    <p class="am-brand-name"></p>
                </div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('awake_latest_brands', 'renderLatestBrands');
?>