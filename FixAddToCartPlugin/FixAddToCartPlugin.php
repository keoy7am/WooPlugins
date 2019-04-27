<?php
/**
  Plugin Name: 修正加入購物車按鈕
  Plugin URI: https://github.com/keoy7am/WooPlugins/tree/master/FixAddToCartPlugin
  Description: 強制加入購物車按鈕顯示為 "加入購物車"
  Author: Elliot.Chen
  Version: 1.0
  Author URI: https://github.com/keoy7am
  License: GPLv2
**/

defined('ABSPATH') || exit;

// 修正 相關產品 的 Javascript
$jQueryStr = <<<'jQueryStrInput'
<script>
jQuery(document).ready(function($) {
  jQuery(".ajax_add_to_cart").text("加入購物車");
  jQuery(".ajax_add_to_cart").attr("aria-label","加入購物車");
});
</script>
jQueryStrInput;

function modify_add_to_cart_text($text)
{
    if (did_action('wc_product_table_before_get_data')) {
        $text = '加入購物車';
    }
    return $text;
}

/**
 * 透過  jQuery修正
 */
function jquery_modify_add_to_cart_text()
{
    global  $jQueryStr;
    echo $jQueryStr;
}

function add_custom_related_products_hooks()
{
    $add_result1 = add_filter('add_to_cart_text', 'modify_add_to_cart_text');
    $add_result2 = add_filter('woocommerce_product_single_add_to_cart_text', 'modify_add_to_cart_text');
    $add_result3 = add_filter('woocommerce_product_add_to_cart_text', 'modify_add_to_cart_text');
    $add_result4 = add_action('woocommerce_after_single_product_summary', 'jquery_modify_add_to_cart_text', 5);

    // Dump
    if (defined('WP_DEBUG') && true === WP_DEBUG) {
        echo "相關商品插件：偵錯資訊<br/>";
        echo "<hr/>result of add_filter to  add_to_cart_text = " . $add_result1 . "<hr/>";
        echo "<hr/>result of add_filter to woocommerce_product_single_add_to_cart_text = " . $add_result2 . "<hr/>";
        echo "<hr/>result of add_filter to  woocommerce_product_add_to_cart_text = " . $add_result3 . "<hr/>";
        echo "<hr/>result of add_action to jquery_modify_add_to_cart_text = " . $add_result4 . "<hr/>";
    }
}
add_action('plugins_loaded', 'add_custom_related_products_hooks');
