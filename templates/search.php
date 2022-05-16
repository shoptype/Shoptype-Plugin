<?php
/*
 * Template name: Search Templet
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */

global $stCurrency;

$searchTearm = $_GET['s'];
$massage='';

?>
<style>
    .container {
        max-width: 1200px;
        margin: auto;
        paddin-right: 30px;
        paddin-left: 30px;
    }

    .product-description {
        width: 100%;
        line-height: 1.2em;
        height: 4em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        text-overflow: ellipsis;
    }

    .product-container {
        display: flex;
        gap: 30px;
    }

    .single-product {
        padding: 25px;
    }

    .product-image,
    .product-title,
    .product-description,
    .product-price {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .product-image img {
        margin: 0px !important;
        padding: 10px;
        max-width: 250px;
        overflow: hidden;
    }

    .product-image {
        display: flex;
        justify-content: center;
    }
</style>
<div class="container">
    <?php
    get_header();

    if (isset($searchTearm)) { ?>
        <h2>Search Result for <?php echo $searchTearm;  ?></h2>
        <?php


        global $stPlatformId;
        global $wp_query;
        $q = $wp_query->query_vars;
        $searchTearms = preg_replace('/\s+/', '%20', $searchTearm);
        $url = "https://backend.shoptype.com/platforms/$stPlatformId/products?count=30&text=$searchTearms";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!empty($result)) {
            $st_products = json_decode($result);

        ?>
            <div class="products-container">

                <?php
                foreach ((array) $st_products->products as $products) { ?>
                    <div class="single-product">
                        <a href="/products/<?php echo $products->id ?>">
                        <div class="product-image"><img src="<?php echo $products->primaryImageSrc->imageSrc ?>" /></div>

                        <div class="product-title">
                            <h3><?php echo $products->title  ?>
                                <h3>
                        </div>
                        <div class="product-price">
                            <h4><span class="currency-symbol"><?php echo $stCurrency[$products->variants[0]->discountedPriceAsMoney->currency]; ?></span><?php echo ($products->variants[0]->discountedPrice)  ?></h4>
                        </div>

                        <div class="product-description"><?php echo $products->description ?> </div>
                        </a>
                    </div>


                <?php } 
        }
        else
        {
            $massage='No result found';
        }
                ?>
                <?php
                global $wp_query;
                ?>

                <?php
                /* loping for each post */
                if (have_posts()) {$massage=''; ?>
                    <?php while (have_posts()) {
                        the_post(); ?>
                        <div class="single-product">
                            <a href="/products/<?php echo get_permalink(); ?>">
                            <div class="product-image"><?php the_post_thumbnail('medium') ?></div>

                            <div class="product-title">
                                <h3><?php the_title();  ?><h3>
                            </div>
                            <div class="product-description"><?php echo substr(get_the_excerpt(), 0, 200); ?> </div>
                            </a>
                        </div>

                    <?php } ?>


                    <?php echo paginate_links(); ?>

            <?php }
            ?>
            </div>


        <?php

?>
<?php }
echo '<h2>'. $massage.'</h2>';
        
        ?>
</div>
<?php
get_footer();
