<?php
/**
  Plugin Name: 發票插件
  Plugin URI: https://github.com/keoy7am/WooPlugins/tree/master/InvoicePlugin
  Description: 二聯／三聯（包括公司抬頭統編）
  Author: Elliot.Chen
  Version: 1.0
  Author URI: https://github.com/keoy7am
  License: GPLv2
**/


/**
 * 移除 WooCommerce 內建的公司名稱欄位
 * 公司抬頭欄位另自訂
 */
add_filter( 'woocommerce_billing_fields', 'remove_defalut_company_name' );
function remove_defalut_company_name($fields) {
unset($fields['billing_company']);
return $fields;
}

/**
 * 自訂欄位
 */
add_action( 'woocommerce_before_order_notes', 'add_invoice_type' );
function add_invoice_type( $checkout ) {
woocommerce_form_field( 'invoice_type', array(
'type' => 'radio',
'class' => array( 'form-row-wide' ),
'label' => '選擇開立發票種類',
'options' => array(
'invoice_no'	=> '二聯式發票',
'invoice_yes'	=> '三聯式發票',
)
),$checkout->get_value( 'invoice_type' ));
woocommerce_form_field( 'company_name', array(
'type' => 'text',
'class' => array( 'form-row-wide' ),
'label' => '公司抬頭',
),$checkout->get_value( 'company_name' ));
woocommerce_form_field( 'company_id', array(
'type' => 'text',
'class' => array( 'form-row-wide' ),
'label' => '統一編號',
),$checkout->get_value( 'company_id' )); 
}

/**
 * 在每次提交訂單時，把我們自行新增的三個欄位的值存到資料庫
 */
add_action('woocommerce_checkout_update_order_meta', 'update_invoice_meta');
function update_invoice_meta( $order_id ) {
if ($_POST['invoice_type']){
update_post_meta( $order_id, 'invoice_type', esc_attr($_POST['invoice_type']));
update_post_meta( $order_id, 'company_name', esc_attr($_POST['company_name']));
update_post_meta( $order_id, 'company_id', esc_attr($_POST['company_id']));
}
}

/**
 * 在後台顯示發票資訊，而只要當訂單需要三聯式發票時，才會顯示公司抬頭與統編的資料。
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_order_meta_invoice', 10, 1 );
function custom_order_meta_invoice($order){
if( get_post_meta( $order->id, 'invoice_type', true ) == 'invoice_yes' ){
echo '<h3><strong>發票:</strong> 三聯式發票</h3>';
echo '<p><strong>公司抬頭:</strong> ' . get_post_meta( $order->id, 'company_name', true );
echo '<br><strong>統一編號:</strong> ' . get_post_meta( $order->id, 'company_id', true ).'</p>';
} else {
echo '<h3><strong>發票:</strong> 二聯式發票</h3>';
} 
}

/**
 * 使用 JS 控制前台公司抬頭與統編欄位的隱藏與顯示
 */
add_filter("woocommerce_after_checkout_form", "invoice_container");
function invoice_container(){
$output = '
<style>label.radio{display:inline-block;margin-right:1rem;}</style>
<script>
var $ = jQuery.noConflict();
$(document).ready(function(){
$("#invoice_type_invoice_no").prop("checked", true);
$("#company_name_field,#company_id_field").hide();
$("input[name=invoice_type]").on("change",function(){
if($("#invoice_type_invoice_yes").is(":checked")) {
$("#company_name_field,#company_id_field").fadeIn();
} else {
$("#company_name_field,#company_id_field").fadeOut();
}
})
});
</script>
';
echo $output;
}
