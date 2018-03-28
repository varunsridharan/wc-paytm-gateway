<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/FrontEnd
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Wc_Paytm_Gateway_Functions {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        add_action('init',array($this,'init_endpoints'),0);
        add_filter( 'query_vars', array($this,'add_query_vars'),0);
        add_action( 'wp', array($this,'check_request' ), 9999 );
        //add_action("wc_paytmg_process_paytm",array($this,'process_paytm_response'));
        
    }
    
    public function check_request(){ 
        global $wp_query;
        
        foreach($this->init_query_vars() as  $id => $key){
            if(isset($wp_query->query[$key['key']])){
                if(has_action("wc_paytmg_".$id))
                    do_action("wc_paytmg_".$id);
            }
        }
    }
    
    public function init_endpoints(){
        $types = $this->init_query_vars();
        foreach($types as $val){
            $type = EP_ROOT;
            if(isset($val['type'])){$type = $val['type'];}
            add_rewrite_endpoint($val['key'], $type);
        }
    }
    
    public function init_query_vars(){
        return array('process_paytm' => array('key' => 'process-paytm','type' => EP_ROOT));
    }
    
    public function add_query_vars($vars){
        foreach($this->init_query_vars() as $key){
            $vars[] = $key['key'];    
        }
        return $vars;
    }

}