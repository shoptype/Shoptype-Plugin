<?php

/*
* Template name: New Shoptype My Shop Wizard
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
* @package shoptype
*/

global $stPlatformId;
global $stFilterJson;
global $stDefaultCurrency;


$wizard_name = urldecode(get_query_var( 'stwizard' ));
$path = dirname(plugin_dir_url( __FILE__ ));
$user_id = get_current_user_id();
$currentUser = get_userdata($user_id);

function findShopByName($array, $name){
    foreach ( $array as $element ) {
        if ( $name == $element->name ) {
            return $element;
        }
    }
    return null;
}

if($wizard_name=="new"){
	$this_store = new stdClass();
	$this_store->name = $currentUser->display_name."s Store";
	$this_store->domain = get_site_url()."/shop/".$this_store->name;
	$this_store->status = "active";
	$this_store->platform_id = $stPlatformId;
}else{
	$st_token = $_COOKIE["stToken"];
	$args = array(
		'headers'=> array(
			"Authorization"=> $st_token
		)
	);
	$result = wp_remote_get( "{$stBackendUrl}/cosellers/mini-stores/", $args );
	if( ! is_wp_error( $result ) ) {
		$body = wp_remote_retrieve_body( $result );
		$user_mini_stores = json_decode($body);
		$this_store = findShopByName($user_mini_stores->mini_stores,$wizard_name);
	}
}

if(!isset($this_store->attributes)){
	$this_store->attributes = new stdClass();
}

$this_store->attributes->username = $currentUser->display_name;
$this_store->attributes->user_nickname = $currentUser->user_nicename;

if(is_null($this_store)){
	header("Location: {$shop_url}");
	exit;
}

$collectionId = "be770fd6-d783-1309-0ffc-0a64db3baa41";
$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/collections/$collectionId");
$resultCollection     = wp_remote_retrieve_body( $response );
$pluginUrl = plugin_dir_url(__FILE__);

