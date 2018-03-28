=== WC Paytm Gateway ===
Contributors: varunms,vaahosttech
Author URI: http://varunsridharan.in/
Plugin URL: https://wordpress.org/plugins/wc-paytm-gateway/
Tags: WC,WC Gateway,Paytm,Paywithpaytm,Payment,WooCommerce,
Donate link: http://paypal.me/varunsridharan23
Requires at least: 3.0
Tested up to: 4.3
WC requires at least: 1.0
WC tested up to: 2.4.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

The simplest of the available ways to do payment & refunds integration to your website with paytm . 

== Description ==
Pay with Paytm is the simplest of the available ways to do payment integration to your website. It helps you create a no frills Payment button on your checkout page where amount can be fixed preconfigured on our servers or can be dynamic. The person who clicks the Pay with Paytm button will be taken to Pay with Paytm website where user will complete the transaction via Paytm and Pay with Paytm returns back the browser handle to the website after payment confirmation. Website will also receive a notification, which will be a server-to-server call as soon as the payment is done.

> Note: By default Paytm blocks refund API in the production environment. You need to send them a request with your MID to activate the refund API.

# Integration 
* In wordpress, Go to Woocommerce -> Settings -> Checkout
* Under Payment Gateways, Pay With Paytm should appear.
* Go to its settings.
* Enable the plugin.

* For Button Id and Button secret:-
    * Signup/Sign In on paywithpaytm.com and fill in the profile details and bank details.
    * Create a pay button in the Catalog section. 
    * Copy button id and button secret.
    * Go to settings->edit
    * Set Success Url :- (yourbasewebsiteurl)/process-paytm/
    * Set Cancel Url :- (yourbasewebsiteurl)/process-paytm/
    * Set Notify Url :- (yourbasewebsiteurl)/process-paytm/

* Set the redirect url where you want to redirect after payment.

All Response from paytm should be directed to site_url//process-paytm/
<br/>
Eg : http://example.com/process-paytm/


== Screenshots ==

== Upgrade Notice ==

== Frequently Asked Questions == 

== Installation ==

= Minimum Requirements =

* WordPress 3.5 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater
* WooCommerce Version 3.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of WC Paytm Gateway, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "WC Paytm Gateway"  and click Search Plugins. Once you've found our plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now"

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your Web Server via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

1. Installing alternatives:
 * via Admin Dashboard:
 * Go to 'Plugins > Add New', search for "WC Paytm Gateway", click "install"
 * OR via direct ZIP upload:
 * Upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
 * OR via FTP upload:
 * Upload `wc-paytm-gateway` folder to the `/wp-content/plugins/` directory
 
2. Activate the plugin through the 'Plugins' menu in WordPress
 

== Changelog == 
= 1.1 = 
* Improved PayTM Refund Logs
= 1.0 =
* Base Version
