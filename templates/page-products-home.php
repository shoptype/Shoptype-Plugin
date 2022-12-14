<?php
/* Template Name:Products Home */
get_header();
?>
<div id="filterContainer" class="menu-main">
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
							<?php foreach ($filter->values as $filterValue) {	?>
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
						<select name="sortOrder" key="currency" id="sortOrder" class="menu-option-select" hidden>
							<option value="<?php echo $stDefaultCurrency;?>"><?php echo $stDefaultCurrency;?></option>
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
	<div class="st-filter-btn" onclick="toggleFilter()"><img src="<?php echo $path ?>/images/Filter-Icon.png" loading="lazy" alt="" class="st-filter-img"></div>
</div>

<script>
	let options={};
	let myshop_offset=0;
	let allLoaded = false;
	let productsLoading = false;
	
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
	
</script>
<div>
	<div class="st-myshop-search" style="margin:auto; padding:10px 20px;">
		<input class="st-myshop-search-box" id="st-search-box" name="Search" >
		<div class="st-product-search-title" onclick="searchProducts()"><img src="<?php echo $path ?>/images/search.svg" loading="lazy" alt="" class="st-product-search-img"></div>
	</div>
	<div count="10" imageSize="200x0" loadmore class="products-container" id="st-product-search-results">
		<div class="product-container single-product" style="display: none;" id="st-product-select-template">
			<div class="product-image">
				<a href="demo/awake/pdp/?product-id={{productId}}" class="am-product-link">
					<img class="am-product-image" src="" loading="lazy" alt="">
				</a>
				<div class="market-product-price am-product-price">$ 00.00</div>
			</div>
			<div class="product-info">
				<p class="am-product-title product-title">Product Title</p>
				<p class="am-product-vendor brand-title">Brand Title</p>
			</div>
		</div>
	</div>
	<div class="st-button-div">
		<button onclick="searchProducts(false)">Load More</button>
	</div>
</div>

<?php  get_footer(); ?>
