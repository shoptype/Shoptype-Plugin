<?php
/*
 * Template name: Shoptype Social Profile
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */
global $stApiKey;
global $stPlatformId;
global $stRefcode;
global $stCurrency;
global $brandUrl;
global $productUrl;
$path = dirname(plugin_dir_url( __FILE__ ));
wp_enqueue_style( 'new-market', $path . '/css/st-profile.css' );
wp_enqueue_script( 'st-bp-js', $path . '/js/st_bp.js' );

get_header(null);
?>

	<div class="st-profile">
		<div class="st-profile-nav">
			<div class="st-profile-menu">
				<div class="profile-menu-item st-selected" id="st-menu-profile"	onclick="select(this, 'st-profile-content')"><img src="<?php echo $path?>/images/profile.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Profile</div>
				</div>
				<div class="profile-menu-item" id="st-menu-activity"	onclick="select(this, 'st-activity')"><img src="<?php echo $path?>/images/clock.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Activity</div>
				</div>
				<div class="profile-menu-item" id="st-menu-messages"	onclick="select(this, 'st-messages')"><img src="<?php echo $path?>/images/message.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Messages</div>
				</div>
				<div class="profile-menu-item" id="st-menu-friends"	onclick="select(this, 'st-friends')"><img src="<?php echo $path?>/images/Friends.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Friends</div>
				</div>
				<div class="profile-menu-item" id="st-menu-groups"	onclick="select(this, 'st-groups')"><img src="<?php echo $path?>/images/groups.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Groups</div>
				</div>
				<div class="profile-menu-item" id="st-menu-groups"	onclick="select(this, 'st-myshop')"><img src="<?php echo $path?>/images/shop.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Shop</div>
				</div>
				<div class="profile-menu-item" id="st-menu-settings" onclick="select(this, 'st-settings')"><img src="<?php echo $path?>/images/settings.svg" loading="lazy" alt="" class="st-profile-menu-img">
					<div class="st-profile-men-txt">Settings</div>
				</div>
			</div>
		</div>
		<div class="st-profile-main">
			<div class="st-profile-content" id="st-profile-content">
				<input type="file" id="profileImageFile" onchange="updateProfileImg()" style="display: none;">
				<input type="file" id="profileBGFile" onchange="updateBgImg()" style="display: none;">
				<div class="st-profile-cover-div"><img src="https://spiritful.co.uk/wp-content/uploads/2022/01/abstract-white-panoramic-background-lines-260nw-1858252165.jpg" loading="lazy" alt="" class="st-profile-cover" id="st-profile-cover"></div>
				<div class="st-profile-header">
					<div class="st-profile-image-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-profile-image" id="st-profile-image"></div>
					<div class="st-profile-name">Name</div>
				</div>
				<div class="st-profile-meta">
					<div class="st-profile-metadata">
						<div class="st-profile-metadata-title">Badge:</div>
						<div class="st-profile-metadata-val">Elite Coseller</div>
					</div>
					<div class="st-profile-metadata">
						<div class="st-profile-metadata-title">Coseller Earnings:</div>
						<div class="st-profile-metadata-val">$000</div>
					</div>
					<div class="st-profile-metadata">
						<div class="st-profile-metadata-title">Member Since:</div>
						<div class="st-profile-metadata-val" id="st-member-from">MMM YYYY</div>
					</div>
					<div class="st-profile-metadata">
						<div class="st-profile-metadata-title">Location:</div>
						<div class="st-profile-metadata-val">India</div>
					</div>
				</div>
				<div class="st-profile-bio-main">
					<div class="st-profile-bio-title">Personal Bio:</div>
					<div class="st-profile-bio"></div>
				</div>
			</div>
			<div class="st-activity" id="st-activity">
				<div class="st-profile-subtitle">Activity</div>
				<div class="st-group-menu">
					<div class="st-groups-menu-item st-selected"	onclick="select(this, 'st-activity-personal')">Personal</div>
					<div class="st-groups-menu-item"	onclick="select(this, 'st-activity-mentions')">Mentions</div>
					<div class="st-groups-menu-item"	onclick="select(this, 'st-activity-fav')">Favourites<br></div>
					<div class="st-groups-menu-item"	onclick="select(this, 'st-activity-friends')">Friends<br></div>
					<div class="st-groups-menu-item"	onclick="select(this, 'st-activity-groups')">Groups<br></div>
				</div>
				<div class="st-profile-content-block">
					<div id="st-activity-personal" style="display: flex;" class="st-groups-main">
						<div id="st-activity-template" style="display: none;" class="st-activity-item">
							<div class="st-activity-time">dd mmm yyyy hh:mm</div>
							<div class="st-activity-title">title</div>
							<div class="st-activity-content">content</div>
						</div>
					</div>
					<div id="st-activity-mentions" class="st-groups-main"></div>
					<div id="st-activity-fav" class="st-groups-main"></div>
					<div id="st-activity-friends" class="st-groups-main"></div>
					<div id="st-activity-groups" class="st-groups-main"></div>
				</div>
			</div>
			<div class="st-messages" id="st-messages">
				<div class="st-profile-subtitle">Messages</div>
				<div class="st-group-menu">
					<div class="st-groups-menu-item st-selected" onclick="select(this, 'st-messages-chats')">Chats</div>
					<div class="st-groups-menu-item" onclick="select(this, 'st-messages-requests')">Requests</div>
					<div class="st-groups-menu-item" onclick="select(this, 'st-messages-notice')">Site Admin Notice<br></div>
				</div>
				<div class="st-profile-content-block">
					<div id="st-messages-chats" class="st-groups-main"></div>
					<div id="st-messages-requests" class="st-groups-main"></div>
					<div id="st-messages-notice" class="st-groups-main"></div>
				</div>.
			</div>
			<div class="st-friends" id="st-friends" >
				<div class="st-profile-subtitle">Friends</div>
				<div class="st-group-menu">
					<div class="st-groups-menu-item st-selected" onclick="select(this, 'st-friends-my')">Friends</div>
					<div class="st-groups-menu-item" onclick="select(this, 'st-friends-requests')">Requests</div>
				</div>
				<div class="st-profile-content-block">
					<div id="st-friends-my" style="display: flex;"	class="st-groups-main">
						<a id="st-user-profile-template" style="display: none;" href="#" class="st-user-link">
							<div class="st-user-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-user-img"></div>
							<div class="st-user-details">
								<div class="st-user-rel-txt">You are now friends with</div>
								<div class="st-user-name">Name</div>
							</div>
						</a>
					</div>
					<div id="st-friends-requests" class="st-groups-main"></div>
				</div>
			</div>
			<div class="st-groups" id="st-groups">
				<div class="st-profile-subtitle">Groups</div>
				<div class="st-group-menu">
					<div class="st-groups-menu-item st-selected" id="st-gp-menu-member" onclick="select(this, 'st-my-groups')">Member</div>
					<div class="st-groups-menu-item" id="st-gp-menu-invited" onclick="select(this, 'st-group-invites')">Invitations</div>
					<div class="st-groups-menu-item" id="st-gp-menu-owner" onclick="select(this, 'st-owner-groups')">Owner<br></div>
				</div>
				<div class="st-profile-content-block">
					<div id="st-my-groups" style="display:flex;" class="st-groups-main">
						<a id="st-my-group-template" style="display:none;" href="#" class="st-group-link">
							<div class="st-group-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-group-img"></div>
							<div class="st-group-name">Group Name</div>
						</a>
					</div>
					<div id="st-group-invites" class="st-groups-main"></div>
					<div id="st-owner-groups" class="st-groups-main"></div>
				</div>
			</div>
			<div class="st-groups" id="st-myshop">
				<div class="st-profile-subtitle">Shop</div>
				<div class="st-profile-content-block">
					<div class="st-add-product-drawer" id="st-add-product-drawer" style="right: -300px;">
						<div class="st-product-drawer" onclick="toggleAddProducts()"><img src="<?php echo $path?>/images/shop.svg" loading="lazy" alt="" class="st-product-drawer-img"></div>
						<div class="st-product-add-drawer">
							<div class="st-product-search">
								<input class="st-product-search-box" id="st-search-box" name="Search" >
								<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path?>/images/Search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
							</div>
							<div class="st-product-search-results" id="st-product-search-results">
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
								<div class="st-button">Cancel</div>
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
			<div class="st-settings" id="st-settings"></div>
		</div>
	</div>

