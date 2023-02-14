<?php
/* Template Name:Products Home */
get_header();
?>
<script>
	let options={};
	let myshop_offset=10;
	let allLoaded = false;
	let productsLoading = false;
	
	function filterProducts(){
		var selected = {};

		Array.from(document.getElementsByClassName("filter-checkbox-item")).forEach(x=>{ 
			var filterVal = x.checked&&x.value!=""?x.value+",":"";
			selected[x.getAttribute("name")] = selected[x.getAttribute("name")]?selected[x.getAttribute("name")] + filterVal:filterVal;
		});

		for (const prop in selected) {
			if(selected[prop]!=""){
				options[prop] = selected[prop];
			}else{
				delete options[prop];
			}
			
		}
		searchProducts(true);
	}
	
	function clearFilters(){
		Array.from(document.getElementsByClassName("menu-option-select")).forEach(x=>x.selectedIndex=0);
		filterProducts();
	}
	
	function toggleFilter(){
		var filter = document.getElementById("filterContainer");
		if(filter.style.display=="none"){
			filter.style.display="";
		}else{
			filter.style.display="none";
		}
		
	}
	
	window.addEventListener('scroll',()=>{
		let productLists = document.querySelector('.products-container');
		const {scrollHeight,scrollTop,clientHeight} = document.documentElement;
		if((scrollTop + clientHeight > scrollHeight-5) && !am_loading){
			searchProducts(false);
		}
	});
	
	function searchProducts(remove=true) {
		if(productsLoading){return;}
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		if(remove){
			removeChildren(productsContainer,productTemplate);
			allLoaded = false;
			myshop_offset=0;
		}
		if(allLoaded){return;}
		productsLoading = true;
		document.querySelector(".st-button-div button").disabled = true;
		options['text'] = document.getElementById('st-search-box').value;
		options['offset'] = myshop_offset;
		fetchProducts(options, productsContainer, productTemplate);
		myshop_offset+=10;
	}

	function removeChildren(node, dontRemove){
		let length = node.children.length;
		for (var i = length - 1; i >= 0; i--) {
			if(node.children[i]!=dontRemove){node.children[i].remove();}
		}
	}
	
	document.addEventListener("amProductsLoaded", ()=>{
		productsLoading = false;
		document.querySelector(".st-button-div button").disabled = false;
	});
		
	document.addEventListener("amProductsLoadFailed", ()=>{
		allLoaded = true;
		productsLoading = false;
		document.querySelector(".st-button-div button").disabled = true;
	});

	var scrollBefore = 0;
	window.addEventListener('scroll',function(e){
	    const scrolled = window.scrollY;
	    if(scrollBefore > scrolled){
	        scrollBefore = scrolled;
	        document.getElementById("filterContainer").style.top = "140px";
	    }else{
	        scrollBefore = scrolled;
	        document.getElementById("filterContainer").style.top = "0px";
	    }
	})

</script>
<style type="text/css">
a.am-product-link {
    color: #000000;
}
.am-product-img-div {
    width: 100%;
    height: 100%;
    display: flex;
}


.menu-container {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    width: 350px;
    height: 100vw;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    background: #fff;
    box-shadow: none;
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
    overflow: hidden;
}
div#filterContainer {
    position: sticky;
    left: 0px;
    top: 0px;
    right: auto;
    bottom: auto;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    height: auto;
    border-radius: 0px;
    max-height: 100vh;
    margin-top: 10px;
    margin-left: 10px;
    z-index: 0;
    transition-duration: .5s;
}
.products-main {
    display: flex;
}
.menu-brand-select {
    display: block !important;
}
div#st-filter {
    box-shadow: none;
    display: flex;
    height: auto;
    max-height: 100vh;
    padding-top: 30px;
}
div#st-product-search-results {
    max-width: 100%;
}
.filter-checkbox-main {
    margin-left: 30px;
}
.st-filter-btn {
    box-shadow: none !important;
    border: 1px solid #bbb;
    border-top-right-radius: 2px !important;
    border-bottom-right-radius: 2px !important;
    margin: 0px 10px;
    display: flex;
    width: 140px !important;
    font-size: 20px;
    align-items: center;
    justify-content: center;
}
img.st-filter-img {
    width: 20px;
    height: 20px;
    margin-left: 20px;
}
input.filter-checkbox-item {
    -ms-transform: scale(1.5);
    -webkit-transform: scale(1.5);
    transform: scale(1.5);
}
.filter-checkbox label {
    font-size: 16px;
    margin-left: 10px;
    line-height: 30px;
}
.on-sale {
    height: 35px;
    line-height: 35px;
    background-color: #abbda5;
    color: #ffffff;
}
.sold-out {
    height: 35px;
    min-width: 75px;
    width: auto;
    line-height: 35px;
    background-color: #666666;
    color: #ffffff;
}
.on-sale, .sold-out{
	color: #ffffff;
    border-radius: 5px;
    display: block;
    font-size: 13px;
    padding: 1px 4px;
    position: absolute;
    right: 5px;
    text-align: center;
    text-transform: uppercase;
    top: 5px;
    min-width: 55px;
    width: auto;
    z-index: 3;
}

