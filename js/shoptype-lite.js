const currentUrl = new URL(window.location);
const st_backend = "https://backend.shoptype.com";
const stLoginEvent = new Event('userLogin');
const stShoptypeInit = new Event('shoptypeInit');
const stCurrency = {"USD":"$", "INR":"₹","GBP":"£"};
const cssUrl = "https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS/shoptype.css";
const stLoadedProducts = {};
const cartUrl = "https://app.shoptype.com/cart";
const st_defaultCurrency = "USD";
const st_loadedJs = [];
let st_refUrl = null;
		currentPageProductId = null,
		stToken = currentUrl.searchParams.get("token"),
		carts = {},
		stProducts = {},
		callStack={},
		st_vendorId = null,
		st_platformId = null,
		st_hostDomain = null,
		st_refCode = null,
		headerOptions = {
			method:'',
			'headers': {
				'Content-Type': 'application/json',
				'X-Shoptype-Api-Key': ""
			},	
			body: null
		};

if(stToken && stToken!=""){
	setCookie("stToken", stToken, 20);
	removeAccessTokenFromUrl();
}else{
	stToken = getCookie("stToken");
}

document.addEventListener("userLogin", function (e) {
	let userMenu = document.getElementById("menu-signout-btn");
	if(userMenu){
	  userMenu.innerHTML = "Sign out";
	  userMenu.setAttribute("onclick","shoptypeLogout()")
	}
});

function st_loadScript(url, callback) {
	var head = document.head;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url;
	script.onreadystatechange = callback;
	script.onload = callback;
	head.appendChild(script);
	st_loadedJs.push(url);
}

function initShoptype(){
	let awakeTags = document.getElementsByTagName("awake");
	let awakeSetup = document.getElementsByTagName("awakesetup");

	if(awakeSetup.length>0){
		st_vendorId= awakeSetup[0].getAttribute("vendorid");
		st_platformId= awakeSetup[0].getAttribute("platformid");
		let apiKey = awakeSetup[0].getAttribute("apikey");
		let overrideCss = awakeSetup[0].getAttribute("css");
		st_refCode = awakeSetup[0].getAttribute("refcode");
		st_hostDomain = awakeSetup[0].getAttribute("hostdomain");

		st_refUrl = awakeSetup[0].getAttribute("refurl")??null;
		if(overrideCss){
			loadCSS(overrideCss);
		}else{
			loadCSS(cssUrl);
		}
		let body = document.getElementsByTagName('body')[0];
		let cartWraper = document.createElement("div");
		cartWraper.innerHTML = cosellMask+
								st_cosell_screen.replace("{{site}}", currentUrl.host)+
								st_loader;		
		body.insertBefore(cartWraper, body.firstChild);
		headerOptions.headers["X-Shoptype-Api-Key"] = apiKey;
		document.dispatchEvent(stShoptypeInit);
		for (var i = 0; i < awakeTags.length; i++) {
			let tagType = awakeTags[i].getAttribute("type");
			if(tagType){
				switch(tagType) {
					case 'cosellBtn':
						setupCosellBtn(awakeTags[i]);
						break;
					default:
						console.info(`Unknown Awake tag type`)
						awakeTags[i].remove;
						break;
				}
			}else{
				console.info(`type not defined`)
				awakeTags[i].remove;
			}
		}
		if(stToken && stToken!=""){
			getUserDetails();
		}
	}
}

function sendProductViewEvent(){
	let tid = currentUrl.searchParams.get("tid");
	if(!tid){sendUserEvent(); return;}
	sendUserEvent(deviceId=>sendViewEvent(tid, deviceId));
}

function registerCheckout(){
	let checkoutId = getCookie("st_checkoutid");
	let productId = createUUID();
	if(checkoutId!=null){
		setCookie("st_checkoutid", null,0);
		sendUserEvent(deviceId=>{sendCheckoutEvent(checkoutId, productId, deviceId);});
	}
	
}

