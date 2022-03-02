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
	document.querySelector(".st-profile-image").src = userData.avatar_urls.full;
	document.querySelector(".st-profile-name").innerHTML = userData.name;
	document.getElementById("st-member-from").innerHTML = userData.registered_since;
	callBpApi(`members/${userData.id}/cover`, setUserCover, "get");
	let bio = userData.xprofile.groups[0].fields.find(x => x.name === 'Bio');
	document.querySelector(".st-profile-bio").innerHTML = bio.value.raw;
	loadShopProducts(userData.user_login);
	callBpApi("friends", function(data){setFriends(data,userData.id)}, 'get',{per_page:30,'user_id': userData.id});
	callBpApi("groups", function(data){setGroups(data,userData.id)}, 'get',{per_page:30,'user_id': userData.id});
	callBpApi("activity", function(data){setActivities(data, "st-activity-personal")}, 'get',{per_page:30});
	callBpApi("activity", function(data){setActivities(data, "st-activity-groups")}, 'get',{per_page:30, component:'groups'});
	callBpApi("activity", function(data){setActivities(data, "st-activity-friends")}, 'get',{per_page:30, component:'friends'});
}

function setUserCover(coverData){
	if(coverData.length>0){
		document.querySelector(".st-profile-cover").src = coverData[0].image;
	}
}

function setActivities(activityData, parentId){
	let activityTemplate = document.getElementById('st-activity-template');
	let activityContainer = document.getElementById(parentId);
	for (var i = 0; i < activityData.length; i++) {
		let newActivity = activityTemplate.cloneNode(true);
		newActivity.id = parentId + '-' + i;
		newActivity.style.display = "";
		const date = new Date(Date.parse(activityData[i].date));
		newActivity.querySelector(".st-activity-time").innerHTML = new Intl.DateTimeFormat('en-GB',{dateStyle:'medium',timeStyle:'short'}).format(date);
		newActivity.querySelector(".st-activity-title").innerHTML = activityData[i].title;
		newActivity.querySelector(".st-activity-content").innerHTML = activityData[i].content.rendered;
		activityContainer.appendChild(newActivity);
	}
}

function setFriends(friendsData, userId){
	let friendTemplate = document.getElementById('st-user-profile-template');
	for (var i = 0; i < friendsData.length; i++) {
		let newFriend = friendTemplate.cloneNode(true);
	let friendData = friendsData[i];
		let friendId = friendsData[i].friend_id == userId?friendData.initiator_id:friendData.friend_id;
		newFriend.id = "friend" + "-" + friendId;
		callBpApi("members/"+ friendId, function(data){populateUserCard(data, newFriend, friendData)}, 'get');
	}
}

function setGroups(groupsData, userId){
	let groupTemplate = document.getElementById("st-my-group-template");
	let memberGpList = document.getElementById("st-my-groups");
	let ownerGpList = document.getElementById("st-owner-groups");
	let memberGpCount = 0, ownerGpCount = 0;
	for (var i = 0; i < groupsData.length; i++) {
		let newGroup = groupTemplate.cloneNode(true);
		newGroup.id = "memberGp-" + groupsData[i].id;
		newGroup.href = groupsData[i].link;
		newGroup.style.display = "";
		newGroup.querySelector(".st-group-img").src = groupsData[i].avatar_urls.full;
		newGroup.querySelector(".st-group-name").innerHTML = groupsData[i].name;
		memberGpList.appendChild(newGroup);
		memberGpCount++;
		if(groupsData[i].creator_id == userId){
			var ownedGroup = newGroup.cloneNode(true);
			ownedGroup.id = "ownedGp-" + groupsData[i].id;
			ownerGpList.appendChild(ownedGroup);
			ownerGpCount++;
		}
	}
	document.getElementById("st-gp-menu-member").innerHTML = `Member(${memberGpCount})`;
	document.getElementById("st-gp-menu-owner").innerHTML = `Owner(${ownerGpCount})`;
}

