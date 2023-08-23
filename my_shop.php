<?php
function getUserProducts( $data ) {
	$store_url = $data['id'];
	$the_user = get_user_by('login', $store_url);
	if(isset($the_user->id)){
		return getProductsByUser($the_user->id, $the_user->user_login, $the_user->display_name, $the_user->user_nicename, $store_url, $data);
	}else{
		$field_id = xprofile_get_field_id_from_name( 'st_shop_url');
		global $wpdb;
		$bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

		$query = $wpdb->prepare(
			"SELECT user_id,user_login,user_nicename,user_email,display_name " .
			"FROM $bp_table B, $wpdb->users U " .
			"WHERE B.user_id = U.ID " .
			"AND B.field_id = %d " .
			"AND B.value = %s"
		   , $field_id
		   , $store_url
		);
		$get_desired = $wpdb->get_results($query);
		
		if(count($get_desired)) {
			return getProductsByUser($get_desired[0]->user_id, $get_desired[0]->user_login, $get_desired[0]->display_name, $get_desired[0]->user_nicename, $store_url, $data);
		}else{
			return  null;
		}
	}
}

function getProductsByUser($user_id, $user_login, $display_name, $user_nicename, $store_url, $data){
	global $stPlatformId;
	global $stBackendUrl;
	
	if(!can_have_myshop($user_id)){
		return null;
	}

	$count=isset($data['count'])?$data['count']:10;
	$offset=isset($data['offset'])?$data['offset']:0;
	$values = xprofile_get_field_data( 'st_products' , $user_id );
	$shop_theme = xprofile_get_field_data( 'st_shop_theme' , $user_id );
	$shop_facebook = xprofile_get_field_data( 'st_shop_facebook' , $user_id );
	$shop_twitter = xprofile_get_field_data( 'st_shop_twitter' , $user_id );
	$shop_instagram = xprofile_get_field_data( 'st_shop_instagram' , $user_id );
	$shop_youtube = xprofile_get_field_data( 'st_shop_youtube' , $user_id );
	$valuesJson = str_replace(' ', ',', $values);
	$valuesParsed = json_decode($valuesJson, true);
	if(empty($values)){
		$values = 'nothing';
	}
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
	$groupSlug = sanitize_title($user_login)."_coseller";
	$products->group_slug = $groupSlug;
	$group_id = BP_Groups_Group::group_exists( $groupSlug );
	if(isset($group_id)){
		$group = groups_get_group($group_id);
		$products->cover = bp_get_group_cover_url($group);
		$products->avatar = bp_get_group_avatar_url($group);
		$products->shop_name = $group->name;
		$products->shop_bio = $group->description;
	}
	$products->user_avatar = get_avatar_url ( $user_id );
	$products->user_cover = bp_attachments_get_attachment( 'url', array( 'item_id' => $user_id ) );
	$products->user_name = $display_name;
	$products->user_nicename = $user_nicename;
	$products->user_login = $user_login;
	$products->shop_url = $store_url;
	$products->facebook = $shop_facebook;
	$products->twitter = $shop_twitter;
	$products->instagram = $shop_instagram;
	$products->youtube = $shop_youtube;
	$products->theme = isset($shop_theme)?$shop_theme:"theme-01";
	return $products;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'shoptype/v1', '/shop/(?P<id>.+)', array(
    'methods' => 'GET',
    'callback' => 'getUserProducts',
  ) );
  register_rest_route( 'shoptype/v1', '/user', array(
    'methods' => 'POST',
    'callback' => 'getUserByFace',
  ) );
  register_rest_route( 'shoptype/v1', '/shop-url-check/(?P<shopurl>.+)', array(
    'methods' => 'GET',
    'callback' => 'checkUrlFree',
  ) );
  register_rest_route( 'shoptype/v1', '/registerface', array(
    'methods' => 'POST',
    'callback' => 'setUserFace',
  ) );
} );

function user_has_role($user_id, $role_name)
{
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
    return in_array($role_name, $user_roles);
}

function can_have_myshop($user_id){
	global $restrict_myshop;
	if(!$restrict_myshop){
		return true;
	}
	if(user_has_role($user_id, "myshop_owner") || user_has_role($user_id, "administrator")){
		return true;
	}else{
		return false;
	}
}