function sendUserEvent(callback){
	let deviceId = getCookie("st_deviceid");
	if(deviceId!=null){
		if(typeof callback === "function"){
			callback(deviceId);
		}
	}else{
		if(typeof fingerprintExcludeOptions=== 'undefined'){
			st_loadScript("https://shoptype-scripts.s3.amazonaws.com/triggerUserEvent.js", function(){sendUserEvent(callback);});
		}else{
			getDeviceId().then(
				deviceId =>{
					setCookie("st_deviceid",deviceId, 10000);
					if(typeof callback === "function"){
						callback(deviceId);
					}
				}
			);
		}
	}
}

function sendViewEvent(tid, deviceId){
	let payload = {
		"device_id": deviceId,
		"url": window.location.href,
		"tracker_id": tid,
		"referrer": window.location.host
	}
	let headerOptions = {
		method:'post',
		'headers': {'content-type': 'application/json'},
		body: JSON.stringify(payload)
	};
	fetch(st_backend + "/track/user-event", headerOptions)
		.then(response=>response.json())
		.then(eventJson=>{
			console.info(eventJson);
		});
}

function sendCheckoutEvent(checkoutId, productId, deviceId){
	let payload = {
		action: "product_checkout",
		checkoutId: checkoutId,
		deviceId: deviceId,
		productId: productId,
		referrer: window.location.host,
		url: window.location.host,
		vendorId: st_vendorId
	}
	let headerOptions = {
		method:'post',
		'headers': {'content-type': 'application/json'},
		body: JSON.stringify(payload)
	};
	fetch(st_backend + "/user-event", headerOptions)
		.then(response=>response.json())
		.then(eventJson=>{
			console.info(eventJson);
		});
}

