jQuery(document).ready(function ($) {
	// ================= P A G E  L O A D E R =================
	$(".loader").fadeOut("3000");

	// ================= S C R O L L   T O   T O P =================
	$(function () {
		$(document).on("scroll", function () {
			if ($(window).scrollTop() > 100) {
				$(".scroll-top-wrapper").addClass("show");
			} else {
				$(".scroll-top-wrapper").removeClass("show");
			}
		});
		$(".scroll-top-wrapper").on("click", scrollToTop);
	});
	function scrollToTop() {
		verticalOffset = typeof verticalOffset != "undefined" ? verticalOffset : 0;
		element = $("body");
		offset = element.offset();
		offsetTop = offset.top;
		$("html, body").animate(
			{
				scrollTop: offsetTop,
			},
			500,
			"linear"
		);
	}
	// ================= M O B I L E  S L I D E  M E N U =================
	(function slideMenu() {
		var trigger = ".trigger"; // the triger class
		var showslide = "menu-active"; // the active class that is added to the body
		var body = "body"; //body of element
		var close = ".slide-close"; // the class that closes the slide
		var slideout = "slideout"; // the class to show the slide
		var mainId = "#slide-menu"; //main wrapper ID
		//open the slide and add class to body to use with css
		jQuery(trigger).click(function () {
			jQuery(body).toggleClass(showslide);
			jQuery(mainId).toggleClass(slideout);
		});
		//close the slide
		jQuery(close).click(function () {
			jQuery(body).removeClass(showslide);
			jQuery(mainId).removeClass(slideout);
		});
	}.call(this));
	// ================= S E L E C T  D R O P D O W N =================
	var x, i, j, l, ll, selElmnt, a, b, c;
	/* Look for any elements with the class "custom-select": */
	x = document.getElementsByClassName("custom-select-box");
	l = x.length;
	for (i = 0; i < l; i++) {
		selElmnt = x[i].getElementsByTagName("select")[0];
		ll = selElmnt.length;
		/* For each element, create a new DIV that will act as the selected item: */
		a = document.createElement("DIV");
		a.setAttribute("class", "select-selected");
		a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
		x[i].appendChild(a);
		/* For each element, create a new DIV that will contain the option list: */
		b = document.createElement("DIV");
		b.setAttribute("class", "select-items select-hide");
		for (j = 1; j < ll; j++) {
			/* For each option in the original select element,
        create a new DIV that will act as an option item: */
			c = document.createElement("DIV");
			c.innerHTML = selElmnt.options[j].innerHTML;
			c.addEventListener("click", function (e) {
				/* When an item is clicked, update the original select box,
            and the selected item: */
				var y, i, k, s, h, sl, yl;
				s = this.parentNode.parentNode.getElementsByTagName("select")[0];
				sl = s.length;
				h = this.parentNode.previousSibling;
				for (i = 0; i < sl; i++) {
					if (s.options[i].innerHTML == this.innerHTML) {
						s.selectedIndex = i;
						h.innerHTML = this.innerHTML;
						y = this.parentNode.getElementsByClassName("same-as-selected");
						yl = y.length;
						for (k = 0; k < yl; k++) {
							y[k].removeAttribute("class");
						}
						this.setAttribute("class", "same-as-selected");
						break;
					}
				}
				h.click();
			});
			b.appendChild(c);
		}
		x[i].appendChild(b);
		a.addEventListener("click", function (e) {
			/* When the select box is clicked, close any other select boxes,
        and open/close the current select box: */
			e.stopPropagation();
			closeAllSelect(this);
			this.nextSibling.classList.toggle("select-hide");
			this.classList.toggle("select-arrow-active");
		});
	}

	function closeAllSelect(elmnt) {
		/* A function that will close all select boxes in the document,
      except the current select box: */
		var x,
			y,
			i,
			xl,
			yl,
			arrNo = [];
		x = document.getElementsByClassName("select-items");
		y = document.getElementsByClassName("select-selected");
		xl = x.length;
		yl = y.length;
		for (i = 0; i < yl; i++) {
			if (elmnt == y[i]) {
				arrNo.push(i);
			} else {
				y[i].classList.remove("select-arrow-active");
			}
		}
		for (i = 0; i < xl; i++) {
			if (arrNo.indexOf(i)) {
				x[i].classList.add("select-hide");
			}
		}
	}
	/* If the user clicks anywhere outside the select box,
    then close all select boxes: */
	document.addEventListener("click", closeAllSelect);

	// ================= H O M E P A G E  P R O D U C T S  S L I D E R ( M O B I L E ) =================
	document.addEventListener("amProductsLoaded", function(){
			console.log("amProductsLoaded");
			$(".m-product").slick({
				dots: true,
				infinite: true,
				arrows: false,
				speed: 300,
				slidesToScroll: 1,
				slidesToShow: 15,
				centerMode: true,
				centerPadding: "80px",
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							infinite: true,
							dots: true,
						},
					},
					{
						breakpoint: 600,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
						},
					},
					{
						breakpoint: 480,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
						},
					},
				],
			});
	});
	// ================= H O M E P A G E  B R A N D S  S L I D E R ( D E S K T O P, M O B I L E ) =================
	document.addEventListener("amBrandsLoaded", function(){
		$(".dBrandSlider").slick({
			dots: true,
			infinite: true,
			arrows: false,
			speed: 300,
			slidesToScroll: 1,
			slidesToShow: 4,
			centerMode: true,
			centerPadding: "80px",
			responsive: [
				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						infinite: true,
						dots: true,
					},
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
					},
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
					},
				},
			],
		});
	});
	// ================= H O M E P A G E  H E R O  S L I D E R ( M O B I L E ) =================
	$(".m-heroBanner").slick({
		dots: true,
		infinite: true,
		arrows: false,
		speed: 300,
		slidesToScroll: 1,
		slidesToShow: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true,
				},
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
		],
	});
	// ================= H O M E P A G E  C O S E L L E R S ( M O B I L E ) =================
	$(".m-cosellers").slick({
		dots: false,
		infinite: true,
		arrows: false,
		speed: 300,
		slidesToScroll: 1,
		slidesToShow: 1,
		centerMode: true,
		centerPadding: "40px",
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true,
				},
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1,
				},
			},
		],
	});
	// ================= H O M E P A G E  O U R  C O M M U N I T I E S ( D E S K T O P, M O B I L E ) =================
	$(".communities").slick({
		dots: false,
		infinite: true,
		arrows: false,
		speed: 300,
		slidesToScroll: 1,
		slidesToShow: 4,
		centerMode: true,
		centerPadding: "80px",
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true,
				},
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
		],
	});

	// ================= T H U M B N A I L  S L I D E R  ( P D P  P A G E  -  D E S K T O P ) =================
	$(".slider-content").slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		fade: false,
		infinite: false,
		speed: 1000,
		asNavFor: ".slider-thumb",
		arrows: false,
	});
	$(".slider-thumb").slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		asNavFor: ".slider-content",
		dots: false,
		centerMode: false,
		focusOnSelect: true,
		arrows: false
	});

	// ================= Q U A N T I T Y  I N P U T  F I E L D  ( P D P  P A G E  -  D E S K T O P ) =================
	var input = document.querySelector("#quantity");
	var btnminus = document.querySelector(".qtyminus");
	var btnplus = document.querySelector(".qtyplus");

	if (input !== undefined && btnminus !== undefined && btnplus !== undefined && input !== null && btnminus !== null && btnplus !== null) {
		var min = Number(input.getAttribute("min"));
		var max = Number(input.getAttribute("max"));
		var step = Number(input.getAttribute("step"));

		function qtyminus(e) {
			var current = Number(input.value);
			var newval = current - step;
			if (newval < min) {
				newval = min;
			} else if (newval > max) {
				newval = max;
			}
			input.value = Number(newval);
			e.preventDefault();
		}

		function qtyplus(e) {
			var current = Number(input.value);
			var newval = current + step;
			if (newval > max) newval = max;
			input.value = Number(newval);
			e.preventDefault();
		}

		btnminus.addEventListener("click", qtyminus);
		btnplus.addEventListener("click", qtyplus);
	}
	// ================= B R A N D S  S L I D E R  O N  B R A N D S - H O M E ( D E S K T O P, M O B I L E ) =================
	$(".brandsSlider").slick({
		dots: true,
		infinite: true,
		arrows: false,
		speed: 300,
		slidesToScroll: 1,
		slidesToShow: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
					dots: true,
				},
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				},
			},
		],
	});

});