function getUserByFace( $data ) {
	$userFaceImage = $data['imageBS64'];
	$field_id = xprofile_get_field_id_from_name( 'st_face_id');
	$body = array('imageBS64' => $userFaceImage);
	$args = array(
        'method'      => 'POST',
        'headers'     => array(
            'Authorization' => 'Basic ' . base64_encode( 'awakeme:ZLbdq25TangQwjAU'),
            'Content-Type'  => 'application/json',
        ),
        'body'        => json_encode($body),
    );
	$response = wp_remote_post("https://s.tangent.ai/authenticate", $args);
	$result     = wp_remote_retrieve_body( $response );
	
	if (!empty($result)) {
		$matchUsers = json_decode($result);
		$sql = "(";
		foreach ($matchUsers->users as $matchedUser) {
			$sql = "{$sql}B.value LIKE '%{$matchedUser}%' OR ";
		}
		$sql = substr($sql, 0, -4);
		$sql = "{$sql})";
		global $wpdb;
		$bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

		$query = $wpdb->prepare(
			"SELECT user_id,user_login,user_nicename,user_email,display_name " .
			"FROM $bp_table B, $wpdb->users U " .
			"WHERE B.user_id = U.ID " .
			"AND B.field_id = $field_id " .
			"AND $sql"
		);

		$get_desired = $wpdb->get_results($query);

		if(count($get_desired)) {
			return $get_desired[0];
		} else {
			return $wpdb->last_query;
		}
	}
	return $body;
}

function setUserFace($data){
	$userFaceImage = $data['imageBS64'];
	$body = array('imageBS64' => $userFaceImage);
	$args = array(
        'method'      => 'POST',
        'headers'     => array(
            'Authorization' => 'Basic ' . base64_encode( 'awakeme:ZLbdq25TangQwjAU'),
            'Content-Type'  => 'application/json',
        ),
        'body'        => json_encode($body),
    );
	$response = wp_remote_post("https://s.tangent.ai/consume", $args);
	$result     = wp_remote_retrieve_body( $response );
	
	if (!empty($result)) {
		$matchUsers = json_decode($result);
		$user_id = get_current_user_id();
		if(isset($matchUsers->id)){
			xprofile_set_field_data( 'st_face_id' , $user_id, $matchUsers->id);
			return true;
		}
	}
	return false;
}

function checkUrlFree( $data ) {
	$shop_url = $data['shopurl'];
	$the_user = get_user_by('login', $shop_url);
	if(isset($the_user->id)){
		return array('status'=>"taken");
	}
	
	$field_id = xprofile_get_field_id_from_name( 'st_shop_url');
	global $wpdb;
    $bp_table = $wpdb->prefix . 'bp_xprofile_data'; 

    $query = $wpdb->prepare(
        "SELECT user_id,user_login,user_nicename,user_email,display_name " .
        "FROM $bp_table B, $wpdb->users U " .
        "WHERE B.user_id = U.ID " .
        "AND B.field_id = %d " .
        "AND B.value = %s"
       , $field_id
       , $shop_url
    );
    $get_desired = $wpdb->get_results($query);

    if(count($get_desired)) {
        return array('status'=>"taken");
    } else {
        return array('status'=>"available");
    }
}

/**
 * Add custom sub-tab on groups page.
 */
function buddyboss_my_shop_tab() {
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
		  'name'                => esc_html__( 'My Shop', 'shoptype' ),
		  'slug'                => 'my-shop',
		  'screen_function'     => 'my_shop_screen',
		  'position'            => 100,
		  'parent_url'          => bp_displayed_user_domain() . '/my-shop/',
		  'parent_slug'         => $bp->profile->slug,
		)
	  );
}

add_action( 'bp_setup_nav', 'buddyboss_my_shop_tab' );



function custom_redirects() {
	global $wp;
	$curr_url = home_url( $wp->request );

	if(str_contains($curr_url,"/groups/")){
		if(str_starts_with(substr($curr_url,strrpos($curr_url,'_')),"_coseller")){
			$shop_name = substr($curr_url,strrpos($curr_url,'/'),strrpos($curr_url,'_')-strrpos($curr_url,'/'));
			wp_redirect( "/shop/$shop_name", 301 );
			exit();
		}
	}
}
add_action( 'template_redirect', 'custom_redirects' );

/**
 * Set template for new tab.
 */
function my_shop_screen() {
	// Add title and content here - last is to call the members plugin.php template.
	if( bp_displayed_user_id() !== get_current_user_id()){
		$displayUser = get_userdata(bp_displayed_user_id());
		wp_redirect( "/shop/".$displayUser->user_login, 302 );
	}else{
		add_action( 'bp_template_title', 'my_shop_tab_title' );
		add_action( 'bp_template_content', 'my_shop_tab_content' );
		bp_core_load_template( 'buddypress/members/single/plugins' );
	}
}

/**
 * Set title for My Shop.
 */
