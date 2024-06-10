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
		
		this.setupUser();
	}

	setCosellWidget(baseUrl){
		let st_loader = '<div class="st-loader-mask" id="st-loader-mask" style="display:none;"><img src="https://user-images.githubusercontent.com/4776769/172153004-febffb83-f0ed-46da-8d79-4be74aa70baf.gif" alt="" style="max-width: 20%;"></div>';

		fetch(`/${baseUrl}cosell/new/`).
		then(response=>response.text()).
		then(responseHtml=>{
			this.st_Wraper.innerHTML = responseHtml+
						st_loader +
						'<div id="st-errors" class="st-error-msg-box"> </div>'
			let body = document.getElementsByTagName('body')[0];
			body.insertBefore(this.st_Wraper, body.firstChild);
			this.loader = document.getElementById("st-loader-mask");
		});
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
		this.setupShare();
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
		this.setupShare();
	}

	setupShare(){
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
		if(this.#userTId!=null && this.#userTId!=""){
			var newUrl = ShoptypeUI.replaceUrlParam(window.location.href, "tid", this.#userTId);
			window.history.replaceState({}, '', newUrl);
		}
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
		let metadata = JSON.parse(button.getAttribute("meta_data"));

		let quantity = 1;
		let quantSelect = button.getAttribute("quantitySelect");
		
		if (typeof stSelectedVariants === "function") { 
			let selectedOptions = [];
	    	selectedOptions = stSelectedVariants();
	    	let variant = getVariant(st_platform.products.product(productId), selectedOptions);
	    	variantId = variant.id;
		}

		if (quantSelect && quantSelect!="" && document.querySelector(quantSelect)!==null){
			quantity = parseInt(document.querySelector(quantSelect).value);
		}
		if(quantity==0){
			return;
		}
		st_platform.addToCart(productId, variantId, variantName, quantity, metadata)
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

	buyNow(button){
		this.stShowLoader();
		let variantId = button.getAttribute("variantid");
		let variantName = JSON.parse(button.getAttribute("variantName"));
		let productId = button.getAttribute("productid");

		window.location.href = `/checkout/new/?productid=${productId}&variantid=${variantId}`;
	}

	addVendorOffers(vendorId, offersDiv){
		var endpoint = {
			resource:"/platforms/" + this.#platformId + "/vendors/?vendorId=" + vendorId,
			method:"get"
		};
		STUtils.request(endpoint,(data)=>{
			var metadata = data.vendors[0];
			var vendorOffers=null;
			data.vendors[0].vendor_meta_data.forEach((item)=>{
				if(item.key=="Promo Offers"){
					vendorOffers = item.value;
				}
			});
			offersDiv.innerHTML=vendorOffers;
		});

	}

	static pluralize = (count, noun, suffix = 's') =>
  		`${count} ${noun}${count !== 1 ? suffix : ''}`;

	showCosell(productId){
		if(!this.user){
			sessionStorage["autoOpen"] = '{"tab":"Cosell","pid":"'+productId+'"}';
			document.getElementById("st-cosell-intro-mask").style.display="flex";
		}else{
			if(productId==null){
				this.setupShareUrl(window.location.href);
			}else{
				st_platform.product(productId, product=>{this.setupShareinfo(product)});
			}
			document.getElementById("st-cosell-sharewidget").style.display="none";
			document.getElementById("st-cosell-mask").style.display="flex";
		}
	}

	setupShareinfo(product){
		product = product.product;
		this.user.getTracket(product.id)
			.then(trackerJson=>{
				let refUrl = window.location.protocol + "//" + window.location.host + this.#productUrl.replace("{{productId}}",product.id).replace("{{tid}}",trackerJson.trackerId);
				this.setupShareDetails(refUrl, product);
			});
	}

	setupShareDetails(shareUrl, product){
		let sharetxt = "Hey found this really interesting product you may be iterested in ";
		let encodedUrl = encodeURIComponent(shareUrl);
		document.getElementById("st-fb-link").href = "https://www.facebook.com/sharer/sharer.php?u="	+ encodedUrl;
		document.getElementById("st-whatsapp-link").href = "whatsapp://send?text=" + sharetxt + product.title + " " + encodedUrl;
		document.getElementById("st-twitter-link").href = "http://twitter.com/share?text=" + sharetxt + "&url="	+ encodedUrl;
		document.getElementById("st-pinterest-link").href = "https://pinterest.com/pin/create/link/?url=" + encodedUrl + "&media=" + product.primaryImageSrc.imageSrc + "&description=" + product.title;
		let description = product.description?product.description.substr(0,250):"";
		document.getElementById("st-linkedin-link").href = "https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=" + encodedUrl + "&title=" + product.title + "&summary=" + description;
		document.getElementById("st-cosell-url-input").value = shareUrl;
	}

	setupShareUrl(shareUrl){
		if(shareUrl){}
		let sharetxt = "Hey found this really interesting article you may be iterested in.";
		let encodedUrl = encodeURIComponent(shareUrl);
		document.getElementById("st-fb-link").href = "https://www.facebook.com/sharer/sharer.php?u="	+ encodedUrl;
		document.getElementById("st-whatsapp-link").href = "whatsapp://send?text=" + sharetxt + " " + encodedUrl;
		document.getElementById("st-twitter-link").href = "http://twitter.com/share?text=" + sharetxt + "&url="	+ encodedUrl;
		document.getElementById("st-pinterest-link").href = "https://pinterest.com/pin/create/link/?url=" + encodedUrl ;
		document.getElementById("st-linkedin-link").href = "https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=" + encodedUrl;
		document.getElementById("st-cosell-url-input").value = shareUrl;
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

	static showInnerError(error, closeTicks = 5000){
		if(error.error)
		{
			ShoptypeUI.showInnerError(error.error, closeTicks);
		}else{
			ShoptypeUI.showError(error.message, closeTicks);
		}
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