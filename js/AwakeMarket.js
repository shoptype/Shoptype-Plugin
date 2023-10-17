let productPage = null;
let brandPage = null;
let offset = 0;
let scrollContainer = null;
let am_platform_brands = null;
const am_currentUrl = new URL(window.location);
const am_Currency = {"USD":"$", "INR":"₹","GBP":"£","CAD":"C$"};
const marketLoaded = new Event('marketLoaded');
let am_loadedContainers = [];
let am_loading=false;

function initMarket(){
	let awakeTags = document.getElementsByTagName("awakeMarket");
	if(awakeTags && awakeTags.length>0){
		productPage = awakeTags[0].getAttribute("productpage");
		brandPage = awakeTags[0].getAttribute("brandPage");
	}
}

function awakenTheMarket(){
	if(typeof ShoptypeUI !== 'undefined'){
		initMarket();
		populateProducts();
		populateBrands();
		am_market_loaded = true;
	}else{
		document.addEventListener("ShoptypeUILoaded", ()=>{
			initMarket();
			populateProducts();
			populateBrands();
			am_market_loaded = true;
		});
	}
}

function populateProducts(){
	let productLists = document.getElementsByClassName('products-container');

	for (var i = 0; i < productLists.length; i++) {
		var autoLoad = productLists[i].getAttribute("autoload");
		if(autoLoad=="false"){continue;}
		offset = 0;
		am_loading=false;
		addProducts(productLists[i]);
		if(productLists[i].getAttribute('loadmore')=='true'){
			scrollContainer = productLists[i];
			window.addEventListener('scroll',()=>{
				const {scrollHeight,scrollTop,clientHeight} = document.documentElement;
				if(scrollTop + clientHeight > (scrollHeight - 5)){
					addProducts(scrollContainer);
				}
			});
		}
	}
}

function populateBrands(){
	let brandsLists = document.getElementsByClassName('brands-container');
	for (var i = 0; i < brandsLists.length; i++) {
		let count = brandsLists[i].getAttribute("count")??20;
		let brandTemplate = brandsLists[i].querySelector(".brand-container");
		getBrands(brandsLists[i], brandTemplate, count)
	}
	populateBrandSelector();
}

function populateBrandSelector(){
	let brandSelect = document.querySelector('.am-brands-selector');
	if(brandSelect){
		fetchBrands(function(brandsJson){
			for (var i = 0; i < brandsJson.length; i++) {
				var opt = document.createElement('option');
				opt.value = brandsJson[i].id;
				opt.innerHTML = brandsJson[i].name;
	    		brandSelect.appendChild(opt);
			}
		});
		brandSelect.setAttribute("onchange","brandChanged(this)");
	}
}

function brandChanged(selectElement){
	let querySelector = selectElement.getAttribute("productsContainer");
	let productsContainer = document.querySelector(querySelector);
	let value = selectElement.options[selectElement.selectedIndex].value;
	if(value ==""){
		productsContainer.removeAttribute("vendorId");
	}else{
		productsContainer.setAttribute("vendorId",value);
	}
	clearProducts(productsContainer);
	offset=0;
	addProducts(productsContainer);
}

function addTagSelect(){
	let tagSelects = document.getElementsByClassName('am-tag-selector');
	for (var i = 0; i < tagSelects.length; i++) {
		tagSelects[i].setAttribute("onchange","tagChanged()");
	}
}

function tagChanged(){
	let tagSelects = document.getElementsByClassName('am-tag-selector');
	let tags = "";
	for (var i = 0; i < tagSelects.length; i++) {
		let value = tagSelects[i].options[tagSelects[i].selectedIndex].value;
		let operator = tags==""?"":tagSelects[i].getAttribute("operator")??"";
		if(value && value!=""){
			tags+=operator+value;
		}
	}

	let querySelector = tagSelects[0].getAttribute("productsContainer");
	let productsContainer = document.querySelector(querySelector);
	productsContainer.setAttribute("tags",tags);
	clearProducts(productsContainer);
	offset=0;
	addProducts(productsContainer);
}