function my_shop_tab_title() {
	echo esc_html__( 'My Shop', 'shoptype' );
}

/**
 * Display content of My Shop.
 */
function my_shop_tab_content() {
	global $stPlatformId;
	global $bp;
	$displayUser = get_userdata(bp_displayed_user_id());
	$groupSlug = strtolower($displayUser->user_login."_coseller");
	//$cosellerGp = bp_get_group_by('slug', $groupSlug);
	$group_id = BP_Groups_Group::group_exists( $groupSlug );
	$user_id = get_current_user_id();
	$currentUser = get_userdata($user_id);
	$editable =	$currentUser==$displayUser;
	if(!isset($group_id)){
		if($editable){
			$group_id = groups_create_group(array(
				'creator_id'=>$user_id,
				'name'=> $currentUser->user_login,
				'slug'=> $groupSlug,
				'enable_forum'=>1
			));
			groups_accept_invite($user_id, $group_id);
		}
	}

	if(isset($group_id)){
		$group = groups_get_group($group_id);
		$group_cover = bp_get_group_cover_url($group);
		$group_img = bp_get_group_avatar_url($group);
	}
?>
<div class="st-groups" id="st-myshop">
	<div class="st-profile-content-block">
		<div class="st-store-grid st-store-header">
			<div id="banner-wrap">
				<div class="st-store-banner-wrap" style="width: 100%; left: 0%;right: 0%;margin-left: 0;margin-right: 0;">
					<img src="<?php echo $group_cover ?>" alt="" class="store-banner" width="439" height="115" onclick="document.getElementById('profileBGFile').click()">
				</div>
				<div id="inner-element" style="display: flex;">
					<div class="store-brand" style="margin-right: 20px;">
						<div class="store-icon-img"><img src="<?php echo $group_img ?>" onclick="document.getElementById('profileImageFile').click()" alt="" class="store-icon" width="150" height="150"> </div>
					</div>
					<div class="store-info">
						<h3 id="store_title"  contenteditable="<?php echo $editable ? 'true' : 'false' ?>"><?php echo $group->name ?></h3>
						<p id="store_bio"  contenteditable="<?php echo $editable ? 'true' : 'false' ?>"><?php echo $group->description ? $group->description : "description" ?></p>
					</div>
				</div>
				<input type="file" id="profileImageFile" onchange="updateProfileImg()" style="display: none;">
				<input type="file" id="profileBGFile" onchange="updateBgImg()" style="display: none;">
			</div>
		</div>

		<div class="st-add-product-drawer" id="st-add-product-drawer">
			<div class="st-product-add-drawer">
				<div class="st-product-search">
					<input class="st-product-search-box" id="st-search-box" name="Search" >
					<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo plugin_dir_url( __FILE__ )  ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
				</div>
				<div class="st-product-search-results" id="st-product-search-results" style="display: none;">
					<div class="st-product-select" id="st-product-select-template" style="display: none;">
						<div class="st-product-select-main">
							<div class="st-product-img-div"><img src="" alt="" class="st-product-img-select"></div>
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
		<div id="st-myshop-main" class="st-groups-main" style="display:flex;">
			<div id="st-product-template" style="display: none;position: relative;" onmouseout="hideRemoveBtn(this)">
			<a href="#" class="st-product-link" >
				<div class="st-product-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-product-img"></div>
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
		loadShopProducts(userData.user_login);
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
		let productTemplate = document.getElementById("st-product-template");
		let productsContainer = document.getElementById("st-myshop-main");
		hideResults();
		removeChildren(productsContainer, productTemplate)
		fetch('/wp-json/shoptype/v1/shop/' + userName + '?count=100')
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
		productNode.querySelector(imgTag).setAttribute("data-src", product.primaryImageSrc.imageSrc);
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
	let debounce_timer;
	let groupId = <?php echo $group_id ?>;

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
	document.getElementById("store_title").addEventListener("input", (e) => updateStoreName(e.currentTarget.textContent), false);
	document.getElementById("store_bio").addEventListener("input", (e) => updateStoreBio(e.currentTarget.textContent), false);
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

function my_bp_custom_group_types() {
    bp_groups_register_group_type( 'coseller_shop', array(
        'labels' => array(
            'name' => 'Coseller Shops',
            'singular_name' => 'Coseller Shop'
        ),
 
        // New parameters as of BP 2.7.
        'has_directory' => 'coseller_shop',
        'show_in_create_screen' => false,
        'show_in_list' => false,
        'description' => 'Shoptype Coseller Shops groups',
        'create_screen_checked' => false
    ) );
}
add_action( 'bp_groups_register_group_types', 'my_bp_custom_group_types' );

