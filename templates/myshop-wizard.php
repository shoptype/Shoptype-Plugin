<?php
/*
* Template name: Shoptype My Shop
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
* @package shoptype
*/
  global $stPlatformId;
  global $stFilterJson;
  global $bp;
  $path = dirname(plugin_dir_url( __FILE__ ));
  $user_id = get_current_user_id();
  $currentUser = get_userdata($user_id);
  $groupSlug = strtolower($currentUser->user_login."_coseller");
  //$cosellerGp = bp_get_group_by('slug', $groupSlug);
  $group_id = BP_Groups_Group::group_exists( $groupSlug );
  $shop_products = xprofile_get_field_data( 'st_products' , $currentUser->id );
  $shop_theme = xprofile_get_field_data( 'st_shop_theme' , $currentUser->id );
  $shop_url = xprofile_get_field_data( 'st_shop_url' , $currentUser->id );
  if(!isset($group_id)){
      $group_id = groups_create_group(array(
        'creator_id'=>$user_id,
        'name'=> $currentUser->user_login,
        'slug'=> $groupSlug,
        'enable_forum'=>1
      ));
      groups_accept_invite($user_id, $group_id);
  }

  if(isset($group_id)){
    $group = groups_get_group($group_id);
    $group_cover = bp_get_group_cover_url($group);
    $group_img = bp_get_group_avatar_url($group);
  }
  $profileImage = get_avatar_url($user_id);
  if(isset($shop_url)){
    $encodedShopUrl = get_site_url()."/shop/".$shop_url;
  }else{
    $encodedShopUrl = get_site_url()."/shop/".$currentUser->user_login;
  }
  get_header(null);
