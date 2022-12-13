<?php
/* Template Name: Shoptype Products Listing Template */
get_header();
$tags = "";
if (isset($_GET) && isset($_GET['tags']) && !empty($_GET['tags'])) $tags = $_GET['tags']; ?>
<!-- ===================================== -->
<div class="main-content m-body">
	<!-- ================= PRODUCT SECTION 01 ================= -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="section-header">
						<h1 class="section-title">The Best Selling Products</h1>
					</div>
                    <div class="no-products-found">
                        <h3>No Products found matching your search criteria.</h3>
                    </div>
					<!-- ===== best selling products for desktop and mobile ===== -->
					<!-- <div class="product-wrapper"> -->
						<?php echo do_shortcode( '[awake_products for_listing="1" slider="0" tags="'.$tags.'" product_classes="product-container single-product"]' ); ?>
					<!-- </div> -->
					<!-- ===== end best selling products for desktop and mobile ===== -->
				</div>
			</div>
		</div>
	</div>
	<!-- ================= END PRODUCT SECTION 01 ================= -->
	<!-- ================= FILTER ================= -->
    <div class="filtermenu-container">
		<input id="menu1Toggle" class="filterMenu" type="checkbox"/>
		<div class="page-wrapper">
			<label class="menu-toggle menu-1-toggle" for="menu1Toggle"><span class="menu-label"><img src="<?php echo get_template_directory_uri(); ?>/img/filterIcon-open.svg" alt=""></span><span class="close-label"><img src="<?php echo get_template_directory_uri(); ?>/img/filterIcon-close.svg" alt=""></span></label>
			<div class="sidebar menu-1">
				<h4 class="filter-title">Filter Menu</h4>
				<!-- location section -->
				<div class="location-box">
					<div class="location">
						<img src="<?php echo get_template_directory_uri(); ?>/img/filtermenu-location-icon.svg" alt="">
						<p>Location: <span>Santa Clara, CA</span></p>
					</div>
					<div class="location-change-button">
						<a href="javascript:void(0)">Change</a>
					</div>
				</div>
				<!-- search radius section -->
				<div class="radius-container">
					<p>Search Radius</p>
					<ul class="distance-list">
						<li>Closest</li>
						<li>10 mi</li>
						<li>25 mi</li>
						<li>50 mi</li>
						<li>All</li>
					</ul>
					<input type='range' min='1' max='5' value='1' step='1' class="n n5"/>
				</div>
                <!-- brands -->
                <div class="dropdown-box">
                    <div class="select-container">
                        <p>Brands</p>
                        <div class="select-box">
                            <div class="custom-select-box">
                                <select class="am-brands-selector brand-selector brq-filter-class" productscontainer=".products-container">
                                    <option value="">All Brands</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
    <!-- ================= END FILTER ================= -->
</div>
<?php get_footer(); ?>
<script type="text/javascript">
    $(document).ready(function($){
        // ...........................
        // Menu Select
        // ...........................
        $("#menu1Toggle").click(function(){
            // if($("#menu1Toggle").prop("checked") == true){
            // }
            if($("#menu1Toggle").prop("checked") == false){
                $("#view-filters").show();
            }
        });

        $("#view-filters").click(function(){
            $(this).hide();
            $("#menu1Toggle").prop("checked",true);
        });

        // ..............................
        // Auto select filter if applied
        // ..............................
        var tagName = "<?php echo $tags; ?>";
        if (tagName != "") {
            $(".tag-selector").val('wine');
        }

        // .................................
        // Show heading if filter selected
        // .................................
        document.addEventListener("amProductsLoaded", function(){
            $(".no-products-found").hide();
            updateSectionHeading();
            $("#menu1Toggle").prop("checked",false);
        });

        // ..................................
        // Show No Products found message
        // ..................................
        document.addEventListener('amProductsLoadFailed', function(){
            $(".brq-products-loaded").hide();
            $(".no-products-found").show();
            updateSectionHeading();
            $("#menu1Toggle").prop("checked",false);
        });
    });

    // ..............................
    // Concate section heading
    // ..............................
    function updateSectionHeading(){
        var allFilters = [];
        var brandFilter = $(".brand-selector").val();
        var tagFilter = $(".tag-selector").val();
        var categoryFilter = $(".category-selector").val();
        if(brandFilter != "" && brandFilter != null && brandFilter != undefined) {
            var selectedBrand = $( ".brand-selector option:selected" ).text();
            allFilters.push(selectedBrand);
        }
        if(tagFilter != "" && tagFilter != null && tagFilter != undefined) {
            var selectedTag = $( ".tag-selector option:selected" ).text();
            allFilters.push(selectedTag);
        }
        if(categoryFilter != "" && categoryFilter != null && categoryFilter != undefined) {
            var selectedCategory = $( ".category-selector option:selected" ).text();
            allFilters.push(selectedCategory);
        }
        if(allFilters.length > 0) {
            var sectionHeaderText = allFilters.join(", ");
            $(".section-title").text("Results for : " + sectionHeaderText);
        }
        else {
            $(".section-title").text("Marketplace");
        }
    }
</script>
<style media="screen">
    .no-products-found {
        display: none;
        border: 1px solid #ddd;
        padding: 30px;
        border-radius: 5px;
    }
    .brq-filter-class {
        width: 100%;
        height: 30px;
        color: #3B3FD9;
        border-radius: 20px;
        font-size: 16px;
        padding: 3px 18px;
        display: block !important;
    }
</style>
