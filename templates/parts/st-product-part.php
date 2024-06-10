<?php

$st_product = $data['st_product'];
$resultProduct = $data['resultProduct'];
$prodCurrency = $data['prodCurrency'];
$vendor = $data['vendor'];
?>

<style>
.product-details-tab { font-weight: 400;}
#show-more{align-items:center;justify-content:center;font-size:24px;cursor:pointer;color:#0d2b24;transform:rotate(-90deg);}
#show-more:hover{color:#fff;background:#00000050;}
.xzoom-thumbs.single-product-image-list {flex-direction: row;flex-wrap: wrap;}
.single-product-image-list {display: flex;gap: 10px;margin-top: 15px;margin-bottom: 15px;}
#show-more, .xzoom-thumbs {display: flex;}
.xzoom-thumbs a:not(:nth-child(-n+3)) {display: none;}
#scroll-container {border: 3px solid black;	border-radius: 5px;	overflow: hidden;	display: flex; background: #00000080;}
#scroll-text {display: flex; flex: 0 0 100%; font: 400 16px/22px sans-serif;
	-moz-transform: translateX(100%);
	-webkit-transform: translateX(100%);
	transform: translateX(100%);
	-moz-animation: my-animation 15s linear infinite;
	-webkit-animation: my-animation 15s linear infinite;
	animation: my-animation 15s linear infinite;
}

/* for Firefox */
@-moz-keyframes my-animation {
  from { -moz-transform: translateX(100%); }
  to { -moz-transform: translateX(-100%); }
}

/* for Chrome */
@-webkit-keyframes my-animation {
  from { -webkit-transform: translateX(100%); }
  to { -webkit-transform: translateX(-100%); }
}

