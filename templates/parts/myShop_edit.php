<?php	
	global $stPlatformId;
	global $stBackendUrl;
	global $stCurrency;
	
	$shop_id = $_GET['shopid'];
	
	$st_token = $_COOKIE["stToken"];
	$args = array(
		'headers'     => array(
			'Authorization' => $st_token,
		),
	); 
	
	$result = wp_remote_get( "{$stBackendUrl}/cosellers/mini-stores", $args);
	
	if( ! is_wp_error( $result ) ) {
		$body = wp_remote_retrieve_body( $result );
		$user_mini_stores = json_decode($body);
		$mini_stores = $user_mini_stores->mini_stores;
		$shop_found = false;
		foreach($mini_stores as $key=>$mini_store){
			if($mini_store->platform_id != $stPlatformId){
				unset($mini_stores[$key]);
			}else{
				if(!empty($shop_id) && $shop_id==$mini_store->id){
					$shop_found=true;
				}
			}
			$index++;
		}
		
		if(empty($shop_id) || !$shop_found){
			$shop_id = $mini_stores[0]->id;
		}

		if($user_mini_stores->count==0){

		}else{
			$result = wp_remote_get( "{$stBackendUrl}/cosellers/mini-stores/".$shop_id );
			$body = wp_remote_retrieve_body( $result );
			$mini_store = json_decode($body);
		}
	}
	
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
	<div>
		Select Shop 
		<select style="margin: 0px 0px 20px;" onchange="changeShop(this)">
		<?php
			foreach($mini_stores as $key=>$store){
				?>
				<option <?php echo $store->id==$shop_id?'selected="selected"':''; ?> value="<?php echo $store->id ?>"><?php echo $store->name ?></option>
				<?php
			}
		?>
		</select>
	</div>
	<div class="st-profile-content-block">
		<div class="st-store-grid st-store-header">
			<div id="banner-wrap">
				<div class="st-store-banner-wrap" style="width: 100%; left: 0%;right: 0%;margin-left: 0;margin-right: 0;">
					<img src="<?php echo $mini_store->attributes->BG_img ?>" alt="" class="store-banner" width="439" height="115" onclick="document.getElementById('profileBGFile').click()">
				</div>
				<div id="inner-element" style="display: flex;">
					<div class="store-brand" style="margin-right: 20px;">
						<div class="store-icon-img"><img src="<?php echo $mini_store->attributes->profile_img ?>" onclick="document.getElementById('shopImageFile').click()" alt="" class="store-icon" width="150" height="150"> </div>
					</div>
					<div class="store-info">
						<h3 id="store_title"  contenteditable="<?php echo $editable ? 'true' : 'false' ?>"><?php echo $mini_store->name ?></h3>
						<p id="store_bio"  contenteditable="<?php echo $editable ? 'true' : 'false' ?>"><?php echo empty($mini_store->attributes->bio) ? "description":$mini_store->attributes->bio ?></p>
					</div>
				</div>
				<input type="file" id="shopImageFile" onchange="updateShopImg()" style="display: none;">
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
							<input class="st-myshop-select" type="checkbox" id="product000" name="" value="">
						</div>
					</div>
				</div>
				<div class="st-shop-buttons">
					<div class="st-button" onclick="addToShop()">Add</div>
					<div class="st-button" onclick="hideResults()">Cancel</div>
				</div>
			</div>
		</div>
		<div id="st-myshop-main" class="st-groups-main" style="display:flex;">
			<div id="st-product-template" class="st-shop-product" style="display:none;position: relative;" >
				<a href="" class="st-product-link" >
					<div class="product-image">
						<div class="am-product-img-div">
							<div class="sold-out" style="display:none;">Sold Out</div>
							<div class="on-sale" style="display:none;">Sale</div>
						</div>
					</div>
					<div class="st-product-img-div"><img src="" loading="lazy" alt="" class="st-product-img"></div>
					<div class="st-product-cost"></div>
					<div class="st-product-name"></div>
				</a>
				<div class="st-remove-product" onclick="event.stopPropagation(); removeProductFromShop(this)">X</div>
			</div>
			<?php
				if($mini_store->product_details){
					foreach($mini_store->product_details as $index=>$product){ ?>
					<?php
						$max_price = 0;
									$min_price = PHP_FLOAT_MAX;
									$soldout = true;
									$on_sale = false;
						foreach ($product->variants as $variant) {
							if($variant->discountedPriceAsMoney->amount<$min_price){
								$min_price = $variant->discountedPriceAsMoney->amount;
							}
							if($variant->discountedPriceAsMoney->amount>$max_price){
								$max_price = $variant->discountedPriceAsMoney->amount;
							}
							if($variant->quantity > 0){ $soldout = false; }
							if($variant->priceAsMoney->amount>$variant->discountedPriceAsMoney->amount){
								$on_sale = true;
							}
							$price_str = $stCurrency[$product->currency] ." ". $min_price;
							if($max_price>$min_price){
								$price_str = $price_str . " - " . $stCurrency[$product->currency] ." ". $max_price;
							}
						}
					?>
					<div id="<?php echo $product->id ?>" class="st-shop-product"  style="position: relative;">
						<a href="/products/<?php echo "{$product->id}?tid={$mini_store->tid}" ?>" class="st-product-link" >
							<div class="product-image">
								<div class="am-product-img-div">
									<?php if($soldout){ ?>
										<div class="sold-out">Sold Out</div>
									<?php } ?>
									<?php if($on_sale){ ?>
										<div class="on-sale" style="display:none;">Sale</div>
									<?php } ?>
								</div>
							</div>
							<div class="st-product-img-div"><img src="<?php echo $product->primaryImageSrc->imageSrc ?>" loading="lazy" alt="" class="st-product-img"></div>
							<div class="st-product-cost"><?php echo $price_str ?></div>
							<div class="st-product-name"><?php echo $product->title ?></div>
						</a>
						<div class="st-remove-product" onclick="event.stopPropagation(); removeProductFromShop(this)">X</div>
					</div>
				<?php }	
				}
				?>

		</div>
		<button class="save-shop-btn" onclick="saveStore()">Save</button>
	</div>
