<?php
$pid = $_REQUEST['pid'];
$id = $_REQUEST['id'];
$datas = get_post_meta($pid,$id,true);

$instance = WC_Paytm_Gateway_Hook::get_instance();
var_dump($datas);
$status_check = array(
    'MID' => $datas['request']['MID'],
    'ORDERID' => $datas['request']['ORDERID'],
    'REFID' => $datas['request']['REFID'],
);

$url = 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/getRefundStatus';
if($instance->mode == 'yes'){
    $url = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getRefundStatus';
}

$status_check['CHECKSUMHASH'] = getChecksumFromArray($status_check,$instance->secret_key,0);
$responseParamList = callNewAPI($url,$status_check);
?>

<?php foreach($responseParamList['REFUND_LIST'] as $refunds): ?>
<h2><?php _e("Refund Transaction Details"); ?></h2>
<table class="widefat striped">
<?php  $hide = array('MID'); foreach($refunds as $id => $key) : if(in_array($id,$hide)){continue;} ?>
<tr>
    <th><strong><?php echo $id; ?></strong></th>
    <td><?php echo $key; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endforeach; ?>
<br/>