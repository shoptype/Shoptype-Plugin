<?php
$args = wp_parse_args(
    $args,
    array(
        'my_data' => array(
            'st_product' => null, // default value
        )
    )
);
$st_product = $args['st_product'];

add_filter('pre_get_document_title', function () use ($st_product) {
	return $st_product->title;
});

add_action('wp_head', function () use ($st_product) {
	$description = substr($st_product->description, 0, 160);
	echo "<meta name='description' content='$description'>";
	echo "<meta property='og:title' content='$st_product->title' />";
	echo "<meta property='og:description' content='".substr(strip_tags($st_product->description),0,250)."' />";
	echo "<meta property='og:image' content='{$st_product->primaryImageSrc->imageSrc}' />";
}, 1);