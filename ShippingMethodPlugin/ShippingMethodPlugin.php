<?php
/**
  Plugin Name: 運送方式插件
  Plugin URI: https://github.com/keoy7am/WooPlugins/ShippingMethodPlugin/
  Description: 貨到付款／貨到刷卡
  Author: Elliot.Chen
  Version: 1.0
  Author URI: https://github.com/keoy7am
  License: GPLv2
**/
/**
 * 自訂欄位
 */
add_action( 'woocommerce_before_order_notes', 'add_shipping_type' );
    function add_shipping_type( $checkout ) {
        woocommerce_form_field( 'shipping_type', array(
        'type' => 'radio',
        'class' => array( 'form-row-wide' ),
        'label' => '選擇運送方式',
        'options' => array(
            'shipping_Cash'	=> '貨到付款',
            'shipping_Card'	=> '貨到刷卡',
              )
    ),$checkout->get_value( 'shipping_type' ));
}
/**
 * 提交訂單時，將自訂欄位的值存進資料庫
 */
add_action('woocommerce_checkout_update_order_meta', 'update_shipping_meta');
    function update_shipping_meta( $order_id ) {
        if ($_POST['shipping_type']){
            update_post_meta( $order_id, '_shipping_type', esc_attr($_POST['shipping_type']));
        }
}
/**
 * 後台顯示發票資訊，當訂單需要三聯式發票時，才顯示公司抬頭與統編。
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_order_meta_shipping', 10, 1 );
    function custom_order_meta_shipping($order){
      echo '<p><strong>付款方式:</strong> ' . get_post_meta( $order->id, '_shipping_type', true );
}
/**
 * 使用 JS 控制前台公司抬頭與統編欄位的隱藏與顯示
 */
add_filter("woocommerce_after_checkout_form", "shipping_container");
function shipping_container(){
$output = '
<script>
var $ = jQuery.noConflict();
$(document).ready(function(){
$("#shipping_type_shipping_Cash").prop("checked", true);
});
</script>
';
echo $output;}