=== Shoptype ===
Contributors: astroajay
Tags: shoptype, wordpress, network, cosell, awake market, 
Requires at least: 5.5
Tested up to: 5.8
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 

Plugin to integrate shoptype features with the networking features of buddypress on wordpress sites for Awake Market Networks  
 
== Description ==
 
Plugin to integrate shoptype features with the networking features of buddypress on wordpress sites for Awake Market Networks . 
 
== Installation ==
 
1. Upload the plugin folder to your /wp-content/plugins/ folder.
1. Go to the **Plugins** page and activate the plugin.
 
== Frequently Asked Questions ==
 
= How do I use this plugin? =
 
Once the plugin is installed 
there are still a number of User specific variables to be set from your shoptype account network profile. 
The following code will need to be added into the functions.php file of your theme directly or via the code snippets plugin 

    function shoptypeSettings() {
        global $stPlatformId;
        $stPlatformId = 'YOUR SHOPTYPE NETWORK'S PLATFORM ID';
		global $stApiKey;
        $stApiKey = 'THE API KEY GENERATED FOR YOUR SHOPTYPE NETWORK';
		global $stRefcode;
        $stRefcode = 'YOUR SHOPTYPE REFERRAL TRACKER CODE';
		global $stCurrency;
		$stCurrency["THREE CHARACTER CURRENCY (EG USD)"] = "THE CURRENCY SYMBOL";
		global $productUrl;
		$productUrl = "/product?id={{productId}}";
		global $brandUrl;
		$brandUrl = "/view-brand?id={{brandId}}";
    }
    add_action( 'after_setup_theme', 'shoptypeSettings' );


= How to uninstall the plugin? =
 
Simply deactivate and delete the plugin. 
 
== Screenshots ==
1. NA 
 
== Changelog ==
= 1.0 =
* Plugin released. 