/* This is the script to open the modal shoptype login window. Call the function openModal() to show the login*/
<script type="text/javascript">
	const openModal = () => {
		stLoginHandler.openSTLoginModal(
			{
				name: "us.awake.market",
				url: "https://us.awake.market",
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
					  window.location.search += "&token="+appRes.user.token;
					  break;
					case "login failed":
					  break;
					case "sign-up success":
					  stLoginHandler.closeSTLoginModal();
					  window.location.search += "&token="+appRes.user.token;
					  break;
					case "sign-up failed":
						break;
				  }
			}
		);
	};
</script>
_________________________________________________________________________________


/* This script clears the token in the cookie and the sessionStore if the user is logged out*/
<?php
function shoptypeLogout(){
	if ( !is_user_logged_in() ) {
		unset( $_COOKIE["stToken"] );
		setcookie( "stToken", '', time() - ( 15 * 60 ) );
		echo '<script>setCookie("stToken",null,0);sessionStorage.removeItem("token");sessionStorage.removeItem("userId");</script>';
	}
}
add_action('wp_head', 'shoptypeLogout');
?>

________________________________________________________________________________

<?php
/* Shoptype login: this will login the shoptype user if a user with the same email exists else it creats a user and logs them in*/
function shoptype_login(){
	$token = $_GET['token'];
	if (is_user_logged_in()) {return;}
	if( empty( $token ) ) {return;}
	
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://backend.shoptype.com/me');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   "Authorization: {$token}"
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		curl_close($ch);
	}
	catch(Exception $e) {
		return false;
	}
	if( empty( $result ) ) {return false;}
	$st_user = json_decode($result);

	$user = get_user_by( 'email', "{$st_user->email}" ); 

	if ( empty( $user ) ) {
		$user_id = wp_insert_user( array(
			'user_login' => $st_user->email,
			'user_pass' => $token,
			'user_email' => $st_user->email,
			'first_name' => $st_user->name,
			'display_name' => $st_user->name,
			'role' => 'subscriber'
		));
		$wp_user = wp_set_current_user($user_id, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		global $current_user;
		$current_user = $wp_user;
	}else{
		$wp_user = wp_set_current_user($user->ID, $st_user->email);
		wp_set_auth_cookie( $user->ID , true);
		global $current_user;
		$current_user = $wp_user;
		do_action( 'wp_login', $wp_user->user_login, $wp_user );
	}
	
	global $wp;
	$url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	header("location: $url");
};
add_action('get_header', 'shoptype_login');


//Redirect users to home after logout
function ST_redirect_after_logout(){
         wp_redirect( '/' );
         exit();
}
add_action('wp_logout','ST_redirect_after_logout');
?>