</div>
<script type="text/javascript">
	var mini_store=<?php echo json_encode($mini_store) ?>;
	var mini_store_id= "<?php echo $mini_store->id ?>";
	
	function changeShop(select){
		var shop_id = select.options[select.selectedIndex].value;
	 	window.location.href = location.protocol + '//' + location.host + location.pathname + "?shopid=" + shop_id;
	}
	
	function hideResults(){
		document.getElementById("st-product-search-results").style.display = "none";
	}

	function showResults(){
		document.getElementById("st-product-search-results").style.display = "";
	}

	function addToShop(){
		let selectorNodes = document.getElementsByClassName("st-myshop-select");
		if(!mini_store.product_ids){mini_store.product_ids=[];}
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !mini_store.product_ids.includes(selectorNodes[i].value)){
				mini_store.product_ids.push(selectorNodes[i].value);
			}
		}
		saveStore();
	}

	function loadShopProducts() {
		let productTemplate = document.getElementById("st-product-template");
		let productsContainer = document.getElementById("st-myshop-main");
		
		hideResults();
		removeChildren(productsContainer, productTemplate)
		STMiniStore.getUserStore(mini_store_id)
			.then(store => {
				for (var i = 0; i < store.product_details.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, store.product_details[i],".st-product-img",".st-product-cost");
					newProduct.querySelector(".st-product-link").href= "/products/"+store.product_details[i].id+"/?tid="+store.tid;
					newProduct.id = store.product_details[i].id;
					productsContainer.appendChild(newProduct);
				}
			});
	}
	
	function saveStore(){
		mini_store.name = document.getElementById("store_title").innerText;
		mini_store.attributes.bio = document.getElementById("store_bio").innerHTML;
		
		shoptype_UI.user.miniStore.updateUserStore(mini_store.id, mini_store).then(x=>{
			if(x.id){
				mini_store=x;
				loadShopProducts();
			}else{
				ShoptypeUI.showInnerError(x);
			}
		});
	}
	
	function updateShopImg(){
		var fileSelect = document.getElementById("shopImageFile");
		if ( ! fileSelect.files || ! fileSelect.files[0] ) {
			return;
		}
		
		var img = new Image;
		img.onload = function() {
			var canvas = scaleImage(img, 600, 600);
			canvas.toBlob((blob)=>{
				shoptype_UI.user.miniStore.addStoreImage(mini_store.name+" store_img.jpg", blob).then(loaded_img=>{
					mini_store.attributes.profile_img = loaded_img[mini_store.name+" store_img.jpg"];
					document.querySelector(".store-icon").src = mini_store.attributes.profile_img+"?"+STUtils.uuidv4();
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
					document.querySelector(".store-banner").src = mini_store.attributes.BG_img+"?"+STUtils.uuidv4();
				});
			});
		}
		img.src = URL.createObjectURL(fileSelect.files[0]);
	}
	
	function updateProfileImg(){
		var fileSelect = document.getElementById("profileImageFile");
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
	
	function addProductDetails(productNode, product, imgTag, priceTag){
		let pricePrefix = shoptype_UI.currency[product.currency]??product.currency;
		productNode.querySelector(imgTag).src = product.primaryImageSrc.imageSrc;
		productNode.querySelector(imgTag).setAttribute("data-src", product.primaryImageSrc.imageSrc);
		productNode.querySelector(".st-product-name").innerHTML = product.title;
		productNode.querySelector(priceTag).innerHTML = pricePrefix + product.variants[0].discountedPriceAsMoney.amount.toFixed(2);
		productNode.style.display="";
	}

	function searchProducts(remove=true) {
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		if(remove){
			removeChildren(productsContainer,productTemplate);
			myshop_offset=0;
		}

		let options = {
			text: document.getElementById('st-search-box').value,
			offset:myshop_offset
		};

		options.count=40;
		showResults();
		st_platform.products(options)
			.then(productsJson => {
				myshop_offset += productsJson.products.length;
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					if(productsJson.products[i].variants && productsJson.products[i].variants.length>0){
					addProductDetails(newProduct, productsJson.products[i],".st-product-img-select",".st-product-cost-select");
						newProduct.id = "search-" + productsJson.products[i].id;
						newProduct.querySelector("input").id = "select-" + productsJson.products[i].id;
						newProduct.querySelector("input").value = productsJson.products[i].id;
						productsContainer.appendChild(newProduct);
					}
				}
				scrollLoading = false;
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

	function removeProductFromShop(removeBtn){
		var productElem = removeBtn.parentElement;
		var productId = productElem.id;
		const index = mini_store.product_ids.indexOf(productId);
		if (index > -1) { // only splice array when item is found
		  mini_store.product_ids.splice(index, 1); // 2nd parameter means remove one item only
		}
		productElem.remove();
	}

	let st_selectedProducts = {};
	let myshop_offset = 0;
	let scrollLoading = false;

	var searchInput = document.getElementById("st-search-box");
	searchInput.addEventListener("keyup", function(event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			searchProducts();
		}
	});
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
	.st-shop-buttons {width: 150px;}
	.st-product-select-main{flex-direction: column;}
	input.st-myshop-select {position: absolute;top: 5px;right: 0px;}
	.st-product-select{width: 170px;};
	.st-product-search-title {height:40px}
	.st-product-search-box {font: 300 14px/16px Poppins;}
	.st-product-cost{color:#376DC9; font: 500 13px/18px Poppins}
	.st-product-name{color:#000; font: 300 14px/18px Poppins}
	#st-myshop-main{min-height:400px}
	.st-product-search-results {max-height:460px; gap: 10px;width: calc(100% - 170px);min-width: 390px;top:50px;left:0px}
	.st-shop-buttons {width:150px}
	.st-product-search {width: calc(100% - 150px)}
	.st-button {height: 40px;padding: 0px 10px !important}
	.st-shop-product:hover .st-remove-product{display: block;}
	#inner-element .store-icon{min-width:150px;}
	@media screen and (max-width: 516px) {
		.st-shop-buttons {width: 360px;justify-content: flex-end;}
		.st-product-search-results {top:100px}
		.st-product-search-box {width: calc(100% - 30px);}
		.st-product-search {width:370px;padding: 5px 0px;}
		.st-product-add-drawer {flex-direction: column;align-content: center;}
	}
</style>