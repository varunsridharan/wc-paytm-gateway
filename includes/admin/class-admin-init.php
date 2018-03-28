<?php
/**
 * Plugin's Admin code
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/Admin
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Wc_Paytm_Gateway_Admin {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_filter( 'plugin_action_links_'.WC_PAYTMG_FILE, array($this,'plugin_action_links'),10,10);
        add_action( 'add_meta_boxes', array($this,'add_order_metabox'),10,2 );
        add_action( 'wp_ajax_wc_paytm_status_check',array($this,'render_ajax'));
	}
    
    public function render_ajax(){
        if(isset($_REQUEST['id'])){
            include(WC_PAYTMG_ADMIN.'views/view-details.php');
        }
        wp_die();
    }
    
    public function add_order_metabox($post_type,$post){
        if($post_type !== 'shop_order'){return;}
        $order = new WC_Order($post);
        if($order->get_payment_method() == 'wc_paytm'){
            add_meta_box('paytm-refunds', __("Paytm Refunds",WC_PAYTMG_TXT),array($this,'render_paytm_metabox'),'shop_order', 'normal');
        }
    }
    
    public function render_paytm_metabox($post){
        $refunds = get_post_meta($post->ID,'_wc_paytm_refund_keys',true);
        if(!empty($refunds)){
            include(WC_PAYTMG_ADMIN.'views/metabox.php');
        }
    }
 
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
    public function plugin_action_links($action,$file,$plugin_meta,$status){
        $menu_link = admin_url('admin.php?page=wc-paytm-gateway-settings');
        $actions[] = sprintf('<a href="%s">%s</a>', $menu_link, __('Settings',WC_PAYTMG_TXT) );
        $actions[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WC_PAYTMG_TXT) );
        $action = array_merge($actions,$action);
        return $action;
    }
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WC_PAYTMG_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('F.A.Q',WC_PAYTMG_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/WC-Paytm-Gateway', __('View On Github',WC_PAYTMG_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('Report Issue',WC_PAYTMG_TXT) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', '#', __('Donate',WC_PAYTMG_TXT) );
		}
		return $plugin_meta;
	}	    
}