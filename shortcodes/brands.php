<?php
/**
* Get the Awake brands
*
* @author Jay Pagnis
*/
function renderAwakeBrands($atts = []){
    ob_start();
    $outerClass = "d-sm-block d-none";
    $wrapperClass = "product-wrapper";
    $totalRows = 1;
    $isSlider = $atts['slider'];
    $slidesToShow = $slidesToScroll = 0;
    $removeTemplate = "";
    if($isSlider == 1){
        $slidesToShow = $atts['slidestoshow'];
        $slidesToScroll = $atts['slidestoscroll'];
        $removeTemplate = "removeTemplate";
    }
    $loadMore = "";
    if( isset($atts["loadmore"]) )
    $loadMore = "loadmore='".$atts["loadmore"]."'";
    if($isSlider == 0) : ?>
    <div class="<?php echo $outerClass; ?>"><!-- Outer Class -->
        <div class="<?php echo $wrapperClass; ?>"><!-- Wrapper Class --> <?php endif; ?>
            <?php for($i=1;$i<=$totalRows;$i++) { ?>
                <div count="<?php echo $atts['per_row']; ?>" imageSize="150x150" <?php echo $removeTemplate;?> class="brands-container <?php echo $atts['container_classes']; ?>" <?php echo $loadMore;?>>
                    <?php if($isSlider == 1) : ?><div><?php endif; ?>
                        <div class="brand-container <?php echo $atts['brand_classes']; ?>">
                            <?php if($isSlider == 1) : ?>
                                <a href="demo/awake/bdp/?brand-id={{brandId}}" class="am-brand-link">
                                    <img class="am-brand-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                                </a>
                                <h4 class="am-brand-name">Brand Name</h4>
                            <?php else : ?>
                                <div class="product-image">
                                    <a href="demo/awake/bdp/?brand-id={{brandId}}" class="am-brand-link">
                                        <img class="am-brand-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <p class="product-title am-brand-name">Brand Name</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($isSlider == 1) : ?></div><?php endif; ?>
                    </div>
                <?php }
            if($isSlider == 0) : ?>
        </div><!-- End Outer Class -->
    </div><!-- End Wrapper Class --> <?php endif; ?>
    <?php return ob_get_clean();
}
add_shortcode('awake_brands', 'renderAwakeBrands');

// ..............................
// Render Latest Brands
// ..............................
function renderLatestBrands($atts = []){
    ob_start();
    $totalRows = 1;
    $removeTemplate = "removeTemplate";
    $loadMore = "";
    if( isset($atts["loadmore"]) )
    $loadMore = "loadmore='".$atts["loadmore"]."'";
    for($i=1;$i<=$totalRows;$i++) { ?>
        <div count="<?php echo $atts['per_row']; ?>" imageSize="150x150" <?php echo $removeTemplate;?> class="brands-container <?php echo $atts['container_classes']; ?>" <?php echo $loadMore;?>>
            <div class="brand-container <?php echo $atts['brand_classes']; ?>">
                <div>
                    <div class="brand-image-container">
                        <img class="am-brand-image" src="https://us.awake.market/wp-content/uploads/2021/12/Display-Pic.jpg" loading="lazy" alt="">
                        <p class="am-brand-name"></p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php return ob_get_clean();
}
add_shortcode('awake_latest_brands', 'renderLatestBrands');
?>