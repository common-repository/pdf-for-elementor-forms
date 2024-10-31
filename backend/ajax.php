<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Ajax {
	function __construct(){
		add_action( 'wp_ajax_yeepdf_builder_text', array($this,'yeepdf_builder_text') );
		add_action( 'wp_ajax_yeepdf_builder_save_video', array($this,"yeepdf_builder_save_video" ));
		add_action( 'wp_ajax_yeepdf_builder_send_email_testing', array($this,'yeepdf_builder_send_email_testing') );
		add_action( 'wp_ajax_yeepdf_builder_export_html', array($this,'yeepdf_builder_export_html') );
		add_action( 'wp_ajax_pdf_reset_template', array($this,'pdf_reset_template') );
		add_action("admin_init",array($this,"pdf_reset_template_php"));
		add_action('add_meta_boxes', array($this,'remove_wp_seo_meta_box'), 100);
	}
	function pdf_reset_template(){
		if( isset($_POST["id"])){ 
			$post_id = sanitize_text_field($_POST['id']);
			update_post_meta( $post_id, 'data_email', '' );
		}
		die();
	}
	function pdf_reset_template_php(){
		if( isset($_GET["pdf_reset"])){ 
			if(wp_verify_nonce($_GET['_wpnonce'], 'pdf_reset')){
				$post_id = sanitize_text_field($_GET['post']);
				update_post_meta( $post_id, 'data_email', '' );
			}
		}
	}
	function remove_wp_seo_meta_box(){
		remove_meta_box('wpseo_meta', "yeepdf", 'normal');
	}
	function yeepdf_builder_export_html(){
		if( isset($_POST["id"])){ 
			$post_id = sanitize_text_field($_POST['id']);
			$id = get_post_meta( $post_id,'data_email_email',true); 
			include YEEPDF_CREATOR_BUILDER_PATH."pdf-templates/header.php";
			echo do_shortcode($id);
			include YEEPDF_CREATOR_BUILDER_PATH."pdf-templates/footer.php";
		}
		die();
	}
	function yeepdf_builder_text(){
		if( class_exists("Yeepdf_Addons_Woocommerce_Shortcodes")){
			$shortcode = new Yeepdf_Addons_Woocommerce_Shortcodes;
			$order_id = sanitize_text_field($_POST["order_id"]);
			$shortcode->set_order_id($order_id);
		}
		$string_with_shortcodes = wp_filter_post_kses($_POST["text"]);
		$type = sanitize_text_field($_POST["type"]);
		if( $type == "barcode" ) {
			$string_with_shortcodes = '[wp_builder_pdf_barcode]'.$string_with_shortcodes.'[/wp_builder_pdf_barcode]';
		}elseif( $type == "qrcode" ){
			$string_with_shortcodes = '[wp_builder_pdf_qrcode]'.$string_with_shortcodes.'[/wp_builder_pdf_qrcode]';
		}
		$string_with_shortcodes = str_replace('\\',"",$string_with_shortcodes);
		$string_with_shortcodes = do_shortcode($string_with_shortcodes);
		echo $string_with_shortcodes; // phpcs:ignore WordPress.Security.EscapeOutput
		die();
	}
	function yeepdf_builder_save_video(){
		WP_Filesystem();
		global $wp_filesystem;
		if( isset($_POST["img"])){
			$img = sanitize_text_field($_POST["img"]);
		    $id = sanitize_text_field($_POST["id"]);
		    $img = str_replace('data:image/png;base64,', '', $img);
		    $img = str_replace(' ', '+', $img);
		    $img          = base64_decode($img) ;
		    $filename  = $id.".png";
		    $upload = wp_upload_dir();
		    $upload_dir = $upload['basedir'];
		    $upload_dir = $upload_dir . '/wpbuider-email-uploads';
		    if ( ! file_exists( $upload_dir ) ) {
		        wp_mkdir_p( $upload_dir );
		    }
		    $upload_path      = $upload_dir."/".$filename;
		    $success = $wp_filesystem->put_contents($upload_path, $img);
		    echo esc_url($upload['baseurl'].'/wpbuider-email-uploads/'.$filename);
		}
	    die();
	}
	function yeepdf_builder_send_email_testing(){
		$post_id = sanitize_text_field($_POST["id"]);
		$email =  sanitize_email($_POST["email"]);
		$data = wp_mail( $email, esc_html__( "WP Buider Email Testing", "pdf-for-wpforms" ), $post_id );
		if($data) {
			esc_html_e("Sent email","pdf-for-wpforms");
		}else{
			esc_html_e("Can't send email","pdf-for-wpforms");
		}
		die();	
	}
}
new Yeepdf_Ajax;