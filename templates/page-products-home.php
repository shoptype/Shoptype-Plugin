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
		var sortBySelect = document.getElementById("sort-by");
		var option = sortBySelect.options[sortBySelect.options.selectedIndex];
		options["sortBy"]=option.getAttribute("sortBy");
		options["orderBy"]=option.getAttribute("orderBy");
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
		options['imgSize'] = "600x0";
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
	cursor:pointer;
	margin: 0px;
}
.all-products{
	max-width:1400px;
	margin:auto;
}
.filter-menu-div{
	margin: 9px;
	display:flex;
	justify-content: space-between;
}
.st-sort-div {
    display: flex;
    font-size: 18px;
    align-items: center;
}
select#sort-by {
    height: 40px;
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
}

</style>
<?php the_content(); ?>
<div class="products-main-container">

<div class="all-products">
	<div class="st-myshop-search">
		<input class="st-myshop-search-box" id="st-search-box" name="Search" >
		<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
	</div>
	<div class="filter-menu-div">
		<div class="st-filter-btn" onclick="toggleFilter()"><div>Filter </div><img src="<?php echo $path ?>/images/Filter-Icon.png?1" loading="lazy" alt="" class="st-filter-img"></div>
		<div class="st-sort-div">
			<div>Sort By :</div>
			<select id="sort-by" name="sortByFacets" class="" onchange="filterProducts()">
				<option sortBy="quantitySold" orderBy="desc" value="sortBy=quantitySold&orderBy=desc">Featured</option>
				<option sortBy="createdAt" orderBy="desc" value="sortBy=createdAt&orderBy=desc">New Arrivals</option>
				<option sortBy="price" orderBy="asc" value="sortBy=price&orderBy=asc">Price, low to high</option>
				<option sortBy="price" orderBy="desc" value="sortBy=price&orderBy=desc">Price, high to low</option>
			</select>
		</div>
	</div>
	
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
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div count="10" imageSize="600x0" loadmore class="products-container" sortBy="createdAt" orderBy="desc" id="st-product-search-results" >
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
</div>
<?php  get_footer(); ?>
