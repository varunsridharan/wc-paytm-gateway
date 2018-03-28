<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/core
 * @since 1.0
 */

if ( ! class_exists( 'Wc_Paytm_Gateway_Dependencies' ) ){
    class Wc_Paytm_Gateway_Dependencies {
		
        private static $active_plugins;
		
        public static function init() {
            self::$active_plugins = (array) get_option( 'active_plugins', array() );
            if ( is_multisite() )
                self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
		
        public static function active_check($pluginToCheck = '') {
            if ( ! self::$active_plugins ) 
				self::init();
            return in_array($pluginToCheck, self::$active_plugins) || array_key_exists($pluginToCheck, self::$active_plugins);
        }
    }
}
/**
 * WC Detection
 */
if(! function_exists('Wc_Paytm_Gateway_Dependencies')){
    function Wc_Paytm_Gateway_Dependencies($pluginToCheck = 'woocommerce/woocommerce.php') {
        return Wc_Paytm_Gateway_Dependencies::active_check($pluginToCheck);
    }
}