<?php
/*
 * Template name: Shoptype MyShop Dashboard
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stDefaultCurrency;
global $stCurrency;
global $brandUrl;
global $stBackendUrl;

$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'cartCss', $path.'/css/st-myaccount.css' );

	$products_field_id = xprofile_get_field_id_from_name( 'st_products');
	$theme_field_id = xprofile_get_field_id_from_name( 'st_shop_theme');
	$url_field_id = xprofile_get_field_id_from_name( 'st_shop_url');
	global $wpdb;
	$bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

	$query = $wpdb->prepare(
		"SELECT user_id,user_login,user_nicename,user_email,display_name, field_id, value " .
		"FROM $bp_table B, $wpdb->users U " .
		"WHERE B.user_id = U.ID " .
		"AND B.value IS NOT NULL"
	);
	$get_desired = $wpdb->get_results($query);

	$foo = array();
	foreach($get_desired as $key=>$dataRow){
		if(!array_key_exists($dataRow->user_id,$foo)){
			$foo[$dataRow->user_id] = array($dataRow->field_id => $dataRow->value);
			$foo[$dataRow->user_id][$dataRow->field_id] = $dataRow->value;
			$foo[$dataRow->user_id]["user_login"] = $dataRow->user_login;
			$foo[$dataRow->user_id]["user_nicename"] = $dataRow->user_nicename;
			$foo[$dataRow->user_id]["display_name"] = $dataRow->display_name;
			$foo[$dataRow->user_id]["user_id"] = $dataRow->user_id;
		}else{
			$foo[$dataRow->user_id][$dataRow->field_id] = $dataRow->value;
		}
	}

?>
<style>
.st-order-col {
    flex: 0 280px;
}
.div-block-7 {
    flex: 0 350px;
}
.st-order-product-info {
    flex-wrap: wrap;
}
</style>
<div class="container">
	<div class="st-account-details">


	</div>
	<div class="st-orders">
		<?php foreach($foo as $key=>$user): 
		if(array_key_exists($products_field_id,$user) && !empty($user[$products_field_id])){
					$valuesJson = str_replace('\"', '"', $user[$products_field_id]);
					$valuesParsed = json_decode($valuesJson, true);
					if(empty($valuesJson)){
						$values = 'nothing';
					}
					else{
						$values="";
						foreach($valuesParsed as $key => $value) {
							$values = $values.$key.",";
						}
					}
		?>
		<div onclick="<?php echo "openShopInfo('".$user["user_id"]."')" ?>" class="st-order">
			<div class="st-order-header">
				<div class="st-order-col">
					<div>user login</div>
					<div class="st-order-date"><?php echo $user["user_login"] ?></div>
				</div>
				<div class="st-order-col">
					<div>user name</div>
					<div class="st-order-total"><?php echo $user["user_nicename"]?></div>
				</div>
				<div class="st-order-col-full">
					<div>display name</div>
					<div class="st-order-address"><?php echo $user["display_name"] ?></div>
				</div>
				<div class="st-order-col">
					<div>No. of Products</div>
					<div class="st-order-no"><?php echo count((array)$valuesParsed); ?></div>
				</div>
				<div class="st-order-col">
					<div>URL</div>
					<a href="<?php echo "/shop/$user[$url_field_id]" ?>"><div class="st-order-no"><?php echo $user[$url_field_id] ?></div></a>
				</div>
			</div>
		</div>
		<?php } endforeach; ?>
	</div>
</div>
<script>
	function openShopInfo(shopUrl){
		window.location.href = "/wp-admin/admin.php?page=my-shop-details&user_id="+shopUrl;
	}
</script>
<?php
