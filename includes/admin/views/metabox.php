<table class="widefat striped">
    <thead>
        <tr>
            <th><?php _e("Ref ID",WC_PAYTMG_TXT); ?></th>
            <th><?php _e("Bank Trans ID",WC_PAYTMG_TXT); ?></th>
            <th><?php _e("Date",WC_PAYTMG_TXT); ?></th>
            <th><?php _e("Amount",WC_PAYTMG_TXT); ?></th>
            <th><?php _e("Response",WC_PAYTMG_TXT); ?></th>
        </tr>
    </thead>
    
    <tbody>
    <?php
        foreach($refunds as $refund){
            $refund_data = get_post_meta($post->ID,$refund,true);
            if(!empty($refund_data)){
                $id = urlencode($refund);
                $pid = $post->ID;
                $url = 'admin-ajax.php?width=600&height=550&action=wc_paytm_status_check&id='.$id.'&pid='.$pid;
    ?>
        <tr>
            <td><a title="<?php _e("Status For ");  echo $refund_data['log_id'];  ?>" class="thickbox" href="<?php echo admin_url($url); ?>"><?php echo $refund_data['log_id']; ?></a></td>
            <td><?php echo @$refund_data['response']['BANKTXNID']; ?></td>
            <td><?php echo date(get_option("date_format")." : ".get_option("time_format"),$refund_data['request_date']); ?></td>
            <td><?php echo wc_price($refund_data['request']['REFUNDAMOUNT']); ?></td>
            <td><?php echo $refund_data['response']['RESPCODE']; ?> : <?php echo $refund_data['response']['RESPMSG']; ?></td>
        </tr>
    <?php
            }
        }
    ?>
    </tbody>

</table>
<?php add_thickbox(); ?>
