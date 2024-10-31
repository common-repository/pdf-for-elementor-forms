<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
if (! function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $needle_len = strlen($needle);
        return ($needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len));
    }
}
class Yeepdf_Create_PDF {
	function __construct(){
		add_filter( 'wp_mail_content_type',array($this,'set_content_type') );
		add_filter('upload_mimes', array($this,'mime_types'));
		add_action('init',array($this,'load_custom_template_woo'));
    }
    public static function cover_array_to_css($array){
		$result = implode(PHP_EOL, array_map(
			function ($v, $k) { 
				if($k == "font-family"){
					$v = "'".$v."', sans-serif;";
				}
				return sprintf("%s: %s;", $k, $v); },
			$array,
			array_keys($array)
		));
		$result = str_replace('"', "'", $result);
		return $result;
    }
    function load_custom_template_woo(){
    	add_filter( 'template_include',array($this,'template_include'),99);
    }
    function template_include($template) {
	    if( isset($_GET['pdf_preview']) ){
	        if( $_GET['pdf_preview'] == "preview") {
	            $template = YEEPDF_CREATOR_BUILDER_PATH."preview.php";   
	        }
	        if ( file_exists( $template ) ) { 
	            return $template;
	        }
	    }else{
	    	return $template;
	    }
	}
	function set_content_type(){
	    return "text/html";
	}
	function mime_types($mimes) {
	    $mimes['json'] = 'text/plain';
	    return $mimes;
	}
	public static function pdf_creator_get_header_footer($attrs,$template = "header"){
		if (!function_exists('str_get_html')) { 
			include YEEPDF_CREATOR_BUILDER_PATH."libs/simple_html_dom.php";
		}
		$data_attrs = shortcode_atts(array(
			"id_template"    => "",
			"type"           => "preview",
			"name"           => "pdf_name",
			"html"           => "",
			"woo_order_id"   => "",
			"woo_shortcode"  => true,
			"datas"          => array(),
			"return_html"    => false,
			"password"       => "",
			"params"         => array()
		),$attrs);
		$id_template = $data_attrs["id_template"];
		return self::get_html($id_template,$data_attrs["datas"],$template);
	}
	public static function pdf_creator_preview($attrs){
		$data_attrs = shortcode_atts(array(
			"id_template"    => "",
			"type"           => "preview",
			"name"           => "pdf_name",
			"html"           => "",
			"woo_order_id"   => "",
			"woo_shortcode"  => true,
			"datas"          => array(),
			"return_html"    => false,
			"password"       => "",
			"params"         => array()
		),$attrs);
		if (!function_exists('str_get_html')) { 
			include YEEPDF_CREATOR_BUILDER_PATH."libs/simple_html_dom.php";
		}
		$upload_dir = wp_upload_dir();
	    $data_orders = array();
	    $id_template = $data_attrs["id_template"];
	    $settings = get_post_meta( $id_template,'_builder_pdf_settings',true); 
        if( !is_array($settings) ) {
            $settings = array("dpi"=>96,"size"=>"A4","orientation"=>"P","show_page"=>"");
        }
	    $orientation = $settings["orientation"];
	    //woocommecre
	    if( $data_attrs["woo_order_id"] !="" && $data_attrs["woo_order_id"] != 0){
	    	$data_orders = explode(",",$data_attrs["woo_order_id"]);
			if($data_attrs["woo_shortcode"]){
				$order_shortcode = new Yeepdf_Addons_Woocommerce_Shortcodes();
				$order_shortcode->set_order_id($data_orders[0]);
			}
		}
		$id = self::get_html($id_template,$data_attrs["datas"]);
		//show header
		$html_header ="";
		if(isset($settings["header"]) && $settings["header"] != ""){
			$data_send_settings = array(
	    		"id_template"=> $settings["header"],
	    		"type"=> "html",
	    		"name"=> "name",
	    		"datas" =>$data_attrs["datas"],
	    		"return_html" =>true,
	    		"header_footer" => true
	    	);
			$header =self::pdf_creator_get_header_footer($data_send_settings,"header");
			ob_start();
			?>
			<htmlpageheader name="page-header" >
				<?php echo $header; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</htmlpageheader>
			<?php
			$html_header= ob_get_clean();
			if(ob_get_length() > 0) {
				ob_clean();
			}
		}	
		//show footer
		$html_footer ="";
		if(isset($settings["footer"]) && $settings["footer"] != ""){
			$data_send_settings = array(
	    		"id_template"=> $settings["footer"],
	    		"type"=> "html",
	    		"name"=> "name",
	    		"datas" =>$data_attrs["datas"],
	    		"return_html" =>true,
	    		"header_footer" => true
	    	);
			$footer =self::pdf_creator_get_header_footer($data_send_settings,"footer");
			ob_start();
			?>
			<htmlpagefooter name="page-footer" >
				<?php echo $footer; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</htmlpagefooter>
			<?php
			$html_footer= ob_get_clean();
			if(ob_get_length() > 0) {
				ob_clean();
			}
		}
		if( $data_attrs["return_html"] ){
			return $html_header.$id.$html_footer;
		}
		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$fontData  = Yeepdf_Settings_Main::get_list_fonts();
		$google_fonts = get_option("pdf_custom_fonts",array());
		$google_fonts = apply_filters("pdf_custom_fonts",$google_fonts );
		$size = $settings["size"];
	    $sizes = explode(",",$size);
	    if( count($sizes) > 1 ){
	    	$size = $sizes;
	    }
		$mpdf = new \Mpdf\Mpdf( [
			    'mode' => 'utf-8',
			    'format' => $size,
			    'margin_left' => 0,
			    'margin_right' => 0,
			    'margin_top' => 0,
			    'margin_bottom' => 0,
			    'margin_header' => 0,
			    'margin_footer' => 0,
			    'debugfonts' => false,
			    'debug' => false,
				'useActiveForms' => true,
			    'orientation' => $orientation,
			    'fontDir' => array_merge($fontDirs, [
			        $upload_dir["basedir"] . '/pdfs/fonts',
			    ]),
			    'fontdata' => $fontData + $google_fonts,
			    'tempDir' => $upload_dir["basedir"] . '/pdfs/tmp',
			]);
		if ( is_rtl() ) {
			$mpdf->SetDirectionality('rtl');
			$mpdf->autoScriptToLang = true;
			$mpdf->autoLangToFont = true;	
		}
		if(isset($settings["disable_active_form"]) && $settings["disable_active_form"] == "yes"){
			$mpdf->useActiveForms = false;
		}
		ob_start();
		//show header
		if( $data_attrs["type"] =="preview"){
			echo $html_header;  // phpcs:ignore WordPress.Security.EscapeOutput
		}
	    include YEEPDF_CREATOR_BUILDER_PATH."pdf-templates/header.php";
	    ?>
	    <style type="text/css">
	    	<?php
			if(isset($settings["css"])){
				echo wp_kses_post($settings["css"]);
			}
			do_action( "yeepdf_add_css", $data_attrs );
			?>
	    </style>
    	<?php
	    if( count($data_orders) > 1 ) {
	    	$i=1;
	    	foreach( $data_orders as $order_id ){
	    		$order_shortcode->set_order_id($order_id);
	    		$id = self::get_html($id_template,$datas);
	    		echo do_shortcode($id); // phpcs:ignore WordPress.Security.EscapeOutput	
	    		if( $i < count($data_orders) ) {
	    			echo wp_kses_post('<div class="page_break"></div>');
	    		}
	    		$i++;
	    	}
	    }else{
	    	if($data_attrs["html"] == ""){
    			echo do_shortcode($id); // phpcs:ignore WordPress.Security.EscapeOutput			
    		}else{
    			$message = do_shortcode($data_attrs["html"] );
				echo str_replace( "data---image", "data:image", $message) ;// phpcs:ignore WordPress.Security.EscapeOutput		
    		}
	    }
	    include YEEPDF_CREATOR_BUILDER_PATH."pdf-templates/footer.php";
	    if( $data_attrs["type"] =="preview"){
			echo $html_footer;  // phpcs:ignore WordPress.Security.EscapeOutput
		}
	    $html= ob_get_clean();
		if(ob_get_length() > 0) {
			ob_clean();
		}
		$pattern = '/([^"\/]*\/?[^".]*\.[^"]*)/';
		$imgExt = ['.png', '.gif', '.jpg', '.jpeg'];
		//change url to path	    
		$html = preg_replace_callback($pattern,
			function ($m) use ($imgExt ,$upload_dir ) {
			    if ( false === $extension = parse_url($m[0], PHP_URL_PATH) )
			        return $m[0];
			    $extension = strtolower(strrchr($extension, '.'));
			    if ( in_array($extension, $imgExt) ){
			    	$links = explode("wp-content/uploads",$m[0] );
			    	if( isset($links[1]) ){
			    		return $path_main = WP_CONTENT_DIR . '/uploads' . $links[1];
			    	}
			        return $m[0];
			    }
			    return $m[0];
			},
    		$html
    	);
	    if( isset($data_attrs["password"]) && $data_attrs["password"] != ""){
	    	$mpdf->SetProtection(array(), $data_attrs["password"], $data_attrs["password"]);
	    }
	    if( isset($settings["watermark_text"]) && $settings["watermark_text"] != ""){
	    	$mpdf->SetWatermarkText(new \Mpdf\WatermarkText($settings["watermark_text"])); 
			$mpdf->showWatermarkText = true;
	    }
	    if( isset($settings["watermark_img"]) && $settings["watermark_img"] != ""){
	    	$mpdf->SetWatermarkImage(
			    $settings["watermark_img"],
			);
			$mpdf->showWatermarkImage = true;
	    }
	    switch( $data_attrs["type"] ){
			case "download":
				$mpdf->WriteHTML($html);
				$mpdf->OutputHttpDownload($data_attrs["name"].".pdf");
				break;
			case "upload":
				$path_main = $upload_dir['basedir'] . '/pdfs/';  
				if ( ! file_exists( $path_main ) ) {
					wp_mkdir_p( $path_main );
				}
				$file_name = $path_main.$data_attrs["name"].".pdf";
				if(file_exists($file_name)){
						unlink($file_name); 
					}
				$mpdf->WriteHTML($html);
				$output = $mpdf->Output($file_name,"F");
				return $file_name;
				break;
			case "html":
				$html = str_replace( "data---image", "data:image", $html);
				echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
				break;
			default:
				$html = str_replace( "data---image", "data:image", $html);
				$mpdf->WriteHTML($html);
				$mpdf->Output($data_attrs["name"].".pdf","I");
				break;
	    }
	}
	public static function get_html( $id_template, $datas="", $footer = false ){
		$html ="";
		$data_json = get_post_meta( $id_template,'data_email',true);
		$data_json = json_decode($data_json,true);
		if(!$data_json){
			return ;
		}
		$container = $data_json["container"];
		$data_contents = $data_json["rows"];
		$datas_builder = apply_filters("yeepdf_builder_block_html",array());
		$class = "container";
		if( $footer ){
			$class ="container-".$footer;
		}
		$html = "";
		ob_start();
		?>
		<style>
			<?php
			if( $footer == false ){
				?>
				body {background-color: <?php echo esc_attr( $container["background-color"] ) ?> !important;}
				@page{
					margin: <?php echo esc_attr( $container["padding-top"] ) ?> <?php echo esc_attr( $container["padding-right"] ) ?> <?php echo esc_attr( $container["padding-bottom"] ) ?> <?php echo esc_attr( $container["padding-left"] ) ?>;
					<?php
					if( isset($container["background-image"])){
						echo wp_kses_post("background-image: ".$container["background-image"].";");
					} 
					if( isset($container["background-color"])){
						echo wp_kses_post("background-color: ".$container["background-color"].";");
					}
					?>
				}
				<?php
			}
			?>
            .<?php echo esc_attr($class) ?> {
            	<?php 
            	unset($container["padding-top"]);
            	unset($container["padding-right"]);
            	unset($container["padding-bottom"]);
            	unset($container["padding-left"]);
            	unset($container["background-color"]);
            	unset($container["background-image"]);
            	$container_css = self::cover_array_to_css($container);
				echo wp_kses_post($container_css);
				?>
            }
		</style>
		<div class="wap" width="100%" style="margin: 0 auto;">
			<div class="<?php echo esc_attr($class) ?>" style="margin: 0 auto;" >
		<?php
		foreach( $data_contents as $row){
			$row_columns = $row["columns"];
			if(isset($row["condition"])){
				$row_condition = $row["condition"];
			}else{
				$row_condition = '';
			}
			$show_row = self::is_logic($row_condition,$datas);
			$row_style = self::cover_array_to_css($row["style"]);
			if( $show_row ) {
			?>
			<div class="container-row" style="width:100%;">
				<div class="row" style="<?php echo ($row_style); ?>">
					<?php 
					$i=0;
					foreach( $row_columns as $column ){
						switch ($row["type"]){
							case "row2":
								$col_width = "50%";
								break;
							case "row3":
								if( $i == 0 ){
									$col_width = "65%";
								}else{
									$col_width = "35%";
								}
								break;
							case "row4":
								if( $i == 0 ){
									$col_width = "35%";
								}else{
									$col_width = "35%";
								}
								break;
							case "row5":
								$col_width = "33.33%";
								break;
							case "row6":
								$col_width = "25%";
								break;
							case "row7":
								$col_width = "20%";
								break;
							case "row8":
								$col_width = "16.66%";
								break;
							case "row9":
								$col_width = "14.28%";
								break;
							default:
								$col_width = "100%";
								break;
						}
						$column_css = self::cover_array_to_css($container);
						?>
						<div class="col" style="width: <?php echo esc_attr( $col_width ); ?>">
							<div class="col-container">
								<?php 
								$elements = $column["elements"];
								if(count($elements) < 1 ){
									echo '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput
								}else{
									foreach( $elements as $element ){
										$element_html = self::cover_type_element_to_html($element,$datas_builder,$datas);
							             echo $element_html; // phpcs:ignore WordPress.Security.EscapeOutput
									}	
								}
								?>
							</div>
						</div>
						<?php
						$i++;
					} ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php
			}
		}
		?>
			</div>
		</div>
		<?php
		$html= ob_get_clean();
		return $html;
	}
	public static function cover_type_element_to_html($element,$datas_builder, $datas=array()){
    	$result = "";
    	$html = "";
    	$datas_builder = $datas_builder["block"];
    	$type = $element["type"];
    	if(!isset($datas_builder[ $type ])){
    		return "";
    	}
    	$inner_attr = $element["inner_attr"];
    	$container_style = self::cover_array_to_css($element["container_style"]);
    	$inner_style = $element["inner_style"];
    	if(isset($element["condition"])){
			$element_condition = $element["condition"];
		}else{
			$element_condition = '';
		}
		if($type == "table"){
			$html_el = str_get_html($datas_builder[ $type ]["builder_table"]);
		}else{
			$html_el = str_get_html($datas_builder[ $type ]["builder"]);
		}
    	$show = self::is_logic($element_condition,$datas);
		if( $show ){
			$html_el->find('.builder-elements-content',0)->setAttribute("style",$container_style);
			foreach( $inner_attr as $key => $attrs ){
				foreach( $attrs as $k => $v ){
					if(!is_array($v)){
						$v = do_shortcode($v);
					}
					switch( $type ){
						case "qrcode":
							if( $k == "html_hide"){
								$html_el->find( $key ,0)->removeClass('hidden');
								$html_el->find( $key ,0)->innertext = '<div class="text-content"><img class="qrcode" src="[yeepdf_qrcode_new]'.strip_tags($v).'[/yeepdf_qrcode_new]" /></div>';
							}elseif( $k == "html" || $k == "html_not_change" ){
								$html_el->find( $key ,0)->remove();
							}else{
								$html_el->find( $key ,0)->setAttribute($k,$v);
							}
							break;
						case "barcode":
							if( $k == "html_hide"){
								$html_el->find( $key ,0)->removeClass('hidden');
								$html_el->find( $key ,0)->innertext = '<div class="text-content"><img src="[yeepdf_barcode_new]'.strip_tags($v).'[/yeepdf_barcode_new]" /></div>';
							}elseif( $k == "html" || $k == "html_not_change" ){
								$html_el->find( $key ,0)->remove();
							}else{
								$html_el->find( $key ,0)->setAttribute($k,$v);
							}
							break;
						case "image":
							if( $attrs["data-type"] == 1){
								$change_data = str_replace('"',"'",$attrs["data-field"]);
								$html_el->find( "img" ,0)->setAttribute("src",$change_data);
							}else{
								if( $v != ""){
									$html_el->find( $key ,0)->setAttribute($k,$v);
								}
							}
							break;
						case "signature":
							if( $attrs["data-field"] != ""){
								$change_data = str_replace('"',"'",$attrs["data-field"]);
								$html_el->find( "img" ,0)->setAttribute("src",$change_data);
							}else{
								if( $v != ""){
									$html_el->find( $key ,0)->setAttribute($k,$v);
								}
							}
						break;
						case "table":
							if( $k == "html_hide_table"){
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;;
						case "form_select":		
							if( $k == "value_select"){
								$options ='';
								foreach($v as $k_o =>$v_o) {
									$options .= '<option value="'.$v_o["value"].'">'.$v_o["text"].'</option>';
								}
								$html_el->find( "select" ,0)->__set("innertext",$options);
								$name = "yeepdf_select_".rand(1000,999999);
								$html_el->find( "select" ,0)->setAttribute("name",$name);
							}else{
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;
						case "form_checkbox":		
							if( $k == "value_checkbox"){
								$checkbox ='';
								foreach($v as $k_o =>$v_o) {
									$name = rand(1000,999999);
									$checkbox .= ' <input name="yeeaddons_checkbox_'.$name.'" type="checkbox" value="'.$v_o["value"].'" /> '.$v_o["text"];
								}
								$html_el->find( $key ,0)->__set("innertext",$checkbox);
							}else{
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;
						case "form_radio":		
							if( $k == "value_radio"){
								$checkbox ='';
								$name = rand(1000,999999);
								foreach($v as $k_o =>$v_o) {
									$checkbox .= ' <input name="yeeaddons_radio_'.$name.'" type="radio" value="'.$v_o["value"].'" /> '.$v_o["text"];
								}
								$html_el->find( $key ,0)->__set("innertext",$checkbox);
							}else{
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;
						case "form_text":		
							if( $k == "value"){
								$name = "yeepdf_text_".rand(1000,999999);
								$html_el->find( "input" ,0)->setAttribute("name",$name);
								$html_el->find( "input" ,0)->setAttribute("value",$v);
							}else{
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;
						case "form_textarea":		
							if( $k == "text"){
								$html_el->find( $key ,0)->innertext = $v;
								$name = "yeepdf_textarea_".rand(1000,999999);
								$html_el->find( "textarea" ,0)->setAttribute("name",$name);
							}else{
								$html_el->find( $key ,0)->__set("innertext",$v);
							}
							break;
						case "order_detail":		
							if( $k == "html_hide"){
								$html_el->find( $key ,0)->removeClass('hidden');
								$table = $v;
								$table= preg_replace('/<td[^>]*\bhidden\b[^>]*>.*?<\/td>/is', '', $table);
								$table= preg_replace('/<th[^>]*\bhidden\b[^>]*>.*?<\/th>/is', '', $table);
								$html_el->find( $key ,0)->__set("innertext",$table);
							}
							elseif( $k == "html"){
								$html_el->find( $key ,0)->remove();
							}
							break;
						default:
							//text
							if( $k == "html_hide"){
								$html_el->find( $key ,0)->removeClass('hidden');
								$html_el->find( $key ,0)->innertext = $v;
							}
							elseif( $k == "html"){
								$html_el->find( $key ,0)->remove();
							}
							else{
								$html_el->find( $key ,0)->setAttribute($k,$v);
							}
							$html_el = apply_filters( "yeepdf_html_type_".$type, $html_el, $element,$datas_builder, $datas );
							break;
					}
				}
			}
			$html_el = apply_filters( "yeepdf_html_type", $html_el, $element,$datas_builder, $datas );
			$html_el = str_get_html($html_el);
			if($type == "table"){ 
				//don't set
			}else{
				foreach( $inner_style as $key => $style ){
					if($key == ".text-content"){
						$key = ".text-content-data";
					}
					$in_style = self::cover_array_to_css($style);
					foreach($html_el->find($key) as $html_el_inner){
						$html_el_inner->setAttribute("style",$in_style);
					}
				}
			}
			return $html_el;
		}
		return $html;
    }
    public static function is_logic($conditional, $datas = null, $test = false){
    	if( isset($_GET["pdf_preview"] )){
    		return true;
    	}
    	if( $conditional != "" ){
	    	$conditional = rawurldecode($conditional);
			$conditional = json_decode($conditional,true);
	    	if( isset($conditional["conditional"]) && count($conditional["conditional"])> 0 ){
	    		$check = array();
	    		foreach( $conditional["conditional"] as $condition ){
	    			$name = $condition["name"];
					if (strpos($name, "{") === false) { 
						$name = '['.$name.']';
					}
	    			$rule = $condition["rule"];
	    			$value = $condition["value"];
	    			if(is_array($value)){
	    				$value = implode(",",$value);
	    			}
	    			if( isset( $datas[ $name] )){
	    				$data_value = $datas[$name];
	    				if(is_array($datas[$name])){
		    				$data_value = implode(",",$datas[$name]);
		    			}
	    			}else{
	    				$data_value = do_shortcode($name);
	    			}
	    			switch( $rule ){
						case "is":
							if( $value == $data_value){
								$check[] = true;
							}
							break;
						case "isnot":
							if( $value != $data_value){
								$check[] = true;
							}
							break;
						case 'greater_than':
							if( $data_value > $value ){
								$check[] = true;
							}
							break;
						case 'less_than':
							if( $data_value < $value ){
								$check[] = true;
							}
							break;
						case 'contains':
							if( str_contains($data_value,$value) ){
								$check[] = true;
							}
							break;
						case 'not_contains':
							if( !str_contains($data_value,$value) ){
								$check[] = true;
							}
							break;
						case 'starts_with':
							if( str_starts_with($data_value,$value) ){
								$check[] = true;
							}
							break;
						case 'ends_with':
							if( str_ends_with($data_value,$value) ){
								$check[] = true;
							}
		 					break;
						}
	    		}
	    		if( $conditional["logic"] == "all" ){
	    			if(count($check) == count($conditional["conditional"]) ){
	    				if( $conditional["type"] == "show" ){ 
	    					return true;
	    				}else{
	    					return false;
	    				}
	    			}else{
	    				if( $conditional["type"] == "show" ){ 
	    					return false;
	    				}else{
	    					return true;
	    				}
	    			}
	    		}else{
	    			if( count($check) >0 ){ 
	    				if( $conditional["type"] == "show" ){ 
	    					return true;
	    				}else{
	    					return false;
	    				}
	    			}else{
	    				if( $conditional["type"] == "show" ){ 
	    					return false;
	    				}else{
	    					return true;
	    				}
	    			}
	    		}
	    	}
	    }
    	return true;
    }
}
new Yeepdf_Create_PDF;