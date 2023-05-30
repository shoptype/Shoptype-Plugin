<?php

/*
* Template name: New Shoptype My Shop Wizard
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
* @package shoptype
*/

global $stPlatformId;
global $stFilterJson;
global $stDefaultCurrency;


$wizard_type = urldecode(get_query_var( 'stwizard' ));
$path = dirname(plugin_dir_url( __FILE__ ));
$user_id = get_current_user_id();
$currentUser = get_userdata($user_id);
$groupSlug = strtolower($currentUser->user_login."_coseller");

$group_id = BP_Groups_Group::group_exists( $groupSlug );
$shop_products = xprofile_get_field_data( 'st_products' , $currentUser->id );
$shop_theme = xprofile_get_field_data( 'st_shop_theme' , $currentUser->id );
$shop_url = xprofile_get_field_data( 'st_shop_url' , $currentUser->id );
$shop_facebook = xprofile_get_field_data( 'myshop-facebook' , $currentUser->id );
$shop_twitter = xprofile_get_field_data( 'myshop-twitter' , $currentUser->id );
$shop_instagram = xprofile_get_field_data( 'myshop-instagram' , $currentUser->id );
$shop_youtube = xprofile_get_field_data( 'myshop-youtube' , $currentUser->id );

if(isset($shop_url)){
	$encodedShopUrl = get_site_url()."/shop/".$shop_url;
}else{
	$encodedShopUrl = get_site_url()."/shop/".$currentUser->user_login;
}

if($wizard_type=="open" && !($shop_products=="" || $shop_products=="{}")){
	header("Location: {$encodedShopUrl}");
	exit;
}

if(!isset($group_id)){
		$group_id = groups_create_group(array(
			'creator_id'=>$user_id,
			'name'=> $currentUser->user_login,
			'slug'=> $groupSlug,
			'enable_forum'=>1
		));
		bp_groups_set_group_type( $group_id, array("coseller_shop") );
		groups_accept_invite($user_id, $group_id);
}

if(isset($group_id)){
	$group = groups_get_group($group_id);
	$group_cover = (empty(bp_get_group_cover_url($group))) ? st_locate_file("images/shop-banner.jpg") : bp_get_group_cover_url($group);
	$group_img =(empty(bp_get_group_avatar_url($group))) ? st_locate_file("images/shop-profile.jpg") : bp_get_group_avatar_url($group);
}
$profileImage = get_avatar_url($user_id);


get_header();

$theme_url = get_template_directory_uri();

// Output the theme path
?>


	<div id="filterContainer" class="menu-main" style="display:none">
		<div class="menu-container" id="st-filter">
			<div class="menu-title">
				<h3 class="menu-title-heading">Filter Menu</h3>
			</div>
			<div class="menu-list">
				<div id="menuOptionList" class="menu-options">
					<div class="menu-filters">	
						<?php
						
						if(isset($stFilterJson)){
							
							$stFilters = json_decode($stFilterJson);
							if(isset($stFilters)){
								foreach ($stFilters as $filter) {
								?>
									<div class="menu-brand-select">
									<div class="menu-option-block1">
									<h4 class="menu-option-title"><?php echo $filter->name ?></h4>
									</div>
									<select name="<?php echo $filter->name ?>" key="<?php echo $filter->key ?>" id="<?php echo str_replace(" ","-",$filter->name) ?>" class="menu-option-select" <?php echo $filter->multi; ?>>
									<?php foreach ($filter->values as $filterValue) {	?>
									<option value="<?php echo $filterValue->value ?>"><?php echo $filterValue->name ?></option>
									<?php } ?>
									</select>
									</div>

								<?php
								}
							}
						}
						?>
						<div class="menu-brand-select">
							<div class="menu-option-block1">
								<h4 class="menu-option-title">Sort By</h4>
							</div>
							<select name="sortBy" key="sortBy" id="sortBy" class="menu-option-select">
							<option value="">None</option>
							<option value="price">Price</option>
							<option value="createdAt">Latest</option>
							<option value="quantitySold">Most Sold</option>
							</select>
						</div>
						<div class="menu-brand-select">
							<div class="menu-option-block1">
								<h4 class="menu-option-title">Sort Order</h4>
							</div>
							<select name="sortOrder" key="orderBy" id="sortOrder" class="menu-option-select">
								<option value="asc">ascending</option>
								<option value="desc">descending</option>
							</select>
							<select name="sortOrder" key="currency" id="sortOrder" class="menu-option-select" hidden>
								<option value="<?php echo $stDefaultCurrency;?>"><?php echo $stDefaultCurrency;?></option>
							</select>		 
						</div>
					</div>
				</div>
			</div>
			<div class="menu-apply-div">
				<div class="menu-apply-button">
					<h3 class="menu-apply-button-lable" onclick="clearFilters()">Reset</h3>
				</div>
				<div class="menu-apply-button">
					<h3 class="menu-apply-button-lable" onclick="filterProducts()">Apply & Refresh</h3>
				</div>
			</div>
		</div>
		<div class="st-filter-btn" onclick="toggleFilter()"><img src="<?php echo st_locate_file("images/Filter-Icon.png"); ?>" loading="lazy" alt="" class="st-filter-img"></div>
	</div>