function addProducts(productsContainer){
	if(am_loadedContainers.includes(productsContainer)){return;}
	if(am_loading){return;}
	am_loading = true;
	let skip = productsContainer.getAttribute('skip')==null?false:true;
	let removeTemplate = productsContainer.getAttribute('removeTemplate')==null?false:true && !(productsContainer.getAttribute('loadmore')=='true');
	if(skip){return;}
	let shopUrl = productsContainer.getAttribute('myshop');
	let productTemplate = productsContainer.querySelector(".product-container");
	productTemplate.style.display = "none";
	let searchString = productsContainer.getAttribute('searchstring');
	let collection = productsContainer.getAttribute('collection');
	let minRange = productsContainer.getAttribute('minRange');
	let maxRange = productsContainer.getAttribute('maxRange');
	let category = productsContainer.getAttribute('category');
	let tags = productsContainer.getAttribute('tags');
	let vendorId = productsContainer.getAttribute('vendorId');
	let count = productsContainer.getAttribute('count')?parseInt(productsContainer.getAttribute('count')):20;
	let imageSize = productsContainer.getAttribute('imageSize');
	let offsetAtt = productsContainer.getAttribute('offset');
	let sortBy = productsContainer.getAttribute('sortBy');
	let orderBy = productsContainer.getAttribute('orderBy');
	let collectionId = productsContainer.getAttribute('collection_id');
	let inStock = productsContainer.getAttribute('instock');

	offset = offsetAtt??offset;
	
	let options = {
		count: count,
		offset: offsetAtt??undefined,
		imgSize: imageSize??undefined,
		text: searchString??undefined,
		minRange: minRange??undefined,
		maxRange: maxRange??undefined,
		category: category??undefined,
		tags: tags??undefined,
		orderBy: orderBy??undefined,
		sortBy: sortBy??undefined,
		vendorId:vendorId??undefined,
		inStock:inStock??undefined
	};

	if(shopUrl){
		var url = shopUrl+"?"+STUtils.toQueryString(options);
		fetchMyStoreProducts(url, productsContainer, productTemplate);
	}else if(collectionId){
		imageSize = imageSize??"300x0";
		fetchCollectionProducts(collectionId, productsContainer, productTemplate, inStock,imageSize);
	}else{
		fetchProducts(options, productsContainer, productTemplate);
	}

	if(removeTemplate){
		productTemplate.remove();
	}
	offset+=count;
}

function loadProduct(productId, successCB, failureCB){
	if(typeof fingerprintExcludeOptions!== 'undefined'){
		fetchProduct(productId, successCB, failureCB);
	}else{
		am_loadScript("https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js", function(){ensureFingerprint2(productId, successCB, failureCB);});
	}
}

function ensureFingerprint2(productId, successCB, failureCB){
	if(typeof Fingerprint2!== 'undefined'){
		fetchProduct(productId, successCB, failureCB);
	}else{
		setTimeout(function(){ ensureFingerprint2(productId, successCB, failureCB); }, 500);
	}
}

function fetchProduct(productId, successCB, failureCB){
	let tid = am_currentUrl.searchParams.get("tid");
	st_platform.product(productId, data=>{
		updateProduct(productJson.product);
		if(isFunction(successCB)){successCB(productJson);}
	},tid);
}

function loadBrand(brandId, successCB, failureCB){
	st_platform.vendors({vendorId:brandId})
		.then(brandJson=>{
			if(brandJson[0]){
				updateBrand(brandJson[0]);
				if(isFunction(successCB)){successCB(brandJson[0]);}				
			}else{
				if(isFunction(failureCB)){failureCB("Brand not found");}
			}
		})
		.catch(function() {
			console.log("Brand not found");
			if(isFunction(failureCB)){failureCB("Brand not found");}
		});
}

function updateProduct(product){
	let productNode = document.querySelector(".am-product-display-container")
	productNode.querySelector(".am-product-main-image").src = product.primaryImageSrc.imageSrc;
	let imagesTemplate = productNode.querySelector(".am-product-other-image");
	if(imagesTemplate && product.secondaryImageSrc){
		for (var i = 0; i < product.secondaryImageSrc.length; i++) {
			let newImg = imagesTemplate.cloneNode(true);
			newImg.src = product.secondaryImageSrc[i].imageSrc;
			imagesTemplate.parentNode.appendChild(newImg);
		}
	}
	productNode.querySelector(".am-product-title").innerHTML = product.title;
	productNode.querySelector(".am-product-vendor").innerHTML = product.vendorName;
	productNode.querySelector(".am-product-price").innerHTML = getProductPrice(product);
	if(product["soldOut"]){
		var soldLable = productNode.querySelector(".sold-out");
		if(soldLable){soldLable.style.display="block"}
	}else if(product["sale"]){
		var saleLable = productNode.querySelector(".on-sale");
		if(saleLable){saleLable.style.display="block"}
	}
	productNode.querySelector(".am-product-description").innerHTML = product.description?product.description.replace(/(?:\r\n|\r|\n)/g, '<br>'):"not available";
	let tags = productNode.querySelector(".am-product-tags");
	if(tags){tags.innerHTML = product.tags.join(',');}
	let addToCartBtn = productNode.querySelector(".am-product-add-cart-btn");
	if(addToCartBtn){
		setCartBtnAttributes(addToCartBtn, product);
	}
	let cosellBtn = productNode.querySelector(".am-cosell-btn");
	if(cosellBtn){
		setCosellAttributes(cosellBtn, product);
	}
	let buyBtn = productNode.querySelector(".am-product-buy-btn");
	if(buyBtn){
		setCartBtnAttributes(buyBtn, product);
	}
	productNode.style.display="";
}