if (!empty($resultCollection)) {
	$st_collections = json_decode($resultCollection);
}

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
			<input class="st-myshop-name" id="myshop-name" onchange="checkUrlAvailable(this)" value="<?php echo $this_store->name ?>"/>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Bio</div>
			<textarea class="st-myshop-bio" id="myshop-bio"><?php echo $this_store->attributes->bio ?></textarea>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store URL</div>
			<input readonly class="st-myshop-bio" id="myshop-url" value="<?php echo $this_store->domain ?>"/>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Social Links</div>
			<div class="st-myshop-social-links">
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Facebook</div>
					<input class="st-myshop-bio" id="myshop-facebook" value="<?php echo $this_store->attributes->fb_url ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Twitter</div>
					<input class="st-myshop-bio" id="myshop-twitter" value="<?php echo $this_store->attributes->twitter_url ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Instagram</div>
					<input class="st-myshop-bio" id="myshop-instagram" value="<?php echo $this_store->attributes->instagram_url ?>"/>
				</div>
				<div class="st-myshop-social-link">
					<div class="st-myshop-details-txt">Youtube</div>
					<input class="st-myshop-bio" id="myshop-youtube" value="<?php echo $this_store->attributes->youtube_url ?>"/>
				</div>
				
			</div>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Logo</div>
			<a href="#" onclick="document.getElementById('profileImageFile').click()" class="st-myshop-img-select" ><img id="store-icon" src="<?php echo $this_store->attributes->profile_img?? st_locate_file("/images/image-plus.png") ?>" loading="lazy" alt="" class="st-myshop-store-img">
				<div class="st-myshop-img-txt-main">
					<div class="st-myshop-img-lnk">UPLOAD IMAGE</div>
				</div>
				<input type="file" id="profileImageFile" onchange="updateShopImg()" style="display: none;">
			</a>
			<div class="st-myshop-img-txt">File Format: JPG / PNG | Ideal Image Ratio: 1:1 <br/> Image should not exceed 1000px or 1mb in size</div>
		</div>
		<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Store Banner Image</div>
			<a href="#" onclick="document.getElementById('profileBGFile').click()" class="st-myshop-img-select"><img id="store-banner" src="<?php echo $this_store->attributes->BG_img?? st_locate_file("/images/image-plus.png") ?>" loading="lazy" alt="" class="st-myshop-store-banner">
				<div class="st-myshop-img-txt-main">
					<div class="st-myshop-img-lnk">UPLOAD IMAGE</div>
				</div>
				<input type="file" id="profileBGFile" onchange="updateBgImg()" style="display: none;">
			</a>
			<div class="st-myshop-img-txt">File Format: JPG / PNG | Ideal Image Ratio: 20:9 <br/> Image should not exceed 2000px or 1mb in size</div>
		</div>
	<div class="st-myshop-details-div">
			<div class="st-myshop-details-txt">Profile Image</div>
			<a href="#" onclick="document.getElementById('profileImgFile').click()" class="st-myshop-img-select"><img id="profile-img" src="<?php echo $this_store->attributes->user_img?? st_locate_file("/images/image-plus.png") ?>" loading="lazy" alt="" class="st-myshop-store-img">
				<div class="st-myshop-img-txt-main">
					<div class="st-myshop-img-lnk">UPLOAD IMAGE</div>
				</div>
				<input type="file" id="profileImgFile" onchange="updateProfileImg()" style="display: none;">
			</a>
			<div class="st-myshop-img-txt">File Format: JPG / PNG | Ideal Image Ratio: 1:1 <br/> Image should not exceed 1000px or 1mb in size</div>
		</div>
	</div>
	<div class="st-myshop-style" style="display:none">
		<div>
			<h2 class="st-myshop-header">Choose your theme for the store</h2>
		</div>
		<div class="st-myshop-theme-list">
			<div class="st-myshop-theme">
				<div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-01" name="theme_select" value="theme-01" <?php echo ($this_store->design_attributes->template=="theme-01")?"checked":"" ?>></div>
				<div class="div-block-9">
					<div class="st-myshop-theme-name">DESIGN 1</div>
					<img src="<?php echo st_locate_file("/images/theme-01.png"); ?>" loading="lazy" alt="<?php echo $shop_theme ?>" class="st-myshop-theme-img">
				</div>
			</div>
			<div class="st-myshop-theme">
				<div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-02" name="theme_select" value="theme-02" <?php echo $this_store->design_attributes->template=="theme-02"?"checked":"" ?>></div>
				<div class="div-block-9">
					<div class="st-myshop-theme-name">DESIGN 2</div>
					<img src="<?php echo st_locate_file("/images/theme-02.png"); ?>" loading="lazy" alt="" class="st-myshop-theme-img">
				</div>
			</div>
		</div>
	</div>
	<div class="st-myshop-products"	style="display:none">
		<div class="st-myshop-collections">
		<?php if(isset($st_collections) && isset($st_collections->collections)) { ?>
			<div class="st-myshop-collections-title">Add Products Collections</div>
			<div class="st-myshop-collections-top">
				<?php foreach ($st_collections->collections as $collection) { ?>
					<div>
						<div class="st-myshop-collection" style="display:flex;">
							<div class="st-myshop-collection-img">
								<img src="<?php echo $collection->preview_image_src; ?>" />
							</div>
							<div class="st-myshop-collection-col">
								<?php echo $collection->name." (".count($collection->product_metas).")"; ?>
							</div>
							<?php $output = array_map(function ($object) { return $object->product_id; }, $collection->product_metas); ?>
							<div class="st-myshop-collection-btn">
								<button productIds="<?php echo implode(', ', $output); ?>" onclick="addCollection(this)" >
								Add
								</button>
							</div>
								
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>	
		</div>
		<h2 class="st-myshop-header">Choose products to add to the store</h2>
		<div class="st-myshop-collections">
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
	var mini_store=<?php echo json_encode($this_store) ?>;
	if(mini_store.attributes==null){mini_store.attributes={}}
	
	function addCollection(addBtn){
		st_productsCsv = addBtn.getAttribute("productIds");
		var collection_products = st_productsCsv.split(",");
		var existing_products = [...mini_store.product_ids];
		collection_products.forEach(x=>{
			var pid=x.trim();
			if(!mini_store.product_ids.includes(pid)){
				mini_store.product_ids.push(pid);
			}
		});
		if(existing_products.length == mini_store.product_ids.length){
			ShoptypeUI.showWarning("Collection already Added");
		}else{
			shoptype_UI.user.miniStore.updateUserStore(mini_store.id, mini_store).then(x=>{
				if(x.id){
					mini_store=x;
					var added_count = mini_store.product_ids.length - existing_products.length;
					ShoptypeUI.showSuccess( added_count + " new products added to you store");
				}else{
					ShoptypeUI.showInnerError(x);
					mini_store.product_ids = existing_products;
				}
			});
		}

	}

	function addToShop(shop){
		let selectorNodes = document.getElementsByClassName("st-myshop-select");
		if(!shop.product_ids){shop.product_ids=[];}
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !shop.product_ids.includes(selectorNodes[i].value)){
				shop.product_ids.push(selectorNodes[i].value);
			}
		}
		saveStore();
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
				if(!mini_store.design_attributes){mini_store.design_attributes={};}
				mini_store.design_attributes.template=selectedTheme;
				moveToProducts();
			break;
			case 3:
				addToShop(mini_store);
				moveToComplete();
			break;
		}
	}
	
	function saveStore(){
		if(mini_store.id){
			shoptype_UI.user.miniStore.updateUserStore(mini_store.id, mini_store).then(x=>{
				if(x.id){
					mini_store=x;
				}else{
					ShoptypeUI.showInnerError(x);
					st_shop_state--;
					moveState();
				}
			});
		}else{
			addStoreCollection();
		}
	}
	
	function addStoreCollection(){
		if(!mini_store.collection_ids || mini_store.collection_ids.length==0){
			var product = st_platform.products({count:1}).then(x=>{
				var newCollection = {
						"name":STUtils.uuidv4(),
						"platform_id":"f4bf32b7-e58b-4226-4681-c1d3870bb580",
						"type":"product",
						"status":"published",
						"product_metas":
						[
							{
								"product_id":x.products[0].id,
								"sequence_id":1
							}
						],
						"dynamic":false
					};
				shoptype_UI.user.miniStore.createCosellerCollection(newCollection).then(collection=>{
					if(collection.id){
						mini_store.collection_ids=[];
						mini_store.collection_ids.push(collection.id);
						shoptype_UI.user.miniStore.createUserStore(mini_store).then(x=>{
							if(x.id){
								mini_store=x;
							}else{
								ShoptypeUI.showInnerError(x);
								moveToDetails();
							}
						});
					}else{
						ShoptypeUI.showError(collection.message);
					}
				});
			});
		}else{
			shoptype_UI.user.miniStore.createUserStore(mini_store).then(x=>{mini_store=x;});
		}
	}
	
	var wizard_tabs = [".st-my-shop-details", ".st-myshop-style",".st-myshop-products", ".st-myshop-complete"];
	function showTab(tabselect){
		var tabState = "st-myshop-state-done";
		wizard_tabs.forEach((tab,index)=>{
			var tabindex = index+1;
			if(tab == tabselect){
				document.querySelector(tab).style.display="";
				document.getElementById("state-"+tabindex).classList.add("st-myshop-state-selected");
				st_shop_state=index;
				tabState="";
			}else{
				document.querySelector(tab).style.display="none";
				document.getElementById("state-"+tabindex).classList.remove("st-myshop-state-selected");
				if(tabState!=""){
					document.getElementById("state-"+tabindex).classList.add(tabState);	
				}
			}
		});
		document.querySelector("#filterContainer").style.display="none";
		document.querySelector("#st-next-button").style.display="";
	}
	
	function moveToDetails(){
		showTab(".st-my-shop-details");
	}
	
	function moveToTheme(){
		clearTimeout(debounce_timer);
		mini_store.name = document.getElementById("myshop-name").value;
		mini_store.attributes.bio = document.getElementById("myshop-bio").value;
		mini_store.domain = document.getElementById("myshop-url").value;
		
		mini_store.attributes.fb_url = document.getElementById("myshop-facebook").value;
		mini_store.attributes.twitter_url = document.getElementById("myshop-twitter").value;
		mini_store.attributes.instagram_url = document.getElementById("myshop-instagram").value;
		mini_store.attributes.youtube_url = document.getElementById("myshop-youtube").value;
		saveStore();
		showTab(".st-myshop-style");
	}

	function moveToProducts(){
		searchProducts();
		saveStore();
		showTab(".st-myshop-products");
	}
	
	function moveToComplete(){
		setShopUrl(mini_store.domain);
		showTab(".st-myshop-complete");
		document.querySelector("#st-next-button").style.display="none";
	}
	
	function updateShopImg(){
		var fileSelect = document.getElementById("profileImageFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		
		var img = new Image;
		img.onload = function() {
			var canvas = scaleImage(img, 600, 600);
			canvas.toBlob((blob)=>{
				shoptype_UI.user.miniStore.addStoreImage(mini_store.name+" store_img.jpg", blob).then(loaded_img=>{
					mini_store.attributes.profile_img = loaded_img[mini_store.name+" store_img.jpg"];
					document.querySelector("#store-icon").src = mini_store.attributes.profile_img+"?"+STUtils.uuidv4();
				});
			});
		}
		img.src = URL.createObjectURL(fileSelect.files[0]);
	}
	
	function scaleImage(img, width, height){
		const canvas = document.createElement("canvas");
		const ctx = canvas.getContext("2d");
		canvas.width = width;
		canvas.height = height;
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
	
	function updateBgImg(){
		var fileSelect = document.getElementById("profileBGFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var img = new Image;
		img.onload = function() {
			var canvas = scaleImage(img, 1300, 225);
			canvas.toBlob((blob)=>{
				shoptype_UI.user.miniStore.addStoreImage(mini_store.name+" store_bg_img.jpg", blob).then(loaded_img=>{
					mini_store.attributes.BG_img = loaded_img[mini_store.name+" store_bg_img.jpg"];
					document.querySelector("#store-banner").src = mini_store.attributes.BG_img+"?"+STUtils.uuidv4();
				});
			});
		}
		img.src = URL.createObjectURL(fileSelect.files[0]);
	}
	
	function updateProfileImg(){
		var fileSelect = document.getElementById("profileImgFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		var img = new Image;
		img.onload = function() {
			var canvas = scaleImage(img, 600, 600);
			canvas.toBlob((blob)=>{
				shoptype_UI.user.miniStore.addStoreImage("profile_img.jpg", blob).then(loaded_img=>{
					mini_store.attributes.user_img = loaded_img["profile_img.jpg"];
					document.querySelector("#store-banner").src = mini_store.attributes.BG_img+"?"+STUtils.uuidv4();
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

	}
	
	function checkUrlAvailable(element){
		var testUrl = element.value;
		STMiniStore.getUserStoreByName("",testUrl).then(result => {
			if(result.count>0){
				ShoptypeUI.showError(`the url ${testUrl} is already in use please choose another one.`);
				mini_store.domain = "";
			}else{
				mini_store.domain = location.protocol + '//' + location.host + "/shop/"+testUrl;
			}
			document.getElementById("myshop-url").value=mini_store.domain;
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
	let debounce_timer;
	let st_shop_state = 0;
	let myshop_offset = 1;
	let scrollLoading = false;
	let options={};
	
	scrollContainer = document.getElementById("st-product-search-results");
	scrollContainer.addEventListener('scroll',(event)=>{
		const {scrollHeight,scrollTop,clientHeight} = event.srcElement;
		if((scrollTop + clientHeight > scrollHeight - 5) && (!scrollLoading)){
		scrollLoading = true;
			searchProducts(false);
		}
	});
</script>
<style>
.st-myshop-social-links {display: flex; flex-wrap: wrap; background: #eee; padding: 10px 5px 20px;border-radius:20px;}
.st-myshop-social-link { margin: 5px; width: calc(50% - 20px);}
.st-filter-btn { margin-top: 21px; width: 40px !important; height: 40px !important; padding: 5px 0px; margin-left: -1px; border-radius: 0px 10px 10px 0px; border: solid 1px #ccc;}
#filterContainer { position: fixed !important; margin-left: 0px !important; top: 150px !important; z-index:99999;left: -300px;}
.menu-apply-div { background: #F8F5EC; margin-top: 20px; border-radius: 0px 20px 20px 0px; height: 65px !important; border-radius: 19px !important;}	
img.st-filter-img { width: 20px !important; height: 20px !important; margin-left: 10px !important;}
.menu-container{border: solid 1px #ccc;}
.st-myshop-details-div input,.st-myshop-details-txt,.st-myshop-state,input#myshop-name{color:#1e1e1e;font-family:Poppins,Arial,sans-serif}
.st-myshop-connector,.st-myshop-state{border:1px solid #1e1e1e;opacity:.2}
footer{margin-top:80px}
.st-myshop-state.st-myshop-state-selected{border-color: #00317F;background-color: #00317F;display: flex;color: #93EAE5;opacity: 1;}
.st-myshop-state{display:flex;justify-content:center;align-items:center;border-radius:20px}
.st-myshop-details-txt{display:flex;align-items:center;text-transform: uppercase;font: 500 16px/24px Poppins;color: #00317F;}
.st-myshop-details-div input,.st-myshop-details-div textarea,input#myshop-name{font-weight:400;font-size:16px;line-height:160%;padding-left:12px;border: 1px solid #B5C5DE;border-radius: 20px;width:100%}
.st-myshop-details-div textarea{resize:none; height: 150px}
.st-myshop-state{font-style:normal;font-weight:400;font-size:18px;line-height:20px;display:flex;align-items:center;text-align:center}
.menu-apply-button,a#goto_shop_btn,a#st-next-button{padding: 14px 24px;background: #00317F;border-radius: 50px;color: #fff;font: 500 17px/20px Poppins;}
.st-product-cost-select,.st-product-name{font-weight:400;font-size:18px;line-height:120%;font-style:normal;display:flex;font-family:Poppins,Arial,sans-serif}
.st-myshop-state.st-myshop-state-done{opacity: 1;background-color: #00317F;border-color: #00317F;color: #00317F;}
.st-myshop-state-done::after{content:"✓";color:#ffff;margin-left:-7px}
.st-myshop-search{background:#fff;margin-bottom:50px}
input#st-search-box{border: 1px solid rgba(0,0,0,.1);width: 100%;border-radius: 20px;border-right: none;padding: 0px 20px;}
.st-product-select-main{padding:20px;flex-direction:column;}
.st-product-img-select{object-fit:contain;width:100%;}
	.st-myshop-store-img{position:relative}
.st-product-name{color:#1e1e1e;height:auto}
.st-product-cost-select{margin-top:8px;color:#075ade}
.st-myshop-product-select{width: calc(50% - 10px);}
.st-myshop-select{appearance:none;-webkit-appearance:none;-moz-appearance:none;outline:0;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;position:absolute;width:18px;height:18px;background:#fff;border:1px solid #1e1e1e;right:5px;top:10px;}
.st-myshop-select::before{font-weight:700;font-size:12px;content:"";position:absolute;top:45%;left:60%;transform:translate(-50%,-50%);width:14px;height:14px;border-radius:3px}
.st-myshop-select:checked::before{content:"\2713";color:#fff}
.st-myshop-select:checked{background-color:orange;border-color:orange}
.st-myshop-complete .text-block-3,.st-myshop-txt{color:#1e1e1e;font-style:normal;font-weight:400;font-size:20px;line-height:110%;font-family:Poppins,Arial,sans-serif}
a.st-myshop-img-select{min-height:170px;border-radius:20px;flex-direction:row-reverse;justify-content:space-around;}
.st-myshop-img-txt-main{display: flex;background: #00317F;border-radius: 20px;padding: 0px 20px;margin-left:10px}
.st-myshop-img-txt{display: flex; align-items: center; text-align: left; color: #00317F; font: 300 11px/16px Poppins;}
.st-myshop-img-lnk,.st-myshop-img-txt-type{line-height:160%;align-items:center;font-style:normal;font-weight:500;display:flex;text-align:center}
.st-myshop-img-txt-type{font-size:14px;color:#1e1e1e;opacity:.3}
.div-block-11{border-radius:8px;padding:10px;background-color:rgba(240,240,240,.8)}
.st-myshop-img-lnk{background:0 0;color: #93EAE5;text-decoration: none;font: 500 13px/20px Poppins;}
.st-myshop-store-banner{position:relative;width:calc(100% - 168px);object-fit:contain;}
img.st-product-search-img{filter:invert(0)}
.st-myshop-theme-name {text-align: left;margin-bottom: 10px;font: 300 15px/15px Poppins;color: #00317F;}
.st-myshop-theme-list{margin-bottom:70px;border:none}
.st-myshop-theme{flex:auto; flex-direction:column;justify-content:center;flex-direction: row-reverse;padding:20px;border-radius:20px}
.st-shop-select{width:25px;height:25px;display:flex;text-align:center;margin:auto auto 20px;align-items:center;margin-right:auto!important}
.st-myshop-theme .div-block-9{flex:auto;min-width:400px}
.st-myshop-theme-list{flex-wrap:nowrap;gap:50px}
.st-myshop-theme-select{width:auto;display: flex;align-content: center;flex-wrap: wrap;padding: 15px;}
.st-myshop-social .image{max-width:25px;width:auto!important;height:auto}
.st-product-search-title{background:0 0 !important;margin-left: -42px !important;}
.st-myshop-social{gap:10px}
.menu-apply-div{background:#f8f5ec;margin-top:20px;border-radius:25px}
.st-myshop-theme-img {border: solid 1px #B5C5DE;padding: 10px;width:300px;border-radius:10px}

.st-myshop-search {
    padding: 20px;
    background: #EFF0FA;
	border-bottom: solid 1px #B5C5DE;
}
div#st-product-search-results {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
	padding: 10px;
	height: 700px;
    overflow-y: auto;
}

	
	
.st-myshop-collections {
    border-radius: 20px;
    border: solid 1px #B5C5DE;
    overflow: hidden;
	padding-bottom: 10px;
    margin-bottom: 40px;
}
h2.st-myshop-header, .st-myshop-collections-title {
    text-align: center;
    font: 500 20px/24px Poppins;
    color: #00317F;
    padding: 15px;
	text-transform: uppercase;
}
.st-myshop-collections-title {
	background: #EFF0FA;
    padding: 15px;
    border-bottom: solid 1px #B5C5DE;
}
.st-myshop-collection {
    display: flex;
    padding: 10px;
    border-bottom: solid 1px #ccc;
}
.st-myshop-collection-img {
    margin-right: 20px;
	min-width: 80px;
}
.st-myshop-collection-img img {
    object-fit: cover;
    width: 80px;
    height: 80px;
    border-radius: 20px;
}
.st-myshop-collection-col {
    width: 100%;
    display: flex;
    align-items: center;
}
.st-myshop-collections-top {
    overflow-y: auto;
    max-height: 400px;
}
.st-myshop-collection-btn button {
    padding: 0px 15px;
    font: 400 16px/24px Poppins;
    border-radius: 20px;
    border: solid 1px #00317F;
    background: #00317F;
    color: #93EAE5;
}
.st-myshop-collection-btn {
    border-left: solid 1px #ccc;
    display: flex;
    align-items: center;
    padding: 10px 0px 10px 10px;
}
@media only screen and (max-width:900px){
	.st-myshop-theme-list{flex-wrap:wrap!important;gap:50px}
	.st-myshop-theme .div-block-9{flex:auto;min-width:auto}
	a#st-next-button{width:80%;margin:auto;left:10%}
}
</style>
<?php

get_footer();