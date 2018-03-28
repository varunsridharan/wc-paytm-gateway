<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wordpress.org/plugin/wc-paytm-gateway/
 * @since             1.0
 * @package           WC Paytm Gateway
 *
 * @wordpress-plugin
 * Plugin Name:       WC Paytm Gateway
 * Plugin URI:        http://wordpress.org/plugin/wc-paytm-gateway/
 * Description:       The simplest of the available ways to do payment & refunds integration to your website with paytm . 
 * Version:           1.1
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-paytm-gateway
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
define('WC_PAYTMG_FILE',plugin_basename( __FILE__ ));
define('WC_PAYTMG_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
define('WC_PAYTMG_INC',WC_PAYTMG_PATH.'includes/'); # Plugin INC Folder
define('WC_PAYTMG_DEPEN','woocommerce/woocommerce.php');

register_activation_hook( __FILE__, 'wc_paytmg_activate_plugin' );
register_deactivation_hook( __FILE__, 'wc_paytmg_deactivate_plugin' );
register_deactivation_hook( WC_PAYTMG_DEPEN, 'wc_paytmg_dependency_deactivate' );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function wc_paytmg_activate_plugin() {
	require_once(WC_PAYTMG_INC.'helpers/class-activator.php');
	Wc_Paytm_Gateway_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_paytmg_deactivate_plugin() {
	require_once(WC_PAYTMG_INC.'helpers/class-deactivator.php');
	Wc_Paytm_Gateway_Deactivator::deactivate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_paytmg_dependency_deactivate() {
	require_once(WC_PAYTMG_INC.'helpers/class-deactivator.php');
	Wc_Paytm_Gateway_Deactivator::dependency_deactivate();
}



require_once(WC_PAYTMG_INC.'functions.php');
require_once(WC_PAYTMG_PATH.'bootstrap.php');

if(!function_exists('Wc_Paytm_Gateway')){
    function Wc_Paytm_Gateway(){
        return Wc_Paytm_Gateway::get_instance();
    }
}

add_filter('woocommerce_payment_gateways', 'paytm_add_gateway' );

function paytm_add_gateway($methods){
    $methods[] = WC_Paytm_Gateway_Hook::get_instance();
    return $methods;
}
Wc_Paytm_Gateway();