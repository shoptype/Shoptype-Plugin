<?php
/**
* Description: Shoptype Settings
* Version: 1.0
**/

class Shoptype_Settings {
    /* Create blank array */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'settings_page' ) );
        add_action( 'admin_init', array( $this, 'setup_init' ) );
    }
    public function settings_page() {
        //Create the menu item and page
        $parent_slug = "shoptype_settings";
        $page_title = "Shoptype Settings Page";
        $menu_title = "Shoptype Settings";
        $capability = "manage_options";
        $slug = "shoptype_settings";
        $callback = array( $this, 'settings_page_content' );
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback );
        //add_submenu_page($page_title, $menu_title, $capability,$slug , $callback,$slug );
	
        flush_rewrite_rules();
    
    }
    
    public static function add_network_settings($token){
        try {
            global $stBackendUrl;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "{$stBackendUrl}/networks");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: {$token}"
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $st_network = json_decode($result);
            echo "<div id='networkData' nid='{$st_network->network->id}' pid='{$st_network->platforms[0]->id}' vid='{$st_network->platforms[0]->vendor_ids[0]}' url='{$st_network->platforms[0]->url}'></div>";
            return $st_network;
        }
        catch(Exception $e) {
        }
    }
    
    public static function get_network_token($token){
        try {
            $postData = array(
                'userType' => 'network'
            );
            global $stBackendUrl;
            $ch = curl_init("{$stBackendUrl}/authenticate");
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: '.$token,
                    'Content-Type: application/json'
                ),
                CURLOPT_POSTFIELDS => json_encode($postData)
            ));
            $response = curl_exec($ch);
            if($response === FALSE){
                die(curl_error($ch));
            }
            $responseData = json_decode($response);
            curl_close($ch);
            return $responseData->token;
        }
        catch(Exception $e) {
        }
    }
    /* Create the page*/
    public function settings_page_content() { 
        global $stBackendUrl;?>
        <div class="wrap">
            <h2> Shoptype Settings </h2>
            <form method="post" action="options.php">
                <?php   
                    if(isset($_COOKIE["stToken"]) && !empty($_COOKIE["stToken"])) {
                        $token=$_COOKIE["stToken"];
                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "{$stBackendUrl}/me");
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                "Authorization: {$token}"
                            ));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($ch);
                            curl_close($ch);
                        }
                        catch(Exception $e) {
                        }
                        if( !empty( $result ) ) {                           
                            $st_user = json_decode($result);
                            echo "<div onclick='populateShoptypeData()' class='st-fetch-data-btn'>Fetch Data From {$st_user->name}</div>";
                            $n_token = self::get_network_token($token);
                            self::add_network_settings($n_token);
                        }
                    } else {
                        echo "<a href='/login' class='st-login-btn'>Login to Shoptype</a>";
                    }
                    settings_fields("shoptype_settings");
                    do_settings_sections("shoptype_settings");
                    $options = get_option( 'myshopUrl' );

                    $storeJson = str_replace("shop","wp-json/shoptype/v1/shop",$options);


                    $storeValue = empty($storeJson)?"": "[awake_products loadmore=\"true\" my_shop=\"{$storeJson}\" per_row=\"12\" container_classes=\"grid-two-by-two\"]";

                    echo "<table class=\"form-table\"><tbody><tr><th>My Shop Shortcode: </th><td><input name=\"myshopShortcode\" id=\"myshopShortcode\" type=\"text\" value='$storeValue'></td></tr></tbody></table>";
                    flush_rewrite_rules();
                    submit_button();
                ?>
            </form>
            <script>
                var myToken = "<?php echo $n_token ?>";

                function genStApiKey(){
                    let data={
                        "name":"ST-plugin-" + window.location.hostname,
                        "scopes":["checkout","cart","Shoptype Go"],
                        "allowed_domain": window.location.hostname
                    };
                    let postHeader = {
                        method:'post',
                        'headers': {
                            'Content-Type': 'application/json',
                            'Authorization': myToken
                        },  
                        body: JSON.stringify(data)
                    };
                    fetch("<?php echo $stBackendUrl; ?>/api-keys", postHeader)
                        .then(response=>response.json())
                        .then(apiJson=>{
                            document.getElementById("shoptype_api_key").value = apiJson.apiKey;
                        });
                }


                function populateShoptypeData(){
                    var networkdiv = document.getElementById("networkData");
                    document.getElementById("platformID").value = networkdiv.getAttribute("pid");
                    document.getElementById("vendorId").value = networkdiv.getAttribute("vid");
                    document.getElementById("networkId").value = networkdiv.getAttribute("nid");
                    genStApiKey();
                }
            </script>
    <?php }
    /* Setup section_callback */
    public function section_callback( $arguments ) {
        /* Set up input*/
        switch( $arguments['id'] ){
            case "shoptype_api_key" :
                echo "Please enter the API key found in the 'Network Operator' profile menu.";
                break;
            case "platformID":
                echo "Please enter the Platform ID found in the 'Network Operator' profile menu. ";
            break;
        }
    }
    public function setup_init() {
        register_setting("shoptype_settings", "shoptype_api_key");
        register_setting("shoptype_settings", "platformID");
        register_setting("shoptype_settings", "vendorId");
        register_setting("shoptype_settings", "networkId");
		register_setting("shoptype_settings", "refCode");
		register_setting("shoptype_settings", "cartCountMatch");
        register_setting("shoptype_settings", "loginUrl");
        register_setting("shoptype_settings", "stDefaultCurrency");
        register_setting("shoptype_settings", "ServerName");
        register_setting("shoptype_settings", "myshopURL");
        register_setting("shoptype_settings", "productsInGroup");
        $serverName=$_SERVER['SERVER_NAME'];
        


        add_settings_section("shoptype_settings", "Network Settings: ", array($this, 'section_callback'), "shoptype_settings");
        add_settings_field( 'shoptype_api_key', 'ST API Key: ', array( $this, 'field_callback' ), 'shoptype_settings', 'shoptype_settings', array("id"=>"shoptype_api_key","name"=>"ST API Key") );
        add_settings_field("platformID", "ST Platform ID: ", array($this, 'field_callback'), "shoptype_settings", 'shoptype_settings', array("id"=>"platformID","name"=>"ST Platform ID"));
        add_settings_field("vendorId", "ST Vendor ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"vendorId","name"=>"ST Vendor ID") );
        add_settings_field("networkId", "ST Network ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"networkId","name"=>"ST Network ID") );
		add_settings_field("refCode", "ST Referral Code: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"refCode","name"=>"ST Referral Code") );
        add_settings_field("cartCountMatch", "ST Cart Count Match String: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"cartCountMatch","name"=>"ST Cart Count Match") );
        add_settings_field("loginUrl", "ST Login Page URL: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"loginUrl","name"=>"ST Login Page URL") );
        add_settings_field("stDefaultCurrency", "ST Default Currency: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"stDefaultCurrency","name"=>"ST Default Currency") );
        add_settings_field("ServerName", "ServerName: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"ServerName","name"=>"ServerName","value"=>"values123") );                        
        add_settings_section( 'buddypress_groups_products', 'Buddypress group settings', array( $this, 'section_callback' ), 'shoptype_settings' );
        add_settings_field("productsInGroup", "Display products in Buddypress groups", array( $this, 'sandbox_checkbox_element_callback' ), "shoptype_settings", "buddypress_groups_products", array("id"=>"productsInGroup","name"=>"productsInGroup"));               
        add_settings_section( 'channel_champion', 'Channel Champion', array( $this, 'section_callback' ), 'shoptype_settings' );
        add_settings_field("myshopUrl", "My Shop URL: ", array( $this, 'field_callback' ), "shoptype_settings", "channel_champion", array("id"=>"myshopURL","name"=>"My Shop URL:") );   
        
        //Setting defult value for server name
        update_option( 'ServerName',$serverName );
        
    }
    /* Create input fields*/
    public function field_callback ( $arguments ) {
        echo "<input name=\"{$arguments['id']}\" id=\"{$arguments['id']}\" type=\"text\" value=\"" .get_option($arguments['id'])."\"\>";

    }
/* Create dropdown fields*/
function sandbox_checkbox_element_callback() {

    $options = get_option( 'productsInGroup' );
    
    $checked = ( isset($options['checkbox_example']) && $options['checkbox_example'] == 1) ? 1 : 0;
    
    $html = '<input type="checkbox" id="checkbox_example" name="productsInGroup[checkbox_example]" value="1"' . checked( 1, $checked, false ) . '/>';    
    $html .= '<label for="checkbox_example"> </label>';

    echo $html;
    echo '<script>jQuery("#ServerName").prop("disabled", true);</script>';
        
    
}
}





$options = get_option( 'productsInGroup' );

if ( ! empty( $options['checkbox_example'] ) ) {
    
     // Checkbox checked
     /**
 * Add custom sub-tab on groups page.
 */
function buddypress_custom_group_tab() {

	// Avoid fatal errors when plugin is not available.
	if ( ! function_exists( 'bp_core_new_subnav_item' ) ||
		 ! function_exists( 'bp_is_single_item' ) ||
		 ! function_exists( 'bp_is_groups_component' ) ||
		 ! function_exists( 'bp_get_group_permalink' ) ) {

		return;

	}

	// Check if we are on group page.
	if ( bp_is_groups_component() && bp_is_single_item() ) {

		global $bp;

		// Get current group page link.
		$group_link = bp_get_group_permalink( $bp->groups->current_group );

		// Tab args.
		$tab_args = array(
			'name'                => esc_html__( 'Products', 'default' ),
			'slug'                => 'products',
			'screen_function'     => 'products_screen',
			'position'            => 0,
			'parent_url'          => $group_link,
			'parent_slug'         => $bp->groups->current_group->slug,
			'default_subnav_slug' => 'products',
			'item_css_id'         => 'products-main',
			'show_tab'			  => 'anyone',
			'visibility'		  => 'public',
			'user_has_access'	  => 'anyone',
		);

		// Add sub-tab.
		bp_core_new_subnav_item( $tab_args, 'groups' );
	}
}

add_action( 'bp_setup_nav', 'buddypress_custom_group_tab' );

/**
 * Set template for new tab.
 */
function products_screen() {
	// Add title and content here - last is to call the members plugin.php template.
	add_action( 'bp_template_title', 'custom_group_tab_title' );
	add_action( 'bp_template_content', 'custom_group_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );
}

/**
 * Set title for custom tab.
 */
function custom_group_tab_title() {
	echo esc_html__( 'Products', 'default_content' );
}

/**
 * Display content of custom tab.
 */
function custom_group_tab_content() {
	$currentGroup = bp_get_current_group_name();
	$currentGroup = str_replace(" ", "%20", $currentGroup);
	echo do_shortcode( '[awake_products for_listing="1" slider="0" tags="'.$currentGroup.'" imagesize="200x200" product_classes="groups-product single-product"]' );

}

/**
 * Set default tab for group.
 *
 * @param  string $default_tab Slug of default tab.
 * @return string
 */
function buddyboss_groups_default_extension( $default_tab ) {
	return 'products'; // Last part of the URL.
}

add_filter( 'bp_groups_default_extension', 'buddyboss_groups_default_extension', 10 );
} else {
     // Not checked
}


new Shoptype_Settings();

function shoptypeSettings() {
    global $stBackendUrl;
    $stBackendUrl = "https://backend.shoptype.com";
	global $stPlatformId;
	$stPlatformId = get_option('platformID');
	global $stVendorId;
	$stVendorId = get_option('vendorId');
	global $stApiKey;
	$stApiKey = get_option('shoptype_api_key');
	global $stRefcode;
	$stRefcode = get_option('refCode');
    global $cartCountMatch;
    $cartCountMatch = get_option('cartCountMatch');
    global $loginUrl;
    $loginUrl = get_option('loginUrl');
    global $stDefaultCurrency;
    $stDefaultCurrency = get_option('stDefaultCurrency');
    global $productUrl;
    $productUrl = "/products/{{productId}}/?tid={{tid}}";
    global $brandUrl;
    $brandUrl = "/brands/{{brandId}}";
	global $stCurrency;
	$stCurrency["USD"] = "$";
    $stCurrency["INR"] = "₹";
    global $productsInGroup;
    $productsInGroup = get_option('productsInGroup');
    global $myshopUrl;
    $myshopUrl = get_option('myshopURL');
    flush_rewrite_rules();
}












add_action( 'after_setup_theme', 'shoptypeSettings' );