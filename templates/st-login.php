<?php
/*
 * Template name: Shoptype Login
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */

get_header();
?>

    <div id="primary" class="content-area bb-grid-cell">
        <main id="main" class="site-main">
        	<div id="login-form" class="login-form"></div>
        </main><!-- #main -->
    </div><!-- #primary -->

	<script type="text/javascript">
		var url = new URL(window.location.href);
		var retUrlStr = url.searchParams.get("redirectUrl")??"<?php echo get_home_url(); ?>";
		var refTid = (url.searchParams.has("tid")&&url.searchParams.get("tid"))!=""?url.searchParams.get("tid"):null;
		refTid = refTid??sessionStorage["st-ctid"];
		var refCode = refTid?"":"<?php global $stRefcode; echo $stRefcode; ?>";
		var returnUrl = new URL(retUrlStr);
		 	const renderForm = () => {
			stLoginHandler.renderSTLoginForm(
				"login-form",
				{
				name: "<?php global $siteName; echo $siteName; ?>",
				url: "<?php global $siteUrl; echo $siteUrl; ?>",
				rid: refCode,
				tid: refTid,
				env:beta
				},
				(appRes) => {
					switch (appRes.app.event) {
						case "form rendered":
						  break;
						case "modal opened":
						  break;
						case "modal closed":
						  break;
						case "modal closed by user":
						  break;
						case "login success":
						  stLoginHandler.closeSTLoginModal();
						  window.location.replace( insertParam("token", appRes.user.token,returnUrl));
						  break;
						case "login failed":
						  break;
						case "sign-up success":
						  stLoginHandler.closeSTLoginModal();
						  window.location.replace( insertParam("token", appRes.user.token,returnUrl));
						  break;
						case "sign-up failed":
							break;
					  }
				}
			);
		};
		
		function insertParam(key, value, url) {
			key = encodeURIComponent(key);
			value = encodeURIComponent(value);

			var kvp = url.search.substr(1).split('&');
			let i=0;

			for(; i<kvp.length; i++){
				if (kvp[i].startsWith(key + '=')) {
					let pair = kvp[i].split('=');
					pair[1] = value;
					kvp[i] = pair.join('=');
					break;
				}
			}

			if(i >= kvp.length){
				kvp[kvp.length] = [key,value].join('=');
			}

			// can return this or...
			let params = kvp.join('&');

			// reload page with new params
			url.search = params;
			return url;
		}

		function renderLogin(){
			if(typeof stLoginHandler !== "undefined"){
				renderForm();
			}else{
				setTimeout(renderLogin, 200);
			}
		}
		renderLogin();
	</script>

<?php
get_footer();
