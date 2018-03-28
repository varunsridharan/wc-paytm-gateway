<form action="<?php echo $gateway_url; ?>" method="post" id="paytm_payment_form">
    <?php echo implode(' ',$paytm_args_array); ?>
    <input type="submit" class="button-alt" id="submit_paytm_payment_form" value="<?php _e(" Pay Via Paytm "); ?>" />

    <a class="button cancel" href="<?php echo $order->get_cancel_order_url(); ?>">
        <?php _e("Cancel order &amp; restore cart"); ?>
    </a>

    <script type="text/javascript">
        jQuery(function() {
          jQuery("body").block({
                message: '<img src="<?php echo WC()->plugin_url(); ?>assets/images/ajax-loader.gif" alt="<?php _e("Redirecting...."); ?>" style="float:left; margin-right: 10px;"/>',
                overlayCSS: {
                    background: "#fff",
                    opacity: 0.6
                },
                css: {
                    padding: 20,
                    textAlign: "center",
                    color: "#555",
                    border: "3px solid #aaa",
                    backgroundColor: "#fff",
                    cursor: "wait",
                    lineHeight: "32px"
                }
            });
            jQuery("#submit_paytm_payment_form").click();
        });

    </script>
</form>