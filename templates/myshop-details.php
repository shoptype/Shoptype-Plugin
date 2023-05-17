<?php
/*
 * Template name: Shoptype MyShop Details
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
wp_enqueue_script( 'my-script-handle', 'url-to/my-script.js', array( 'bp-api-request' ) );
wp_enqueue_style( 'cartCss', $path.'/css/st-myaccount.css' );
$store_user_id = $_GET["user_id"];


$products_field_id = xprofile_get_field_id_from_name( 'st_products');
$theme_field_id = xprofile_get_field_id_from_name( 'st_shop_theme');
$url_field_id = xprofile_get_field_id_from_name( 'st_shop_url');
global $wpdb;
$bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

$query = $wpdb->prepare(
	"SELECT user_id,user_login,user_nicename,user_email,display_name, field_id, value " .
	"FROM $bp_table B, $wpdb->users U " .
	"WHERE (B.user_id = U.ID " .
	"AND B.user_id = $store_user_id ".
	"AND B.value IS NOT NULL)"
);
$get_desired = $wpdb->get_results($query);

$user = null;
foreach($get_desired as $key=>$dataRow){
	if(!isset($user)){
		$user = array($dataRow->field_id => $dataRow->value);
		$user[$dataRow->field_id] = $dataRow->value;
		$user["user_login"] = $dataRow->user_login;
		$user["user_nicename"] = $dataRow->user_nicename;
		$user["display_name"] = $dataRow->display_name;
		$user["user_id"] = $dataRow->user_id;
	}else{
		$user[$dataRow->field_id] = $dataRow->value;
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
.div-block-7 {
    position: relative;
    margin: 20px;
}
.st-remove-btn{
	position: absolute;
	right: 0px;
	cursor:pointer;
}
.st-remove-btn img {
    width: 16px;
    height: 16px;
}
</style>
<div class="container">
	<div class="st-account-details">


	</div>
	<div class="st-orders">
		<?php 
		$groupSlug = strtolower($user["user_login"]."_coseller");
		$group_id = BP_Groups_Group::group_exists( $groupSlug );
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
		<div class="st-order">
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
					<div>Products</div>
					<div class="st-order-no"><?php echo count((array)$valuesParsed); ?></div>
				</div>
				<div class="st-order-col">
					<div>URL</div>
					<a href="<?php echo "/shop/$user[$url_field_id]" ?>"><div class="st-order-no"><?php echo $user[$url_field_id] ?></div></a>
				</div>
			</div>
			<div class="st-add-product-drawer" id="st-add-product-drawer">
				<div class="st-product-add-drawer">
					<div class="st-product-search">
						<input class="st-product-search-box" id="st-search-box" name="Search" >
						<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
					</div>
					<div class="st-product-search-results" id="st-product-search-results" style="display: none;">
						<div class="st-product-select" id="st-product-select-template" style="display: none;">
							<div class="st-product-select-main">
								<div class="st-product-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-product-img-select"></div>
								<div class="st-product-details-block">
									<div class="st-product-name">Product Name</div>
									<div class="st-product-cost-select">$00.00</div>
								</div>
								<input class="st-shop-select" type="checkbox" id="product000" name="" value="" onchange="productSelect(this)">
							</div>
						</div>
					</div>
					<div class="st-shop-buttons">
						<div class="st-button" onclick="callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,addToShop,'get')">Add to Shop</div>
						<div class="st-button" onclick="hideResults()">Cancel</div>
					</div>
				</div>
			</div>
			<div class="st-order-product">
				<div class="st-order-product-info">
					<?php 

					$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?count=100&productIds=$values");
					$result = wp_remote_retrieve_body( $response );
					$products = json_decode($result);

					foreach($products->products as $product): 
					?>
					<div class="div-block-7">
						<div class="st-remove-btn" productid="<?php echo $product->id ?>" onclick="removeProductFromShop(this)"><img src="<?php echo $path ?>/images/delete.png"></div>
						<div class="st-order-vendor"><?php echo $product->vendor_name ?></div>
						<div class="st-order-product-div">
							<div><img src="<?php echo $product->primaryImageSrc->imageSrc ?>" loading="lazy" alt="" class="st-order-product-img"></div>
							<div class="st-order-product-details">
								<div class="st-order-product-name">removeProductFromShop</div>
								<div class="st-order-product-cost"><?php echo "{$stCurrency[$product->variants[0]->discountedPriceAsMoney->currency]} ". number_format($product->variants[0]->discountedPriceAsMoney->amount,2) ?></div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>

				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	function callBpApi(dataUri, callBack, type, data){
		wp.apiRequest( {
			path: "buddypress/v1/"+dataUri,
			type: type,
			data: data,
		} ).done( function( data ) {
			callBack(data);
		} ).fail( function( error ) {
			ShoptypeUI.showError(error.responseJSON.message);
			return error;
		} );
	}

	function pushBpApi(dataUri, callBack, type, data){
		wp.apiRequest( {
			path: "buddypress/v1/"+dataUri,
			type: type,
			data: data,
			contentType: false,
			processData: false
		} ).done( function( data ) {
			callBack(data);
		} ).fail( function( error ) {
			ShoptypeUI.showError(error.responseJSON.message);
			return error;
		} );
	}

	function addUserDetails(userData){
		currentBpUser = userData;
	}
	
	function hideResults(){
		document.getElementById("st-product-search-results").style.display = "none";
	}

	function showResults(){
		document.getElementById("st-product-search-results").style.display = "";
	}

	function addToShop(shopProducts){
		let selectorNodes = document.getElementsByClassName("st-shop-select");
		let products = shopProducts[0].value.unserialized[0]??"";
		let newProducts = {};
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !products.includes(selectorNodes[i].value)){
				newProducts[selectorNodes[i].value] = shoptype_UI.getUserTracker();
				productAddedActivity(selectorNodes[i]);
			}
		}
		hideResults();
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x, newProducts,x=>loadShopProducts(currentBpUser.user_login)),'get');
	}
	
	function productAddedActivity(selector){
		var productName = selector.parentNode.querySelector(".st-product-name").innerHTML;
		var productImg = selector.parentNode.querySelector(".st-product-img-select").src;
		var productId = selector.value;
		data = {
			context: 'edit',
			user_id: profileUser,
			component: 'activity',
			group_id: groupId,
			link: "/products/"+productId,
			type: 'activity_update',
			content: `<b>New Product Added to store</b><a href="/products/"${productId}><img src="${productImg}">${productName}</a>`
		}
		callBpApi(`activity`,x=>{},'POST',data);
	}

	function loadShopProducts(userName) {

	}
	
	function updateStoreName(name){
		clearTimeout(debounce_timer);
		var data = {
			context: 'edit',
			name: name
		}
		debounce_timer = setTimeout(()=>{callBpApi("groups/"+groupId,(d)=>{document.getElementById("store_title").innerHTML=d[0].name},"put",data);}, 5000);
	}
	
	function updateStoreBio(description){
		clearTimeout(debounce_timer);
		var data = {
			context: 'edit',
			description: description
		}
		debounce_timer = setTimeout(()=>{callBpApi("groups/"+groupId,(d)=>{document.getElementById("store_bio").innerHTML=d[0].description.rendered},"put",data);}, 5000);
	}
	
	function updateProfileImg(){
		console.info("updateProfileImg");
		var fileSelect = document.getElementById("profileImageFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var formData = new FormData();
		formData.append( 'action', 'bp_avatar_upload' );
		formData.append( 'file', fileSelect.files[0] );
		pushBpApi(`groups/${groupId}/avatar`, (d)=>{document.querySelector(".store-icon").src = d[0].full}, "post", formData);
	}
	
	function updateBgImg(){
		console.info("updateBgImg");
		var fileSelect = document.getElementById("profileBGFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var formData = new FormData();
		formData.append( 'action', 'bp_cover_image_upload' );
		formData.append( 'file', fileSelect.files[0] );
		pushBpApi(`groups/${groupId}/cover`, (d)=>{document.querySelector(".store-banner").src = d[0].image}, "post", formData);
	}
	
	function showRemoveBtn(productNode){
		productNode.querySelector(".st-remove-product").style.display = "block";
	}

	function hideRemoveBtn(productNode){
		productNode.querySelector(".st-remove-product").style.display = "none";
	}

	function addProductDetails(productNode, product, imgTag, priceTag){
		let pricePrefix = shoptype_UI.currency[product.currency]??product.currency;
		productNode.querySelector(imgTag).src = product.primaryImageSrc.imageSrc;
		productNode.querySelector(".st-product-name").innerHTML = product.title;
		productNode.querySelector(priceTag).innerHTML = pricePrefix + product.variants[0].discountedPriceAsMoney.amount.toFixed(2);
		productNode.style.display="";
	}

	function searchProducts() {
		let options = {
			text: document.getElementById('st-search-box').value,
			offset:0
		};
		
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		removeChildren(productsContainer,productTemplate);
		showResults();
		st_platform.products(options)
			.then(productsJson => {
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, productsJson.products[i],".st-product-img-select",".st-product-cost-select");
					newProduct.id = "search-" + productsJson.products[i].id;
					newProduct.querySelector("input").id = "select-" + productsJson.products[i].id;
					newProduct.querySelector("input").value = productsJson.products[i].id;
					productsContainer.appendChild(newProduct);
				}
			});
	}

	function removeChildren(node, dontRemove){
		let length = node.children.length;
		for (var i = length - 1; i >= 0; i--) {
			if(node.children[i]!=dontRemove){node.children[i].remove();}
		}
	}

	function productSelect(selectBox){
		let productId = selectBox.value;
		st_selectedProducts[productId]=shoptype_UI.getUserTracker();
	}

	function removeProductFromShop(productBtn){
		var productId = productBtn.getAttribute("productid");
		let products = {};
		products[productId]='';
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x,products,x=>removeProduct(productBtn),true),'get');
	}
	
	function removeProduct(productBtn){
		productBtn.parentNode.remove();
	}

	function addProductByIdToShop(shopProducts, newProducts, callBack, removeProduct=false){
		let productsJson = shopProducts[0].value.unserialized[0]??'{}';
		productsJson = productsJson.replace(/ /g, ',');
		let products = JSON.parse(productsJson);
		for (const [key, value] of Object.entries(newProducts)) {
		  	if(removeProduct){
				delete products[key];
			}else{
				products[key] = value;
			}
		}

		productsJson = JSON.stringify(products);
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`, callBack, 'post',{context: 'edit', value:productsJson});
	}

	var myUrl = new URL(window.location);
	var profileUser = <?php echo bp_displayed_user_id() ?>;
	let userId = <?php echo get_current_user_id() ?>==profileUser?"me":profileUser;
	var currentBpUser = null;
	let st_selectedProducts = {};
	let productsDataId = <?php echo $products_field_id ?>;
	let debounce_timer;
	let groupId = <?php echo $group_id ?>;

	callBpApi("members/<?php echo $user["user_id"] ?>", addUserDetails, 'get',{populate_extras:true});
	
	var searchInput = document.getElementById("st-search-box");
	searchInput.addEventListener("keyup", function(event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			searchProducts();
		}
	});
</script>