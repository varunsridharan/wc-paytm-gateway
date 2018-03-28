<?php
if ( ! defined( 'WPINC' ) ) { die; }
global $wc_paytmg_db_settins_values, $wc_paytmg_vars;
$wc_paytmg_db_settins_values = array();
$wc_paytmg_vars = array();

if(!function_exists('wc_paytmg_vars')){
    function wc_paytmg_vars($key,$values = false){
        global $wc_paytmg_vars;
        if(isset($wc_paytmg_vars[$key])){ 
            return $wc_paytmg_vars[$key]; 
        }
        return $values;
    }
}
if(!function_exists('wc_paytmg_add_vars')){
    function wc_paytmg_add_vars($key,$values){
        global $wc_paytmg_vars;
        if(! isset($wc_paytmg_vars[$key])){ 
            $wc_paytmg_vars[$key] = $values; 
            return true; 
        }
        return false;
    }
}
if(!function_exists('wc_paytmg_remove_vars')){
    function wc_paytmg_remove_vars($key){
        global $wc_paytmg_vars;
        if(isset($wc_paytmg_vars[$key])){ 
            unset($wc_paytmg_vars[$key]);
            return true; 
        }
        return false;
    }
}


if(!function_exists('wc_paytmg_is_request')){
    /**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
    function wc_paytmg_is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }
}

if(!function_exists('wc_paytmg_current_screen')){
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    function wc_paytmg_current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
}


if(!function_exists('wc_paytmg_is_screen')){
    function wc_paytmg_is_screen($check_screen = '',$current_screen = ''){
        if(empty($check_screen)) {$check_screen = wc_paytmg_get_screen_ids(); }
        if(empty($current_screen)) {$current_screen = wc_paytmg_current_screen(); }
        
        if(is_array($check_screen)){
            if(in_array($current_screen , $check_screen)){
                return true;
            }
        }
        
        if(is_string($check_screen)){
            if($check_screen == $current_screen){
                return true;
            }
        }
        return false;
    }
}

if(!function_exists('wc_paytmg_get_screen_ids')){
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    function wc_paytmg_get_screen_ids(){
        $screen_ids = array();
        $screen_ids[] = 'woocommerce_page_wc-paytm-gateway-settings';
        $screen_ids[] = wc_paytmg_vars('settings_page');
        return $screen_ids;
    }
}

if(!function_exists('wc_paytmg_dependency_message')){
	function wc_paytmg_dependency_message(){
		$text = __( WC_PAYTMG_NAME . ' requires <b> WooCommerce </b> To Be Installed..  <br/> <i>Plugin Deactivated</i> ', WC_PAYTMG_TXT);
		return $text;
	}
}

function paytm_is_wc3(){
    return version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' );
}

function paytm_get_pages($title = false, $indent = true) {
    $wp_pages = get_pages('sort_column=menu_order');
    $page_list = array();
    if ($title) $page_list[] = $title;
    foreach ($wp_pages as $page) {
        $prefix = '';
        // show indented child pages?
        if ($indent) {
            $has_parent = $page->post_parent;
            while($has_parent) {
                $prefix .=  ' - ';
                $next_page = get_page($has_parent);
                $has_parent = $next_page->post_parent;
            }
        }
        // add to page list array array
        $page_list[$page->ID] = $prefix . $page->post_title;
    }
    return $page_list;
}

function encrypt_e($input, $ky)
{
    $key   = html_entity_decode($ky);
    $size  = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
    $input = pkcs5_pad_e($input, $size);
    $td    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
    $iv    = "@@@@&&&&####$$$$";
    mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $data = base64_encode($data);
    return $data;
}

function decrypt_e($crypt, $ky)
{
    
    $crypt = base64_decode($crypt);
    $key   = html_entity_decode($ky);
    $td    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
    $iv    = "@@@@&&&&####$$$$";
    mcrypt_generic_init($td, $key, $iv);
    $decrypted_data = mdecrypt_generic($td, $crypt);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $decrypted_data = pkcs5_unpad_e($decrypted_data);
    $decrypted_data = rtrim($decrypted_data);
    return $decrypted_data;
}

function pkcs5_pad_e($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

function pkcs5_unpad_e($text)
{
    $pad = ord($text{strlen($text) - 1});
    if ($pad > strlen($text))
        return false;
    return substr($text, 0, -1 * $pad);
}

function generateSalt_e($length)
{
    $random = "";
    srand((double) microtime() * 1000000);
    
    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";
    
    for ($i = 0; $i < $length; $i++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
    }
    
    return $random;
}

function checkString_e($value)
{
    $myvalue = ltrim($value);
    $myvalue = rtrim($myvalue);
    if ($myvalue == 'null')
        $myvalue = '';
    return $myvalue;
}

function getChecksumFromArray($arrayList, $key, $sort = 1)
{
    if ($sort != 0) {
        ksort($arrayList);
    }
    $str         = getArray2Str($arrayList);
    $salt        = generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash        = hash("sha256", $finalString);
    $hashString  = $hash . $salt;
    $checksum    = encrypt_e($hashString, $key);
    return $checksum;
}

function verifychecksum_e($arrayList, $key, $checksumvalue)
{
    $arrayList = removeCheckSumParam($arrayList);
    ksort($arrayList);
    $str        = getArray2Str($arrayList);
    $paytm_hash = decrypt_e($checksumvalue, $key);
    $salt       = substr($paytm_hash, -4);
    
    $finalString = $str . "|" . $salt;
    
    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;
    
    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function getArray2Str($arrayList) {
	$findme   = 'REFUND';
	$findmepipe = '|';
	$paramStr = "";
	$flag = 1;	
	foreach ($arrayList as $key => $value) {
		$pos = strpos($value, $findme);
		$pospipe = strpos($value, $findmepipe);
		if ($pos !== false || $pospipe !== false) 
		{
			continue;
		}
		
		if ($flag) {
			$paramStr .= checkString_e($value);
			$flag = 0;
		} else {
			$paramStr .= "|" . checkString_e($value);
		}
	}
	return $paramStr;
}

function redirect2PG($paramList, $key)
{
    $hashString = getchecksumFromArray($paramList);
    $checksum   = encrypt_e($hashString, $key);
}

function removeCheckSumParam($arrayList)
{
    if (isset($arrayList["CHECKSUMHASH"])) {
        unset($arrayList["CHECKSUMHASH"]);
    }
    return $arrayList;
}

function getTxnStatus($requestParamList)
{
    return callAPI(PAYTM_STATUS_QUERY_URL, $requestParamList);
}

function initiateTxnRefund($requestParamList)
{
    $CHECKSUM                     = getChecksumFromArray($requestParamList, PAYTM_MERCHANT_KEY, 0);
    $requestParamList["CHECKSUM"] = $CHECKSUM;
    return callAPI(PAYTM_REFUND_URL, $requestParamList);
}

function callAPI($apiURL, $requestParamList)
{
    $jsonResponse      = "";
    $responseParamList = array();
    $JsonData          = json_encode($requestParamList);
    $postData          = 'JsonData=' . urlencode($JsonData);
    $ch                = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ));
    $jsonResponse      = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}

function sanitizedParam($param)
{
    $pattern[0]     = "%,%";
    $pattern[1]     = "%#%";
    $pattern[2]     = "%\(%";
    $pattern[3]     = "%\)%";
    $pattern[4]     = "%\{%";
    $pattern[5]     = "%\}%";
    $pattern[6]     = "%<%";
    $pattern[7]     = "%>%";
    $pattern[8]     = "%`%";
    $pattern[9]     = "%!%";
    $pattern[10]    = "%\\$%";
    $pattern[11]    = "%\%%";
    $pattern[12]    = "%\^%";
    $pattern[13]    = "%=%";
    $pattern[14]    = "%\+%";
    $pattern[15]    = "%\|%";
    $pattern[16]    = "%\\\%";
    $pattern[17]    = "%:%";
    $pattern[18]    = "%'%";
    $pattern[19]    = "%\"%";
    $pattern[20]    = "%;%";
    $pattern[21]    = "%~%";
    $pattern[22]    = "%\[%";
    $pattern[23]    = "%\]%";
    $pattern[24]    = "%\*%";
    $pattern[25]    = "%&%";
    $sanitizedParam = preg_replace($pattern, "", $param);
    return $sanitizedParam;
}

function callNewAPI($apiURL, $requestParamList) {
	$jsonResponse = "";
	$responseParamList = array();
	$JsonData =json_encode($requestParamList);
	$postData = 'JsonData='.urlencode($JsonData);
	$ch = curl_init($apiURL);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                         
	'Content-Type: application/json', 
	'Content-Length: ' . strlen($postData))                                                                       
	);  
	$jsonResponse = curl_exec($ch); 
	$responseParamList = json_decode($jsonResponse,true);
	return $responseParamList;
}


function getRefundArray2Str($arrayList) {	
	$findmepipe = '|';
	$paramStr = "";
	$flag = 1;	
	foreach ($arrayList as $key => $value) {		
		$pospipe = strpos($value, $findmepipe);
		if ($pospipe !== false) 
		{
			continue;
		}
		
		if ($flag) {
			$paramStr .= checkString_e($value);
			$flag = 0;
		} else {
			$paramStr .= "|" . checkString_e($value);
		}
	}
	return $paramStr;
}

function getRefundChecksumFromArray($arrayList, $key, $sort=1) {
	if ($sort != 0) {
		ksort($arrayList);
	}
	$str = getRefundArray2Str($arrayList);
	$salt = generateSalt_e(4);
	$finalString = $str . "|" . $salt;
	$hash = hash("sha256", $finalString);
	$hashString = $hash . $salt;
	$checksum = encrypt_e($hashString, $key);
	return $checksum;
}