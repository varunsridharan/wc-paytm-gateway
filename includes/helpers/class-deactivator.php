<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/core
 * @since 1.0
 */
class Wc_Paytm_Gateway_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() { }

	public static function dependency_deactivate(){ 
		if ( is_plugin_active(WC_PAYTMG_FILE) ) {
			add_action('update_option_active_plugins', array(__CLASS__,'deactivate_dependent'));
		}
	}
	
	public static function deactivate_dependent(){
		deactivate_plugins(WC_PAYTMG_FILE);
	}
}