<script type="text/javascript">
	var myUrl = new URL(window.location);
	let userId = myUrl.searchParams.get("id")??"me";
	let currentBpUser = null;
	let st_selectedProducts = {};
	let productsDataId =6;
	callBpApi("members/"+userId, addUserDetails, 'get',{populate_extras:true});
	if(userId=='me'){
		document.getElementById("st-profile-image").setAttribute('onclick','document.getElementById("profileImageFile").click()');
		document.getElementById("st-profile-cover").setAttribute('onclick','document.getElementById("profileBGFile").click()');
		document.getElementById("st-product-template").setAttribute('onmouseover','showRemoveBtn(this)');
	}
	else{
		document.getElementById('st-menu-settings').style.display='none';
		document.getElementById('st-menu-activity').style.display='none';
		document.getElementById('st-menu-messages').style.display='none';
		document.getElementById('st-gp-menu-owner').style.display='none';
		document.getElementById('st-gp-menu-invited').style.display='none';
	}
	var searchInput = document.getElementById("st-search-box");
	searchInput.addEventListener("keyup", function(event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			searchProducts();
		}
	});

	function addToShop(shopProducts){
		let selectorNodes = document.getElementsByClassName("st-shop-select");
		let products = shopProducts[0].value.unserialized[0]??"";
		let newProducts = {};
		for (var i = 0; i < selectorNodes.length; i++) {
			if(selectorNodes[i].checked && !products.includes(selectorNodes[i].value)){
				newProducts[selectorNodes[i].value] = st_selectedProducts[selectorNodes[i].value];
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
					newProduct.querySelector(".st-product-link").href= "<?php echo $productUrl ?>".replace("{{productId}}",productsJson.products[i].id)+"&tid="+productsJson.products[i].tid;
					newProduct.id = productsJson.products[i].id;
					if(userId=='me'){
						newProduct.querySelector(".st-remove-product").setAttribute("onclick",`event.stopPropagation(); removeProductFromShop("${productsJson.products[i].id}")`);
					}
					productsContainer.appendChild(newProduct);
				}
			});
	}


	function removeProductFromShop(productId){
		let products = {};
		products[productId]='';
		callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x,products,x=>loadShopProducts(currentBpUser.user_login),true),'get');
	}

	
	function searchProducts() {
		let text = document.getElementById('st-search-box').value;
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		removeChildren(productsContainer,productTemplate);
		fetch(st_backend + "/platforms/<?php echo $stPlatformId?>/products?text="+text)
			.then(response => response.json())
			.then(productsJson => {
				for (var i = 0; i < productsJson.products.length; i++) {
					let newProduct = productTemplate.cloneNode(true);
					addProductDetails(newProduct, productsJson.products[i],".st-product-img-select",".st-product-cost-select");
					newProduct.id = "search-" + productsJson.products[i].id;
					newProduct.querySelector("input").id = "select-" + productsJson.products[i].id;
					newProduct.querySelector("input").value = productsJson.products[i].id;
					productsContainer.appendChild(newProduct);
				}
			})
	}
</script>

<?php
get_footer();