<script>
	function filterProducts(){
		var selected = {};
		Array.from(document.getElementsByClassName("menu-option-select")).forEach(x=>{ 
					selected[x.getAttribute("key")] = [...x.options]
										.filter(option => option.selected)
										.map(option => option.value);
		});
		for (const prop in selected) {
			options[prop] = selected[prop].join(",");
		}
		searchProducts(true);
		toggleFilter();
	}
	
	function clearFilters(){
		Array.from(document.getElementsByClassName("menu-option-select")).forEach(x=>x.selectedIndex=0);
		filterProducts();
	}
	
	function toggleFilter(){
		var btn = document.getElementById("filterContainer");
		if(btn.style.left=="0px"){
			 btn.style.left="-300px";
		}else{
			 btn.style.left="0px";
		}
	}

	function ensureUserLogin(){
		if(shoptype_UI.user==null){
			window.location.replace(st_settings.loginUrl+"?redirectUrl="+window.location.href);
		}
	}

	if(typeof shoptype_UI !== 'undefined'){
		ensureUserLogin();
	}else{
		document.addEventListener("ShoptypeUILoaded", ()=>{
			ensureUserLogin();
		});
	}

</script>
<div class="st-myshop">
	<div class="st-myshop-status">
		<div class="st-myshop-state st-myshop-state-selected" id="state-1">1</div>
		<div class="st-myshop-connector"></div>
		<div class="st-myshop-state" id="state-2">2</div>
		<div class="st-myshop-connector"></div>
		<div class="st-myshop-state" id="state-3">3</div>
		<div class="st-myshop-connector"></div>
		<div class="st-myshop-state" id="state-4">4</div>
	</div>
	<div class="st-my-shop-details">
		<div>
			<h2 class="st-myshop-header">Create Store</h2>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Name</div>
			<input class="st-myshop-name" id="myshop-name" value="<?php echo $group->name ?>"/>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Bio</div>
			<input class="st-myshop-bio" id="myshop-bio" value="<?php echo $group->description ?>"/>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store URL</div>
			<input class="st-myshop-bio" id="myshop-url" onchange="checkUrlAvailable(this)" value="<?php echo $shop_url ?>"/>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Social Links</div>
			<div class="st-myshop-social-links">
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Facebook</div>
					<input class="st-myshop-bio" id="myshop-facebook" value="<?php echo $shop_facebook ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Twitter</div>
					<input class="st-myshop-bio" id="myshop-twitter" value="<?php echo $shop_twitter ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Instagram</div>
					<input class="st-myshop-bio" id="myshop-instagram" value="<?php echo $shop_instagram ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Youtube</div>
					<input class="st-myshop-bio" id="myshop-youtube" value="<?php echo $shop_youtube ?>"/>
				</div>
				
			</div>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Logo</div>
			<a href="#" onclick="document.getElementById('profileImageFile').click()" class="st-myshop-img-select" ><img id="store-icon" src="<?php echo $group_img ?>" loading="lazy" alt="" class="st-myshop-store-img">
				<div class="div-block-11">
<div class="st-myshop-img-txt-main">

					<div class="st-myshop-img-txt">To upload you file<br><div class="st-myshop-img-txt-type">
						File format: JPG / PNG
						</div></div>
					<div class="st-myshop-img-lnk">Click here</div>
