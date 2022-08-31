<?php
function getUserProducts( $data ) {
	global $stPlatformId;
	global $stBackendUrl;
	$the_user = get_user_by('login', $data['id']);
	$count=isset($data['count'])?$data['count']:10;
	$offset=isset($data['offset'])?$data['offset']:0;
	$values = xprofile_get_field_data( 'st_products' , $the_user->id );
	$shop_name = xprofile_get_field_data( 'st_shop_name' , $the_user->id );
	$valuesJson = str_replace(' ', ',', $values);
	$valuesParsed = json_decode($valuesJson, true);
	if(empty($values)){$values = 'nothing';}
	else{
		$values="";
		foreach($valuesParsed as $key => $value) {
			$values = $values.$key.",";
		}
	}
	
	$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?count=$count&offset=$offset&productIds=$values");
	$result = wp_remote_retrieve_body( $response );
	
	$products = json_decode($result);
	foreach ($products->products as $product) {
		if(isset($valuesParsed[$product->id])){
			$product->tid=$valuesParsed[$product->id];
		}
	}
	$products->avatar = get_avatar_url ( $the_user->id);
	$products->cover = bp_attachments_get_attachment( 'url', array( 'item_id' => $the_user->id ) );
	$products->shop_name = $shop_name;
	return $products;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'shoptype/v1', '/shop/(?P<id>.+)', array(
    'methods' => 'GET',
    'callback' => 'getUserProducts',
  ) );
} );

/**
 * Add custom sub-tab on groups page.
 */
function buddyboss_my_shop_tab() {
	wp_enqueue_script( 'my-script-handle', 'url-to/my-script.js', array( 'bp-api-request' ) );
	$path = dirname(plugin_dir_url( __FILE__ ));
	wp_enqueue_style( 'my-shop-css', plugin_dir_url( __FILE__ ) . '/css/st-my-shop.css' );
	// Avoid fatal errors when plugin is not available.
	if ( ! function_exists( 'bp_core_new_nav_item' ) ||
		 ! function_exists( 'bp_loggedin_user_domain' ) ||
		 empty( bp_displayed_user_id() ) ) {

		return;

	  }

	  global $bp;

	  bp_core_new_nav_item(
		array(
		  'name'                => esc_html__( 'My Shop', 'default' ),
		  'slug'                => 'my-shop',
		  'screen_function'     => 'my_shop_screen',
		  'position'            => 100,
		  'parent_url'          => bp_displayed_user_domain() . '/my-shop/',
		  'parent_slug'         => $bp->profile->slug,
		)
	  );
}

add_action( 'bp_setup_nav', 'buddyboss_my_shop_tab' );

/**
 * Set template for new tab.
 */