function updateBrand(brand){
	let brandNode = document.querySelector(".am-brand-display-container")
	brandNode.querySelector(".am-brand-name").innerHTML = brand.name;
	brandNode.querySelector(".am-brand-logo").src = brand.logo;
	let brandCat = brandNode.querySelector(".am-brand-categories");
	if(brandCat){brandCat.innerHTML = brand.productCategories?brand.productCategories.join(","):"";}
	let brandUrl = brandNode.querySelector(".am-brand-pageUrl");
	if(brandUrl){brandUrl.href = brand.url;}
	let brandCountryState=brandNode.querySelector(".am-brand-countryState")
	if(brandCountryState){brandCountryState.innerHTML = brand.store.countryState;}
	
	let brandProducts = brandNode.querySelector(".products-container");
	if(brandProducts){
		brandProducts.setAttribute("vendorid",brand.id);
		brandProducts.removeAttribute("skip");
		addProducts(brandProducts)
	}

}

function fetchProducts(options, productsContainer, productTemplate, callback){
	st_platform.products(options).then(productsJson=>{
		if(!productsJson.products){
			var amProductsLoadFailed = new CustomEvent("amProductsLoadFailed", {'container': productsContainer});
			document.dispatchEvent(amProductsLoadFailed);
			return;
		}
		for (var i = 0; i < productsJson.products.length; i++) {
			let product = productsJson.products[i];
			let newProduct = createProduct(productTemplate, product);
			newProduct.style.display = "";
			productsContainer.appendChild(newProduct);
		}
		am_loading = false;
		if (callback){callback(productsJson);}
		var amProductsLoaded = new CustomEvent("amProductsLoaded", {'container': productsContainer});
		document.dispatchEvent(amProductsLoaded);
	})
}

function fetchCollectionProducts(collectionId, productsContainer, productTemplate,inStock,imageSize){
	st_platform.collection(collectionId).then(collectionJson=>{
		for (var i = 0; i < collectionJson.product_details.length; i++) {
			let product = collectionJson.product_details[i];
			product.vendorName="";
			product.primaryImageSrc.imageSrc=`https://images.shoptype.com/unsafe/${imageSize}/`+encodeURIComponent(product.primaryImageSrc.imageSrc);
			let newProduct = createProduct(productTemplate, product);
			if(inStock && product["soldOut"]){
				continue;
			}else{
				newProduct.style.display = "";
				productsContainer.appendChild(newProduct);
			}
		}
		var amProductsLoaded = new CustomEvent("amProductsLoaded", {'container': productsContainer});
		document.dispatchEvent(amProductsLoaded);
	})
}

function fetchMyStoreProducts(url, productsContainer, productTemplate){
	fetch(url)
		.then(response=>{
			if (response.status >= 200 && response.status < 300) {
				return Promise.resolve(response.json());
			}else{
				am_loading = false;
				return Promise.reject("nothing here");
			}
		})
		.then(productsJson=>{
			for (var i = 0; i < productsJson.products.length; i++) {
				let product = productsJson.products[i];
				let newProduct = createProduct(productTemplate, product);
				newProduct.style.display = "";
				productsContainer.appendChild(newProduct);
			}
			var amProductsLoaded = new CustomEvent("amProductsLoaded", {'container': productsContainer});
			am_loading = false;
			document.dispatchEvent(amProductsLoaded);
		})		
		.catch(function() {
			am_loading = false;
			console.log("No more products to load");
			var amProductsLoadFailed = new CustomEvent("amProductsLoadFailed", {'container': productsContainer});
			document.dispatchEvent(amProductsLoadFailed);
			am_loadedContainers.push(productsContainer);
		});
}

function clearProducts(productsContainer){
	var children = productsContainer.getElementsByClassName("product-container");
	am_loadedContainers=[];
	offset = 0;
	for (var i = children.length - 1; i >= 1; i--) {
		children[i].remove();
	}
}

function createProduct(productTemplate, product){
	let newProduct = productTemplate.cloneNode(true);
	newProduct.id = product.id;
	newProduct.querySelector(".am-product-image").src = product.primaryImageSrc.imageSrc;
	newProduct.querySelector(".am-product-title").innerHTML = product.title;
	newProduct.querySelector(".am-product-vendor").innerHTML = product.vendorName;
	let productPrice = newProduct.querySelector(".am-product-price");
	if(productPrice){
		productPrice.innerHTML = getProductPrice(product);
		if(product["soldOut"]){
			var soldLable = newProduct.querySelector(".sold-out");
			if(soldLable){soldLable.style.display="block"}
		}else if(product["sale"]){
			var saleLable = newProduct.querySelector(".on-sale");
			if(saleLable){saleLable.style.display="block"}
		}
	}
	let addToCartBtn = newProduct.querySelector(".am-product-add-cart-btn");
	if(addToCartBtn){
		setCartBtnAttributes(addToCartBtn, product);
	}
	let buyBtn = newProduct.querySelector(".am-product-buy-btn");
	if(buyBtn){
		setCartBtnAttributes(buyBtn, product);
	}	
	let productLink = newProduct.querySelector(".am-product-link");
	if(productLink){
		productLink.href = productPage.replace("{{productId}}", product.id).replace("{{tid}}", product.tid??"");
	}
	return newProduct;
}

