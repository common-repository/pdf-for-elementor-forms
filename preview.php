<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( isset($_GET['id'])) {
    $id_template = sanitize_text_field( $_GET['id'] );
    $type = "preview";
    if( isset($_GET["download"]) ){
        $type = "download";
    }
    if( isset($_GET["html"]) ){
        $type = "html";
    }
    $name ="pdf_name";
    if( isset($_GET["pdf_name"]) ){
        $name = sanitize_text_field($_GET["pdf_name"]);
        $name = urldecode($name);
        $name = sanitize_file_name($name);
    }
    $user = wp_get_current_user();
    $allowed_roles = array('editor', 'administrator', 'author',"shop_manager");
    $check = false;
    if ( isset( $_REQUEST['_wpnonce']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'pdf_creator' ) ) {
        $check = true;
    }
    if( array_intersect($allowed_roles, $user->roles ) ) {
        $check = true;  
    }
    if( $check ){
        $order_id = "";
        if( isset($_GET["woo_order"]) ) {
            $order_id = sanitize_text_field( $_GET['woo_order'] );
        }
        $data_send_settings = array(
            "id_template"=> $id_template,
            "type"=> $type,
            "woo_order_id" =>$order_id,
            "name" => $name
        );
        Yeepdf_Create_PDF::pdf_creator_preview($data_send_settings);
    }
}