?>
  <style type="text/css">
    .menu-main {
    position: fixed;
    left: -350px;
    top: calc(50% - 250px);
    right: auto;
    bottom: auto;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    height: 100%;
    border-radius: 20px 20px 0px 0px;
    max-height: 500px;
    margin-top: auto;
    z-index: 999;
    }

    .menu-apply-div {
      position: absolute;
      left: 0%;
      top: auto;
      right: 0%;
      bottom: 0%;
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      width: 100%;
      height: 80px;
      -webkit-box-pack: center;
      -webkit-justify-content: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      -webkit-align-items: center;
      -ms-flex-align: center;
      align-items: center;
      background-color: #cab470;
      color: #000;
    }

    .menu-container {
      position: relative;
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      width: 350px;
      height: 100%;
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
      -webkit-flex-direction: column;
      -ms-flex-direction: column;
      flex-direction: column;
    background: #fff;
      box-shadow: 1px 1px 5px 1px #666;
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
    overflow:hidden;
    }

    .menu-title-heading {
      font-family: Georgia, Times, 'Times New Roman', serif;
      color: #000;
      font-size: 26px;
      font-weight: 400;
      text-align: center;
    }

    .menu-title {
      -webkit-box-flex: 0;
      -webkit-flex: 0 60px;
      -ms-flex: 0 60px;
      flex: 0 60px;
    }

    .menu-list {
      overflow: auto;
    }

    .menu-apply-button {
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      margin-right: 10px;
      margin-left: 10px;
      padding: 5px;
      -webkit-box-pack: center;
      -webkit-justify-content: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      -webkit-align-items: center;
      -ms-flex-align: center;
      align-items: center;
      border-radius: 5px;
      background-color: #fff;
      box-shadow: 1px 1px 3px 0 #000;
      cursor: pointer;
    }

    .menu-apply-button-lable {
      margin-top: 0px;
      margin-bottom: 0px;
      padding-right: 10px;
      padding-left: 10px;
      border-style: solid;
      border-width: 1px;
      border-color: #696969;
      border-radius: 4px;
      font-family: 'Playfair Display', sans-serif;
      color: #696969;
      font-size: 18px;
      font-weight: 700;
      text-align: center;
    }

    .menu-option-block1 {
      -webkit-box-flex: 0;
      -webkit-flex: 0 140px;
      -ms-flex: 0 140px;
      flex: 0 140px;
    }

    .menu-option-select {
      height: 30px;
      -webkit-box-flex: 0;
      -webkit-flex: 0 210px;
      -ms-flex: 0 210px;
      flex: 0 210px;
    padding:0px 10px;
    margin-right:5px;
      border-radius: 15px;
    }

    .menu-option-title {
      margin-top: 0px;
      margin-bottom: 0px;
      padding-left: 10px;
      font-family: 'PT Sans', sans-serif;
      color: #696969;
      font-size: 16px;
      line-height: 30px;
    }

    .menu-brand-select {
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .body {
      border: 1px none #000;
    }

    .st-filter-btn {
      position: relative;
      left: auto;
      top: calc(50% - 21px);
      bottom: auto;
      width: 42px;
      height: 42px;
      padding-left: 5px;
      border-top-right-radius: 15px;
      border-bottom-right-radius: 15px;
      background-color: #fff;
      box-shadow: 1px 1px 3px 0 #666;
    }

    .st-filter-img {
      height: 38px;
      padding-top: 2px;
    }
  </style>
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
        foreach ($stFilters as $filter) {
        ?>
          <div class="menu-brand-select">
            <div class="menu-option-block1">
            <h4 class="menu-option-title"><?php echo $filter->name ?></h4>
            </div>
            <select name="<?php echo $filter->name ?>" key="<?php echo $filter->key ?>" id="<?php echo str_replace(" ","-",$filter->name) ?>" class="menu-option-select" <?php echo $filter->multi; ?>>
            <?php foreach ($filter->values as $filterValue) {  ?>
            <option value="<?php echo $filterValue->value ?>"><?php echo $filterValue->name ?></option>
            <?php } ?>
            </select>
          </div>
          
          <?php
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
            </div>
        </div>
        </div>
      </div>
      <div class="menu-apply-div">
        <div class="menu-apply-button">
          <h3 class="menu-apply-button-lable" onclick="clearFilters()">Reset</h3>
        </div>
        <div class="menu-apply-button">
          <h3 class="menu-apply-button-lable" onclick="filterProducts()">Apply &amp; Refresh</h3>
        </div>
      </div>
    </div>
    <div class="st-filter-btn" onclick="toggleFilter()"><img src="<?php echo $path ?>/images/settings.svg" loading="lazy" alt="" class="st-filter-img"></div>
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
       btn.style.left="-350px";
    }else{
       btn.style.left="0px";
    }
    
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
      <input class="st-myshop-name" id="myshop-name" value="<?php echo $group->name ?>"/>
    </div>
    <div class="st-myshop-details-div">
      <div class="st-myshop-details-txt">Store Bio</div>
      <input class="st-myshop-bio" id="myshop-bio" value="<?php echo $group->description ?>"/>
    </div>
    <div class="st-myshop-details-div">
      <div class="st-myshop-details-txt">Store URL</div>
      <input class="st-myshop-bio" id="myshop-url" onchange="checkUrlAvailable(this)" value="<?php echo $shop_url ?>"/>
    </div>
    <div class="st-myshop-details-div">
      <div class="st-myshop-details-txt">Store Logo</div>
      <a href="#" onclick="document.getElementById('profileImageFile').click()" class="st-myshop-img-select" ><img id="store-icon" src="<?php echo $group_img ?>" loading="lazy" alt="" class="st-myshop-store-img">
        <div class="div-block-11">
          <div class="st-myshop-img-txt">JPG/PNG<br>To upload you file</div>
          <div class="st-myshop-img-lnk">Click here</div>
                <input type="file" id="profileImageFile" onchange="updateShopImg()" style="display: none;">
        </div>
      </a>
    </div>
    <div class="st-myshop-details-div">
      <div class="st-myshop-details-txt">Store Banner Image</div>
      <a href="#" onclick="document.getElementById('profileBGFile').click()" class="st-myshop-img-select"><img id="store-banner" src="<?php echo $group_cover ?>" loading="lazy" alt="" class="st-myshop-store-banner">
        <div class="div-block-11">
          <div class="st-myshop-img-txt">JPG/PNG min image size(1300px X 225px)<br>To upload you file</div>
          <div class="st-myshop-img-lnk">Click here</div>
                <input type="file" id="profileBGFile" onchange="updateBgImg()" style="display: none;">
        </div>
      </a>
    </div>
  <div class="st-myshop-details-div">
      <div class="st-myshop-details-txt">Profile Image</div>
      <a href="#" onclick="document.getElementById('profileImgFile').click()" class="st-myshop-img-select"><img id="profile-img" src="<?php echo $profileImage ?>" loading="lazy" alt="" class="st-myshop-store-img">
        <div class="div-block-11">
          <div class="st-myshop-img-txt">JPG/PNG<br>To upload you file</div>
          <div class="st-myshop-img-lnk">Click here</div>
                <input type="file" id="profileImgFile" onchange="updateProfileImg()" style="display: none;">
        </div>
      </a>
    </div>
  </div>
  <div class="st-myshop-style" style="display:none">
    <div>
      <h2 class="st-myshop-header">Choose your theme for the store</h2>
    </div>
    <div class="st-myshop-theme-list">
      <div class="st-myshop-theme">
        <div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-01" name="theme_select" value="theme-01" <?php echo $shop_theme=="theme-01"?"checked":"" ?>></div>
        <div class="div-block-9"><img src="<?php echo "$path/images/boxed_theme.png" ?>" loading="lazy" alt="<?php echo $shop_theme ?>" class="st-myshop-theme-img">
          <div class="st-myshop-theme-name">design 1</div>
        </div>
      </div>
      <div class="st-myshop-theme">
        <div class="st-myshop-theme-select"><input class="st-shop-select" type="radio" id="theme-02" name="theme_select" value="theme-02" <?php echo $shop_theme=="theme-02"?"checked":"" ?>></div>
        <div class="div-block-9"><img src="<?php echo "$path/images/fullwidth_theme.png" ?>" loading="lazy" alt="" class="st-myshop-theme-img">
          <div class="st-myshop-theme-name">design 2</div>
        </div>
      </div>
    </div>
  </div>
  <div class="st-myshop-products"  style="display:none">
    <h2 class="st-myshop-header">Choose products to add to the store</h2>
    
    <div>
      <div class="st-myshop-search">
        <input class="st-myshop-search-box" id="st-search-box" name="Search" >
        <div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
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
  <div class="st-myshop-complete"  style="display:none">
    <div class="div-block-10"></div>
    <h2 class="st-myshop-header">CONGRATULATIONS</h2>
    <div class="st-myshop-txt">Your Store Setup is Complete</div>
    <div>
      <div class="text-block-3">Share your store on social media</div>
      <div class="st-myshop-social">
        <a id="fb_link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encodedShopUrl ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/fb_icon.png" loading="lazy" alt="" class="image"></a>
        <a id="wa_link" href="whatsapp://send?text=<?php echo "$sharetxt $encodedShopUrl" ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/whatsapp_icon.png" loading="lazy" alt="" class="image"></a>
        <a id="tw_link" href="http://twitter.com/share?text=<?php echo "{$sharetxt}&url={$encodedShopUrl}" ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/twitter_icon.png" loading="lazy" alt="" class="image"></a>
        <a id="pi_link" href="https://pinterest.com/pin/create/link/?url=<?php echo "{$encodedShopUrl}&media={$group_img}&description={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/insta_icon.png" loading="lazy" alt="" class="image"></a>
        <a id="tgram_link" href="https://telegram.me/share/url?url=<?php echo "{$encodedShopUrl}&TEXT={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/telegram_icon.png" loading="lazy" alt="" class="image"></a>
        <a id="ln_link" href="https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=<?php echo "{$encodedShopUrl}&title={$group->name}&summary={$sharetxt}" ?>" class="st-myshop-social-link"><img src="<?php echo $path ?>/images/linkedIn_icon.png" loading="lazy" alt="" class="image"></a>
      </div>
    </div>
    <a href="<?php echo $encodedShopUrl ?>" class="st-myshop-button" id="goto_shop_btn">Go to Store</a>
  </div>
  <div class="st-myshop-bottom">
    <a id="st-next-button" href="#" onclick="moveState()" class="st-myshop-button">Save &amp; Continue</a>
  </div>
</div>
<script type="text/javascript">
  function callBpApi(dataUri, callBack, type, data){
    wp.apiRequest( {
      path: "buddypress/v1/"+dataUri,
      type: type,
      data: data,
    } ).done( function( data ) {
      callBack(data);
    } ).fail( function( error ) {
      ShoptypeUI.showError(error.responseJSON.message);
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
      ShoptypeUI.showError(error.responseJSON.message);
      return error;
    } );
  }

  function addUserDetails(userData){
    currentBpUser = userData;
  }
  
  function hideResults(){
    document.getElementById("st-product-search-results").style.display = "none";
  }

  function showResults(){
    document.getElementById("st-product-search-results").style.display = "";
  }

  function addToShop(shopProducts){
    let selectorNodes = document.getElementsByClassName("st-myshop-select");
    let products = shopProducts[0].value.unserialized[0]??"";
    let newProducts = {};
    for (var i = 0; i < selectorNodes.length; i++) {
      if(selectorNodes[i].checked && !products.includes(selectorNodes[i].value)){
        newProducts[selectorNodes[i].value] = shoptype_UI.getUserTracker();
      }
    }
    hideResults();
    callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,x=>addProductByIdToShop(x, newProducts,x=>loadShopProducts(currentBpUser.user_login)),'get');
  }

  function loadShopProducts(userName) {
    let productTemplate = document.getElementById("st-product-template");
    let productsContainer = document.getElementById("st-myshop-main");
    hideResults();
    removeChildren(productsContainer, productTemplate)
    fetch('/wp-json/shoptype/v1/shop/' + userName + '?count=1000')
      .then(response => response.json())
      .then(productsJson => {
        for (var i = 0; i < productsJson.products.length; i++) {
          let newProduct = productTemplate.cloneNode(true);
          addProductDetails(newProduct, productsJson.products[i],".st-product-img",".st-product-cost");
          newProduct.querySelector(".st-product-link").href= "/products/"+productsJson.products[i].id+"/?tid="+productsJson.products[i].tid;
          newProduct.id = productsJson.products[i].id;
          newProduct.querySelector(".st-remove-product").setAttribute("onclick",`event.stopPropagation(); removeProductFromShop("${productsJson.products[i].id}")`);
          productsContainer.appendChild(newProduct);
        }
      });
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
      callBpApi(`xprofile/${themesId}/data/${currentBpUser.id}`, x=>{}, 'post',{context: 'edit', value:selectedTheme});
      moveToProducts();
      break;
      case 3:
      callBpApi(`xprofile/${productsDataId}/data/${currentBpUser.id}`,addToShop,'get');
      moveToComplete();
      break;
    }
    st_shop_state++;
  }
  
  function moveToDetails(){
    document.querySelector(".st-my-shop-details").style.display="";
    document.querySelector(".st-myshop-style").style.display="none";
    document.querySelector(".st-myshop-products").style.display="none";
    document.querySelector(".st-myshop-complete").style.display="none";
    document.querySelector("#st-next-button").style.display="";
    document.querySelector("#st-next-button").style.position="";  
    document.getElementById("state-1").classList.add("st-myshop-state-selected");
    document.getElementById("state-2").classList.remove("st-myshop-state-selected");
    document.getElementById("state-3").classList.remove("st-myshop-state-selected");
    document.getElementById("state-4").classList.remove("st-myshop-state-selected");
  }
  function moveToTheme(){
    clearTimeout(debounce_timer);
    var data = {
      context: 'edit',
      name: document.getElementById("myshop-name").value,
      description: document.getElementById("myshop-bio").value,
    }
    callBpApi("groups/"+groupId,(d)=>{showThemeSelect();},"put",data);
  var shopUrl = document.getElementById("myshop-url").value;
  shopUrl = encodeURI(shopUrl);
  callBpApi(`xprofile/${myshopUrlId}/data/${currentBpUser.id}`,(d)=>{},"post",{context: 'edit', value:shopUrl});
  }
  function moveToProducts(){
    searchProducts();
    document.querySelector(".st-my-shop-details").style.display="none";
    document.querySelector(".st-myshop-style").style.display="none";
    document.querySelector(".st-myshop-products").style.display="";
  document.querySelector("#filterContainer").style.display="";  
    document.querySelector(".st-myshop-complete").style.display="none";
    document.querySelector("#st-next-button").style.display="";
    document.querySelector("#st-next-button").style.position="fixed";
    document.getElementById("state-3").classList.add("st-myshop-state-selected");
    document.getElementById("state-2").classList.remove("st-myshop-state-selected");
    document.getElementById("state-2").classList.add("st-myshop-state-done");
  }
  function moveToComplete(){
    document.querySelector(".st-my-shop-details").style.display="none";
    document.querySelector(".st-myshop-style").style.display="none";
    document.querySelector(".st-myshop-products").style.display="none";
  document.querySelector("#filterContainer").style.display="none";
    document.querySelector(".st-myshop-complete").style.display="";
    document.querySelector("#st-next-button").style.display="none";
    document.querySelector("#st-next-button").style.position="";
    document.getElementById("state-4").classList.add("st-myshop-state-selected");
    document.getElementById("state-3").classList.remove("st-myshop-state-selected");
    document.getElementById("state-3").classList.add("st-myshop-state-done");
  }
  function showThemeSelect(){
    document.querySelector(".st-my-shop-details").style.display="none";
    document.querySelector(".st-myshop-style").style.display="";
    document.querySelector(".st-myshop-products").style.display="none";
    document.querySelector(".st-myshop-complete").style.display="none";
    document.querySelector("#st-next-button").style.display="";
    document.querySelector("#st-next-button").style.position="";
    document.getElementById("state-1").classList.remove("st-myshop-state-selected");
    document.getElementById("state-1").classList.add("st-myshop-state-done");
    document.getElementById("state-2").classList.add("st-myshop-state-selected");
  }
  
   
  function updateShopImg(){
    var fileSelect = document.getElementById("profileImageFile");
    if ( ! fileSelect.files || ! fileSelect.files[0] ) {
      return;
    }
    var formData = new FormData();
    formData.append( 'action', 'bp_avatar_upload' );
    formData.append( 'file', fileSelect.files[0] );
    pushBpApi(`groups/${groupId}/avatar`, (d)=>{document.getElementById("store-icon").src = d[0].full}, "post", formData);
  }
  
  function updateBgImg(){
    var fileSelect = document.getElementById("profileBGFile");
    if ( ! fileSelect.files || ! fileSelect.files[0] ) {
      return;
    }
    var formData = new FormData();
    formData.append( 'action', 'bp_cover_image_upload' );
    formData.append( 'file', fileSelect.files[0] );
    pushBpApi(`groups/${groupId}/cover`, (d)=>{document.getElementById("store-banner").src = d[0].image}, "post", formData);
  }
  
  function updateProfileImg(){
  var fileSelect = document.getElementById("profileImgFile");
    if ( ! fileSelect.files || ! fileSelect.files[0] ) {
      return;
    }
    var formData = new FormData();
    formData.append( 'action', 'bp_avatar_upload' );
    formData.append( 'file', fileSelect.files[0] );
    pushBpApi(`members/${profileUser}/avatar`, (d)=>{document.getElementById("profile-img").src = d[0].image}, "post", formData);
    var file = fileSelect.files[0];
    var reader = new FileReader();
    reader.onloadend = function() {
      fetch('https://shopthatface-com.ibrave.host/wp-json/shoptype/v1/registerface', {
        method: 'post',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({'imageBS64':reader.result})
      }).then(async (response) => {
      }).catch((error) => {
      });
    }
    reader.readAsDataURL(file);
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
    
    showResults();
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
      if(node.children[i]!=dontRemove){node.children[i].remove();}
    }
  }

  function productSelect(selectBox){
    let productId = selectBox.value;
    st_selectedProducts[productId]=shoptype_UI.getUserTracker();
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
  
  function checkUrlAvailable(element){
    var testUrl = element.value;
    testUrl= testUrl.trim();
    testUrl = testUrl.replace(/[&\/\\#, +()$~%.':*?<>{}]/g, '-');
    element.value = testUrl;
    fetch("/wp-json/shoptype/v1/shop-url-check/"+testUrl)
      .then((response) => response.json())
      .then(data=>{
      if(data.status === "taken"){
        element.value = currentShopUrl;
        if(testUrl != currentShopUrl){
          ShoptypeUI.showError(`the url ${testUrl} is already in use please choose another one.`);
        }
      }else{
        setShopUrl("/shop/"+testUrl);
      }
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
  let productsDataId = null;
  let myshopUrlId = null;
  let themesId = null;
  let debounce_timer;
  let groupId = <?php echo $group_id ?>;
  let st_shop_state = 0;
  let myshop_offset = 1;
  let scrollLoading = false;
  let options={};
  function initMyShop(){
    if (typeof wp !== "undefined") { 
      callBpApi("members/"+profileUser, addUserDetails, 'get',{populate_extras:true});
      callBpApi("xprofile/fields", setFieldId, 'get',{populate_extras:true});
    }else{
      setTimeout(initMyShop,200);
    }
  }

  function setFieldId(data){
    themesId = data.find(field=>field.name=="st_shop_theme").id;
  myshopUrlId = data.find(field=>field.name=="st_shop_url").id;
    productsDataId = data.find(field=>field.name=="st_products").id;
  }
  
  scrollContainer = document.getElementById("st-product-search-results");
  window.addEventListener('scroll',()=>{
    const {scrollHeight,scrollTop,clientHeight} = document.documentElement;
    if((scrollTop + clientHeight > scrollHeight - 5) && (!scrollLoading)){
    scrollLoading = true;
      searchProducts(false);
    }
  });

  initMyShop();
  document.getElementById("store_title").addEventListener("input", (e) => updateStoreName(e.currentTarget.textContent), false);
  document.getElementById("store_bio").addEventListener("input", (e) => updateStoreBio(e.currentTarget.textContent), false);
</script>
<?php
function pagemyshop_enqueue_style() {
    wp_enqueue_style( 'my-shop-css', plugin_dir_url( __FILE__ ) . '/css/st-my-shop.css' );
}

add_action( 'wp_enqueue_scripts', 'pagemyshop_enqueue_style' );

get_footer();