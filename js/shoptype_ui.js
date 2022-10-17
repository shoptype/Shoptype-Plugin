class ShoptypeUI{
	#productUrl = null;
	#stLoginUrl = null;
	#platformId = null;
	#userTId = null;
	#cosellerTId = null;
	#rid = null;
	#eventSent = false;
	currency = {"USD":"$", "INR":"₹","GBP":"£"};

	constructor(){
		this.currentUrl = new URL(window.location);
		this.st_Wraper = document.createElement("div");
		let st_loader = '<div class="st-loader-mask" id="st-loader-mask" style="display:none;"><img src="https://user-images.githubusercontent.com/4776769/172153004-febffb83-f0ed-46da-8d79-4be74aa70baf.gif" alt="" style="max-width: 20%;"></div>';
		let st_cosell_screen = `<div class="st-cosell-link-mask" id="st-cosell-intro-mask" style="display:none" onclick="ShoptypeUI.hide(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-cosell-links-header" id="st-cosell-links-header">{{site}} is proud to introduce &quot;Cosell&quot; , A unique way to boost the influencer in you.<br><span class="st-cosell-links-header-span">Share and make Money Instantly.</span></div><div class="st-cosell-body"><div class="st-cosell-steps-div"><div class="st-cosell-exp"><div class="st-cosell-exp-header-div"><h3 class="st-cosell-exp-header">How to be a Coseller</h3></div><div class="st-cosell-exp-steps"><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">1</div><div class="st-cosell-step-img-div"><img src="https://user-images.githubusercontent.com/4776769/164172794-7618254d-eac2-4bd3-a7c2-5d5a12195b71.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Signup</div></div><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">2</div><div class="st-cosell-step-img-div"><img src="https://user-images.githubusercontent.com/4776769/164173181-bff98789-3c04-4448-a0d9-7f70ff24b800.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Click Cosell on cool products</div></div><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">3</div><div class="st-cosell-step-img-div"><img src="https://user-images.githubusercontent.com/4776769/164172794-7618254d-eac2-4bd3-a7c2-5d5a12195b71.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Share with your Network</div></div></div></div><div class="st-cosell-signup"><div class="st-cosell-sugnup-btn" onclick="shoptype_UI.showLogin()">Become a Coseller</div></div></div><div class="st-cosell-adv"><div class="st-cosell-step-pts"><div class="st-cosell-step-no">1</div><div class="st-cosell-step-txt">Coselling is Free, No membership fee.</div></div><div class="st-cosell-step-pts"><div class="st-cosell-step-no">2</div><div class="st-cosell-step-txt">Cosell across all participating Market Networks, across the Internet.</div></div><div class="st-cosell-step-pts"><div class="st-cosell-step-no">3</div><div class="st-cosell-step-txt">Cosell links are unique. Share, get paid when inviting others to grow your referral Network.</div></div></div></div><div class="st-cosell-links-footer"><div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a></div> <a href="#" target="_blank" class="st-link-block"><div class="st-cosell-page-txt">Learn more about Coselling</div> </a></div></div></div>`;
		let st_coseller_profile = `<div class="st-cosell-link-mask" id="coseller-profile-mask" style="display:none" onclick="ShoptypeUI.hide(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-redirect"><div class="st-redirect-txt">To view earnings across all market networks, please visit:</div><div class="st-redirect-btn-div"> <a href="https://app.shoptype.com/" target="_blank" class="st-redirect-btn w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164174316-a309c175-ea8b-4ebb-946b-66c7ec487da3.png" loading="lazy" alt="" class="st-redirect-btn-image"><div class="st-redirect-btn-title">Visit Shoptype</div> </a><div class="st-redirect-btn-txt">(Redirects to Shoptype. Opens in new tab)</div></div></div><div class="st-coseller-db"><div class="st-coseller-db-title-div"><h1 id="st-coseller-db-heading" class="st-coseller-db-heading">Your Dashboard {site}</h1></div><div class="st-coseller-db-data"><div class="st-duration-selectors" style="display:none;"><div id="st-duration-select-all" class="st-duration-select st-btn-select">All Time</div><div id="st-duration-select-month" class="st-duration-select">This Month</div><div id="st-duration-select-week" class="st-duration-select">This Week</div><div id="st-duration-select-day" class="st-duration-select">Today</div></div><div class="st-coseller-kpi-div"><div class="div-block-137"><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Total Earnings</div><div id="st-coseller-kpi-val-tot-earning" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Clicks</div><div id="st-coseller-kpi-val-tot-click" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Publishes</div><div id="st-coseller-kpi-val-tot-publish" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Currency</div><div id="st-coseller-kpi-val-currency" class="st-coseller-kpi-val">USD</div></div></div><div class="st-coseller-kpi-products"><div><h3 class="st-coseller-products-title">Products Published</h3></div><div class="st-coseller-products-list" id="st-coseller-products-list"><div class="st-coseller-product" id="st-coseller-product-000" style="display: none;"><div class="st-coseller-product-div"><div class="st-coseller-product-details"><div class="st-coseller-product-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-coseller-product-img"></div><div class="st-coseller-product-desc"><div class="st-coseller-product-name">Product Name</div><div class="st-coseller-product-vendor">Vendor Name</div></div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Total Earnings</div><div class="st-coseller-kpi-val st-product-tot-earnings">$ 000</div></div></div><div class="div-block-146"><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Product Price</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Clicks</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Publishes</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-nudge-btn">Cosell</div></div></div></div></div></div></div></div></div></div></div>`;
		let cosellMask = `<div id="st-cosell-mask" style="display:none" class="st-cosell-link-mask" onclick="ShoptypeUI.hide(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-cosell-links-header">Here’s your unique Cosell link!</div><div class="st-cosell-body"><div class="st-cosell-links-image"><img src="https://user-images.githubusercontent.com/4776769/164173060-33787091-37fc-45a9-b16e-2c3eb1fb82e7.png" loading="lazy" alt=""></div><div class="st-cosell-social-links"><div class="st-cosell-social-title">Share it on Social Media</div><div class="st-cosell-socialshare"> <a id="st-fb-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164173335-e156685a-9be9-468f-9aef-145e4d6b8ee7.png" loading="lazy" alt=""></a> <a id="st-twitter-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164174320-1234c471-5b69-473e-8b63-46b4d8f61189.png" loading="lazy" alt=""></a> <a id="st-whatsapp-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164174179-5103826f-d131-4677-b581-031727195c0e.png" loading="lazy" alt=""></a> <a id="st-pinterest-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164173344-e0f1fbe1-1ac0-4846-837b-97f47a556bf5.png" loading="lazy" alt=""></a> <a id="st-linkedin-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://user-images.githubusercontent.com/4776769/164173350-af72f6b5-7926-42c6-abb4-c77b6db9da58.png" loading="lazy" alt=""></a></div></div><div class="st-cosell-links-txt">or</div><div class="st-cosell-sharelink"><div class="st-cosell-sharelink-div"><div class="st-cosell-sharelink-url"><div class="st-cosell-link-copy-btn" onclick="shoptype_UI.stCopyCosellUrl('st-cosell-url-input')">🔗 Copy to Clipboard</div> <input type="text" id="st-cosell-url-input" class="st-cosell-sharelink-url-txt"></input></div></div><div id="st-cosell-sharewidget" class="st-cosell-sharelink-div"><div class="st-cosell-share-widget-txt">Share on Blogs</div><div id="st-widget-btn" class="st-cosell-share-widget-btn">Get an Embed</div></div></div></div><div class="st-cosell-links-footer"><div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a></div> <a href="#" target="_blank" class="w-inline-block" style="display:none;"><div class="st-cosell-page-txt">Learn more about Coselling</div> </a></div></div></div>`;

		this.st_Wraper.innerHTML = st_coseller_profile.replace("{site}", this.currentUrl.hostname) +
								cosellMask+
								st_cosell_screen.replace("{{site}}", this.currentUrl.host)+
								st_loader +
								'<div id="st-errors" class="st-error-msg-box"> </div>'
		
		let body = document.getElementsByTagName('body')[0];
		body.insertBefore(this.st_Wraper, body.firstChild);
		this.loader = document.getElementById("st-loader-mask");
		this.setupUser();
	}

	setProductUrl(productUrl){
		this.#productUrl = productUrl;
	}

	setLoginUrl(loginUrl){
		this.#stLoginUrl = loginUrl;
	}

	setPlatform(platformId){
		this.#platformId = platformId;
		if(this.user){
			this.user.setPlatform(this.#platformId);
		}
		this.setupShareUrl();
	}

	setRefferId(rid){
		this.#rid = rid;
	}

	setupUser(){
		let stToken = this.currentUrl.searchParams.get("token")
		if(stToken && stToken!=""){
			STUtils.setCookie("stToken", stToken, 20);
			this.removeAccessTokenFromUrl();
		}else{
			stToken = STUtils.getCookie("stToken");
		}

		if(stToken && stToken!=""){
			this.user = new STUser(stToken);
			if(this.#platformId!=null){
				this.user.setPlatform(this.#platformId);
			}
		}
		this.setupShareUrl();
	}

	setupShareUrl(){
		if((typeof ignoreEvents !== 'undefined') && ignoreEvents){return;}
		const { location } = window;
		const { search } = location;
		var thisUrl = new URL(location);
		this.#cosellerTId = sessionStorage["st-ctid"];
		this.getUserTracker();
		
		var param_tid = thisUrl.searchParams.get("tid");
		if(param_tid != this.#userTId && param_tid!="" && param_tid!=null){
			if(param_tid.startsWith("nv_")){
				sessionStorage["st-ctid"]=param_tid;
			}
			this.#cosellerTId = param_tid;
		}
		if((this.#platformId||this.#cosellerTId) && (!this.#eventSent)){
			STUser.sendUserEvent(this.#cosellerTId, this.#platformId);
			this.#eventSent = true;
		}
		this.updateUrlTid();
	}

	getUserTracker(){
		this.#userTId = sessionStorage["st-utid"];
		if(!this.#userTId && this.user){
			this.user.getNetworkTracker(location.href)
			.then(tracker=>{
				if(tracker.trackerId){
					sessionStorage["st-utid"] = tracker.trackerId;
					this.#userTId = sessionStorage["st-utid"];
					this.updateUrlTid();			
				}
			});
		}
		return this.#userTId;
	}

	updateUrlTid(){
		var newUrl = ShoptypeUI.replaceUrlParam(window.location.href, "tid", this.#userTId);
		window.history.replaceState({}, '', newUrl);
	}

	sendViewEvent(platformId){
		this.#platformId = platformId;
		STUser.sendUserEvent(this.#cosellerTId, this.#platformId);
	}

	removeAccessTokenFromUrl() {
		const { history, location } = window;
		const { search } = location;
		if (search && search.indexOf('token') !== -1 && history && history.replaceState) {
			const cleanSearch = search.replace(/(\&|\?)token([_A-Za-z0-9=\.\-%]+)/g, '').replace(/^&/, '?');
			const cleanURL = location.toString().replace(search, cleanSearch);
			history.replaceState({}, '', cleanURL);
		}
	}

	addToCart(button){
		this.stShowLoader();
		let variantId = button.getAttribute("variantid");
		let variantName = JSON.parse(button.getAttribute("variantName"));
		let productId = button.getAttribute("productid");
		let thisVendorId = button.getAttribute("vendorid");

		let quantity = 1;
		let quantSelect = button.getAttribute("quantitySelect");
		
		if (typeof stSelectedVariants === "function") { 
			let selectedOptions = [];
	    	selectedOptions = stSelectedVariants();
	    	let variant = getVariant(st_platform.products.product(productId), selectedOptions);
	    	variantId = variant.id;
		}

		if (quantSelect && quantSelect!=""){
			quantity = parseInt(document.querySelector(quantSelect).value);
		}
		if(quantity==0){
			return;
		}
		st_platform.addToCart(productId, variantId, variantName, quantity)
			.then(cartJson=>{
				this.stHideLoader();
				if(cartJson.cart_lines){
					ShoptypeUI.showSuccess( ShoptypeUI.pluralize(quantity, "item") + " added to cart")
				}else{
					ShoptypeUI.showError(cartJson.message);
				}
			});
		return false;
	}

	static pluralize = (count, noun, suffix = 's') =>
  		`${count} ${noun}${count !== 1 ? suffix : ''}`;

	showCosell(productId){
		if(!this.user){
			sessionStorage["autoOpen"] = '{"tab":"Cosell","pid":"'+productId+'"}';
			document.getElementById("st-cosell-intro-mask").style.display="flex";
		}else{
			st_platform.product(productId, product=>{this.setupShare(product)});
			document.getElementById("st-cosell-mask").style.display="flex";
		}
	}

	setupShare(product){
		document.getElementById("st-cosell-sharewidget").style.display="none";
		product = product.product;
		this.user.getTracket(product.id)
			.then(trackerJson=>{
				let sharetxt = "Hey found this really interesting product you may be iterested in ";
				let refUrl = window.location.protocol + "//" + window.location.host + this.#productUrl.replace("{{productId}}",product.id).replace("{{tid}}",trackerJson.trackerId);
				let encodedUrl = encodeURIComponent(refUrl);
				document.getElementById("st-fb-link").href = "https://www.facebook.com/sharer/sharer.php?u="	+ encodedUrl;
				document.getElementById("st-whatsapp-link").href = "whatsapp://send?text=" + sharetxt + product.title + " " + encodedUrl;
				document.getElementById("st-twitter-link").href = "http://twitter.com/share?text=" + sharetxt + "&url="	+ encodedUrl;
				document.getElementById("st-pinterest-link").href = "https://pinterest.com/pin/create/link/?url=" + encodedUrl + "&media=" + product.primaryImageSrc.imageSrc + "&description=" + product.title;
				let description = product.description?product.description.substr(0,250):"";
				document.getElementById("st-linkedin-link").href = "https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=" + encodedUrl + "&title=" + product.title + "&summary=" + description;
				document.getElementById("st-cosell-url-input").value = refUrl;
			});
	}

	showLogin(){
		let tid = this.currentUrl.searchParams.get("tid");

		if(!this.user){
			let loginUrl = this.#stLoginUrl + '?redirectUrl=' + encodeURIComponent(window.location.href);
			if(tid){
				loginUrl += tid?"&tid=" + tid:"";
			}else{
				loginUrl += this.#rid?"&rid=" + this.#rid:"";
			}
			window.location.replace(loginUrl);
		}
	}

	stShowLoader(hideDelay=10000){
		this.loader.style.display = "";
		setTimeout(()=>{this.stHideLoader()}, hideDelay);
	}

	stHideLoader(){
		this.loader.style.display = "none";
	}

	stCopyCosellUrl(elementID) {
		let copyText = document.getElementById(elementID);
		copyText.select();
		copyText.setSelectionRange(0, 99999); 
		document.execCommand("copy");
	}

	static sendUserEvent(){
		let url = new URL(window.location);
		let tid = url.searchParams.get("tid");
		STUser.sendUserEvent(tid);
	}

	static hide(element){
		element.style.display="none";
	}

	static showMessage(message, type, closeTicks = 5000){
		const newError = document.createElement("div");
		const newErrorMsg = document.createElement("h3");
		const newCloseBtn = document.createElement("a");
		newErrorMsg.innerHTML=message;
		newCloseBtn.innerHTML="&times;"; 
		newCloseBtn.setAttribute("onclick", "ShoptypeUI.hideMessage(this)")
		newError.appendChild(newErrorMsg);
		newError.appendChild(newCloseBtn);
		newError.classList.add("st-alert");
		newError.classList.add(type);
		newCloseBtn.classList.add("st-close");
		document.getElementById("st-errors").appendChild(newError);
		setTimeout(()=>{ShoptypeUI.hideMessage(newCloseBtn)}, closeTicks)
	}

	static showError(message, closeTicks = 5000){
		ShoptypeUI.showMessage(message, "st-error", closeTicks);
	}

	static showWarning(message, closeTicks = 5000){
		ShoptypeUI.showMessage(message, "st-warning", closeTicks);
	}

	static showSuccess(message, closeTicks = 5000){
		ShoptypeUI.showMessage(message, "st-success", closeTicks);
	}

	static hideMessage(closeBtn){
		closeBtn.parentElement.remove();
	}

	static replaceUrlParam(url, paramName, paramValue)
	{
	    if (paramValue == null) {
	        paramValue = '';
	    }
	    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
	    if (url.search(pattern)>=0) {
	        return url.replace(pattern,'$1' + paramValue + '$2');
	    }
	    url = url.replace(/[?#]$/,'');
	    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
	}
}

const shoptype_UI = new ShoptypeUI();

STUtils.sendEvent("ShoptypeUILoaded","");

		
class STCheckout{
	#cartHtml = `<div class="st-checkout-window"> <div class="st-checkout-close" onclick="stCheckout.hideCheckout()">X</div><div class="st-cart"> <div class="st-cart-head"> <h1>Cart</h1> </div><div class="st-cart-main"> <div class="st-cart-products"> <div id="st-cart-product" class="st-cart-product" style="display:none"> <div class="st-cart-product-details"> <div class="st-cart-product-img-div"><img src="" loading="lazy" alt="" class="st-cart-product-img"></div><div class="st-cart-product-sum"> <h2 class="st-cart-product-title"></h2> <div> <div id="st-cart-product-var" class="st-cart-product-var"> <div class="st-cart-product-var-title">Variant:</div><div class="st-cart-product-var-val"></div></div></div></div></div><div class="st-cart-product-pricing"> <div class="st-cart-product-price"></div><input type="number" pid="" vid="" name="" class="st-cart-product-qty" value="" onchange="stCheckout.cartUpdateProductQuant(this)"> <div class="st-cart-product-tot-price"></div></div><div class="st-cart-product-remove" onclick="removeProduct(this)"><img src="<?php echo $path ?>/images/delete.png" loading="lazy" alt=""></div></div></div><div class="st-cart-details"> <div class="st-cart-top"> <h3 class="st-cart-sum-title">CART TOTALS</h3> </div><div> <div class="st-cart-details-title"> <div class="st-cart-subtitle">Subtotal</div><div class="st-cart-subtotal"></div></div><div class="st-cart-shipping"> <div class="st-cart-subtitle">Shipping</div><div class="st-shipping-details"> <div class="st-shipping-cost">Calculated at Checkout</div><div class="st-shipping-add"></div></div></div><div class="st-cart-details-title"> <div class="st-cart-subtitle">Total</div><div class="st-cart-total"></div></div></div><div class="st-cart-checkout-btn" onclick="stCheckout.checkout()"> <div class="st-cart-checkout-btn-txt">Proceed to Checkout</div></div></div></div></div><div class="st-chkout-container" style="display:none"> <div class="st-chkout-top"> <h1 class="st-chkout-title">Checkout</h1> <div class="st-chkout-coupon" style="display:none;"> <div class="st-chkout-coupon-title">Have a coupon?</div><div class="st-chkout-coupon-code">Click here to enter your code</div></div></div><div class="st-chkout-main"> <div class="st-chkout-billing"> <form id="modalAddressForm"> <div class="st-chkout-billing-title">BILLING DETAILS</div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Name *</div><input type="text" name="name" class="st-chkout-billing-fld-val" value="" onchange="stCheckout.updateAddress()" required> </div><div class="st-chkout-billing-fld" style="display: none;"> <div class="st-chkout-billing-fld-name">Last Name</div><input type="text" name="lastName" class="st-chkout-billing-fld-val"> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Street Address *</div><input type="text" name="address" class="st-chkout-billing-fld-val" value="" required onchange="stCheckout.updateAddress()"> <input type="text" name="address2" class="st-chkout-billing-fld-val" onchange="stCheckout.updateAddress()"> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Town / City *</div><input type="text" name="city" class="st-chkout-billing-fld-val" value="" required onchange="stCheckout.updateAddress()"> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">State *</div><select name="state" class="st-chkout-billing-fld-val" id="st-chkout-state" value="" required onchange="stCheckout.updateAddress()"> <option value="">Select state</option> </select> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Country *</div><select name="country" class="st-chkout-billing-fld-val" id="st-chkout-country" value="" required onchange="stCheckout.updateAddress()"> <option value="">Select Country</option> </select> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">PIN *</div><input type="text" name="pincode" class="st-chkout-billing-fld-val" value="" required onchange="stCheckout.updateAddress()"> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Phone</div><input type="text" name="phone" class="st-chkout-billing-fld-val" value="" onchange="stCheckout.updateAddress()"> </div><div class="st-chkout-billing-fld"> <div class="st-chkout-billing-fld-name">Email Address *</div><input type="text" name="email" class="st-chkout-billing-fld-val" value="" required onchange="stCheckout.updateAddress()"> </div></form> </div><div class="st-chkout-sum"> <div class="st-chkout-products"> <div id="st-chkout-products-list" class="st-chkout-products-list" style="display:none"> <select name="shippingOption" orderId="" class="st-chkout-billing-fld-val" id="st-shipping-" value="" required onchange="stCheckout.onShippingChanged(this)"> <option value="" ></option> </select> <div class="st-chkout-products-head"> <div class="st-chkout-products-title">PRODUCT</div><div class="st-chkout-products-tot">SUBTOTAL</div></div><div id="st-chkout-product" class="st-chkout-product"> <div class="div-block-18"> <div class="st-chkout-product-title"><span class="st-chkout-product-qty">x </span></div></div><div class="st-chkout-product-tot"></div></div></div></div><div class="st-chkout-details"> <div class="st-chkout-tot-row"> <div class="st-chkout-tot-title">SUBTOTAL</div><div class="st-chkout-cost"></div></div><div class="st-chkout-tot-row"> <div class="st-chkout-tot-title">SHIPPING</div><div class="st-chkout-shipping-tot"></div></div><div class="st-chkout-tot-row"> <div class="st-chkout-tot-title">TAX</div><div id="st-chkout-tax-tot" class="st-chkout-shipping-tot"></div></div><div class="st-chkout-tot-row"> <div class="st-chkout-tot-title">TOTAL</div><div class="st-chkout-tot-cost"></div></div></div><div id="payment-container"></div><div class="st-chkout-btn" onclick="stCheckout.showPayment()"> <div class="st-chkout-btn-txt">PLACE ORDER</div></div></div></div></div><div class="st-checkout-success" style="display:none"><div class="div-block-21"><h2 class="st-success-heading">Checkout Successful!</h2><div class="st-success-txt">Thank you for shopping with us! <br>You’ll be notified about your order status and tracking by email.</div></div><div class="st-success-details"><div id="st-success-product" class="st-success-product" style="display:none"><div class="st-success-prod-img-box"><img src="" loading="lazy" alt="" class="st-success-prod-ing"></div><div class="st-success-desc"> <div class="st-success-prod-details"></div></div></div></div></div></div>`;
	#stCheckoutWraper = null;
	#myCurrency = "";
	#checkoutId = null;

	constructor(){
		let body = document.getElementsByTagName('body')[0];
		this.#stCheckoutWraper = document.createElement("div");
		this.#stCheckoutWraper.classList.add("st-checkout");
		this.#stCheckoutWraper.style.display="none";
		this.#stCheckoutWraper.innerHTML = this.#cartHtml;		
		body.insertBefore(this.#stCheckoutWraper, body.firstChild);
		this.checkoutSetCountry();
	}

	showCheckout(){
		this.setUpCart();
		this.showCartPage();
		this.#stCheckoutWraper.style.display="";
	}

	hideCheckout(){
		this.#stCheckoutWraper.style.display="none";
	}

	showCartPage(){
		this.#stCheckoutWraper.querySelector(".st-cart").style.display="";
		this.#stCheckoutWraper.querySelector(".st-chkout-container").style.display="none";
		this.#stCheckoutWraper.querySelector(".st-checkout-success").style.display="none";
	}

	showCheckoutPage(){
		this.#stCheckoutWraper.querySelector(".st-cart").style.display="none";
		this.#stCheckoutWraper.querySelector(".st-checkout-success").style.display="none";
		this.#stCheckoutWraper.querySelector(".st-chkout-container").style.display="";
	}

	showCheckoutSuccess(checkoutId){
		shoptype_UI.stShowLoader();
		st_platform.checkout(checkoutId)
		.then(checkout=>{
			switch(checkout.payment.status) {
				case "success":
					this.#stCheckoutWraper.querySelector(".st-chkout-container").style.display="none";
					this.#stCheckoutWraper.querySelector(".st-cart").style.display="none";
					this.setupCheckoutSuccess(checkout);
					this.#stCheckoutWraper.querySelector(".st-checkout-success").style.display="";
				break;
				case "failure":
				ShoptypeUI.showError("Payment Failed! please retry.", 10000);
				this.showCheckoutPage();
				break;
				case " created":
				default:
					shoptype_UI.stShowLoader();
					setTimeout(function(){stCheckout.showCheckoutSuccess(checkoutId)}, 500);
				break;
			}
		});
	}

	setupCheckoutSuccess(checkout){
		var productTemplate = this.#stCheckoutWraper.querySelector("#st-success-product");
		var productList = this.#stCheckoutWraper.querySelector(".st-success-details");
		Object.keys(checkout.order_details_per_vendor).forEach(key => {
			for (var i = 0; i < checkout.order_details_per_vendor[key].cart_lines.length; i++) {
				var product = checkout.order_details_per_vendor[key].cart_lines[i];
				var newProduct = productTemplate.cloneNode(true);
				newProduct.id = product.product_id + "-" + i;
				newProduct.style.display = "";
				newProduct.querySelector(".st-success-prod-ing").src = product.image_src;
				var title = product.name + "<br/>";
				Object.keys(product.variant_name_value).forEach(varKey => {
					title += varKey+":"+product.variant_name_value[varKey]+",<br/>";
				});
				title += " x " + product.quantity;
				newProduct.querySelector(".st-success-prod-details").innerHTML = title;
				productList.appendChild(newProduct);
			}
		});
	}

	setUpCart(){
		st_platform.getCart()
			.then(thisCart=>{
				var productsList = this.#stCheckoutWraper.querySelector(".st-cart-products");
				var productTemplate = this.#stCheckoutWraper.querySelector("#st-cart-product");
				this.#stCheckoutWraper.querySelectorAll(".st-cart-product").forEach(x=>{
					if(x!=productTemplate){
						x.remove();
					}
				})
				for (var i = 0; i < thisCart.cart_lines.length; i++) {
					var newProduct = productTemplate.cloneNode(true);
					newProduct.id = thisCart.cart_lines[i].product_id + i;

					newProduct.querySelector(".st-cart-product-img").src = thisCart.cart_lines[i].image_src;
					newProduct.querySelector(".st-cart-product-price").innerHTML = thisCart.cart_lines[i].price.amount;
					newProduct.querySelector(".st-cart-product-tot-price").innerHTML = thisCart.cart_lines[i].quantity*thisCart.cart_lines[i].price.amount;
					var qtySelect = newProduct.querySelector(".st-cart-product-qty");
					qtySelect.value = thisCart.cart_lines[i].quantity;
					qtySelect.setAttribute("pid",thisCart.cart_lines[i].product_id);
					qtySelect.setAttribute("vid",thisCart.cart_lines[i].product_variant_id);
					qtySelect.setAttribute("vname", JSON.stringify(thisCart.cart_lines[i].variant_name_value));
					
					var variantTitle = "";
					if(thisCart.cart_lines[i].variant_name_value){
						Object.keys(thisCart.cart_lines[i].variant_name_value).forEach(key => {
							variantTitle += key + ":" + thisCart.cart_lines[i].variant_name_value[key] + ", ";
						});
					}

					newProduct.querySelector(".st-cart-product-title").innerHTML = thisCart.cart_lines[i].name;
					newProduct.querySelector(".st-cart-product-var-title").innerHTML = variantTitle;
					newProduct.style.display="";
					productsList.appendChild(newProduct);
				}
				this.#stCheckoutWraper.querySelector(".st-cart-subtotal").innerHTML=thisCart.sub_total.amount;
				this.#stCheckoutWraper.querySelector(".st-cart-total").innerHTML=thisCart.sub_total.amount;
			});
	}

	cartUpdateProductQuant(qtyInput){
		var productId = qtyInput.getAttribute("pid");
		var variantId = qtyInput.getAttribute("vid");
		var variantName = JSON.parse(qtyInput.getAttribute("vname"));
		var quantity = parseInt(qtyInput.value);
		shoptype_UI.stShowLoader();
		st_platform.updateCart(productId, variantId, variantName, quantity)
			.then(cartJson => {
				if(quantity==0){
					qtyInput.parentElement.parentElement.remove();
				}else{
					var parent = qtyInput.parentElement;
					var prodVal = parent.querySelector(".st-cart-product-price").innerHTML.replace(this.#myCurrency,"");
					prodVal = parseFloat(prodVal);
					parent.querySelector(".st-cart-product-tot-price").innerHTML = this.#myCurrency + (quantity*prodVal);
				}
				var totQuant = 0;
				var totSum = this.#myCurrency+"0";
				if(cartJson.sub_total){
					totQuant = cartJson.total_quantity;
					totSum = this.#myCurrency+cartJson.sub_total.amount;
				}
				let shoptypeCartCountChanged =new CustomEvent('shoptypeCartCountChanged', {'detail': {
					"count": totQuant
				}});
				document.dispatchEvent(shoptypeCartCountChanged);
				this.#stCheckoutWraper.querySelector(".st-cart-subtotal").innerHTML = totSum;
				this.#stCheckoutWraper.querySelector(".st-cart-total").innerHTML = totSum;
					shoptype_UI.stHideLoader();
			});
	}

	checkout(){
		shoptype_UI.stShowLoader();
		st_platform.createCheckout((checkoutJson)=>{
			if(checkoutJson.message){
				shoptype_UI.stHideLoader();
				ShoptypeUI.showError(checkoutJson.message);
			}else{
				this.#checkoutId=checkoutJson.checkout_id;
				st_platform.checkout(checkoutJson.checkout_id)
					.then(checkout=>{
						shoptype_UI.stHideLoader();
						this.setupCheckout(checkout);
						this.showCheckoutPage(); 
					});
			}
		})
	}

	setupCheckout(checkoutJson){
		var vendorList = this.#stCheckoutWraper.querySelector(".st-chkout-products");
		var vendorTemplate = this.#stCheckoutWraper.querySelector("#st-chkout-products-list");
		var vendorProductTemplate = vendorTemplate.querySelector("#st-chkout-product");

		if(typeof initSTPayment=== 'undefined'){
			STUtils.st_loadScript("https://shoptype-scripts.s3.amazonaws.com/payment_js/st-payment-handlers-bundle.js");
			STUtils.st_loadScript("https://js.stripe.com/v3/");
			STUtils.st_loadScript("https://checkout.razorpay.com/v1/checkout.js");
		}

		this.#stCheckoutWraper.querySelectorAll(".st-chkout-products-list").forEach(x=>{
			if(x!=vendorTemplate){
				x.remove();
			}
		})
		
		Object.keys(checkoutJson.order_details_per_vendor).forEach(key => {
			var vendorCart = checkoutJson.order_details_per_vendor[key];
			var vendorCartElem = vendorTemplate.cloneNode(true);
			vendorCartElem.style.display="";
			vendorCartElem.id = key;
			vendorCartElem.querySelector(".st-chkout-product").remove();
			vendorCartElem.querySelector(".st-chkout-billing-fld-val").id = "st-shipping-" + key;
			for (var i = 0; i < vendorCart.cart_lines.length; i++) {
				var newCartProduct = vendorProductTemplate.cloneNode(true);
				var cartLine = vendorCart.cart_lines[i];
				newCartProduct.id = cartLine.product_id + "-" + cartLine.product_variant_id;
				var lineTitle = cartLine.name + " - ";
				if(cartLine.variant_name_value){
					Object.keys(cartLine.variant_name_value).forEach(variantKey => {
						lineTitle += variantKey+":"+cartLine.variant_name_value[variantKey]+", "
					});					
				}
				newCartProduct.querySelector(".st-chkout-product-title").innerHTML = lineTitle;
				//newCartProduct.querySelector(".st-chkout-product-qty").innerHTML = "X " + cartLine.quantity;
				newCartProduct.querySelector(".st-chkout-product-tot").innerHTML = this.#myCurrency+cartLine.price.amount;
				vendorCartElem.appendChild(newCartProduct);
			}
			vendorList.appendChild(vendorCartElem);
		});
		this.updateStCheckout(checkoutJson);
		this.updateAddress();
	}

	removeProduct(removeBtn){
		var quantity = removeBtn.parentElement.querySelector(".st-cart-product-qty");
		quantity.value = 0;
		cartUpdateProductQuant(quantity)
	}

	checkoutSetCountry(){
		STUtils.countries()
		.then(countriesJson => {
			let countryField = document.getElementById("st-chkout-country");
			let selectedCntry =	countryField.getAttribute("value");
			let selectedVal = null;
			for (var i = 0; i < countriesJson.data.length; i++) {
				var option = document.createElement("option");
				option.text = countriesJson.data[i].name;
				option.value = countriesJson.data[i].iso2;
				if(selectedCntry==countriesJson.data[i].name){
					option.setAttribute("selected","");
				}
				countryField.add(option);
			}
			countryField.addEventListener('change', () => {
				if(countryField.value && countryField.value != ""){
					STUtils.states(countryField.value)
					.then(statesJson => {
						let stateField = document.getElementById("st-chkout-state");
						let selectedState =	stateField.getAttribute("value");
						for (var i = stateField.options.length-1; i > 0; i--) {
							stateField.options[i] = null;
						}
						for (var i = 0; i < statesJson.data.length; i++) {
							var option = document.createElement("option");
							option.text = statesJson.data[i].name;
							option.value = statesJson.data[i].state_code;
							if(selectedState==statesJson.data[i].name){
								option.setAttribute("selected","");
							}
							stateField.add(option);
						}
					});
				}
			});
			countryField.dispatchEvent(new Event('change'));
		});
	}

	onShippingChanged(shippingSelect){
		shoptype_UI.stShowLoader();
		let orderKey = shippingSelect.getAttribute("orderId");
		let shippingBody = {
			"method_key_per_vendor": {
				[orderKey]: {
					method_key: shippingSelect.options[shippingSelect.selectedIndex].value
				}
			}
		};
		this.#stCheckoutWraper.querySelector(".st-chkout-btn").style.display="";
		this.#stCheckoutWraper.getElementById("payment-container").innerHTML="";
		st_platform.updateShipping(this.#checkoutId,shippingBody).then(checkoutJson=>{
			this.updateStCheckout(checkoutJson);
			shoptype_UI.stHideLoader();
		});
	}

	updateStCheckout(checkout){
		Object.keys(checkout.order_details_per_vendor).forEach(key => {
			let vendorCart = checkout.order_details_per_vendor[key];
			let cartShipSelect = document.getElementById("st-shipping-"+key);
			this.removeOptions(cartShipSelect);
			if(vendorCart.shipping_options){
				for (var i = 0; i < vendorCart.shipping_options.length; i++) {
					var shipOption = vendorCart.shipping_options[i];
					var option = document.createElement("option");
					option.text = shipOption.method_title;
					option.value = shipOption.method_key;
					cartShipSelect.add(option);
					if(vendorCart.shipping_selected.method_key==shipOption.method_key){
						option.setAttribute("selected","");
					}
				}
			}
			if(!checkout.requires_shipping && vendorCart.shipping_options.length==0){
				var option = document.createElement("option");
				option.text = "Shipping Not Required";
				option.value = "No Shipping";
				option.setAttribute("selected","");
				cartShipSelect.add(option);
			}

		});
		this.#stCheckoutWraper.querySelector(".st-chkout-cost").innerHTML = this.#myCurrency+checkout.sub_total.amount;
		this.#stCheckoutWraper.querySelector(".st-chkout-shipping-tot").innerHTML = this.#myCurrency+checkout.shipping.amount;
		this.#stCheckoutWraper.querySelector("#st-chkout-tax-tot").innerHTML = this.#myCurrency+checkout.taxes.amount;
		this.#stCheckoutWraper.querySelector(".st-chkout-tot-cost").innerHTML = this.#myCurrency+checkout.total.amount;
	}

	removeOptions(selectElement) {
		if(selectElement.options.length==0){return;}
		 var L = selectElement.options.length - 1;
		 for(var i = L; i >= 0; i--) {
				selectElement.remove(i);
		 }
	}

	updateAddress(){
		var addressForm = document.getElementById("modalAddressForm");
		if(!addressForm.checkValidity()){
			return;
		}
		shoptype_UI.stShowLoader();
		let countrySelect = document.getElementById("st-chkout-country");
		let stateSelect = document.getElementById("st-chkout-state");
		let checkoutBody = {
			"shipping_address": {
				"name": addressForm.querySelector('[name="name"]').value,
				"phone": addressForm.querySelector('[name="phone"]').value,
				"email": addressForm.querySelector('[name="email"]').value,
				"street1": addressForm.querySelector('[name="address"]').value+ " " + addressForm.querySelector('[name="address2"]').value,
				"city": addressForm.querySelector('[name="city"]').value,
				"country": countrySelect.options[countrySelect.selectedIndex].text,
				"countryCode": countrySelect.value,
				"state": stateSelect.options[stateSelect.selectedIndex].text,
				"stateCode": stateSelect.value,
				"postalCode": addressForm.querySelector('[name="pincode"]').value
			},
			"is_shipping_billing": true
		};
		checkoutBody.billing_address = checkoutBody.shipping_address

		st_platform.updateAddress(this.#checkoutId,checkoutBody)
			.then(checkoutJson => {
				if(checkoutJson.error){
					shoptype_UI.stHideLoader();
					ShoptypeUI.showError(checkoutJson.message);
					var shippingSelect = this.#stCheckoutWraper.querySelector(".st-chkout-products").getElementsByClassName("st-chkout-billing-fld-val");
					for (let i = 0; i < shippingSelect.length; i++) {
						this.removeOptions(shippingSelect[i]);
						var option = document.createElement("option");
						option.text = checkoutJson.message;
						option.value = "";
						shippingSelect[i].add(option);
					}
				}else{
					this.updateStCheckout(checkoutJson);
					shoptype_UI.stHideLoader();
				}
			});
	}
	
	showPayment(){
		var shippingSelect = this.#stCheckoutWraper.querySelector(".st-chkout-products").getElementsByClassName("st-chkout-billing-fld-val");
		for (let i = 0; i < shippingSelect.length; i++) {
			if(shippingSelect[i].id=='st-shipping-'){continue;}
			if(shippingSelect[i].value == null ||shippingSelect[i].value == "" ){
				ShoptypeUI.showError("Shipping method not selected");
				return;
			}
		}
		try{
			initSTPayment(this.#checkoutId, STUtils.backendUrl, st_platform.apiKey, (payload)=>{stCheckout.onPaymentReturn(payload, this.#checkoutId)} );
			this.#stCheckoutWraper.querySelector(".st-chkout-btn").style.display="none";
			this.#stCheckoutWraper.querySelector("#payment-container").style.display="";
		}catch(e){
			ShoptypeUI.showError(e.message);
		}
	}

	onPaymentReturn(payload, checkoutId){
		switch(payload.status){
		case "failed":
			ShoptypeUI.showError(payload.message);
			this.#stCheckoutWraper.querySelector(".st-chkout-btn").style.display="";
			this.#stCheckoutWraper.querySelector("#payment-container").style.display="none";
			break;
		case "closed":
			this.#stCheckoutWraper.querySelector(".st-chkout-btn").style.display="";
			this.#stCheckoutWraper.querySelector("#payment-container").style.display="none";
			break;
		case "success":
			STUtils.setCookie("carts","",0);
			this.showCheckoutSuccess(checkoutId);
			break;
		default:
			this.#stCheckoutWraper.querySelector(".st-chkout-btn").style.display="";
			this.#stCheckoutWraper.querySelector("#payment-container").style.display="none";
			break;
		}
	}
	
	createCheckout(cartId){
		if(this.#checkoutId == "new"){
			var currentUrl = new URL(window.location);
			let tid = currentUrl.searchParams.get("tid");
			STUser.sendUserEvent(tid, "<?php echo $stPlatformId ?>");
			st_platform.createCheckout(data=>{
				this.#checkoutId = data.checkout_id;
			},cartId);	
		}
	}

}

let stCheckout = null;
if(typeof modalCheckout=== 'undefined' || modalCheckout){
	stCheckout = new STCheckout();
}