let st_loaderMask = `<div id="st-loader-mask" style="display:none" class="st-loader-mask"><div class="st-loader"></div></div>`;
let cosellMask = `<div id="st-cosell-mask" style="display:none" class="st-cosell-link-mask" onclick="hideElement(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-cosell-links-header">Here’s your unique Cosell link!</div><div class="st-cosell-body"><div class="st-cosell-links-image"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/Share-Link.png" loading="lazy" alt=""></div><div class="st-cosell-social-links"><div class="st-cosell-social-title">Share it on Social Media</div><div class="st-cosell-socialshare"> <a id="st-fb-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/facebook.png" loading="lazy" alt=""></a> <a id="st-twitter-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/twitter.png" loading="lazy" alt=""></a> <a id="st-whatsapp-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/whatsapp.png" loading="lazy" alt=""></a> <a id="st-pinterest-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/instagram.png" loading="lazy" alt=""></a> <a id="st-linkedin-link" href="#" class="st-cosell-socialshare-link w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/linkedin.png" loading="lazy" alt=""></a></div></div><div class="st-cosell-links-txt">or</div><div class="st-cosell-sharelink"><div class="st-cosell-sharelink-div"><div class="st-cosell-sharelink-url"><div class="st-cosell-link-copy-btn" onclick="stCopyCosellUrl('st-cosell-url-input')">🔗 Copy to Clipboard</div> <input type="text" id="st-cosell-url-input" class="st-cosell-sharelink-url-txt" readonly></input></div></div><div id="st-cosell-sharewidget" class="st-cosell-sharelink-div"><div class="st-cosell-share-widget-txt">Share on Blogs</div><div id="st-widget-btn" class="st-cosell-share-widget-btn">Get an Embed</div></div></div></div><div class="st-cosell-links-footer"><div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a></div> <a href="#" target="_blank" class="w-inline-block"><div class="st-cosell-page-txt">Learn more about Coselling</div> </a></div></div></div>`;
let loginMask = `<div id="st-login-mask" style="display:none" class="st-login-mask"><div class="st-login-content"><div class="st-login-close-button" onclick="closeLogin()">X</div><div class="st-login-window"> <iframe id="st-loginIframe" src="https://login.shoptype.com/signin" width="400" height="600"></iframe></div></div></div>`; 
let cosellBtn = `<div class="st-cosell"><div id="st-product-cosell-button" class="st-product-cosell-button" onclick="showCosell()">COSELL</div></div><div class="st-cosell-note"><div id="st-cosell-earn1" class="st-cosell-text">NEW! - Earn up to $$$ every co-sale.<br>Rewarded with real money through attributions.</div></div>`;
let st_cosellText = "NEW! - Earn up to {commission} every co-sale.<br/>Rewarded with real money through attributions.";
let st_cosell_screen = `<div class="st-cosell-link-mask" id="st-cosell-intro-mask" style="display:none" onclick="hideElement(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-cosell-links-header" id="st-cosell-links-header">{{site}} is proud to introduce &quot;Cosell&quot; , A unique way to boost the influencer in you.<br><span class="st-cosell-links-header-span">Share and make Money Instantly.</span></div><div class="st-cosell-body"><div class="st-cosell-steps-div"><div class="st-cosell-exp"><div class="st-cosell-exp-header-div"><h3 class="st-cosell-exp-header">How to be a Coseller</h3></div><div class="st-cosell-exp-steps"><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">1</div><div class="st-cosell-step-img-div"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/Phone-Register.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Signup</div></div><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">2</div><div class="st-cosell-step-img-div"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/Phone-Product.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Click Cosell on cool products</div></div><div class="st-cosell-step"><div class="st-cosell-step-no st-cosell-step-overlay">3</div><div class="st-cosell-step-img-div"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/Phone-Register.png" loading="lazy" alt="" class="st-cosell-step-img"></div><div class="st-cosell-step-title">Share with your Network</div></div></div></div><div class="st-cosell-signup"><div class="st-cosell-sugnup-btn" onclick="showLogin()">Become a Coseller</div></div></div><div class="st-cosell-adv"><div class="st-cosell-step-pts"><div class="st-cosell-step-no">1</div><div class="st-cosell-step-txt">Coselling is Free, No membership fee.</div></div><div class="st-cosell-step-pts"><div class="st-cosell-step-no">2</div><div class="st-cosell-step-txt">Cosell across all participating Market Networks, across the Internet.</div></div><div class="st-cosell-step-pts"><div class="st-cosell-step-no">3</div><div class="st-cosell-step-txt">Cosell links are unique. Share, get paid when inviting others to grow your referral Network.</div></div></div></div><div class="st-cosell-links-footer"><div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a></div> <a href="#" target="_blank" class="st-link-block"><div class="st-cosell-page-txt">Learn more about Coselling</div> </a></div></div></div>`;
let st_coseller_profile = `<div class="st-cosell-link-mask" id="coseller-profile-mask" style="display:none" onclick="hideElement(this)"><div class="st-cosell-links" onclick="event.stopPropagation()"><div class="st-redirect"><div class="st-redirect-txt">To view earnings across all market networks, please visit:</div><div class="st-redirect-btn-div"> <a href="https://app.shoptype.com/" target="_blank" class="st-redirect-btn w-inline-block"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/Shoptype-Logo-White.png" loading="lazy" alt="" class="st-redirect-btn-image"><div class="st-redirect-btn-title">Visit Shoptype</div> </a><div class="st-redirect-btn-txt">(Redirects to Shoptype. Opens in new tab)</div></div></div><div class="st-coseller-db"><div class="st-coseller-db-title-div"><h1 id="st-coseller-db-heading" class="st-coseller-db-heading">Your Dashboard {site}</h1></div><div class="st-coseller-db-data"><div class="st-duration-selectors" style="display:none;"><div id="st-duration-select-all" class="st-duration-select st-btn-select">All Time</div><div id="st-duration-select-month" class="st-duration-select">This Month</div><div id="st-duration-select-week" class="st-duration-select">This Week</div><div id="st-duration-select-day" class="st-duration-select">Today</div></div><div class="st-coseller-kpi-div"><div class="div-block-137"><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Total Earnings</div><div id="st-coseller-kpi-val-tot-earning" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Clicks</div><div id="st-coseller-kpi-val-tot-click" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Publishes</div><div id="st-coseller-kpi-val-tot-publish" class="st-coseller-kpi-val">000</div></div><div class="st-coseller-kpi"><div class="st-coseller-kpi-txt">Currency</div><div id="st-coseller-kpi-val-currency" class="st-coseller-kpi-val">USD</div></div></div><div class="st-coseller-kpi-products"><div><h3 class="st-coseller-products-title">Products Published</h3></div><div class="st-coseller-products-list" id="st-coseller-products-list"><div class="st-coseller-product" id="st-coseller-product-000" style="display: none;"><div class="st-coseller-product-div"><div class="st-coseller-product-details"><div class="st-coseller-product-img-div"><img src="https://d3e54v103j8qbb.cloudfront.net/plugins/Basic/assets/placeholder.60f9b1840c.svg" loading="lazy" alt="" class="st-coseller-product-img"></div><div class="st-coseller-product-desc"><div class="st-coseller-product-name">Product Name</div><div class="st-coseller-product-vendor">Vendor Name</div></div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Total Earnings</div><div class="st-coseller-kpi-val st-product-tot-earnings">$ 000</div></div></div><div class="div-block-146"><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Product Price</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Clicks</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-kpi-txt">Publishes</div><div class="st-coseller-kpi-val">00</div></div><div class="st-coseller-product-kpi"><div class="st-coseller-nudge-btn">Cosell</div></div></div></div></div></div></div></div></div></div></div>`;
let st_loader = '<div class="st-loader-mask" id="st-loader-mask" style="display:none;"><img src="https://in.awake.market/wp-content/themes/marketo/assets/images/loader.gif" alt="" style="max-width: 20%;"></div>';