function setCartBtnAttributes(btn, product){
		btn.setAttribute("variantid",product.variants[0].id);
		btn.setAttribute("productid",product.id);
		btn.setAttribute("vendorid",product.vendor.id);
		btn.setAttribute("quantitySelect",".am-add-cart-quantity");
}

function setCosellAttributes(btn, product){
		btn.setAttribute("onclick", `showCosell("${product.id}")`);
		btn.innerHTML = btn.innerHTML.replace('{{commission}}', getCommissionStr(product));
}
function onVariantSelectChanged(){

}

function getProductPrice(product){
	if(!product.hasOwnProperty('variants')){
		return "";
	}
	var sale = false;
	var soldOut = true;
	var productMax = product.variants[0].discountedPriceAsMoney;
	var productMin = product.variants[0].discountedPriceAsMoney;
	for (var i = 0; i < product.variants.length; i++) {
		var variant = product.variants[i];
		if(variant.priceAsMoney && variant.discountedPriceAsMoney.amount<variant.priceAsMoney.amount){
			sale=true;
		}
		if(variant.quantity>0){
			soldOut=false;
		}
		if (variant.discountedPriceAsMoney.amount>productMax.amount) {
			productMax = variant.discountedPriceAsMoney;
		}else if(variant.discountedPriceAsMoney.amount<productMin.amount){
			productMin = variant.discountedPriceAsMoney
		}
	}
	var priceStr = getPriceStr(productMin);
	product["sale"] = sale;
	product["soldOut"] = soldOut;
	if(productMax.amount>productMin.amount){
		priceStr += " - " + getPriceStr(productMax);
	}
	return priceStr;
}

function getPriceStr(money,decimal=2){
	let curr = am_Currency[money.currency]?am_Currency[money.currency]:money.currency;
	return curr + Number(money.amount).toFixed(decimal);
}

function getCommissionStr(product,decimal=2){
	let curr = am_Currency[product.currency]?am_Currency[product.currency]:product.currency;
	let commission = product.variants[0].discountedPriceAsMoney.amount * product.productCommission.percentage / 100;
	return curr + " " + Number(commission).toFixed(decimal);
}

function getBrands(brandsContainer, brandTemplate, count=50){
	let removeTemplate = brandsContainer.getAttribute('removeTemplate')==null?false:true && !(brandsContainer.getAttribute('loadmore')=='true');
	
	fetchBrands(function(brandsJson){
		for (var i = 0; i < brandsJson.length||i<count; i++) {
			let brand = brandsJson[i];
			let newBrand = createBrand(brandTemplate, brand);
			newBrand.style.display = "";
			brandsContainer.appendChild(newBrand);
		}
		var amBrandsLoaded = new CustomEvent("amBrandsLoaded", {'container': brandsContainer});
		if(removeTemplate){
			brandTemplate.remove();
		}
		document.dispatchEvent(amBrandsLoaded);
	});
}

function fetchBrands(callback){
	if(am_platform_brands){
		callback(am_platform_brands);
	}else{
		st_platform.vendors()
			.then(brandsJson=>{
				am_platform_brands = brandsJson;
				callback(brandsJson);
			});
	}
}

function createBrand(brandTemplate, brand){
	let newBrand = brandTemplate.cloneNode(true);
	newBrand.id = brand.id;
	newBrand.querySelector(".am-brand-image").src = brand.logo;
	newBrand.querySelector(".am-brand-name").innerHTML = brand.name;
	let brandId=newBrand.querySelector(".am-brand-id");
	if(brandId){brandId.innerHTML = brand.id;}
	let brandLink = newBrand.querySelector(".am-brand-link");
	if(brandLink){brandLink.href = brandPage.replace("{{brandId}}", brand.id);}
	return newBrand;
}

function am_loadScript(url, callback) {
	var head = document.head;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url;
	script.onreadystatechange = callback;
	script.onload = callback;
	head.appendChild(script);
}

function isFunction(functionName) {
    if(eval("typeof(" + functionName + ") == typeof(Function)")) {
        return true;
    }else{
    	return false;
    }
}

document.dispatchEvent(marketLoaded);