</div>
								<input type="file" id="profileImageFile" onchange="updateShopImg()" style="display: none;">
				</div>
			</a>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Banner Image</div>
			<a href="#" onclick="document.getElementById('profileBGFile').click()" class="st-myshop-img-select"><img id="store-banner" src="<?php echo $group_cover ?>" loading="lazy" alt="" class="st-myshop-store-banner">
				<div class="div-block-11">
<div class="st-myshop-img-txt-main">
					<div class="st-myshop-img-txt">To upload you file<br><div class="st-myshop-img-txt-type">
						File format: JPG / PNG
<br>
Min. image size: 1300px x 225px
						</div></div>
					<div class="st-myshop-img-lnk">Click here</div>
</div>
								<input type="file" id="profileBGFile" onchange="updateBgImg()" style="display: none;">
				</div>
			</a>
		</div>
	<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Profile Image</div>
			<a href="#" onclick="document.getElementById('profileImgFile').click()" class="st-myshop-img-select"><img id="profile-img" src="<?php echo $profileImage ?>" loading="lazy" alt="" class="st-myshop-store-img">
				<div class="div-block-11">
<div class="st-myshop-img-txt-main">

					<div class="st-myshop-img-txt">To upload you file<br><div class="st-myshop-img-txt-type">
						File format: JPG / PNG
						</div></div>
					<div class="st-myshop-img-lnk">Click here</div>
					</div>
					<input type="file" id="profileImgFile" onchange="updateProfileImg()" style="display: none;">
				</div>
			</a>
		</div>
	</div>
	<div class="st-myshop-style" style="display:none">
		<div>
			<h2 class="st-myshop-header">Choose your theme for the store</h2>
		</div>
		<div class="st-myshop-theme-list">
			<div class="st-myshop-theme">
				<div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-01" name="theme_select" value="theme-01" <?php echo ($shop_theme=="theme-01")?"checked":"" ?>></div>
				<div class="div-block-9"><img src="<?php echo $path; ?>/images/theme-01.png" loading="lazy" alt="<?php echo $shop_theme ?>" class="st-myshop-theme-img">
					<div class="st-myshop-theme-name">design 1</div>
				</div>
			</div>
			<div class="st-myshop-theme">
				<div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-02" name="theme_select" value="theme-02" <?php echo $shop_theme=="theme-02"?"checked":"" ?>></div>
				<div class="div-block-9"><img src="<?php echo $path; ?>/images/theme-02.png" loading="lazy" alt="" class="st-myshop-theme-img">
					<div class="st-myshop-theme-name">design 2</div>
				</div>
			</div>
		</div>
	</div>
	<div class="st-myshop-products"	style="display:none">
		<h2 class="st-myshop-header">Choose products to add to the store</h2>
		
		<div>
			<div class="st-myshop-search">
				<input class="st-myshop-search-box" id="st-search-box" name="Search" >
				<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $theme_url ?>/img/search-icon.png" loading="lazy" alt="" class="st-product-search-img"></div>
			</div>
			<div class="st-myshop-search-results" id="st-product-search-results">
				<div class="st-myshop-product-select" id="st-product-select-template" style="display: none;">
					<div class="st-product-select-main">
						<div class="st-product-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-product-img-select"></div>
						<div class="st-product-details-block">
							<div class="st-product-name">Product Name</div>
							<div class="st-product-cost-select">$00.00</div>
						</div>
						<input class="st-myshop-select" type="checkbox" id="product000" name="" value="" onchange="productSelect(this)">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="st-myshop-complete"	style="display:none">
		<div class="div-block-10"></div>
		<h2 class="st-myshop-header">CONGRATULATIONS</h2>
		<div class="st-myshop-txt">Your Store Setup is Complete</div>
		<div>
			<div class="text-block-3">Share your store on social media</div>
			<div class="st-myshop-social">
				<a id="fb_link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encodedShopUrl ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/fb_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
				<a id="wa_link" href="whatsapp://send?text=<?php echo "$sharetxt $encodedShopUrl" ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/whatsapp_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
				<a id="tw_link" href="http://twitter.com/share?text=<?php echo "{$sharetxt}&url={$encodedShopUrl}" ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/twitter_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
				<a id="pi_link" href="https://pinterest.com/pin/create/link/?url=<?php echo "{$encodedShopUrl}&media={$group_img}&description={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/insta_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
				<a id="tgram_link" href="https://telegram.me/share/url?url=<?php echo "{$encodedShopUrl}&TEXT={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/telegram_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
				<a id="ln_link" href="https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=<?php echo "{$encodedShopUrl}&title={$group->name}&summary={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo st_locate_file("images/linkedIn_icon.png"); ?>" loading="lazy" alt="" class="image"></a>
			</div>
		</div>
		<a href="<?php echo $encodedShopUrl ?>" class="st-myshop-button" id="goto_shop_btn">Go to Store</a>
	</div>
	<div class="st-myshop-bottom">
		<a id="st-next-button" href="#" onclick="moveState()" class="st-myshop-button">Save & Continue</a>
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
		let selectorNodes = document.getElementsByClassName("st-myshop-select");
		let products = shopProducts[0].value.unserialized[0]??"";
		let newProducts = {};
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !products.includes(selectorNodes[i].value)){
				newProducts[selectorNodes[i].value] = shoptype_UI.getUserTracker();
			}
		}
		hideResults();
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x, newProducts,x=>loadShopProducts(currentBpUser.user_login)),'get');
	}

	function loadShopProducts(userName) {
		let productTemplate = document.getElementById("st-product-template");
		let productsContainer = document.getElementById("st-myshop-main");
		hideResults();
		removeChildren(productsContainer, productTemplate)
		fetch('/wp-json/shoptype/v1/shop/' + userName + '?count=1000')
			.then(response => response.json())
			.then(productsJson => {
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, productsJson.products[i],".st-product-img",".st-product-cost");
					newProduct.querySelector(".st-product-link").href= "/products/"+productsJson.products[i].id+"/?tid="+productsJson.products[i].tid;
					newProduct.id = productsJson.products[i].id;
					newProduct.querySelector(".st-remove-product").setAttribute("onclick",`event.stopPropagation(); removeProductFromShop("${productsJson.products[i].id}")`);
					productsContainer.appendChild(newProduct);
				}
			});
	}
	
	function moveState(){
		switch(st_shop_state+1) {
			case 0:
				moveToDetails();
			break;
			case 1:
				if(document.getElementById("myshop-name").value == ""){
					ShoptypeUI.showError("Shop Name cannot be empty.");
					return;
				}
				if(document.getElementById("myshop-url").value == ""){
					ShoptypeUI.showError("Store URL cannot be empty.");
					return;
				}	
				if(document.getElementById("store-icon").src == ""){
					ShoptypeUI.showError("Shop Icon cannot be empty.");
					return;
				}
				moveToTheme();
			break;
			case 2:
				var selectedTheme = document.querySelector('input[name="theme_select"]:checked').value;
				if(!selectedTheme || selectedTheme === ""){
					ShoptypeUI.showError("You must select a theme.");
					return;
				}
				callBpApi(`xprofile/${themesId}/data/${currentBpUser.id}`, x=>{}, 'post',{context: 'edit', value:selectedTheme});
				moveToProducts();
			break;
			case 3:
				callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,addToShop,'get');
				moveToComplete();
			break;
		}
		st_shop_state++;
	}
	
	function moveToDetails(){
		document.querySelector(".st-my-shop-details").style.display="";
		document.querySelector(".st-myshop-style").style.display="none";
		document.querySelector(".st-myshop-products").style.display="none";
		document.querySelector(".st-myshop-complete").style.display="none";
		document.querySelector("#st-next-button").style.display="";
		document.querySelector("#st-next-button").style.position="";	
		document.getElementById("state-1").classList.add("st-myshop-state-selected");
		document.getElementById("state-2").classList.remove("st-myshop-state-selected");
		document.getElementById("state-3").classList.remove("st-myshop-state-selected");
		document.getElementById("state-4").classList.remove("st-myshop-state-selected");
	}
	
	function moveToTheme(){
		clearTimeout(debounce_timer);
		var data = {
			context: 'edit',
			name: document.getElementById("myshop-name").value,
			description: document.getElementById("myshop-bio").value,
		}
		callBpApi("groups/"+groupId,(d)=>{showThemeSelect();},"put",data);
		var shopUrl = document.getElementById("myshop-url").value;
		shopUrl = encodeURI(shopUrl);
		callBpApi(`xprofile/${myshopUrlId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:shopUrl});
		callBpApi(`xprofile/${myshopFacebookId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:document.getElementById("myshop-facebook").value});
		callBpApi(`xprofile/${myshopTwitterId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:document.getElementById("myshop-twitter").value});
		callBpApi(`xprofile/${myshopInstagramId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:document.getElementById("myshop-instagram").value});
		callBpApi(`xprofile/${myshopYoutubeId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:document.getElementById("myshop-youtube").value});
	}
	function moveToProducts(){
		searchProducts();
		document.querySelector(".st-my-shop-details").style.display="none";
		document.querySelector(".st-myshop-style").style.display="none";
		document.querySelector(".st-myshop-products").style.display="";
		document.querySelector("#filterContainer").style.display="";	
		document.querySelector(".st-myshop-complete").style.display="none";
		document.querySelector("#st-next-button").style.display="";
		document.querySelector("#st-next-button").style.position="fixed";
		document.getElementById("state-3").classList.add("st-myshop-state-selected");
		document.getElementById("state-2").classList.remove("st-myshop-state-selected");
		document.getElementById("state-2").classList.add("st-myshop-state-done");
	}
	
	function moveToComplete(){
		document.querySelector(".st-my-shop-details").style.display="none";
		document.querySelector(".st-myshop-style").style.display="none";
		document.querySelector(".st-myshop-products").style.display="none";
    	document.querySelector("#filterContainer").style.display="none";
		document.querySelector(".st-myshop-complete").style.display="";
		document.querySelector("#st-next-button").style.display="none";
		document.querySelector("#st-next-button").style.position="";
		document.getElementById("state-4").classList.add("st-myshop-state-selected");
		document.getElementById("state-3").classList.remove("st-myshop-state-selected");
		document.getElementById("state-3").classList.add("st-myshop-state-done");
	}
	
	function showThemeSelect(){
		document.querySelector(".st-my-shop-details").style.display="none";
		document.querySelector(".st-myshop-style").style.display="";
		document.querySelector(".st-myshop-products").style.display="none";
		document.querySelector(".st-myshop-complete").style.display="none";
		document.querySelector("#st-next-button").style.display="";
		document.querySelector("#st-next-button").style.position="";
		document.getElementById("state-1").classList.remove("st-myshop-state-selected");
		document.getElementById("state-1").classList.add("st-myshop-state-done");
		document.getElementById("state-2").classList.add("st-myshop-state-selected");
	}
	 
	function updateShopImg(){
		var fileSelect = document.getElementById("profileImageFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var img = new Image;
		img.onload = function() {
			getImageScaled(img,(blob)=>{
				var formData = new FormData();
				formData.append( 'action', 'bp_avatar_upload' );
				formData.append( 'file', blob, "shop_profile.jpg" );
				pushBpApi(`groups/${groupId}/avatar`, (d)=>{document.getElementById("store-icon").src = d[0].full}, "post", formData);
			});
		}
		img.src = URL.createObjectURL(fileSelect.files[0]);
	}
	function scaleImage(img){
		const canvas = document.createElement("canvas");
		const ctx = canvas.getContext("2d");
		canvas.width = 600;
		canvas.height = 600;
		var hRatio = canvas.width  / img.width    ;
		var vRatio =  canvas.height / img.height  ;
		var ratio  = Math.min ( hRatio, vRatio );
		var centerShift_x = ( canvas.width - img.width*ratio ) / 2;
		var centerShift_y = ( canvas.height - img.height*ratio ) / 2;  
		ctx.clearRect(0,0,canvas.width, canvas.height);
		ctx.drawImage(img, 0,0, img.width, img.height, centerShift_x,centerShift_y,img.width*ratio, img.height*ratio);
		return canvas;
	}
	
	function getImageScaled(img, callBack) {
		var canvas = scaleImage(img);
		canvas.toBlob(callBack, 'image/png');
	}
	
	function getImageScaledBase64(img){
		var canvas = scaleImage(img);
		return canvas.toDataURL();
	}
	
	function updateBgImg(){
		var fileSelect = document.getElementById("profileBGFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var formData = new FormData();
		formData.append( 'action', 'bp_cover_image_upload' );
		formData.append( 'file', fileSelect.files[0] );
		pushBpApi(`groups/${groupId}/cover`, (d)=>{document.getElementById("store-banner").src = d[0].image}, "post", formData);
	}
	
	function updateProfileImg(){
		var fileSelect = document.getElementById("profileImgFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}

		var img = new Image;
		img.onload = function() {
			var canvas = scaleImage(img);
			canvas.toBlob((blob)=>{
				var formData = new FormData();
				formData.append( 'action', 'bp_avatar_upload' );
				formData.append( 'file', blob, "user_profile_img.jpg" );
				pushBpApi(`members/${profileUser}/avatar`, (d)=>{document.getElementById("profile-img").src = d[0].image}, "post", formData);
				fetch('https://shopthatface-com.ibrave.host/wp-json/shoptype/v1/registerface', {
					method: 'post',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify({'imageBS64':canvas.toDataURL()})
				}).then(async (response) => {
					document.getElementById('profile-img').src = canvas.toDataURL();
				}).catch((error) => {
				});
			});
		}
		img.src = URL.createObjectURL(fileSelect.files[0]);
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

	function searchProducts(remove=true) {
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		if(remove){
		removeChildren(productsContainer,productTemplate);
		myshop_offset=1;
		}
 
		options['text'] = document.getElementById('st-search-box').value;
		options['offset'] = myshop_offset;
		
		showResults();
		st_platform.products(options)
			.then(productsJson => {
				myshop_offset+=productsJson.products.length;
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, productsJson.products[i],".st-product-img-select",".st-product-cost-select");
					newProduct.id = "search-" + productsJson.products[i].id;
					newProduct.querySelector("input").id = "select-" + productsJson.products[i].id;
					newProduct.querySelector("input").value = productsJson.products[i].id;
					productsContainer.appendChild(newProduct);
				}
			scrollLoading = false;
			});
	}

	function removeChildren(node, dontRemove){
		let length = node.children.length;
		for (var i = length - 1; i >= 0; i--) {
			if(node.children[i]!=dontRemove && !node.children[i].querySelector(".st-myshop-select").checked){
				node.children[i].remove();
			}
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
	
	function checkUrlAvailable(element){
		var testUrl = element.value;
		testUrl= testUrl.trim();
		testUrl = testUrl.replace(/[&\/\\#, +()$~%.':*?<>{}]/g, '-');
		element.value = testUrl;
		fetch("/wp-json/shoptype/v1/shop-url-check/"+testUrl)
			.then((response) => response.json())
			.then(data=>{
			if(data.status === "taken"){
				element.value = currentShopUrl;
				if(testUrl != currentShopUrl){
					ShoptypeUI.showError(`the url ${testUrl} is already in use please choose another one.`);
				}
			}else{
				setShopUrl("/shop/"+testUrl);
			}
			});
	}
	
	function setShopUrl(shopUrl){
		document.getElementById("fb_link").href = "https://www.facebook.com/sharer/sharer.php?u=" + shopUrl;
		document.getElementById("wa_link").href = "whatsapp://send?text=<?php echo "$sharetxt" ?> " + shopUrl;
		document.getElementById("tw_link").href = "http://twitter.com/share?text=<?php echo $sharetxt ?>&url=" + shopUrl;
		document.getElementById("pi_link").href = "https://pinterest.com/pin/create/link/?url=" + shopUrl + "<?php echo "{$encodedShopUrl}&media={$group_img}&description={$sharetxt}" ?>";
		document.getElementById("tgram_link").href = "https://telegram.me/share/url?url=" + shopUrl + "<?php echo "&TEXT={$sharetxt}" ?>";
		document.getElementById("ln_link").href = "https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=" + shopUrl + "<?php echo "&title={$group->name}&summary={$sharetxt}" ?>";
		document.getElementById("goto_shop_btn").href = shopUrl;
	}

	var myUrl = new URL(window.location);
	var profileUser = <?php echo get_current_user_id() ?>;
	var currentShopUrl = "<?php echo $shop_url ?>";
	var currentBpUser = null;
	let st_selectedProducts = {};
	let productsDataId = null;
	let myshopUrlId = null;
	let myshopFacebookId = null;
	let myshopTwitterId = null;
	let myshopInstagramId = null;
	let myshopYoutubeId = null;
	let themesId = null;
	let debounce_timer;
	let groupId = <?php echo $group_id ?>;
	let st_shop_state = 0;
	let myshop_offset = 1;
	let scrollLoading = false;
	let options={};
	function initMyShop(){
		if (typeof wp !== "undefined") { 
			callBpApi("members/"+profileUser, addUserDetails, 'get',{populate_extras:true});
			callBpApi("xprofile/fields", setFieldId, 'get',{populate_extras:true});
		}else{
			setTimeout(initMyShop,200);
		}
	}

	function setFieldId(data){
		themesId = data.find(field=>field.name=="st_shop_theme").id;
		myshopUrlId = data.find(field=>field.name=="st_shop_url").id;
		myshopFacebookId = data.find(field=>field.name=="myshop-facebook").id;
		myshopTwitterId = data.find(field=>field.name=="myshop-twitter").id;
		myshopInstagramId = data.find(field=>field.name=="myshop-instagram").id;
		myshopYoutubeId = data.find(field=>field.name=="myshop-youtube").id;
		productsDataId = data.find(field=>field.name=="st_products").id;
	}
	
	scrollContainer = document.getElementById("st-product-search-results");
	window.addEventListener('scroll',()=>{
		const {scrollHeight,scrollTop,clientHeight} = document.documentElement;
		if((scrollTop + clientHeight > scrollHeight - 5) && (!scrollLoading)){
		scrollLoading = true;
			searchProducts(false);
		}
	});

	initMyShop();
	document.getElementById("store_title").addEventListener("input", (e) => updateStoreName(e.currentTarget.textContent), false);
	document.getElementById("store_bio").addEventListener("input", (e) => updateStoreBio(e.currentTarget.textContent), false);
</script>
<style>
.st-myshop-social-links {display: flex; flex-wrap: wrap; background: #eee; padding: 10px 5px 20px;}
.st-myshop-social-link { margin-right: 5px; margin-left: 5px; width: calc(50% - 10px);}
.st-filter-btn { margin-top: 21px; width: 40px !important; height: 40px !important; padding: 5px 0px; margin-left: -1px; border-radius: 0px 10px 10px 0px; border: solid 1px #ccc;}
#filterContainer { position: fixed !important; margin-left: 0px !important; top: 150px !important; z-index:99999;left: -300px;}
.menu-apply-div { background: #F8F5EC; margin-top: 20px; border-radius: 0px 20px 20px 0px; height: 65px !important; border-radius: 19px !important;}	
img.st-filter-img { width: 20px !important; height: 20px !important; margin-left: 10px !important;}
.menu-container{border: solid 1px #ccc;}
.st-myshop-details-div input,.st-myshop-details-txt,.st-myshop-state,input#myshop-name{color:#1e1e1e;font-family:Poppins,Arial,sans-serif}
.st-myshop-connector,.st-myshop-state{border:1px solid #1e1e1e;opacity:.2}
footer{margin-top:80px}
.st-myshop-state.st-myshop-state-selected{border-color:#075ade;background-color:#075ade;font-style:normal;font-weight:500;font-size:18px;line-height:20px;display:flex;align-items:center;text-align:center;color:#fff;font-family:Poppins,Arial,sans-serif;opacity:1}
.st-myshop-state{display:flex;justify-content:center;align-items:center}
.st-myshop-details-txt{font-style:normal;font-weight:400;font-size:16px;line-height:160%;display:flex;align-items:center}
.st-myshop-details-div input,input#myshop-name{font-weight:400;font-size:16px;line-height:160%;padding-left:12px}
h2.st-myshop-header{font-style:normal;font-weight:700;font-size:32px;line-height:110%;text-align:center;color:#1e1e1e;padding-top:50px;padding-bottom:50px}
.st-myshop-state{font-style:normal;font-weight:400;font-size:18px;line-height:20px;display:flex;align-items:center;text-align:center}
.menu-apply-button,a#goto_shop_btn,a#st-next-button{padding:14px 24px;background:#f99a42;border-radius:50px;font-style:normal;font-weight:700;font-size:16px;line-height:20px;text-align:center;color:#fff}
.st-product-cost-select,.st-product-name{font-weight:400;font-size:18px;line-height:120%;font-style:normal;display:flex;font-family:Poppins,Arial,sans-serif}
.st-myshop-state.st-myshop-state-done{opacity:1;background-color:#f99a42;border-color:#f99a42;color:#f99a42}
.st-myshop-state-done::after{content:"✓";color:#ffff;margin-left:-7px}
.st-myshop-search{background:#fff;margin-bottom:50px}
input#st-search-box{border:1px solid rgba(0,0,0,.1);width:100%;border-radius:4px;border-right:none}
.st-product-select-main{padding:20px}
.st-product-img-select{max-height:80px}
.st-product-name{color:#1e1e1e;height:auto}
.st-product-cost-select{margin-top:8px;color:#075ade}
.st-myshop-product-select{margin-top:25px}
.st-myshop-select{appearance:none;-webkit-appearance:none;-moz-appearance:none;outline:0;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;position:relative;width:18px;height:18px;background:#fff;border:1px solid #1e1e1e}
.st-myshop-select::before{font-weight:700;font-size:12px;content:"";position:absolute;top:45%;left:60%;transform:translate(-50%,-50%);width:14px;height:14px;border-radius:3px}
.st-myshop-select:checked::before{content:"\2713";color:#fff}
.st-myshop-select:checked{background-color:orange;border-color:orange}
.st-myshop-complete .text-block-3,.st-myshop-txt{color:#1e1e1e;font-style:normal;font-weight:400;font-size:20px;line-height:110%;font-family:Poppins,Arial,sans-serif}
a.st-myshop-img-select{min-height:170px}
.st-myshop-img-txt-main{display:flex;justify-content:flex-start;align-items:baseline}
.st-myshop-img-txt{font-family:Poppins,Arial,sans-serif;flex-direction:column;font-style:normal;font-weight:500;font-size:16px;line-height:160%;display:flex;align-items:center;text-align:center;color:#1e1e1e}
.st-myshop-img-lnk,.st-myshop-img-txt-type{line-height:160%;align-items:center;font-style:normal;font-weight:500;display:flex;text-align:center}
.st-myshop-img-txt-type{font-size:14px;color:#1e1e1e;opacity:.3}
.div-block-11{border-radius:8px;padding:10px;background-color:rgba(240,240,240,.8)}
.st-myshop-img-lnk{font-size:16px;background:0 0;font-family:Poppins,Arial,sans-serif;color:orange;text-decoration:none}
.st-myshop-store-banner{width:auto}
img.st-product-search-img{filter:invert(0)}
.st-product-search-title{border:1px solid rgba(0,0,0,.1);border-radius:4px;border-left:none}
.st-myshop-theme-name{font-style:normal;font-weight:500;font-size:20px;line-height:110%;align-items:center;text-align:center;color:#1e1e1e;margin-top:25px;margin-bottom:10px}
.st-myshop-theme-list{margin-bottom:70px;border:none}
.st-myshop-theme{flex-direction:column;border:none;align-content:center;justify-content:center;align-content:center;margin:auto}
.st-shop-select{width:25px;height:25px;display:flex;text-align:center;margin:auto auto 20px;align-items:center;margin-right:auto!important}
.st-myshop-theme .div-block-9{flex:auto;min-width:400px}
.st-myshop-theme{flex:auto}
.st-myshop-theme-list{flex-wrap:nowrap;gap:50px}
.st-myshop-theme-select{padding:0;width:auto}
.st-myshop-social .image{max-width:25px;width:auto!important;height:auto}
.st-product-search-title{background:0 0!important;border-bottom-left-radius:0!important}
.st-myshop-social{gap:10px}
.menu-apply-div{background:#f8f5ec;margin-top:20px;border-radius:25px}
@media only screen and (max-width:900px){
	.st-myshop-theme-list{flex-wrap:wrap!important;gap:50px}
	.st-myshop-theme .div-block-9{flex:auto;min-width:auto}
	a#st-next-button{width:80%;margin:auto;left:10%}
}
</style>
<?php
function pagemyshop_enqueue_style() {
		wp_enqueue_style( 'my-shop-css', plugin_dir_url( __FILE__ ) . '/css/st-my-shop.css' );
}

add_action( 'wp_enqueue_scripts', 'pagemyshop_enqueue_style' );

get_footer();