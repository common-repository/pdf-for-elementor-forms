<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Builder_PDF_Shortcode {
	function __construct() {
		add_shortcode( 'yeepdf_barcode', array($this,'shortcode_barcode') );
		add_shortcode( 'yeepdf_barcode_new', array($this,'shortcode_barcode_new') );
		add_shortcode( 'yeepdf_qrcode', array($this,'shortcode_qrcode') );
		add_shortcode( 'yeepdf_qrcode_new', array($this,'shortcode_qrcode_new') );
		add_shortcode( 'pdf_download', array($this,'pdf_download') );
		$lists = self::list_shortcodes(false);
		foreach($lists as $key=>$values){
			foreach($values as $k=>$v){
				if(!is_array($v)){
					add_shortcode( $k, array($this,'shortcode_main') );
				}else{
					foreach($v as $kc=>$vc){
						add_shortcode( $kc, array($this,'shortcode_main') );
					}
				}
			}
		}
		add_filter( 'yeepdf_builder_shortcode', array($this,'builder_shortcode') );
	}
	public static function list_shortcodes($filter = true){
		$shortcodes = array(
			"Genaral" => array(
				"yeepdf_site_name" => "Site Name",
				"yeepdf_site_url" => "Site URL",
				"yeepdf_admin_email" => "Admin Email",
				"yeepdf_current_date" => "Current Date",
				"yeepdf_current_time" => "Current Time",
				"yeepdf_images" => "Images(link,link,..)",
			),
			"User" => array(
				"yeepdf_user_login_url" => "User Login URL",
				"yeepdf_user_logout_url" => "User Logout URL",
				"yeepdf_user_id" => "User ID",
				"yeepdf_user_login" => "User Login",
				"yeepdf_user_name" => "User Name",
				"yeepdf_user_email" => "User Email",
				"yeepdf_user_url" => "User URL",
				"yeepdf_user_display_name" => "User Display Name",
			),
			"PDF" => array(
				"{PAGENO}" => "Current page number",
				"{nbpg}" => "Total page number",
				"yeepdf_dotab" => "Dottab",
				"yeepdf_dotab_content" => "Dottab content",
			),
		);
		if($filter){
			return apply_filters( "yeepdf_shortcodes", $shortcodes );
		}else{
			return $shortcodes;
		}
		
	}
	function builder_shortcode($shortcodes){
		$lists = self::list_shortcodes();
		foreach($lists as $key=>$values){
			foreach($values as $k=>$v){
				if(!is_array($v)){
					$shortcodes[$k] = do_shortcode( "[".$k."]");
				}else{
					foreach($v as $kc=>$vc){
						$shortcodes[$kc] = do_shortcode( "[".$kc."]");
					}
				}
			}
		}
		return $shortcodes;
	}
	function pdf_download($atts,$content = ""){
		return get_option("pdf_download_last");
	}
	function shortcode_main($atts, $content="", $tag=""){
		switch ($tag) {
			case "yeepdf_site_url":
				return site_url();
				break;
			case "yeepdf_site_name":
				if ( is_multisite() ) {
					$site_name = get_network()->site_name;
				} else {
					$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				}
				return $site_name;
				break;
			case "yeepdf_current_date":
				return wp_date(get_option('date_format'));
				break;
			case "yeepdf_current_time":
				return date(get_option('time_format'));
				break;
			case "yeepdf_admin_email":
				return get_option('admin_email');
				break;
			case "yeepdf_user_login":
			case "yeepdf_user_name":	
				$current_user = wp_get_current_user();
				return $current_user->user_login;
				break;
			case "yeepdf_user_email":
				$current_user = wp_get_current_user();
				return $current_user->user_email;
				break;
			case "yeepdf_user_url":
				return site_url();
				break;
			case "yeepdf_user_display_name":
				$current_user = wp_get_current_user();
				return $current_user->display_name;
				break;
			case "yeepdf_user_login_url":
				return '<a href="' . wp_login_url() . '"> '.esc_html__('Log in', "pdf-for-wpforms").' </a>';
				break;
			case "yeepdf_user_logout_url":
				return '<a href="' . wp_logout_url( home_url()) . '"> '.esc_html__('Log in', "pdf-for-wpforms").' </a>';
				break;
			case "yeepdf_dotab":
				$atts = shortcode_atts( array(
					'outdent' => 0,
				), $atts );
				if( is_admin() ){
					return '<span class="dotab">..........</span>';		
				}else{
					return '<dottab outdent="'.$atts["outdent"].'" />';	
				}
			case "yeepdf_dotab_content":
				return '<span class="dotab_content">'.$content.'</span>';	
				break;
			case "yeepdf_user_id":
				return $this->get_ip();
				break;
			case "yeepdf_images":
				$images = "";
				$atts = shortcode_atts( array(
					'width' => 'auto',
					'height' => 'auto',
				), $atts);
				$width = $atts["width"];
				$height = $atts["height"];
				if(is_numeric($height) ){
					$height .= "px";
				}
				if(is_numeric($width) ){
					$width .= "px";
				}
				if($content != ""){
					$fields= explode(",",$content);
					foreach( $fields as $field ){
						$field = trim($field);
						$field = str_replace('"',"'",$field);
						$images .='<img src="'.$field.'" style="width: '.$width.'; height: '.$height.'" />';
					}
				}
				return $images;
				break;
			default:
				return $tag;
				break;
		}
	}
	function shortcode_qrcode($atts, $content= "Change Text"){
		if($content == ""){
			$content ="Change Text";
		}	
		$content = do_shortcode($content);
		$content = wp_strip_all_tags($content);
		$img_qr = QRcode::png($content,'*');
		return '<div class="text-content"><img class="qrcode" src="data:image/png;base64,'.$img_qr.'"></div>';
	}
	function shortcode_qrcode_new($atts, $content= "Change Text"){
		if($content == ""){
			$content ="Change Text";
		}	
		$content = do_shortcode($content);
		$img_qr = QRcode::png($content,'*');
		return 'data---image/png;base64,'.$img_qr;
	}
	function shortcode_barcode($atts, $content= "Change Text"){
		if($content == ""){
			$content ="Change Text";
		}	
		$content = do_shortcode($content);
		$content = wp_strip_all_tags($content);
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$img = base64_encode($generator->getBarcode($content, $generator::TYPE_CODE_128));
		return '<img class="barcode" src="data:image/png;base64,'.$img.'">';
	}
	function shortcode_barcode_new($atts, $content= "Change Text"){
		if($content == ""){
			$content ="Change Text";
		}	
		$content = do_shortcode($content);
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$img = base64_encode($generator->getBarcode($content, $generator::TYPE_CODE_128));
		return 'data---image/png;base64,'.$img;
	}
	function get_ip() {
		$ip = false;
		if ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ip = filter_var( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ), FILTER_VALIDATE_IP );
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = filter_var( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ), FILTER_VALIDATE_IP );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ips = explode( ',', wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			if ( is_array( $ips ) ) {
				$ip = filter_var( $ips[0], FILTER_VALIDATE_IP );
			}
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
		}
		$ip       = false !== $ip ? $ip : '127.0.0.1';
		$ip_array = explode( ',', $ip );
		$ip_array = array_map( 'trim', $ip_array );
		return sanitize_text_field( apply_filters( 'pdf_get_ip', $ip_array[0] ) );
	}
}
new Yeepdf_Builder_PDF_Shortcode;