function setupCosellBtn(awakeTag){
	const wraperDiv = document.createElement("div");
	wraperDiv.innerHTML = cosellBtn;
	let wraperWidth = awakeTag.getAttribute("width");
	if(wraperWidth && wraperWidth!=""){
		wraperDiv.style.width = wraperWidth;
	}
	awakeTag.parentNode.insertBefore(wraperDiv, awakeTag);
	let cosellBtnElem = wraperDiv.querySelector(".st-product-cosell-button");
	if(awakeTag.getAttribute("details")=="hidden"){
		let details = wraperDiv.querySelector(".st-cosell-note");
		details.style.display="none";
		details.style.position="absolute";
		details.style.width=wraperWidth;
		wraperDiv.querySelector(".st-cosell").style.borderRadius="10px";
	}

	getProductUrl(awakeTag,function(productJson){
			if(productJson==null){
				let stNoProduct =new CustomEvent('shoptypeNoProduct', {'detail': {
					"button": "cosell",
					"product_id": awakeTag.getAttribute("stproductid"),
					"ext_product_id": awakeTag.getAttribute("extproductid")
				}});
				wraperDiv.remove();
				document.dispatchEvent(stNoProduct);
				return;
			}
			cosellBtnElem.setAttribute("onclick","showCosell('"+productJson.id+"')");
			let btnTxt = awakeTag.getAttribute("btnTxt");
			let pricePrefix = stCurrency[productJson.currency]?" "+stCurrency[productJson.currency]:" " + productJson.currency;
			let commission = (productJson.variants[0].discountedPrice * productJson.productCommission.percentage)/100;
			if(btnTxt){
				btnTxt = btnTxt.replace("{commission}", pricePrefix + commission.toFixed(2));
				cosellBtnElem.innerHTML = btnTxt;
			}
			if(awakeTag.getAttribute("details")!="hidden"){
				st_cosellText = awakeTag.getAttribute("cosellText")??st_cosellText;
				wraperDiv.querySelector("#st-cosell-earn1").innerHTML = st_cosellText.replace("{commission}", pricePrefix + commission.toFixed(2));
			}
			awakeTag.remove();
			sendUserEvent();
		});
}

