class STPlatform {
  constructor(platformId, apiKey) {
    this.platformId = platformId;
    this.apiKey = apiKey;
    this.offset = 0;
    this.count= 10;
    this.productsCache={};
    this.cartId = null;
    this.endpoints = {
      products: {
        list: (options = {}) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/products?`+STUtils.toQueryString(options),
            method: 'get' 
          };
          return endpoint;
        },
        product: (productId, deviceId, tid) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/products/${productId}`,
            header: {'Content-Type': 'application/json'},
            body: {
              "device_id": deviceId,
              "tracker_id": tid??undefined
            },
            method: 'post' 
          };
          return endpoint;
        },
      },

      collections: {
        list: (options = {}) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/collections?`+STUtils.toQueryString(options),
            method: 'get' 
          };
          return endpoint;
        },
        collection: (collectionId) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/collections/${collectionId}`,
            method: 'get' 
          };
          return endpoint;
        },
      },


      vendors: {
        list: (options = {}) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/vendors?`+STUtils.toQueryString(options),
            method: 'get' 
          };
          return endpoint;
        },
        products: (vendorId, options = {}) => {
          var endpoint = {
            resource: `/platforms/${this.platformId}/vendors?vendorId=${vendorId}&`+STUtils.toQueryString(options),
            method: 'get' 
          };
          return endpoint;
        },
      },

      carts:{
        create: () => {
          var endpoint = {
            resource: `/cart`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body:{},
            method: 'post' 
          };
          return endpoint;
        },
        addProduct: (cartId, productId, variantId, variantName, quantity, metadata) => {
          var endpoint = {
            resource: `/cart/${cartId}/add`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: {
              'product_id':productId, 
              'product_variant_id':variantId, 
              'variant_name_value':variantName,
              'quantity': quantity,
              'metadata': metadata
            },
            method: 'post' 
          };
          return endpoint;          
        },
        cart: (cartId) => {
          var endpoint = {
            resource: `/cart/${cartId}`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            method: 'get' 
          };
          return endpoint;  
        },
        update: (cartId, productId, variantId, variantName, quantity) => {
          var endpoint = {
            resource: `/cart/${cartId}`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: {'product_id':productId, 'product_variant_id':variantId, 'variant_name_value':variantName, 'quantity': quantity},
            method: 'put' 
          };
          return endpoint;
        },
        delete: (cartId) => {
          var endpoint = {
            resource: `/cart/${cartId}`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            method: 'delete' 
          };
          return endpoint;
        },
      },

      checkouts:{
        create: (deviceId, cartId) => {
          var endpoint = {
            resource: `/checkout`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: {'deviceId':deviceId, 'cartId':cartId},
            method: 'post' 
          };
          return endpoint;
        },
        checkout: (checkoutId) => {
          var endpoint = {
            resource: `/checkout/${checkoutId}`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            method: 'get' 
          };
          return endpoint;          
        },
        updateAddress: (checkoutId, address = {}) => {
          var endpoint = {
            resource: `/checkout/${checkoutId}/address`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: address,
            method: 'put' 
          };
          return endpoint;
        },
        updateShipping: (checkoutId, vendorShippingKey) => {
          var endpoint = {
            resource: `/checkout/${checkoutId}/shipping-method`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: vendorShippingKey,
            method: 'put' 
          };
          return endpoint;          
        },
        payment: (checkoutId) => {
          var endpoint = {
            resource: `/checkout/${checkoutId}/payment`,
            header: {'X-Shoptype-Api-Key': this.apiKey, 'X-Shoptype-PlatformId': this.platformId},
            body: {},
            method: 'post' 
          };
          return endpoint;            
        },
      }
    };
    this.setupCart();
  STUtils.sendEvent('stPlatformCreated', "" );
  }

  setupCart(){
    let cartsString = STUtils.getCookie("carts");
    var carts = {};
    if(cartsString && cartsString!=""){
      carts = JSON.parse(cartsString);
      if(carts['shoptypeCart']){
        this.cartId = carts['shoptypeCart'];
        this.updateCartCount();
      }
    }

    if(!this.cartId){
      this.createCart()
      .then(cartData=>{
        this.cartId = cartData.id;
        carts['shoptypeCart'] = this.cartId;
        STUtils.setCookie("carts",JSON.stringify(carts),100);
      });
    }
  }

  updateCartCount(){
    this.getCart()
    .then((cartJson=>{
      if(cartJson.total_quantity){
        STUtils.sendEvent('cartQuantityChanged', {count: cartJson.total_quantity} )
      }
    }))
  }

  products(options = {}) {
    if(typeof options.offset === 'undefined' || options.offset === null){options.offset = this.offset;}
    if(!options.count){options.count = this.count;}
    var products = STUtils.request(this.endpoints.products.list(options));
    this.offset = this.offset + this.count;
    return products;
  }

  product(productId, callback, tid = null) {
    if(this.productsCache[productId]){
      return this.productsCache[productId];
    }
    if(typeof fingerprintExcludeOptions=== 'undefined'){
      STUtils.st_loadScript("https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js", ()=>{this.product(productId, callback, tid);});
    }else{
      getDeviceId().then(deviceId =>{       
        return STUtils.request(this.endpoints.products.product(productId, deviceId, tid), (product)=>{ 
          this.productsCache[product.id] = product;
          callback(product);
        });
      });
    }
  }

  collections(options = {}) {
    return STUtils.request(this.endpoints.collections.list(options));
  }

  collection(collectionId) {
    return STUtils.request(this.endpoints.collections.collection(collectionId));
  }


  vendors(options = {}) {
    return STUtils.request(this.endpoints.vendors.list(options));
  }

  vendorProducts(vendorId, options = {}) {
    return STUtils.request(this.endpoints.vendors.products(vendorId, options));
  }

  createCart(){
    return STUtils.request(this.endpoints.carts.create());
  }

  getCart(cartId=this.cartId){
    return STUtils.request(this.endpoints.carts.cart(cartId));
  }

  addToCart(productId, variantId, variantName, quantity, metadata, cartId = this.cartId){
    var callback = data=>{
      if(data.total_quantity){
        STUtils.sendEvent('cartQuantityChanged', {count: data.total_quantity} );
      }
    };
    return STUtils.request(this.endpoints.carts.addProduct(cartId, productId, variantId, variantName, quantity, metadata), callback);
  }

  updateCart(productId, variantId, variantName, quantity, cartId=this.cartId){
    var callback = data=>{
      var quant = data.total_quantity??data.cart_lines.length;
      STUtils.sendEvent('cartQuantityChanged', {count: quant} )
    };
    return STUtils.request(this.endpoints.carts.update(cartId, productId, variantId, variantName, quantity), callback);
  }

  deleteCart(cartId){
    let cartsString = getCookie("carts");
    if(cartsString && cartsString!=""){
      carts = JSON.parse(cartsString);
      delete carts.shoptypeCart;
      setCookie('carts', JSON.stringify(carts),100);
      setupCart();
    }

    return STUtils.request(this.endpoints.carts.delete(cartId));
  }

  createCheckout(callback, cartId=this.cartId){
    if(typeof fingerprintExcludeOptions=== 'undefined'){
      STUtils.st_loadScript("https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js", ()=>this.createCheckout(callback, cartId));
    }else{
      getDeviceId()
      .then(deviceId =>{
        return STUtils.request(this.endpoints.checkouts.create(deviceId, cartId), callback);
      });
    }
  }

  checkout(checkoutId){
    return STUtils.request(this.endpoints.checkouts.checkout(checkoutId));
  }

  updateAddress(checkoutId, address){
    return STUtils.request(this.endpoints.checkouts.updateAddress(checkoutId, address));
  }

  updateShipping(checkoutId, vendorShippingKey){
    return STUtils.request(this.endpoints.checkouts.updateShipping(checkoutId, vendorShippingKey));
  }

  checkoutPayment(checkoutId, vendorShippingKey){
    return STUtils.request(this.endpoints.checkouts.payment(checkoutId));
  }

  toQueryString(obj) {
    var str = [];
    for (var p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  }

  toGAProduct(product, item_index){
    var gaProduct = {
      item_id: product.product_id,
      item_name: product.name,
      index: item_index,
      item_brand: product.vendor_name,
      item_variant: product.variant_name_value,
      price: product.price.amount,
      quantity: product.quantity
    }
    console.info(gaProduct);
    return gaProduct;
  }
}


class STUser {
  #token = null;
  #platformId = null;
  #sessionCTid = null;
  #vendor_token = null;
  miniStore = null;

  constructor(token) {
    this.#token = token;
    this.miniStore = new STMiniStore(token);
    this.endpoints = {
      user: {
        me: () => {
          var endpoint = {
            resource: '/me',
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
        vendorToken: () => {
          var endpoint = {
            resource: '/authenticate',
            header: {'authorization':this.#token},
            body:{userType: "vendor"},
            method: 'post' 
          };
          return endpoint;
        },
        vendorMe: (_token) => {
          var endpoint = {
            resource: '/me',
            header: {'authorization':_token},
            method: 'get' 
          };
          return endpoint;
        },
        coseller: () =>{
          var endpoint = {
            resource: '/cosellers',
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
        productTracker:(productId)=>{
          var endpoint = {
            resource: `/track/publish-slug?productId=${productId}&platformId=${this.#platformId}`,
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
        networkTracker:(refUrl)=>{
          var endpoint = {
            resource: `/track/network?referrer=${refUrl}`,
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
        referral: () => {
          var endpoint = {
            resource: `/invites`,
            header: {'authorization':this.#token},
            body:{
              "type": "referrer",
              "platformId": this.#platformId
            },
            method: 'post' 
          };
          return endpoint;
        },
        referralTree: () => {
          var endpoint = {
            resource: `/referrals`,
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
      }
    }
  }

  setPlatform(platformId){
    this.#platformId = platformId;
    this.miniStore.setPlatform(platformId);
  }

  getTracket(productId){
    return STUtils.request(this.endpoints.user.productTracker(productId));
  }

  getNetworkTracker(referUrl){
    return STUtils.request(this.endpoints.user.networkTracker(referUrl));
  }

  details(){
    return STUtils.request(this.endpoints.user.me());
  }

  coseller(){
    return STUtils.request(this.endpoints.user.coseller());
  }

  referralId(){
    return STUtils.request(this.endpoints.user.referral());
  }

  getReferralTree(){
    return STUtils.request(this.endpoints.user.referralTree());
  }

  getVendorToken(){
    return STUtils.request(this.endpoints.user.vendorToken(), token=>{this.#vendor_token = token;});
  }

  getVendorDetails(token, callback){
    var my_token = token??this.#vendor_token;
    return STUtils.request(this.endpoints.user.vendorMe(my_token),callback);
  }

  static sendUserEvent(tid, platformId = null){
    if(typeof fingerprintExcludeOptions=== 'undefined'){
      STUtils.st_loadScript("https://cdn.jsdelivr.net/gh/shoptype/Shoptype-JS@main/stOccur.js", ()=>{this.sendUserEvent(tid,platformId)});
    }else{
      getDeviceId()
      .then(deviceId =>{
        var postBody={
          "device_id": deviceId,
          "url": window.location.href,
          "referrer": window.location.host
        };
        if(tid && tid!=""){
          postBody["tracker_id"]=tid;
        }else{
            postBody["platform_id"]=platformId;
        }
        var endpoint = {
          resource: '/track/user-event',
          body: postBody,
          method: 'post' 
        };
        return STUtils.request(endpoint);
      });
    }
  }
}

class STMiniStore {
  #token = null;
  #platformId = null;

  constructor(token) {
    this.#token = token;
    this.endpoints = {
      miniStore: {
        allStores: () => {
          var endpoint = {
            resource: '/cosellers/mini-stores',
            header: {'authorization':this.#token},
            method: 'get' 
          };
          return endpoint;
        },
        getStore: (storeId) =>{
          var endpoint = {
            resource: `/cosellers/mini-stores/${storeId}`,
            method: 'get' 
          };
          return endpoint;
        },
        create:(store)=>{
          var endpoint = {
            resource: '/cosellers/mini-stores',
            header: {'authorization':this.#token},
            body: store,
            method: 'POST' 
          };
          return endpoint;
        },
        update:(storeId, store)=>{
          var endpoint = {
            resource: `/cosellers/mini-stores/${storeId}`,
            header: {'authorization':this.#token},
            body: store,
            method: 'PUT' 
          };
          return endpoint;
        },
        delete:(storeId)=>{
          var endpoint = {
            resource: `/cosellers/mini-stores/${storeId}`,
            header: {'authorization':this.#token},
            method: 'DELETE' 
          };
          return endpoint;
        },
        getCollection:(collectionId)=>{
            var endpoint = {
            resource: '/cosellers/collections',
            method: 'GET' 
          };
          return endpoint;
        },
        createCollection:(collection)=>{
            var endpoint = {
            resource: '/cosellers/collections',
            header: {'authorization':this.#token},
            body: collection,
            method: 'POST' 
          };
          return endpoint;
        },
        updateCollection:(collectionId, collection)=>{
            var endpoint = {
            resource: `/cosellers/collections/${collectionId}`,
            header: {'authorization':this.#token},
            body: collection,
            method: 'PUT' 
          };
          return endpoint;
        },
        addImage:(name, blob)=>{
          var formData = new FormData();
          formData.append( 'fileNames', "[\""+name+"\"]");
          formData.append( name, blob, name);
          
          var endpoint = {
            resource: '/command?type=addMedia',
            header: {'authorization':this.#token},
            body: formData,
            method: 'POST' 
          };
          return endpoint;
        },
      }
    }
  }

  setPlatform(platformId){
    this.#platformId = platformId;
  }

  getUserStores(){
    return STUtils.request(this.endpoints.miniStore.allStores());
  }

  static getUserStore(storeId){
    var endpoint = {
          resource: `/cosellers/mini-stores/${storeId}`,
          method: 'get'  
        };
    return STUtils.request(endpoint);
  }

  static getAllUserStores(platformId){
    var endpoint = {
          resource: `/cosellers/fetch-mini-stores?platformId=${platformId}`,
          method: 'get'  
        };
    return STUtils.request(endpoint);
  }

  static getUserStoreByName(platformId, name){
    var endpoint = {
          resource: `/cosellers/fetch-mini-stores?platformId=${platformId}&name=${name}`,
          method: 'get'
        };
    return STUtils.request(endpoint);
  }

  createUserStore(store){
    return STUtils.request(this.endpoints.miniStore.create(store));
  }

  updateUserStore(storeId, store){
    return STUtils.request(this.endpoints.miniStore.update(storeId, store));
  }

  deleteUserStore(storeId){
    return STUtils.request(this.endpoints.miniStore.delete(storeId));
  }

  addStoreImage(name, blob){
    return STUtils.mpfRequest(this.endpoints.miniStore.addImage(name, blob));
  }

  createCosellerCollection(collection){
    return STUtils.request(this.endpoints.miniStore.createCollection(collection));
  }

  updateCosellerCollection(collectionId, collection){
    return STUtils.request(this.endpoints.miniStore.updateCollection(collectionId, collection));
  }

  static getCosellerCollection(collectionId){
    return STUtils.request(this.endpoints.miniStore.getCollection(collectionId));
  }

}

class STUtils{
  static backendUrl ='https://backend.shoptype.com';
  
  static request(endpoint = {},callback=null) {
    if(endpoint.method && endpoint.method.toLowerCase()!="get"){
      if(endpoint.header){
        endpoint.header['Content-Type'] = 'application/json';
      }else{
        endpoint.header={'Content-Type':'application/json'};
      }
    }

    return fetch(`https://backend.shoptype.com${endpoint.resource}`, {
      method: endpoint?.method,
      headers: endpoint?.header,
      body: endpoint?.body ? JSON.stringify(endpoint.body) : null,
    }).then(async (response) => {
      const data = await response.json();
      if (callback){callback(data)}
      return data;
    }).catch((error) => {
      return error;
    });
  }

  static mpfRequest(endpoint = {},callback=null) {
    return fetch(`https://backend.shoptype.com${endpoint.resource}`, {
      method: endpoint?.method,
      headers: endpoint?.header,
      body: endpoint?.body ? endpoint.body : null,
    }).then(async (response) => {
      const data = await response.json();
      if (callback){callback(data)}
      return data;
    }).catch((error) => {
      return error;
    });
  }

  static countries(){
    var endpoint = {
      resource:"/countries",
      method:"get"
    };
    return STUtils.request(endpoint);
  }

  static states(country){
    var endpoint = {
      resource:"/states/"+country,
      method:"get"
    };
    return STUtils.request(endpoint);
  }

  static st_loadScript(url, callback) {
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    script.onreadystatechange = callback;
    script.onload = callback;
    head.appendChild(script);
  }

  static toQueryString(obj) {
    var str = [];
    for (var p in obj)
      if (obj.hasOwnProperty(p) && obj[p]) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  }

  static setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  static getCookie(cname) {
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
    return "";
  }

  static sendEvent(eventName, value){
    const event = new CustomEvent(eventName, { detail: value });
    document.dispatchEvent(event);
  }

  static uuidv4() {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
      (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
  }
}

STUtils.sendEvent("ShoptypeJsLoaded","");