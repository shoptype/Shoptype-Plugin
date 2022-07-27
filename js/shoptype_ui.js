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
			sessionStorage["st-ctid"]=param_tid;
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
		st_platform.addToCart(productId, variantId, quantity)
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