function autoOpen(){
	let openTabJson = sessionStorage['autoOpen'];

	if(openTabJson && openTabJson!=null && openTabJson!=""){
		let openOptions = JSON.parse(openTabJson);
		switch(openOptions.tab) {
			case 'Cosell':
			showCosell(openOptions.pid);
			break
			case 'CosellerDashboard':
			stShowCosellerDashboard();
			break
			default:
			break;
		}
	}
	sessionStorage.removeItem('autoOpen');
}

function setProductId(productId){
	currentPageProductId = productId;
}

function fetchProduct(productKey, productUrl, callback){
	if(stLoadedProducts[productKey] && stLoadedProducts[productKey]!=null){
		callback(stLoadedProducts[productKey]);
	}else{
		if(callStack[productKey] && callStack[productKey]!=null){
			callStack[productKey].push(callback);
		}else{
			callStack[productKey] = [callback];
			fetch(productUrl)
				.then(response => response.json())
				.then(productJson => {
					if((!productJson.id) && !(productJson.products)){
						productJson = null;
					}
					if(productJson.products){
						productJson = productJson.products[0];
					}
					if(productJson!=null){
						stLoadedProducts[productKey] = productJson;
						if(productKey!=productJson.id){
							stLoadedProducts[productJson.id] = productJson;
						}
					}
					for (var i = 0; i < callStack[productKey].length; i++) {
						callStack[productKey][i](productJson);
					}
					callStack[productKey]=null;
				});
		}
	}
}

function getProductUrl(tag,callback){
	let stProductId = currentPageProductId??tag.getAttribute("stproductid");
	let extProductId = tag.getAttribute("extproductid");
	let productUrl = st_backend + "/products";
	if(stProductId && stProductId!=""){
		fetchProduct(stProductId, productUrl + "/" + stProductId, callback);
	}else{
		let extPId = "";
		if(extProductId && extProductId!=""){
			extPId = extProductId;
		}else if(typeof meta !== 'undefined'){//for Shopify product page
			extPId = meta.product.id;
		}
		fetchProduct(st_vendorId+"-"+extPId, productUrl + `?vendorId=${st_vendorId}&externalId=${extPId}`, callback);
	}
}

function getVariant(product, selectedOptions){
	for (var i = 0; i < product.variants.length; i++) {
		let title = "";
		for (var j = 0; j < selectedOptions.length; j++) {
			if (product.variants[i].variantNameValue[selectedOptions[j].name] != selectedOptions[j].value) {
				break;
			}
			else {
				if(j < selectedOptions.length-1){
					continue;
				}
	        	return product.variants[i];
			}
		}
	}
	return product.variants[0];
}

