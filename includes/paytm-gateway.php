<?php
if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

class WC_Paytm_Gateway_Hook extends WC_Payment_Gateway {
    protected $msg = array();
    
    protected static $_instance = null;
    
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    public function __construct(){
        $this->id = 'wc_paytm';
        $this->icon = WC_PAYTMG_IMG. 'logo.gif';
        $this->method_title = __('Paytm',WC_PAYTMG_TXT);
        $this->method_description = __( 'India online payment solutions for all your transactions by paytm', 'woocommerce' );
        $this->has_fields = false;
        
        $this->init_form_fields();
		$this->init_settings();
        
        $this->title = $this->settings['title'];
        $this->description = $this->settings['description'];
        $this->merchantIdentifier = $this->settings['merchantIdentifier'];
        $this->secret_key = $this->settings['secret_key']; 
        $this->industry_type = $this->settings['industry_type'];
        $this->channel_id = $this->settings['channel_id'];
        $this->website = $this->settings['website'];
        $this->mode = $this->settings['mode'];
        $this->callbackurl = $this->settings['callbackurl'];


        $this->supports  = array('products','refunds');
        
        $this->msg['message'] = "";
        $this->msg['class'] = "";	

        add_action("wc_paytmg_process_paytm",array($this,'process_paytm_response'));
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action('woocommerce_receipt_'.$this->id, array(&$this, 'receipt_page'));
        
        if(isset($_GET['is-paytm']))
            add_action('the_content', array(&$this, 'paytmShowMessage'));
    }
    
    public function paytmShowMessage($content){
        if(isset($_GET['paytm-msg']))
            return '<div class="box '.htmlentities($_GET['paytm-type']).'-box">'.htmlentities(urldecode($_GET['paytm-msg'])).'</div>'.$content;
    }
    
    public function payment_fields(){ if($this->description) echo wpautop(wptexturize($this->description)); }
    
