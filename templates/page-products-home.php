<?php
/* Template Name:Products Home */

global $stPlatformId;
global $stBackendUrl;
global $shoptypeUrlBase;
global $stCurrency;
global $marketUrl;
global $productUrl;

$trimmed_productUrl = str_replace("tid={{tid}}", "", $productUrl);
$trimmed_productUrl = rtrim($trimmed_productUrl,"?");
$pg_str = $_GET['pg'];
$pg = (int)$pg_str;
$st_count = 20;
if($pg==0){$pg=1;}
get_header();
?>
<script>
	let options={};
	let allLoaded = false;
	let productsLoading = false;
	let st_current_page=1;
	const st_count = <?php echo $st_count; ?>;
	
	function filterProducts(page=0,newSearch=false){
		var selected = {};

		Array.from(document.getElementsByClassName("filter-checkbox-item")).forEach(x=>{ 
			var filterVal = x.checked&&x.value!=""?x.value+",":"";
			selected[x.getAttribute("name")] = selected[x.getAttribute("name")]?selected[x.getAttribute("name")] + filterVal:filterVal;
		});
		var filter_str = "";
		if(newSearch){
			var search_elem = document.getElementById('st-search-box');
			selected["text"] = search_elem.value;
		}
		for (const prop in selected) {
			if(selected[prop]!=""){
				options[prop] = selected[prop];
				if(prop != "text"){
					filter_str += prop + "=" + selected[prop] + "&";
				}
			}else{
				delete options[prop];
			}	
		}

		searchProducts(page, filter_str);
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
	
	function ajaxLoad(page){
		filterProducts(page);
		return false;
	}
	
	function searchProducts(page=0, filter_str) {
		if(productsLoading){return;}
		let productTemplate = document.getElementById("st-product-select-template");
		let productsContainer = document.getElementById("st-product-search-results");
		removeChildren(productsContainer,productTemplate);
		productsLoading = true;
		var sortBySelect = document.getElementById("sort-by");
		var option = sortBySelect.options[sortBySelect.options.selectedIndex];
		options["sortBy"]=option.getAttribute("sortBy");
		filter_str += "sortBy=" + options["sortBy"] + "&";
		options["orderBy"]=option.getAttribute("orderBy");
		filter_str += "orderBy=" + options["orderBy"] + "&";
		var search_elem = document.getElementById('st-search-box');
		if(options["text"]){
			search_elem.value = options["text"].replaceAll(","," ").trim();
		}
		if(search_elem.value !=""){
			filter_str += "text=" + search_elem.value + "&";
		}
		st_current_page = page+1;
		filter_str += "pg=" + st_current_page;
		if (history.pushState) {
			var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?'+filter_str;
			window.history.pushState({path:newurl},'',newurl);
		}
		options['text'] = document.getElementById('st-search-box').value;
		options['imgSize'] = "600x0";
		options['offset'] = page * st_count;
		options['count'] = <?php echo $st_count; ?>;
		if(!options['inStock']){options['inStock']=true;}
		var am_pages = document.getElementById("am-pages");
		removeChildren(am_pages,null);
		fetchProducts(options, productsContainer, productTemplate,(x)=>{resetPageCount(x.count,st_current_page)});
	}
	
	function resetPageCount(product_count, mid_page){
		var am_pages = document.getElementById("am-pages");
		var page_count = Math.ceil(product_count/st_count);
		let params = (new URL(document.location)).searchParams;
		var selected_pg = parseInt(params.get("pg"));
		var pg_url = new URL(window.location.href);
		var addNext = false;
		for(var i = 1; i<=page_count; i++){
			var page_classes = ["st-page"];
			
			if(addNext){
				addPageLink("...", "#", "return loadNext("+product_count+","+ mid_page +")",["st-page", "st-disp-page"], am_pages);
			}
			
			if((i>=mid_page-2 && i<=mid_page+2)||(i==1 || i==page_count)){
				page_classes.push("st-disp-page");
			}else if(i==2){
				addPageLink("...", "#", "return loadPrev("+product_count+","+ mid_page +")",["st-page", "st-disp-page"], am_pages);
			}else if(i==page_count-1){
				addNext=true;
			}
			
			if(i==selected_pg){
				page_classes.push("selected-page");
			}
			pg_url.searchParams.set('pg', i);
			addPageLink(i, pg_url.href, "return ajaxLoad("+ (i-1) +")",page_classes, am_pages);
		}
	}
	
	function addPageLink(pageNo, pg_url, onClick, page_classes, pages_elem){
		var newIl = document.createElement("il");
		var newPage = document.createElement("a");
		newPage.innerText = pageNo;
		newPage.href = pg_url;
		page_classes.forEach(x=>{newPage.classList.add(x);});
		newPage.setAttribute("onclick", onClick);
		newIl.appendChild(newPage);
		pages_elem.appendChild(newIl);
	}
	
	function loadNext(total_prods, mid_page){
		var am_pages = document.getElementById("am-pages");
		removeChildren(am_pages, null);
		resetPageCount(total_prods, mid_page+3);
		return false;
	}
	
	function loadPrev(total_prods, mid_page){
		var am_pages = document.getElementById("am-pages");
		removeChildren(am_pages, null);
		resetPageCount(total_prods, mid_page-3);
		return false;
	}

	function removeChildren(node, dontRemove){
		let length = node.children.length;
		for (var i = length - 1; i >= 0; i--) {
			if(node.children[i]!=dontRemove){node.children[i].remove();}
		}
	}
	
	document.addEventListener("amProductsLoaded", ()=>{
		productsLoading = false;
	});
	
	addEventListener("DOMContentLoaded", ()=>{
		document.getElementById("st-search-box").addEventListener("keypress", function(event) {
			if (event.key === "Enter") {
				event.preventDefault();
				console.info("enter click");
				document.querySelector(".st-product-search-title").click();
			}
		});
	});
		
	document.addEventListener("amProductsLoadFailed", ()=>{
		allLoaded = true;
		productsLoading = false;
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
.st-filter-btn{box-shadow:none!important;border:1px solid #bbb;border-top-right-radius:2px!important;border-bottom-right-radius:2px!important;margin:0;display:flex;width:140px!important;font-size:20px;align-items:center;justify-content:center;cursor:pointer}
.all-products{max-width:1240px;margin:auto}
.filter-menu-div{margin:9px;display:flex;justify-content:space-between}
.st-sort-div{display:flex;font-size:18px;align-items:center}
select#sort-by{height:40px;width: 130px;}
ul.st-pages{margin:10px 0 20px;display: flex;flex-wrap:wrap;}
.products-main {display: flex;min-height: calc(100vw - 400px);}
.st-pages il a.selected-page {background: #333;color: #fff;display: flex;}
.single-product, .single-brand {max-width:calc(25% - 6px);min-width:300px;}
.st-product-search-title {background: #000;cursor: pointer;}
div .product-container{min-width:250px;}
.st-pages il a {padding:5px;background:#eee;margin:2px 3px;width: 24px;justify-content: center;font-size: 12px !important;}
a.st-page {display: none;}
a.st-page.st-disp-page {display:flex;}
.product-container:hover .st-buy-options {display: flex;}
.st-buy-options{position:absolute;z-index: 1;display:none;flex-direction: column;height: 100px;width: 200px;justify-content: space-evenly;left: calc(50% - 100px);top: 75px;align-content: center;flex-wrap: wrap;}
.st-buynow-btn, .st-addcart-btn{background: #ffffffaa;padding: 2px 10px;width: 110px;height: 30px;border-radius: 15px;cursor: pointer;text-align: center; border: solid 1px #333;font: 400 14px/28px sans-serif;}
.st-buynow-btn:hover, .st-addcart-btn:hover{background: #ffffff;}
@media screen and (max-width:767px){
	div#filterContainer{position:fixed;left:0;top:0;width:100vw;height:100vh;background:#ffffffa0;border-radius:0;max-height:100vh;margin-left:0;z-index:999}
	div#st-filter{margin-left:auto;margin-right:0}
	.single-product, .single-brand {max-width:calc(100% - 20px);min-width:300px;}
}
</style>
<?php the_content(); ?>
<div class="products-main-container">

<div class="all-products">
	<div class="st-myshop-search">
		<input class="st-myshop-search-box" id="st-search-box" name="Search" value="<?php echo htmlspecialchars(wp_unslash($_GET['text'])); ?>" >
		<div class="st-product-search-title" onclick="filterProducts(0,true)"><img src="<?php echo st_locate_file("images/search.svg") ?>" loading="lazy" alt="" class="st-product-search-img"></div>
	</div>
	<div class="filter-menu-div">
		<div class="st-filter-btn" onclick="toggleFilter()"><div>Filter </div><img src="<?php echo st_locate_file("images/Filter-Icon.png") ?>" loading="lazy" alt="" class="st-filter-img"></div>
		<div class="st-sort-div">
			<div>Sort By :</div>
			<select id="sort-by" name="sortByFacets" class="" onchange="filterProducts()">
				<option sortBy="quantitySold" orderBy="desc" value="sortBy=quantitySold&orderBy=desc">Featured</option>
				<option sortBy="createdAt" orderBy="desc" selected value="sortBy=createdAt&orderBy=desc">New Arrivals</option>
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
								 if ($stFilters != null) {
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
												<?php
													$checked = "";
													$filter_param = $_GET[$filter->key];
													if(isset($filter_param) && str_contains($filter_param,$filterValue->value)){
														$checked = "checked";
													}
												 ?>
												<input class="filter-checkbox-item" type="<?php echo $inputType ?>" id="<?php echo htmlentities("{$filter->key}-{$filterValue->value}"); ?>" name="<?php echo $filter->key ?>" value="<?php echo htmlentities($filterValue->value) ?>" <?php echo $checked; ?>  onchange="filterProducts()">
												<label for="<?php echo "{$filter->key}-{$filterValue->value}" ?>"><?php echo $filterValue->name ?></label>
											</div>
										<?php } ?>
										</div>
										</div>

										<?php
									}
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div count="<?php echo $st_count; ?>" imageSize="600x0" loadmore class="products-container" autoLoad="false" id="st-product-search-results" >
				<?php
					$offset = ($pg-1) * $st_count;
					$url_params = "";
					if(!isset($_GET["sortBy"])){
						$url_params = "&sortBy=createdAt&orderBy=desc";
					}
					foreach($_GET as $key => $value){
					  	$url_params = $url_params . "&" . $key . "=" . wp_unslash($value);
					}
					if(!isset($_GET["inStock"])){
						$url_params = $url_params . "&inStock=true";
					}
					$response = wp_remote_get("{$stBackendUrl}/platforms/$stPlatformId/products?imgSize=600x0&offset={$offset}&count={$st_count}{$url_params}");
					$resultProduct     = wp_remote_retrieve_body( $response );
					$pluginUrl = plugin_dir_url(__FILE__);
					$total_pages = 0;
					if (!empty($resultProduct)) {
						$st_products = json_decode($resultProduct);
					}
				?>
				<div class="product-container single-product" style="position:relative;display: none;" id="st-product-select-template">
					<div class="st-buy-options" style="display:none">
						<div class="st-buynow-btn" variantid="" variantName='' productid="" onclick="shoptype_UI.buyNow(this)">Buy Now</div>
						<div class="st-addcart-btn" variantid="" variantName='' productid="" onclick="shoptype_UI.addToCart(this)">Add to Cart</div>
					</div>
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
				<?php if(isset($st_products->products)){ ?>
					<?php $total_pages = ceil($st_products->count/$st_count) ?>
					<?php foreach ($st_products->products as $product) { ?>
						<?php
							if(isset($product->variants)){
								$max_price = 0;
								$min_price = PHP_FLOAT_MAX;
								$soldout = true;
								$on_sale = false;
								foreach ($product->variants as $variant) {
									if($variant->discountedPriceAsMoney->amount<$min_price){
										$min_price = $variant->discountedPriceAsMoney->amount;
									}
									if($variant->discountedPriceAsMoney->amount>$max_price){
										$max_price = $variant->discountedPriceAsMoney->amount;
									}
									if($variant->quantity > 0){ $soldout = false; }
									if($variant->priceAsMoney->amount>$variant->discountedPriceAsMoney->amount){
										$on_sale = true;
									}
								}
								$price_str = $stCurrency[$product->currency] ." ". $min_price;
								if($max_price>$min_price){
									$price_str = $price_str . " - " . $stCurrency[$product->currency] ." ". $max_price;
								}
							}

						?>
						<div class="product-container single-product" style="position:relative;" id="st-product-select-template">
							<?php if(count($product->variants)==1){ ?>
							<div class="st-buy-options">
								<div class="st-buynow-btn" variantid="<?php echo $product->variants[0]->id ?>" variantName='<?php echo json_encode($product->variants[0]->variantNameValue) ?>' productid="<?php echo $product->id ?>" onclick="shoptype_UI.buyNow(this)">Buy Now</div>
								<div class="st-addcart-btn" variantid="<?php echo $product->variants[0]->id ?>" variantName='<?php echo json_encode($product->variants[0]->variantNameValue) ?>' productid="<?php echo $product->id ?>" onclick="shoptype_UI.addToCart(this)">Add to Cart</div>
							</div>

							<?php } ?>
							<a href="<?php echo str_replace("{{productId}}",$product->id, $trimmed_productUrl) ?>" class="am-product-link">
								<div class="product-image">
									<div class="am-product-img-div">
										<?php if($soldout){ ?>
											<div class="sold-out">Sold Out</div>
										<?php } ?>
										<?php if($on_sale){ ?>
											<div class="on-sale" style="display:none;">Sale</div>
										<?php } ?>
										<img class="am-product-image" src="<?php echo $product->primaryImageSrc->imageSrc ?>" loading="lazy" alt="<?php echo $product->title ?>">
									</div>
								</div>
								<div class="product-info">
									<p class="am-product-title product-title"><?php echo $product->title ?></p>
									<p class="am-product-vendor brand-title"><?php echo $product->vendorName ?></p>
									<div class="market-product-price am-product-price"><?php echo $price_str ?></div>
								</div>
							</a>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="st-button-div" style="justify-content: center;">
				<ul class="st-pages" id="am-pages">
					<?php
						global $wp;
						$current_url = home_url( add_query_arg( NULL, NULL ) );
						$url = preg_replace('/(.*)(?|&)pg=[^&]+?(&)(.*)/i', '$1$2$4', $current_url .'&');
						$url = substr($url, 0, -1);
						$url = preg_replace('/(.*)(?|&)tid=[^&]+?(&)(.*)/i', '$1$2$4', $url .'&');
						$url = substr($url, 0, -1);
						$url = preg_replace('/(.*)(?|&)tid&/i', '$1$2$4', $url .'&');
						$url = substr($url, 0, -1);
						if (strpos($url, '?') === false) {
							$current_url = ($url .'?pg={{page}}');
						} else {
							$current_url = ($url .'&pg={{page}}');
						}

						for ($x = 1; $x <= $total_pages; $x++) {
							$pageUrl = str_replace("{{page}}",$x,$current_url);
							$p_count = $x -1;
							$pg_class = "st-page";
							$disp_next = "";
							if(($pg >= $x-2 && $pg <= $x+2)||($x==$total_pages || $x==1)){
								$pg_class = $pg_class." st-disp-page";
							}else if($x==2){
								echo "<il><a onclick=\"return loadPrev({$st_products->count},{$pg});\" href=\"#\" class=\"st-page st-disp-page\" >...</a></il>";
							}else if($x==$total_pages-1){
								$disp_next = "<il><a onclick=\"return loadNext({$st_products->count},{$pg});\" href=\"#\" class=\"st-page st-disp-page\" >...</a></il>";
							}
							if($pg == $x){$pg_class = $pg_class." selected-page";}
							
							echo "<il><a onclick=\"return ajaxLoad({$p_count});\" href=\"{$pageUrl}\" class=\"{$pg_class}\" >$x</a></il>$disp_next";
						}
					?>
				</ul>
				<!-- <button onclick="searchProducts(false)">Load More</button> -->
			</div>
		</div>
	</div>
</div>
</div>
<?php  
get_footer(); ?>