function setupShare(product){
	headerOptions.method ='get';
	headerOptions.headers.Authorization = sessionStorage["token"];
	document.getElementById("st-cosell-sharewidget").style.display="none";
	fetch( st_backend+"/track/publish-slug?productId=" + product.id, headerOptions)
		.then(response => response.json())
		.then(trackerJson=>{
			let sharetxt = "Hey found this really interesting product you may be iterested in ";
			let params = removeParam("token", window.location.search);
			let refUrl = "";
			if(st_refUrl!=null){
				refUrl = st_refUrl.replace("{pid}", product.id);
				refUrl += refUrl.indexOf("?")<0?"?":"&";
				refUrl += "tid=" + trackerJson.trackerId;
			}else{
				refUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + insertParam("tid", trackerJson.trackerId, params);
			}
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

function insertParam(key, value, urlParams) {
    key = encodeURIComponent(key);
    value = encodeURIComponent(value);
    var kvp = urlParams.substr(1).split('&');
    let i=0;

    for(; i<kvp.length; i++){ 
        if(kvp[i]==""){
            kvp.pop(); 
            continue;
        }
        if (kvp[i].startsWith(key + '=')) {
            let pair = kvp[i].split('=');
            pair[1] = value;
            kvp[i] = pair.join('=');
            break;
        }
    }

    if(i >= kvp.length){
        kvp[kvp.length] = [key,value].join('=');
    }

    let params = kvp.join('&');
    return "?" + params;
}

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function stCacheProduct(productJson){
	stLoadedProducts[productJson.id]=productJson;
}

function shoptypeLogout(){
	setCookie("stToken",null,0);
	sessionStorage.clear();
	location.reload();
	let cosellerMenu = document.getElementById("st-coseller-profile-menu");
	if(cosellerMenu){cosellerMenu.style.display="none";}
}

function showCosell(productId){
	if(!sessionStorage["userId"] || sessionStorage["userId"]==""){
		sessionStorage["autoOpen"] = '{"tab":"Cosell","pid":"'+productId+'"}';
		document.getElementById("st-cosell-intro-mask").style.display="flex";
	}else{
		stCallWithProduct(productId, setupShare);
		document.getElementById("st-cosell-mask").style.display="flex";
	}
}

function stCopyCosellUrl(elementID) {
	let copyText = document.getElementById(elementID);
	copyText.select();
	copyText.setSelectionRange(0, 99999); 
	document.execCommand("copy");
}
function loadCSS(cssUrl){
	let cssId = btoa(cssUrl);
	if (!document.getElementById(cssId))
	{
		let head	= document.getElementsByTagName('head')[0];
		let link	= document.createElement('link');
		link.id	 = cssId;
		link.rel	= 'stylesheet';
		link.type = 'text/css';
		link.href = cssUrl;
		link.media = 'all';
		head.appendChild(link);
	}
}
function showLogin(){
	let tid = currentUrl.searchParams.get("tid");

	if(stToken && stToken!=""){
		getUserDetails();
	}else{
		let loginUrl = 'https://login.shoptype.com/signup?redirectUrl=' + encodeURIComponent(window.location.href);
		if(tid){
			loginUrl += "&tid=" + tid;
		}else{
			loginUrl += "&rid=" + st_refCode;
		}
		window.location.replace(loginUrl);
	}
}
function getUserDetails(){
	headerOptions.method = "get";
	headerOptions.headers.Authorization = stToken;
	fetch(st_backend + "/me",headerOptions)
		.then(response => response.json())
		.then(userJson => {
			sessionStorage['userId'] = userJson._id;
			sessionStorage['token'] = stToken;
			sessionStorage["userEmail"]=userJson.email;
			sessionStorage["userName"]=userJson.name;
			sessionStorage["userPhone"]=userJson.phone;
			document.dispatchEvent(stLoginEvent);
			autoOpen();
		});
}

function addClass(element, className){
	arr = element.className.split(" ");
	if (arr.indexOf(className) == -1) {
		element.className += " " + className;
	}
}

function removeClass(element, className){
	element.className = element.className.replace(className,"");	
}


function stCallWithProduct(productId, callback){
	if(stLoadedProducts[productId]){
		callback(stLoadedProducts[productId])
	}else{
		fetch(st_backend +"/products/"+productId)
		.then(response => response.json())
		.then(productJson => {
			stLoadedProducts[productId] = productJson;
			callback(productJson);
		});
	}
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return null;
}
function removeAllChildNodes(parent) {
	while (parent.firstChild) {
		parent.removeChild(parent.firstChild);
	}
}
function hideElement(element){
	element.style.display="none";
}
function stToggleElement(selector){
	let element = document.querySelector(selector);
	if(element){
		if(element.style.display=="none"){
			element.style.display="";
		}else{
			element.style.display="none";
		}
	}
}
function stShowLoader(hideDelay=10000){
	document.getElementById("st-loader-mask").style.display = "";
	setTimeout(function(){stHideLoader();}, hideDelay);
}
function stHideLoader(){
	document.getElementById("st-loader-mask").style.display = "none";
}
function createUUID() {
   return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
   });
}
function removeAccessTokenFromUrl() {
  const { history, location } = window
  const { search } = location
  if (search && search.indexOf('token') !== -1 && history && history.replaceState) {
    const cleanSearch = search.replace(/(\&|\?)token([_A-Za-z0-9=\.\-%]+)/g, '').replace(/^&/, '?');
    const cleanURL = location.toString().replace(search, cleanSearch);
    history.replaceState({}, '', cleanURL);
  }
}
sendProductViewEvent();