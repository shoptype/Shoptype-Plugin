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
		var urlStr = url.searchParams.get("url")??"<?php echo get_home_url(); ?>";
		var returnUrl = new URL(urlStr);
		const renderForm = () => {
			stLoginHandler.renderSTLoginForm(
				"login-form",
				{
				name: "<?php global $siteName; echo $siteName; ?>",
				url: "<?php global $siteUrl; echo $siteUrl; ?>",
				rid: "<?php global $stRefcode; echo $stRefcode; ?>",
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
						  window.location.replace(returnUrl.href + (returnUrl.search==''?'?':returnUrl.search+"&")+"token="+appRes.user.token);
						  break;
						case "login failed":
						  break;
						case "sign-up success":
						  stLoginHandler.closeSTLoginModal();
						  window.location.replace(returnUrl.href + (returnUrl.search==''?'?':returnUrl.search+"&")+"token="+appRes.user.token);
						  break;
						case "sign-up failed":
							break;
					  }
				}
			);
		};

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