@keyframes my-animation {
  from { -moz-transform: translateX(100%); -webkit-transform: translateX(100%); transform: translateX(100%);}
  to { -moz-transform: translateX(-100%); -webkit-transform: translateX(-100%); transform: translateX(-100%);
}
</style>
<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main-product">
		<!-- PDP Custom page -->
		<div class="page-wrapper">
			<div class="outer-container">
				<div class="am-product-display-container">
					<!-- product details section -->
					<div class="row">
						<div class="col-md-12">
							<div class="product-section">
								<div class="details-box">
									<!-- product slider -->
									<div class="xzoom-container single-product-image" >
										<div class="single-product-image-container"><img src="<?php echo $st_product->primaryImageSrc->imageSrc ?>" xoriginal="<?php echo $st_product->primaryImageSrc->imageSrc ?>" id="xzoom-default single-product-image-main" class="xzoom " alt="" /></div>
										<div class="xzoom-thumbs single-product-image-list">
										
											<?php
											$imgIcon = "https://images.shoptype.com/unsafe/100x0/".urlencode($st_product->primaryImageSrc->imageSrc);
											$imgListStr = '"'.$imgIcon.'"';
											echo "<a href='{$st_product->primaryImageSrc->imageSrc}'><img src='{$imgIcon}' class='xzoom-gallery' alt='' width='80' height='80'/></a>";
											if(isset($st_product->secondaryImageSrc)){
												foreach ($st_product->secondaryImageSrc as $img) {
													$imgIcon = "https://images.shoptype.com/unsafe/100x0/".urlencode($img->imageSrc);
													$imgListStr = $imgListStr.',"'.$imgIcon.'"';
													echo "<a href='{$img->imageSrc}'><img src='{$imgIcon}' class='xzoom-gallery' alt='' width='80' height='80'/></a>";
												}
											}
											?>
										</div>
									</div>
									<!-- ends product slider -->
									<!-- product details -->
									<div class="product-info">
										<h5 class="am-product-vendor"><a href="<?php echo str_replace("{{brandId}}", $st_product->catalogId, $brandUrl); ?>"><?php echo $st_product->vendorName ?></a></h5>
										<h1 class="am-product-title"><?php echo $st_product->title ?></h1>
										<script type="application/ld+json">
										{
										  "@context": "https://schema.org/",
										  "@type": "Product",
										  "name": <?php echo json_encode($st_product->title) ?>,
										  "image": [<?php echo $imgListStr ?>],
										  "description": <?php echo json_encode($st_product->description) ?>,
										  "sku": "<?php echo $st_product->id ?>",
										  "mpn": "<?php echo $st_product->sourceId ?>",
										  "brand": {
											"@type": "Brand",
											"name": "<?php echo $st_product->vendorName ?>"
										  },
										  "offers": {
											"@type": "Offer",
											"url": "<?php echo "https://".$domain.str_replace("{{productId}}",$st_product->id, $trimmed_productUrl) ?>",
											"priceCurrency": "<?php echo $st_product->variants[0]->discountedPriceAsMoney->currency ?>",
											"price": <?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?>,
											"priceValidUntil": "2024-11-20",
											"itemCondition": "https://schema.org/NewCondition",
											"availability": "https://schema.org/InStock"
										  }
										}
										</script>
										<h4 class="am-product-price"><span class="currency-symbol"><?php echo $prodCurrency ?></span><span id="productprice"><?php echo number_format($st_product->variants[0]->discountedPrice, 2) ?></span></h4>
										<div class="options-container">
											<div class="single-option">
												<form method="post" class="single-option" id="varientform">
													<?php if (count($st_product->options) > 0) {
														foreach ($st_product->options as $optionName) {
															if ($optionName->name != 'title') { ?>
																<div>
																	<div class="product-option-text">
																		<h4> <?php echo "$optionName->name "; ?></h4>
																	</div>
																	<div class="custom-select">
																		<div class="form-group">
																			<select name="<?php echo $optionName->name ?>" id="<?php echo $optionName->name ?>" class="form-control product-option-select" onchange="varientChang()">
																				<?php foreach ($optionName->values as $optionValue) {
																					echo '<option value="' . htmlspecialchars($optionValue) . '">' . $optionValue . '</option>';
																				}
																				?>
																			</select>
																		</div>
																	</div>
																</div>
													<?php }
														}
													} ?>
												</form>
											</div>
										</div>
												<div class="addToCart-container">
													<div class="add-box">
														<span>
															Quantity
														</span>
														<div>
															<div class="btn-minus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)<1?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)-1">-</div>
															<input class="product-quantity am-add-cart-quantity" type="number" id="quantity" name="quantity" value="1" max=""/>
															<div class="btn-plus" onclick="document.querySelector('#quantity').value=parseInt(document.querySelector('#quantity').value)>(document.getElementById('quantity').max)?parseInt(document.querySelector('#quantity').value):parseInt(document.querySelector('#quantity').value)+1">+</div>
														</div>
													</div>
													<div class="addButton-container">
														<button class="btn btn-standard am-product-add-cart-btn" role="button" onclick="shoptype_UI.addToCart(this,false)" variantid="<?php echo $st_product->variants[0]->id ?>" variantName='<?php echo json_encode($st_product->variants[0]->variantNameValue) ?>' productid="<?php echo $st_product->id ?>" vendorid="<?php echo $st_product->catalogId ?>" quantityselect=".am-add-cart-quantity">add to cart</button>

<?php 
                                                    if(is_user_logged_in())
                                                    {
                                                    $user = wp_get_current_user();
                                                    $user = get_userdata( $user->ID  );
                                                    $iscoseller=in_array( 'coseller', (array) $user->roles); 
                                                    }
                                                    else
                                                    {
                                                        $iscoseller=false;
                                                    }
                                                if(get_option('manage_coseller')== 1 || $iscoseller || current_user_can( 'manage_options' )) 
                                                    {?>
                                                <button type="button" class="btn btn-standard cosell-btn am-cosell-btn" onclick="shoptype_UI.showCosell(null)" >Cosell and Earn</button>

												<?php } ?>

													</div>
												</div>
												<?php if(isset($meta_fields["Promo Offers"]) && !empty($meta_fields["Promo Offers"])){ ?>
													<div id="scroll-container">
													  <div id="scroll-text"><?php echo $meta_fields["Promo Offers"] ?><div>
													</div>
												<?php } ?>
										
										
									</div>
									<!-- ends product details -->

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End PDP Custom page -->
	</main><!-- #main -->
</div><!-- #primary -->
	<div class="product-details-tab outer-container">
		<div class="row">
			<div class="col-md-12">
				<div class="custom-tabs">
					<ul class="tabs product-details-tabs">
						<li class="product-details-tab active" rel="description">Product Details</li>
						<?php if(isset($meta_fields["Description"])){ ?>
							<li class="product-details-tab" rel="additional_information">About <?php echo $vendorName ?></li>
						<?php } ?>
						<?php if(isset($meta_fields["Shipping Policy"])){ ?>
							<li class="product-details-tab" rel="shipping">Shipping Policy</li>
						<?php } ?>
						<?php if(isset($meta_fields["Return & Refund Policy"])){ ?>
							<li class="product-details-tab" rel="returns">Return &amp; Refund Policy</li>
						<?php } ?>
					</ul>
					<div class="tab_container product-details-content">
						<h3 class="d_active tab_drawer_heading product-details-tab" rel="description">Product Details</h3>
						<div id="description" class="tab_content am-product-description">
							<div><?php 
								$description = do_shortcode(wpautop($st_product->description));
								echo '<div class="product-description">' . $description . '</div>';
								?>
							</div>
						</div>
						<!-- #tab1 -->
						<?php if(isset($meta_fields["Description"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="additional_information">Additional Information</h3>
							<div id="additional_information" class="tab_content am-product-description">
								<p><? echo $meta_fields["Description"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab2 -->
						<?php if(isset($meta_fields["Shipping Policy"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="shipping">Shipping Policy</h3>
							<div id="shipping" class="tab_content am-product-description">
								<p><? echo $meta_fields["Shipping Policy"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab3 -->
						<?php if(isset($meta_fields["Return & Refund Policy"])){ ?>
							<h3 class="tab_drawer_heading product-details-tab" rel="returns">Return &amp; Refund Policy</h3>
							<div id="returns" class="tab_content am-product-description">
								<p><? echo $meta_fields["Return & Refund Policy"] ?></p>
							</div>
						<?php } ?>
						<!-- #tab4 -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row-blogs outer-container">
		<div class="col-md-12">
			<div class="custom-tabs">
				<div>
					<ul class="tabs product-details-tabs">
						<li class="product-details-tab active" rel="description">You may also like</li>
					</ul>
				</div>	
			</div>
		</div>
	</div>
	<div class="row-blogs outer-container">
		<div count="8" imageSize="400x0" class="products-container" inStock="true">
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
	</div>

<!-- PDP PAGE = PLUS/MINUS -->
<script>
	jQuery(function($) {

		$(document).ready(function() {
			//-- Click on QUANTITY
			
			$(".tab_content").hide();
			$(".tab_content:first").show();

			/* if in tab mode */
			$("ul.tabs li").click(function() {

				$(".tab_content").hide();
				var activeTab = $(this).attr("rel");
				$("#" + activeTab).fadeIn();

				$("ul.tabs li").removeClass("active");
				$(this).addClass("active");

				$(".tab_drawer_heading").removeClass("d_active");
				$(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

			});
			/* if in drawer mode */
			$(".tab_drawer_heading").click(function() {

				$(".tab_content").hide();
				var d_activeTab = $(this).attr("rel");
				$("#" + d_activeTab).fadeIn();

				$(".tab_drawer_heading").removeClass("d_active");
				$(this).addClass("d_active");

				$("ul.tabs li").removeClass("active");
				$("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
			});
		});
	});
			

</script>
<script>
	const product = <?php echo $resultProduct; ?>;

	var variantsJson = product.products[0].variants;
	var productImages = {};
	productImages[product.products[0].primaryImageSrc.id] = product.products[0].primaryImageSrc.imageSrc;
	if(product.products[0].secondaryImageSrc){
		for (var i = 0; i < product.products[0].secondaryImageSrc.length; i++) {
			productImages[product.products[0].secondaryImageSrc[i].id] = product.products[0].secondaryImageSrc[i].imageSrc;
		}
	}
	
	var json = {};
	var variantquntity;

	function varientChang() {
		var variantSelected = false;
		var varients = document.getElementsByClassName("product-option-select");
		var addtocartbtn = document.querySelector(".am-product-add-cart-btn");
		var addtocart = document.querySelector(".addToCart-container");
		for (var i = 0; i < varients.length; i++) {
			json[varients[i].getAttribute('id')] = varients[i].value;
		}
		$(".onadd-box > div > input").val(1);
		for (var key in variantsJson) {
			var obj1 = variantsJson[key]['variantNameValue'];
			if (isVariantSame(obj1,json)||Object.keys(json)==0) {
				variantSelected = true;
				var productprice = variantsJson[key]['discountedPrice'];
				var productCommission = variantsJson[key].discountedPriceAsMoney.amount * product.products[0].productCommission.percentage / 100;
				variantquntity=variantsJson[key]['quantity'];
				document.getElementById("quantity").max =variantquntity;
				jQuery("#quantity").prop('max',variantquntity);
				if(variantquntity<=0)
				{
					variantSoldOut(addtocart,addtocartbtn);
				}else{
					variantAvailable(addtocart,addtocartbtn);
				}
				
				if(variantsJson[key].imageIds && productImages[variantsJson[key].imageIds[0]]){
				   document.querySelector(".single-product-image-container img").src = productImages[variantsJson[key].imageIds[0]];
				}
				
				var cosellBtn = document.querySelector(".am-cosell-btn");
				cosellBtn.innerHTML = cosellBtn.innerHTML.replace(/(\d*\.)?\d+/g,productCommission.toFixed(2));
				addtocartbtn.setAttribute("variantid", variantsJson[key]['id']);
				addtocartbtn.setAttribute("variantname", JSON.stringify(variantsJson[key]['variantNameValue']));
				document.getElementById("productprice").innerHTML = productprice;
			}
		}
		if(!variantSelected){
			variantSoldOut(addtocart,addtocartbtn);
			addtocartbtn.setAttribute("variantid", "soldout");
		}
	}

	function variantSoldOut(container, button){
		container.style.opacity='60%';
		document.getElementById("quantity").max =1;
		document.getElementById("quantity").disabled = true;
		button.style.pointerEvents = "none";
		if (!document.getElementById("soldOut")) {
			jQuery("<p id='soldOut' style='color:red;font-weight:600;text-align: center;margin-bottom:20px;font-size:18px'> Sold Out</p>").insertAfter(".addToCart-container");
		}
	}

	function variantAvailable(container, button){
		container.style.opacity='';
		document.getElementById("quantity").disabled = false;
		button.style.pointerEvents = "";
		if (document.getElementById("soldOut")){
			document.getElementById('soldOut').remove();
		} 
	}

	function isVariantSame(variant1, variant2){
		return Object.keys(variant1).every(key=>variant2.hasOwnProperty(key)&&variant2[key]===variant1[key]);
	}
	window.onload = varientChang;
</script>


<script type="text/javascript" src="<?php echo untrailingslashit( plugin_dir_url( __FILE__ ) ) ;?>/js/imageslider.js"></script>
<script>
	jQuery(".xzoom, .xzoom-gallery").xzoom({
		tint: '#333',
		Xoffset: 15
	});
</script>

<script>
// Get the container element
const container = document.querySelector('.xzoom-thumbs.single-product-image-list');

// Create a down arrow button element
const downArrowBtn = document.createElement('button');
downArrowBtn.setAttribute('id', 'show-more');
downArrowBtn.innerHTML = '&#x25BC;';

// Add the down arrow button element to the container element
container.appendChild(downArrowBtn);

const showMoreButton = document.getElementById("show-more");
const thumbLinks = document.querySelectorAll(".xzoom-thumbs a");

showMoreButton.addEventListener("click", () => {
  thumbLinks.forEach(link => link.style.display = "inline-block");
  showMoreButton.style.display = "none";
});

if (thumbLinks.length > 3) {
  const showMoreArrow =document.getElementById("show-more");  showMoreArrow.addEventListener("click", () => {
    thumbLinks.forEach(link => link.style.display = "inline-block");
    showMoreArrow.style.display = "none";
  });
}

</script>