@media screen and (max-width: 767px) {
	div#filterContainer {
	    position: fixed;
	    left: 0px;
	    top: 0px;
	    width: 100vw;
	    height: 100vh;
	    background: #ffffffa0;
	    border-radius: 0px;
	    max-height: 100vh;
	    margin-left: 0px;
	    z-index: 999;
	}
	div#st-filter {
	    margin-left: auto;
	    margin-right: 0;
	}
	.single-product, .single-brand {
	    flex: 1 1 calc(33% - 6px);
	    max-width: calc(33% - 6px);
	}
}
@media screen and (max-width: 575px) {
	.single-product, .single-brand {
	    flex: 1 1 calc(50% - 6px);
	    max-width: calc(50% - 6px);
	}
}
</style>
<div>
	<div class="st-myshop-search" style="margin:auto; padding:10px 20px;">
		<input class="st-myshop-search-box" id="st-search-box" name="Search" >
		<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
	</div>
	<div class="st-filter-btn" onclick="toggleFilter()"><div>Filter </div><img src="<?php echo $path ?>/images/Filter-Icon.png" loading="lazy" alt="" class="st-filter-img"></div>
	<div class="products-main">
		<div id="filterContainer" class="menu-main" onclick="toggleFilter()" style="display: none;">
			<div class="menu-container" id="st-filter" onclick="event.stopPropagation()">
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
									<div class="filter-checkbox-main">
									<?php 
										if($filter->multi == 0){
											$inputType = "radio";
										}else{
											$inputType = "checkbox";
										}
									    foreach ($filter->values as $filterValue) {	?>
										<div class="filter-checkbox">
											<input class="filter-checkbox-item" type="<?php echo $inputType ?>" id="<?php echo "{$filter->key}-{$filterValue->value}" ?>" name="<?php echo $filter->key ?>" value="<?php echo $filterValue->value ?>" onchange="filterProducts()">
											<label for="<?php echo "{$filter->key}-{$filterValue->value}" ?>"><?php echo $filterValue->name ?></label>
										</div>
									<?php } ?>
									</div>
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
									<option value="createdAt" selected>Latest</option>
									<option value="quantitySold">Most Sold</option>
								</select>
							</div>
							<div class="menu-brand-select">
								<div class="menu-option-block1">
									<h4 class="menu-option-title">Sort Order</h4>
								</div>
								<select name="sortOrder" key="orderBy" id="sortOrder" class="menu-option-select">
									<option value="asc">ascending</option>
									<option value="desc" selected>descending</option>
								</select>
								<select name="sortOrder" key="currency" id="sortOrder" class="menu-option-select" hidden>
									<option value="<?php echo $stDefaultCurrency;?>"><?php echo $stDefaultCurrency;?></option>
								</select>		 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div count="10" imageSize="200x0" loadmore class="products-container" sortBy="createdAt" orderBy="desc" id="st-product-search-results" >
				<div class="product-container single-product" style="display: none;" id="st-product-select-template">
					<a href="demo/awake/pdp/?product-id={{productId}}" class="am-product-link">
						<div class="product-image">
							<div class="am-product-img-div">
								<div class="sold-out" style="display:none;">Sold Out</div>
								<div class="on-sale" style="display:none;">Sale</div>
								<img class="am-product-image" src="" loading="lazy" alt="">
							</div>
						</div>
						<div class="product-info">
							<p class="am-product-title product-title">Product Title</p>
							<p class="am-product-vendor brand-title">Brand Title</p>
							<div class="market-product-price am-product-price">$ 00.00</div>
						</div>
					</a>
				</div>
			</div>
			<div class="st-button-div">
				<button onclick="searchProducts(false)">Load More</button>
			</div>
		</div>
	</div>
</div>

<?php  get_footer(); ?>
