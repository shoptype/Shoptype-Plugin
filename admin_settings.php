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
            $args = array(
                'headers' => array(
                  'Authorization' => $token
                  ));
            $response = wp_remote_get("{$stBackendUrl}/networks",$args);
            $result = wp_remote_retrieve_body( $response );
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
                 $args = array(
                'body'        => $body,
                'headers'     => array(
                    'Authorization'=> $token,
                    'Content-Type'=> 'application/json'
                )
            );
                
            $response = wp_remote_post( '{$stBackendUrl}/authenticate', $args );
            $responseData = json_decode($response);
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
                            
                            $args = array(
                                'headers' => array(
                                  'Authorizatione' => $token
                                  ));
                            $response = wp_remote_get("{$stBackendUrl}/me",$args);
                            $result = wp_remote_retrieve_body( $response );
                            
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
        register_setting("shoptype_settings", "shoptypeUrlBase");
        register_setting("shoptype_settings", "loginUrl");
        register_setting("shoptype_settings", "marketUrl");
        register_setting("shoptype_settings", "myAccountUrl");
        register_setting("shoptype_settings", "stDefaultCurrency");
        register_setting("shoptype_settings", "stFilter");
        register_setting("shoptype_settings", "ServerName");
        register_setting("shoptype_settings", "myshopURL");
        register_setting("shoptype_settings", "productsInGroup");
        register_setting("shoptype_settings", "manage_coseller");
        register_setting("shoptype_settings", "restrict_myshop");
        register_setting("shoptype_settings", "addProductsInSearch");
        add_settings_section("shoptype_settings", "Network Settings: ", array($this, 'section_callback'), "shoptype_settings");
        add_settings_field( 'shoptype_api_key', 'ST API Key: ', array( $this, 'field_callback' ), 'shoptype_settings', 'shoptype_settings', array("id"=>"shoptype_api_key","name"=>"ST API Key") );
        add_settings_field("platformID", "ST Platform ID: ", array($this, 'field_callback'), "shoptype_settings", 'shoptype_settings', array("id"=>"platformID","name"=>"ST Platform ID"));
        add_settings_field("vendorId", "ST Vendor ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"vendorId","name"=>"ST Vendor ID") );
        add_settings_field("networkId", "ST Network ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"networkId","name"=>"ST Network ID") );
        add_settings_field("refCode", "ST Referral Code: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"refCode","name"=>"ST Referral Code") );
        add_settings_field("cartCountMatch", "ST Cart Count Match String: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"cartCountMatch","name"=>"ST Cart Count Match") );
        add_settings_field("shoptypeUrlBase", "ST Base URL Path: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"shoptypeUrlBase","name"=>"ST Login Page URL") );
        add_settings_field("loginUrl", "ST Login Page URL: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"loginUrl","name"=>"ST Login Page URL") );
        add_settings_field("marketUrl", "ST Market Page URL: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"marketUrl","name"=>"ST Market Page URL") );
        add_settings_field("myAccountUrl", "ST My Account Page URL: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"myAccountUrl","name"=>"ST My Account Page URL") );
        add_settings_field("stDefaultCurrency", "ST Default Currency: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"stDefaultCurrency","name"=>"ST Default Currency") );
        add_settings_field("stFilter", "Product Filter JSON: ", array( $this, 'field_textarea_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"stFilter","name"=>"Product Filter JSON") );
        add_settings_field("addProductsInSearch", "Show products in site search", array( $this, 'checkbox_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"addProductsInSearch","name"=>"addProductsInSearch"));
        add_settings_field("restrict_myshop", "Allow only users with myshop_owner role to have Myshop", array( $this, 'checkbox_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"restrict_myshop","name"=>"restrict_myshop"));
        add_settings_section( 'manage_coseller_setting', 'Coseller manage setting', array( $this, 'section_callback' ), 'shoptype_settings' );
        add_settings_field("manage_coseller", "Allow all user to cosell product", array( $this, 'checkbox_callback' ), "shoptype_settings", "manage_coseller_setting", array("id"=>"manage_coseller","name"=>"manage_coseller"));
    }
    
    /* Create input fields*/
    public function field_callback ( $arguments ) {
        echo "<input name=\"{$arguments['id']}\" id=\"{$arguments['id']}\" type=\"text\" style=\"width:100%;\" value=\"" .get_option($arguments['id'])."\"/>";
    }

    /* Create textarea fields*/
    public function field_textarea_callback ( $arguments ) {
        echo "<textarea  name=\"{$arguments['id']}\" id=\"{$arguments['id']}\" style=\"width:100%;height:300px\" >".get_option($arguments['id'])."</textarea >";
    }
    
    function checkbox_callback($arguments) {
        $field_value = get_option($arguments['id']) ? "checked" : "";
        echo "<input name=\"{$arguments['id']}\" id=\"{$arguments['id']}\" value=\"1\" type=\"checkbox\" $field_value />";
    }
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
    global $shoptypeUrlBase;
    $shoptypeUrlBase = get_option('shoptypeUrlBase');    
    global $loginUrl;
    $loginUrl = get_option('loginUrl');
    global $marketUrl;
    $marketUrl = get_option('marketUrl');
    global $myAccountUrl;
    $myAccountUrl = get_option('myAccountUrl');
    global $stFilterJson;
    $stFilterJson = get_option('stFilter');
    global $stDefaultCurrency;
    $stDefaultCurrency = get_option('stDefaultCurrency');
    global $productUrl;
    $productUrl = "/{$shoptypeUrlBase}products/{{productId}}/?tid={{tid}}";
    global $brandUrl;
    $brandUrl = "/{$shoptypeUrlBase}brands/{{brandId}}";
    global $stCurrency;
    $stCurrency["USD"] = "$";
    $stCurrency["INR"] = "₹";
    global $productsInGroup;
    $productsInGroup = get_option('productsInGroup');
    global $restrict_myshop;
    $restrict_myshop = get_option('restrict_myshop');
    global $addProductsInSearch;
    $addProductsInSearch = get_option('addProductsInSearch');
    flush_rewrite_rules();
}


add_action( 'after_setup_theme', 'shoptypeSettings' );