function my_shop_screen() {
	// Add title and content here - last is to call the members plugin.php template.
	add_action( 'bp_template_title', 'my_shop_tab_title' );
	add_action( 'bp_template_content', 'my_shop_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );
}

/**
 * Set title for My Shop.
 */
function my_shop_tab_title() {
	echo esc_html__( 'My Shop', 'default' );
}

/**
 * Display content of My Shop.
 */
function my_shop_tab_content() {
	global $stPlatformId;
?>
<div class="st-groups" id="st-myshop">
	<div class="st-profile-subtitle">Shop</div>
	<div class="st-profile-content-block">
		<div class="st-add-product-drawer" id="st-add-product-drawer" style="right: -300px;">
			<div class="st-product-drawer" onclick="toggleAddProducts()"><img src="<?php echo plugin_dir_url( __FILE__ )  ?>/images/shop.svg" loading="lazy" alt="" class="st-product-drawer-img"></div>
			<div class="st-product-add-drawer">
				<div class="st-product-search">
					<input class="st-product-search-box" id="st-search-box" name="Search" >
					<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo plugin_dir_url( __FILE__ )  ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
				</div>
				<div class="st-product-search-results" id="st-product-search-results">
					<div class="st-product-select" id="st-product-select-template" style="display: none;">
						<div class="st-product-select-main">
							<div class="st-product-img-div"><img src="<?php echo plugin_dir_url( __FILE__ )  ?>/images/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-product-img-select"></div>
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
					<div class="st-button">Cancel</div>
				</div>
			</div>
		</div>
		<div id="st-myshop-main" class="st-groups-main" style="display:flex;">
			<div id="st-product-template" style="display: none;position: relative;" onmouseout="hideRemoveBtn(this)">
			<a href="#" class="st-product-link" >
				<div class="st-product-img-div"><img src="<?php echo plugin_dir_url( __FILE__ )  ?>/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-product-img"></div>
				<div class="st-product-cost">$00.00</div>
				<div class="st-product-name">Product Name</div>
			</a>
			<div class="st-remove-product" style="display:none;">X</div>
			</div>
		</div>
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
			return error;
		} );
	}

	function addUserDetails(userData){
		currentBpUser = userData;
		loadShopProducts(userData.user_login);
	}

	function toggleAddProducts(){
		let drawer = document.getElementById("st-add-product-drawer");
		if(drawer.style.right == "0px"){
			drawer.style.right = "-300px";
		}else{
			drawer.style.right = "0px";
		}
	}

	function addToShop(shopProducts){
		let selectorNodes = document.getElementsByClassName("st-shop-select");
		let products = shopProducts[0].value.unserialized[0]??"";
		let newProducts = {};
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !products.includes(selectorNodes[i].value)){
				newProducts[selectorNodes[i].value] = shoptype_UI.getUserTracker();
			}
		}
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x, newProducts,x=>loadShopProducts(currentBpUser.user_login)),'get');
	}

	function loadShopProducts(userName) {
		let productTemplate = document.getElementById("st-product-template");
		let productsContainer = document.getElementById("st-myshop-main");
		removeChildren(productsContainer, productTemplate)
		fetch('/wp-json/shoptype/v1/shop/' + userName)
			.then(response => response.json())
			.then(productsJson => {
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, productsJson.products[i],".st-product-img",".st-product-cost");
					newProduct.querySelector(".st-product-link").href= "/products/"+productsJson.products[i].id+"/?tid="+productsJson.products[i].tid;
					newProduct.id = productsJson.products[i].id;
					if(userId=='me'){
						newProduct.querySelector(".st-remove-product").setAttribute("onclick",`event.stopPropagation(); removeProductFromShop("${productsJson.products[i].id}")`);
					}
					productsContainer.appendChild(newProduct);
				}
			});
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
		let options = {text: document.getElementById('st-search-box').value};
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		removeChildren(productsContainer,productTemplate);
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

	function removeProductFromShop(productId){
		let products = {};
		products[productId]='';
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x,products,x=>loadShopProducts(currentBpUser.user_login),true),'get');
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
	let productsDataId = null;

	function initMyShop(){
		if (typeof wp !== "undefined") { 
		    callBpApi("members/"+userId, addUserDetails, 'get',{populate_extras:true});
			callBpApi("xprofile/fields", data=>productsDataId = data.find(field=>field.name=="st_products").id, 'get',{populate_extras:true});
		}else{
			setTimeout(initMyShop,200);
		}

	}

	if(userId=='me'){
		document.getElementById("st-product-template").setAttribute('onmouseover','showRemoveBtn(this)');
	}
	var searchInput = document.getElementById("st-search-box");
	searchInput.addEventListener("keyup", function(event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			searchProducts();
		}
	});
	initMyShop();
	getUserTracker();
</script>
<?php
}

/**
 * Add user menu for My Shop.
 *
 * @return void
 */
function buddyboss_add_my_shop_menu() {

	// Bail, if anything goes wrong.
	if ( ! function_exists( 'bp_loggedin_user_domain' ) ) {
		return;
	}

	printf(
		"<li class='logout-link'><a href='%s'>%s</a></li>",
		trailingslashit( bp_loggedin_user_domain() ) . 'my-shop/',
		esc_html__( 'My Shop', 'default' )
	);
}

add_action( 'buddyboss_theme_after_bb_profile_menu', 'buddyboss_add_my_shop_menu' );
