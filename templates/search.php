<?php
/*
 * Template name: Search Template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */

global $stCurrency;

$searchTerm = $_GET['s'];
$massage = '';

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

    if (isset($searchTerm)) { ?>
        <h2>Search Result for <?php echo $searchTerm; ?></h2>
        <?php

        global $stPlatformId;
        global $wp_query;
        global $stBackendUrl;
        $q = $wp_query->query_vars;
        $searchTerms = preg_replace('/\s+/', '%20', $searchTerm);
        $url = "{$stBackendUrl}/platforms/$stPlatformId/products?count=30&text=$searchTerms";
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
                foreach ((array)$st_products->products as $products) { ?>
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
         else {
            $massage = 'No result found';
        }
    }
        ?>
<?php
 global $query_string;

                $query_args = explode("&", $searchTearm);
                $search_query = array();
                
                foreach($query_args as $key => $string) {
                    $query_split = explode("=", $string);
                    $search_query[$query_split[0]] = $query_split[1];
                } // foreach
                
                $search = new WP_Query($search_query,array( 's' => 'WAIKIKI'));
                global $wp_query;
$total_results = $wp_query->found_posts;
                
?>

<?php
/* looping for each post */
if ($search->have_posts()) {
    $massage='';
?>
    <div class="products-container">
        <?php while ($search->have_posts()) {
           $search->the_post();
        ?>
            <div class="single-product">
                <a href="<?php echo get_permalink(); ?>">
                    <div class="product-image"><?php the_post_thumbnail('medium') ?></div>
                    <div class="product-title">
                        <h3><?php the_title(); ?></h3>
                    </div>
                    <div class="product-description"><?php echo substr(get_the_excerpt(), 0, 200); ?></div>
                </a>
            </div>
        <?php } ?>

        <?php echo paginate_links(); ?>

    </div>
<?php
} else {
    $massage='No result found';
}
echo '<h2>'. $massage.'</h2>';

get_footer();
?>