function populateUserCard(userData, userNode, friendData){
	userNode.querySelector(".st-user-img").src = userData.avatar_urls.full;
	userNode.querySelector(".st-user-name").innerHTML = userData.name;
	userNode.href = "new-profile/?id=" + userData.id;
	userNode.style.display="";
	if(friendData.is_confirmed){
		let friendsContainer = document.getElementById("st-friends-my");
		friendsContainer.appendChild(userNode);
	}else{
		let inviteContainer = document.getElementById("st-friends-requests");
		if(friendData.initiator_id == userData.id){
			userNode.querySelector(".st-user-rel-txt").innerHTML = "Friend request pending from";
		}else{
			userNode.querySelector(".st-user-rel-txt").innerHTML = "Your Friend request has been sent to";
		}			
		inviteContainer.appendChild(userNode);
	}
}

function select(selectedTab, selectedNodeId){
	let selectedNode = document.getElementById(selectedNodeId);
	let siblings = selectedNode.parentNode.children;
	let menus = selectedTab.parentNode.children;
	for (var i = 0; i < menus.length; i++) {
		menus[i].classList.remove("st-selected");
	}
	selectedTab.classList.add("st-selected");
	for (var i = 0; i < siblings.length; i++) {
		siblings[i].style.display="none";
	}
	selectedNode.style.display="flex";
}

function updateProfileImg(){
	var fileSelect = document.getElementById("profileImageFile");

	if ( ! fileSelect.files || ! fileSelect.files[0] ) {
		return;
	}

	var formData = new FormData();
	formData.append( 'action', 'bp_avatar_upload' );
			formData.append( 'file', fileSelect.files[0] );
			pushBpApi(`members/${currentBpUser.id}/avatar`, updateMyAvatar, "post", formData);
}

function updateBgImg(){
	var fileSelect = document.getElementById("profileBGFile");
	if ( ! fileSelect.files || ! fileSelect.files[0] ) {
		return;
	}
	var formData = new FormData();
	formData.append( 'action', 'bp_cover_image_upload' );
			formData.append( 'file', fileSelect.files[0] );
			pushBpApi(`members/${currentBpUser.id}/cover`, setUserCover, "post", formData);
}

function updateMyAvatar(data){
	if(data.length>0){
		document.querySelector(".st-profile-image").src = data[0].full;
	}
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

function showRemoveBtn(productNode){
	productNode.querySelector(".st-remove-product").style.display = "block";
}

function hideRemoveBtn(productNode){
	productNode.querySelector(".st-remove-product").style.display = "none";
}

function addProductDetails(productNode, product, imgTag, priceTag){
	let pricePrefix = stCurrency[product.currency]??product.currency;
	productNode.querySelector(imgTag).src = product.primaryImageSrc.imageSrc;
	productNode.querySelector(".st-product-name").innerHTML = product.title;
	productNode.querySelector(priceTag).innerHTML = pricePrefix + product.variants[0].discountedPriceAsMoney.amount.toFixed(2);
	productNode.style.display="";
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

function removeChildren(node, dontRemove){
	let length = node.children.length;
	for (var i = length - 1; i >= 0; i--) {
		if(node.children[i]!=dontRemove){node.children[i].remove();}
	}
}

function addProductToShop(productId){
	headerOptions.method = "get";
	fetch( st_backend+"/track/publish-slug?productId=" + productId, headerOptions)
		.then(response => response.json())
		.then(trackerJson=>{
			let products = {};
			products[productId]=trackerJson.trackerId;
			callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x, products),'get');
		});
}

function productSelect(selectBox){
	let productId = selectBox.value;
	fetch( st_backend+"/track/publish-slug?productId=" + productId, headerOptions)
		.then(response => response.json())
		.then(trackerJson=>{
			st_selectedProducts[productId]=trackerJson.trackerId;
		});
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