<?php
/*
 * Template name: my-shop-templet-01
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package shoptype
 */

global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;

$shop_name = urldecode(get_query_var( 'shop' ));

$result = wp_remote_get( "{$stBackendUrl}/cosellers/fetch-mini-stores?name=$shop_name" );
if( ! is_wp_error( $result ) ) {
  $body = wp_remote_retrieve_body( $result );
  $user_mini_stores = json_decode($body);
  if($user_mini_stores->count==0){
    
  }else{
    $result = wp_remote_get( "{$stBackendUrl}/cosellers/mini-stores/".$user_mini_stores->mini_stores[0]->id );
    $body = wp_remote_retrieve_body( $result );
    $mini_store = json_decode($body);
  }
}
ob_start();
get_header('shop');
$header = ob_get_clean();
$header = preg_replace('#<title>(.*?)<\/title>#', "<title>$mini_store->name</title>", $header);
echo $header;
add_action('wp_head', function () use ($mini_store) {
      $description = substr($mini_store->attributes->bio, 0, 160);
      echo "<meta name='description' content='$description'>";
      echo "<meta property='og:title' content='$mini_store->name' />";
      echo "<meta property='og:description' content='$description' />";
      echo "<meta property='og:image' content='{$mini_store->attributes->profile_img}' />";
    }, 1);
   
wp_enqueue_style( 'my-shop-css', st_locate_file('css/st-my-shop.css' ));

st_locate_template( 
  "parts/${mini_store->design_attributes->template}.php", 
  true, 
  array( 
    'data' => array(
      'mini_store' => $mini_store,
    )
  )
);

?>

<?php
get_footer('shop');



