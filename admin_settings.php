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
    }
    
    public static function add_network_settings($token){
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://backend.shoptype.com/networks');
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
            $ch = curl_init('https://backend.shoptype.com/authenticate');
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
    public function settings_page_content() { ?>
        <div class="wrap">
            <h2> Shoptype Settings </h2>
            <form method="post" action="options.php">
                <?php   
                    if(isset($_COOKIE["stToken"]) && !empty($_COOKIE["stToken"])) {
                        $token=$_COOKIE["stToken"];
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
                    fetch("https://backend.shoptype.com/api-keys", postHeader)
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

        add_settings_section("shoptype_settings", "Network Settings: ", array($this, 'section_callback'), "shoptype_settings");
        add_settings_field( 'shoptype_api_key', 'ST API Key: ', array( $this, 'field_callback' ), 'shoptype_settings', 'shoptype_settings', array("id"=>"shoptype_api_key","name"=>"ST API Key") );
        add_settings_field("platformID", "ST Platform ID: ", array($this, 'field_callback'), "shoptype_settings", 'shoptype_settings', array("id"=>"platformID","name"=>"ST Platform ID"));
        add_settings_field("vendorId", "ST Vendor ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"vendorId","name"=>"ST Vendor ID") );
        add_settings_field("networkId", "ST Network ID: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"networkId","name"=>"ST Network ID") );
		add_settings_field("refCode", "ST Referral Code: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"refCode","name"=>"ST Referral Code") );
        add_settings_field("refCode", "ST Cart Count Match String: ", array( $this, 'field_callback' ), "shoptype_settings", "shoptype_settings", array("id"=>"cartCountMatch","name"=>"ST Cart Count Match") );        
    }
    /* Create input fields*/
    public function field_callback ( $arguments ) {
        echo "<input name=\"{$arguments['id']}\" id=\"{$arguments['id']}\" type=\"text\" value=\"" .get_option($arguments['id'])."\"\>";

    }
}

new Shoptype_Settings();

function shoptypeSettings() {
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
	global $stCurrency;
	$stCurrency["USD"] = "$";
    $stCurrency["INR"] = "₹";
}
add_action( 'after_setup_theme', 'shoptypeSettings' );