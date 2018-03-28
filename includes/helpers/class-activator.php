<?php 
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/core
 * @since 1.0
 */
class Wc_Paytm_Gateway_Activator {
	
    public function __construct() { }
	
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once(WC_PAYTMG_INC.'helpers/class-version-check.php');
		require_once(WC_PAYTMG_INC.'helpers/class-dependencies.php');
		
		if(Wc_Paytm_Gateway_Dependencies(WC_PAYTMG_DEPEN)){
			Wc_Paytm_Gateway_Version_Check::activation_check('3.7');	
		} else {
			if ( is_plugin_active(WC_PAYTMG_FILE) ) { deactivate_plugins(WC_PAYTMG_FILE);} 
			wp_die(wc_paytmg_dependency_message());
		}
	} 
}