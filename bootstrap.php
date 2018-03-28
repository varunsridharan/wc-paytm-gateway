<?php 
/**
 * Plugin Main File
 *
 * @link http://wordpress.org/plugin/wc-paytm-gateway/
 * @package WC Paytm Gateway
 * @subpackage WC Paytm Gateway/core
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }
 
class Wc_Paytm_Gateway {
	public $version = '1.1';
	public $plugin_vars = array();
	
	protected static $_instance = null; # Required Plugin Class Instance
    protected static $functions = null; # Required Plugin Class Instance
	protected static $admin = null;     # Required Plugin Class Instance
	protected static $settings = null;  # Required Plugin Class Instance

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->define_constant();
        $this->load_required_files();
        $this->init_class();
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
        add_action("woocommerce_loaded",array($this,'add_gateway_file'));
    }
	
	/**
	 * Throw error on object clone.
	 *
	 * Cloning instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning instances of the class is forbidden.', WC_PAYTMG_TXT), WC_PAYTMG_V );
	}	

	/**
	 * Disable unserializing of the class
	 *
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of the class is forbidden.',WC_PAYTMG_TXT), WC_PAYTMG_V);
	}

    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
        
       $this->load_files(WC_PAYTMG_INC.'class-*.php');
	   $this->load_files(WC_PAYTMG_SETTINGS.'class-wp-*.php');
        
       if(wc_paytmg_is_request('admin') || wc_paytmg_is_request('ajax')){
           $this->load_files(WC_PAYTMG_ADMIN.'class-*.php');
       } 
        do_action('wc_paytmg_before_addons_load');
    }
    
   
    public function add_gateway_file(){
        require_once(WC_PAYTMG_PATH.'includes/paytm-gateway.php');
    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){
        do_action('wc_paytmg_before_init');
        self::$functions = new Wc_Paytm_Gateway_Functions;

        if(wc_paytmg_is_request('admin') || wc_paytmg_is_request('ajax')){
            self::$admin = new Wc_Paytm_Gateway_Admin;
        }
        
        do_action('wc_paytmg_after_init');
    }
    
	# Returns Plugin's Functions Instance
	public function func(){
		return self::$functions;
	}
	
	# Returns Plugin's Settings Instance
	public function settings(){
		return self::$settings;
	}
	
	# Returns Plugin's Admin Instance
	public function admin(){
		return self::$admin;
	}
    
    /**
     * Loads Files Based On Give Path & regex
     */
    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){
            if($type == 'require'){ require_once( $files ); } 
			else if($type == 'include'){ include_once( $files ); }
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(WC_PAYTMG_TXT, false, WC_PAYTMG_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (WC_PAYTMG_TXT === $domain)
            return WC_PAYTMG_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('WC_PAYTMG_NAME', 'WC Paytm Gateway'); # Plugin Name
        $this->define('WC_PAYTMG_SLUG', 'wc-paytm-gateway'); # Plugin Slug
        $this->define('WC_PAYTMG_TXT',  'wc-paytm-gateway'); #plugin lang Domain
		$this->define('WC_PAYTMG_DB', 'wc_paytmg_');
		$this->define('WC_PAYTMG_V',$this->version); # Plugin Version
		
		$this->define('WC_PAYTMG_LANGUAGE_PATH',WC_PAYTMG_PATH.'languages'); # Plugin Language Folder
		$this->define('WC_PAYTMG_ADMIN',WC_PAYTMG_INC.'admin/'); # Plugin Admin Folder
		$this->define('WC_PAYTMG_SETTINGS',WC_PAYTMG_ADMIN.'settings_framework/'); # Plugin Settings Folder
		$this->define('PLUGIN_ADDON',WC_PAYTMG_PATH.'addons/');
        
		$this->define('WC_PAYTMG_URL',plugins_url('', __FILE__ ).'/');  # Plugin URL
		$this->define('WC_PAYTMG_CSS',WC_PAYTMG_URL.'includes/css/'); # Plugin CSS URL
		$this->define('WC_PAYTMG_IMG',WC_PAYTMG_URL.'includes/img/'); # Plugin IMG URL
		$this->define('WC_PAYTMG_JS',WC_PAYTMG_URL.'includes/js/'); # Plugin JS URL
        
        
        $this->define('PLUGIN_ADDON_URL',WC_PAYTMG_URL.'addons/');  # Plugin URL
		$this->define('PLUGIN_ADDON_CSS',PLUGIN_ADDON_URL.'includes/css/'); # Plugin CSS URL
		$this->define('PLUGIN_ADDON_IMG',PLUGIN_ADDON_URL.'includes/img/'); # Plugin IMG URL
		$this->define('PLUGIN_ADDON_JS',PLUGIN_ADDON_URL.'includes/js/'); # Plugin JS URL
    }
	
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }   
}