    public function init_form_fields(){   
        $this->form_fields = array( 
                'enabled' => array(
                    'title' => __('Enable/Disable'),
                    'type' => 'checkbox',
                    'label' => __('Enable paytm Payment Module.'),
                    'default' => 'no'),
                'title' => array(
                    'title' => __('Title:'),
                    'type'=> 'text',
                    'description' => __('This controls the title which the user sees during checkout.'),
                    'default' => __('paytm')),
                'description' => array(
                    'title' => __('Description:'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.'),
                    'default' => __('The best payment gateway provider in India for e-payment through credit card, debit card & netbanking.')),

                'merchantIdentifier' => array(
                    'title' => __('Merchant Identifier'),
                    'type' => 'text',
                    'description' => __('This id(USER ID) available at "Generate Secret Key" of "Integration->Card payments integration at paytm."')),

                'secret_key' => array(
                    'title' => __('Secret Key'),
                    'type' => 'text',
                    'description' =>  __('Given to Merchant by paytm'),
                    ),
                'industry_type' => array(
                    'title' => __('Industry Type'),
                    'type' => 'text',
                    'description' =>  __('Given to Merchant by paytm'),
                    ),
                'channel_id' => array(
                    'title' => __('Channel ID'),
                    'type' => 'text',
                    'description' =>  __('WEB - for desktop websites / WAP - for mobile websites'),
                    ),
                'website' => array(
                    'title' => __('Web Site'),
                    'type' => 'text',
                    'description' =>  __('Given to Merchant by paytm'),
                    ),
                'mode' => array(
                    'title' => __('Enable Test Mode'),
                    'type' => 'checkbox',
                    'label' => __('Select to enable Sandbox Enviroment'),
                    'description' => "Unchecked means in Production Enviroment",
                    'default' => 'no'                   
                )
            );
        }

    public function receipt_page($order){
        echo '<p>'.__('Thank you for your order, please click the button below to pay with paytm.').'</p>';
        echo $this->generate_paytm_form($order);
    }

    public function process_payment($order_id){
        $order = new WC_Order($order_id);
        $orderID = null;
        
        if ( paytm_is_wc3() ) { $orderID = $order->get_id(); } 
        else  { $orderID = $order->id; }
        
        return array(
            'result' => 'success', 
            'redirect' => $order->get_checkout_payment_url(true),
        );
    }
    
    public function process_refund($order_id,$amount = '',$reason = ''){
        if($order_id){
            $data = get_post_meta($order_id,'_wc_paytm_raw_response',true);
            if(is_array($data)){
                $order = new WC_Order($order_id);
                $PaytmKey = 'WC_PAYTM_'.time().rand(1,10);
                $requestParamList = array();
                $requestParamList['MID'] = $this->merchantIdentifier;
                $requestParamList['ORDERID'] = $order_id;
                $requestParamList['REFUNDAMOUNT'] = $amount;
                $requestParamList['REFID'] = $PaytmKey;
                $requestParamList['TXNID'] = $data['TXNID'];
                $requestParamList['TXNTYPE'] = 'REFUND';
                $requestParamList['CHECKSUM'] = getRefundChecksumFromArray($requestParamList, $this->secret_key);
                $requestParamList['COMMENTS'] = $reason;
                
                
                 $requestParamList['MID'] = $this->merchantIdentifier;
                $requestParamList['ORDERID'] = 153;
                $requestParamList['REFUNDAMOUNT'] = '2';
                $requestParamList['REFID'] = $PaytmKey;
                $requestParamList['TXNID'] = 7091418242;
                $requestParamList['TXNTYPE'] = 'REFUND';
                $requestParamList['CHECKSUM'] = getRefundChecksumFromArray($requestParamList, $this->secret_key);
                $requestParamList['COMMENTS'] = $reason;
                
                if($this->mode=='yes') { $check_status_url = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/REFUND'; }
                else { $check_status_url = 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/REFUND'; }
                
                $response = callNewAPI($check_status_url, $requestParamList);
                
                $status = false;
                if($response){
                    if($response['RESPCODE'] == '10'){
                        $msg = 'Paytm Order Refund Successful <br/>';
                        $msg .= '<strong>Refund ID</strong> : '.$response['REFUNDID'].'<br/>';
                        $msg .= '<strong>REF ID</strong> : '.$response['REFID'].'<br/>';
                        $msg .= '<strong>Response</strong> : '.$response['RESPMSG'].'<br/>';
                        $order->add_order_note($msg); 
                        $status = true;
                    } else {
                        if(is_array($response)){
                            $response['REFID'] = $PaytmKey;
                            $response['RESPCODE'] = isset($response['ErrorCode']) ? $response['ErrorCode'] : null;
                            $response['RESPMSG'] = isset($response['ErrorMsg']) ? $response['ErrorMsg'] : null;
                        }
                        $msg = 'Paytm Order Refund Failed <br/>';
                        $msg .= '<strong>Refund ID</strong> : '.$response['REFUNDID'].'<br/>';
                        $msg .= '<strong>REF ID</strong> : '.$response['REFID'].'<br/>';
                        $msg .= '<strong>Response Code</strong> : '.$response['RESPCODE'].'<br/>';
                        $msg .= '<strong>Response</strong> : '.$response['RESPMSG'].'<br/>';
                        $order->add_order_note($msg);  
                        
                    
                    }
                } else {
                    if(is_array($response)){
                        $response['REFID'] = $PaytmKey;
                        $response['RESPCODE'] = isset($response['ErrorCode']) ? $response['ErrorCode'] : null;
                        $response['RESPMSG'] = isset($response['ErrorMsg']) ? $response['ErrorMsg'] : null;
                    }                    
                }
                
                $ex_data = get_post_meta($order_id,'_wc_paytm_refund_keys',true);
                if(!is_array($ex_data)){ $ex_data = array(); }
                $ex_data[] = '_wc_paytm_refund_'.$PaytmKey;
                $save_data = array( 
                    'log_id' => $PaytmKey,
                    'request' => $requestParamList, 
                    'response' => $response,
                    'request_date' => time(),
                );
                update_post_meta($order_id,'_wc_paytm_refund_keys',$ex_data);
                
                update_post_meta($order_id,'_wc_paytm_refund_'.$PaytmKey,$save_data);
                return $status;
            }
        }
        return false;
    }
    
    public function get_gateway_url(){
        $gateway_url = '';
        if($this->mode=='yes') {
            $gateway_url = 'https://pguat.paytm.com/oltp-web/processTransaction';
        } else {
            $gateway_url = 'https://secure.paytm.in/oltp-web/processTransaction';
        }
        return $gateway_url;
    }
    
    public function generate_paytm_form($order_id){
        global $woocommerce;
        $txnDate = date('Y-m-d');			
        $milliseconds = (int) (1000 * (strtotime(date('Y-m-d'))));
        $order = new WC_Order($order_id);
        
        $call = get_site_url() . '/process-paytm';			
        
        $order_id = $order_id;
        $amt=	$order->get_total();
        $txntype='1';
        $ptmoption='1';
        $currency="INR";
        $purpose="1";
        $productDescription='paytm';
        $ip=$_SERVER['REMOTE_ADDR'];

        $email = $order->get_billing_email();
        $mobile_no = preg_replace('#[^0-9]{0,13}#is','',$order->get_billing_phone());

        $post_variables = array(
            "MID" => $this->merchantIdentifier,
            "ORDER_ID" => $order_id,
            "CUST_ID" => $order->get_billing_first_name(),
            "TXN_AMOUNT" => $amt,
            "CHANNEL_ID" => $this->channel_id,
            "INDUSTRY_TYPE_ID" => $this->industry_type,
            "WEBSITE" => $this->website,
            "EMAIL" => $email,
            "MOBILE_NO" => $mobile_no,
            'CALLBACK_URL' => $call,
        );
        
        $checksum = getChecksumFromArray($post_variables, $this->secret_key);

        $paytm_args = array(
           'merchantIdentifier' => $this->merchantIdentifier,
            'orderId' => $order_id,
            'returnUrl' => $call,
            'buyerEmail' => $order->get_billing_email(),
            'buyerFirstName' => $order->get_billing_first_name(),
            'buyerLastName' => $order->get_billing_last_name(),
            'buyerAddress' => $order->get_billing_address_1(),
            'buyerCity' => $order->get_billing_city(),
            'buyerState' => $order->get_billing_state(),
            'buyerCountry' => $order->get_billing_country(),
            'buyerPincode' => $order->get_billing_postcode(),
            'buyerPhoneNumber' => $order->get_billing_phone(),
            'txnType' => $txntype,
            'ptmoption' => $ptmoption,
            'mode' => $this->mode,
            'currency' => $currency,
            'amount' => $amt,
            'merchantIpAddress' => $ip,
            'purpose' => $purpose,
            'productDescription' => $productDescription,
            'txnDate' =>  $txnDate,
            'checksum' => $checksum
        );
        
        $paytm_args_array = array();
        $paytm_args_array[] = "<input type='hidden' name='MID' value='".  $this->merchantIdentifier ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='ORDER_ID' value='". $order_id ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='WEBSITE' value='". $this->website ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='INDUSTRY_TYPE_ID' value='". $this->industry_type ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='CHANNEL_ID' value='". $this->channel_id ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='TXN_AMOUNT' value='". $amt ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='CUST_ID' value='". $order->get_billing_first_name() ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='EMAIL' value='". $email ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='MOBILE_NO' value='". $mobile_no ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='CALLBACK_URL' value='" . $call . "'/>";
        $paytm_args_array[] = "<input type='hidden' name='txnDate' value='". date('Y-m-d H:i:s') ."'/>";
        $paytm_args_array[] = "<input type='hidden' name='CHECKSUMHASH' value='". $checksum ."'/>";
        $gateway_url = $this->get_gateway_url();
        
        ob_start();
            include('html-paytm.php');
        return ob_get_clean();
    }
    
    public function process_paytm_response(){        
        global $woocommerce;		
        if(isset($_REQUEST['ORDERID']) && isset($_REQUEST['RESPCODE'])){
            $order_sent = $_REQUEST['ORDERID'];
            $responseDescription = $_REQUEST['RESPMSG'];
            $order = new WC_Order($_REQUEST['ORDERID']);
            $redirect_url = $order->get_checkout_order_received_url();
            $this->msg['class'] = 'error';
            $this->msg['message'] = "Thank you for shopping with us. However, the transaction has been Failed For Reason  : " . $responseDescription;
            
            $success = false;
            if($_REQUEST['RESPCODE'] == 01) {
                $order_amount = $order->get_total();
                if(($_REQUEST['TXNAMOUNT']	== $order_amount)){
                    $order_sent			      = $_POST['ORDERID'];
                    $res_code				  = $_POST['RESPCODE'];
                    $responseDescription      = $_POST['RESPMSG'];
                    $checksum_recv			  = $_POST['CHECKSUMHASH'];
                    $paramList			      = $_POST;
                    $order_amount = $_POST['TXNAMOUNT'];
                    $bool = "FALSE";
                    $bool = verifychecksum_e($paramList, $this->secret_key, $checksum_recv);

                    if ($bool == "TRUE") {
                        $requestParamList = array("MID" => $this->merchantIdentifier , "ORDERID" => $order_sent);
                        $StatusCheckSum = getChecksumFromArray($requestParamList, $this->secret_key);
                        $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                        
                        if($this->mode=='yes') { $check_status_url = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus'; }
                        else { $check_status_url = 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/getTxnStatus'; }
                        
                        $responseParamList = callNewAPI($check_status_url, $requestParamList);
                        if($responseParamList['STATUS']=='TXN_SUCCESS' && $responseParamList['TXNAMOUNT']==$order_amount) {
                            $success = true;
                            if($order->get_status() !=='completed'){
                                $this->msg['message'] = "Thank you for your order . Your transaction has been successful.";
                                $this->msg['class'] = 'success';
                                $order->set_transaction_id($_POST['TXNID']);
                                $order->save();
                                $datas = $_POST;
                                array_map('wc_clean',$datas);
                                update_post_meta($order->get_id(),'_wc_paytm_raw_response',$datas);
                                
                                if($order->get_status() == 'processing'){
                                } else {
                                    $order->payment_complete();
                                    $order->add_order_note('Mobile Wallet payment successful');
                                    $order->add_order_note($this->msg['message']);
                                    $order->add_order_note('Paytm TNX ID : '.$_POST['TXNID']);
                                    WC()->cart->empty_cart();
                                }
                            }
                        } else {
                            $this->msg['class'] = 'error';
                            $this->msg['message'] = "Order Mismatch Occur";
                        }						
                    } else{
                        $this->msg['class'] = 'error';
                        $this->msg['message'] = "Severe Error Occur.";
                    }

                } else{	
                    $this->msg['class'] = 'error';
                    $this->msg['message'] = "Order Mismatch Occur";
                }					
            }
            
            if(!$success){
                $order->update_status('failed');
                $order->add_order_note('Failed');
                $order->add_order_note($responseDescription);
                $order->add_order_note($this->msg['message']);
            }

            $redirect_url = $order->get_checkout_order_received_url();
            $redirect_url = add_query_arg(array('paytm-msg'=> urlencode($this->msg['message']), 'paytm-type'=>$this->msg['class'],'is-paytm' => true), $redirect_url);
            wp_redirect( $redirect_url );
            exit;		
        }
        wp_redirect(site_url());
        exit;
    }    
}

return WC_Paytm_Gateway_Hook::get_instance();