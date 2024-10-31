<?php
use Elementor\Plugin;
use ElementorPro\Core\Utils\Collection;
use ElementorPro\Modules\Forms\Fields\Upload;
class Yeepdf_Creator_form_widget_Backend {
	private $attachments_array = array();
	private $submission_pdf = array();
	private $pdf_id = "";
	function __construct(){
		add_filter("yeepdf_shortcodes",array($this,"add_shortcode"));
		add_action("yeepdf_head_settings",array($this,"add_head_settings"));
		add_action( 'save_post_yeepdf',array( $this, 'save_metabox' ), 10, 2 );
        add_action('elementor_pro/forms/process', array($this,'send_data'),11, 2);
        add_action('elementor_pro/forms/new_record', array($this,'submit_update_data'));
        add_filter("superaddons_pdf_check_pro",array($this,"check_pro"));
	}
	function check_pro($pro){
		$check = get_option( '_redmuber_item_1538');
		if($check == "ok"){
			$pro = true;
		}
		return $pro;
	}
	function elementor_conditional_logic_check_single($value_id,$operator,$value){
        $rs = false;
        switch($operator) {
			case "==":
				if( $value_id == $value){
					$rs = true;
				}   
			break;
			case "!=":
				if( $value_id != $value){
						$rs = true;
				}
				break;
			case "e":
				if( $value_id == ""){
						$rs = true;
				}
				break;
			case "!e":
				if( $value_id != ""){
						$rs = true;
				}
				break;
			case "c":
				if( str_contains($value_id,$value) ){
					$rs = true;
				}
				break;
			case "!c":
				if( !str_contains($value_id,$value) ){
					$rs = true;
				}
			break;
			case "^":
				if( str_starts_with($value_id,$value) ){
					$rs = true;
				}
				break;
			case "~":
				if( str_ends_with($value_id,$value) ){
					$rs = true;
				}
				break;
			case ">":
				if( $value_id > $value){
					$rs = true;
				}
				break;
			case "<":
				if( $value_id < $value){
						$rs = true;
				}
				break;
			case "array":
				$values= array_map('trim', explode(',', $value));
				if( in_array($value_id,$values)){
						$rs = true;
				}
				break;
			case "!array":
				$values= array_map('trim', explode(',', $value));
				if( !in_array($value_id,$values)){
						$rs = true;
				}
				break;
			case "array_contain":
				$values= array_map('trim', explode(',', $value));
				foreach($values as $vl){
					if( str_contains($value_id,$vl) ){
						$rs = true;
					}
				}
				break;
			case "!array_contain":
				$values= array_map('trim', explode(',', $value));
				$rs = true;
				foreach($values as $vl){
					if( str_contains($value_id,$vl) ){
						$rs = false;
					}
				}    
				break;   
            default: 
                break;
            }
            return $rs;
    }
	protected function get_control_id( $control_id ) {
        return $control_id . $this->pdf_id;
    }
	function check_conditional_logic($record){
		$settings = $record->get( 'form_settings' );
		$send_status = true;
		if( isset($settings[$this->get_control_id( 'pdf_conditional_logic' )]) && $settings[$this->get_control_id( 'pdf_conditional_logic' )] == "yes" ){
			$display = $settings[$this->get_control_id( 'pdf_conditional_logic_display' )];
            $trigger = $settings[$this->get_control_id( 'pdf_conditional_logic_trigger' )];
            $datas = $settings[$this->get_control_id( 'pdf_conditional_logic_datas' )];
            $rs = array();
            $form_fields = $record->get("fields");
            foreach ( $datas as $logic_key => $logic_values ) {
                if(isset($form_fields[$logic_values["conditional_logic_id"]])){
                    $value_id = $form_fields[$logic_values["conditional_logic_id"]]["value"];
                    if( is_array($value_id) ){
                        $value_id = implode(", ",$value_id);
                    }
                }else{
                	$value_id = $logic_values["conditional_logic_id"];
                }
                $operator = $logic_values["conditional_logic_operator"];
                $value = $logic_values["conditional_logic_value"];
                $rs[] = $this->elementor_conditional_logic_check_single($value_id,$operator,$value);
            }
            if( $trigger =="ALL"  ){
                $check_rs = true;
                foreach ( $rs as $fkey => $fvalue ) {
                    if( $fvalue == false ){
                        $check_rs =false;
                        break;
                    }
                }
			}else{
				$check_rs = false;
				foreach ( $rs as $fkey => $fvalue ) {
					if( $fvalue == true ){
						$check_rs =true;
						break;
					}
				}
			}
			if($display == "show"){
					if( $check_rs == true ){
						$send_status = true;
					}else{
						$send_status = false;
					}
			}else{
					if( $check_rs == true ){
						$send_status = false;
					}else{
						$send_status = true;
					}
			}
		}
		return $send_status;
	}
	function send_data($record, $ajax_handler){
		$raw_fields = $record->get( 'fields' );
		$form_data = array();
	    foreach ( $raw_fields as $id => $field ) {
	        $form_data[ "[field id='".$id."']" ] = $field['value'];
	    }
		$upload_dir   = wp_upload_dir();
		for($i = 1; $i<11; $i++) {
			if($i != 1){
				$this->pdf_id = "_".$i;
			}
			$send_status = $this->check_conditional_logic($record);
			$template = $record->get_form_settings( $this->get_control_id("template_pdf"));
			if( $send_status ==  true && $template != ""){
				$show_id = $record->get_form_settings( $this->get_control_id('pdf_name_show_id'));
				$attach_email_pdf = $record->get_form_settings( $this->get_control_id('attach_email_pdf'));
				$name = $record->get_form_settings( $this->get_control_id('name_pdf'));
				$password = $record->get_form_settings( $this->get_control_id('password_pdf'));
				$check_attach = $record->get_form_settings( $this->get_control_id('attach_email_pdf'));
				if( $template != "" && $template > 0) {
					$paths = $this->add_pdf($name,$show_id,$password,$template,$form_data,$record);
					//Get submit Submission id
					$path_main = $upload_dir['baseurl'] . '/pdfs/'.$paths["name"].".pdf";
					$this->submission_pdf = array($path_main);
					if($check_attach == "yes"){
						$this->attachments_array[] = $paths["path"];
					}
				}
			}
		}
		// if local var has attachments setup filter hook
		if ( 0 < count( $this->attachments_array )) {
			if(defined('ElementorPro\Modules\Forms\Fields\Upload::MODE_ATTACH')){
				$settings = $record->get( 'form_settings' );
				$attachments_mode_attach = $this->get_file_by_attachment_type( $settings['form_fields'], $record, Upload::MODE_ATTACH );
				$attachments_mode_both = $this->get_file_by_attachment_type( $settings['form_fields'], $record, Upload::MODE_BOTH );
				$attachments_mode = array_merge($attachments_mode_attach,$attachments_mode_both);
				$this->attachments_array = array_merge($attachments_mode,$this->attachments_array);
			}
			add_filter( 'wp_mail', [ $this, 'wp_mail' ] );
			add_action( 'elementor_pro/forms/new_record', [ $this, 'remove_wp_mail_filter' ], 5 );
		}
	}
	function get_file_by_attachment_type( $form_fields, $record, $type ) {
		return Collection::make( $form_fields )
			->filter( function ( $field ) use ( $type ) {
				return $type === $field['attachment_type'];
			} )
			->map( function ( $field ) use ( $record ) {
				$id = $field['custom_id'];
				return $record->get( 'files' )[ $id ]['path'] ?? null;
			} )
			->filter()
			->flatten()
			->values();
	}
	function add_pdf($name,$show_id,$password,$template,$form_data,$record){
		if( $name == ""){
    		$name= "elementor-form";
    	}else{
    		$name = $record->replace_setting_shortcodes( $name );
    		$name = $this->replace_setting_shortcodes( $name, $form_data );
    	}
    	if( $password != ""){
    		$password= $record->replace_setting_shortcodes( $password );
    		$password = $this->replace_setting_shortcodes( $password, $form_data );
    	}
		$name = sanitize_file_name($name);
		if($show_id == "yes"){
			$name = rand(1000,9999)."-".$name;
		}
    	$data_send_settings = array(
    		"id_template"=> $template,
    		"type"=> "html",
    		"name"=> $name,
    		"datas" =>$form_data,
    		"return_html" =>true,
    	);
    	$content =Yeepdf_Create_PDF::pdf_creator_preview($data_send_settings);
    	$content = $record->replace_setting_shortcodes( $content );
    	$content = $this->replace_setting_shortcodes( $content, $form_data );
    	$message =$this->replace_content_shortcodes( $content, $record, '<br>' );
    	$message= apply_filters( 'elementor_pro/forms/wp_mail_message', $message );
    	$data_send_settings_download = array(
    		"id_template"=> $template,
    		"type"=> "upload",
    		"name"=> $name,
    		"datas" =>$form_data,
    		"html" =>$message,
    		"password" =>$password,
    	);
    	$path =Yeepdf_Create_PDF::pdf_creator_preview($data_send_settings_download);
    	return array("name"=>$name,"path"=>$path);
	}
	function submit_update_data($record){
		$submission_pdf = $this->submission_pdf;
		update_option("pdf_download_last",implode(",",$submission_pdf));
		if( count($submission_pdf) > 0  ){
			$form_post_id = $record->get_form_settings( 'id' );
			$submission_id = $this->get_last_submission_id($form_post_id);
			if($submission_id > 0){
				foreach($submission_pdf as $path_main){
					if($path_main != ""){
						$this->add_meta_submission($submission_id,$path_main);
					}
				}
			}
		}
	}
	function get_last_submission_id($form_id = null) {
		global $wpdb;
		$table_e_submissions = $wpdb->prefix."e_submissions";
		if(!$form_id){
			$datas = $wpdb->get_row("SELECT * FROM $table_e_submissions ORDER BY id DESC",ARRAY_A);
		}else{
			$datas = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_e_submissions WHERE element_id = %s ORDER BY id DESC", $form_id), ARRAY_A);
		}
		if( isset($datas["id"])){
			return $datas["id"];
		}else{
			return null;
		}
	}
	function add_meta_submission($submission_id,$link){
		global $wpdb;
		$table_e_submissions_meta = $wpdb->prefix."e_submissions_values";
		$wpdb->insert(
			$table_e_submissions_meta,
			array(
				'submission_id' => $submission_id,
				'key' => 'Yeepdf',
				'value' => $link,
			),
			array(
				'%d',
				'%s',
				'%s'
			)
		);
		return $wpdb->insert_id;
	}
	public function remove_wp_mail_filter() {
		$this->attachments_array = [];
		remove_filter( 'wp_mail', [ $this, 'wp_mail' ] );
	}
	public function wp_mail( $args ) {
		$old_attachments = $args['attachments'];
		$args['attachments'] = array_merge( $this->attachments_array, $old_attachments );
		return $args;
	}
	private function replace_content_shortcodes( $email_content, $record, $line_break ) {
		$email_content = do_shortcode( $email_content );
		$all_fields_shortcode = '[all-fields]';
		if ( false !== strpos( $email_content, $all_fields_shortcode ) ) {
			$text = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
			$style = 'padding-top: 25px;padding-bottom: 25px;border-top: 1px solid #e2e2e2;min-width: 113px;padding-right: 10px;line-height: 22px;';
			$style_first = 'padding-top: 25px;padding-bottom: 25px;min-width: 113px;padding-right: 10px;line-height: 22px;';
			$i = 0;
			foreach ( $record->get( 'fields' ) as $field ) {
				$formatted = $this->field_formatted( $field );
				if(is_array($formatted)){
					if ( ( 'textarea' === $field['type'] ) && ( '<br>' === $line_break ) ) {
						$formatted["value"] = str_replace( [ "\r\n", "\n", "\r" ], '<br />', $formatted["value"] );
					}
					if($i == 0){
						$text .= '<tr>
						<td style="'.$style_first.'"><strong>'.$formatted["title"].'</strong></td>
						<td style="'.$style_first.'">'.$formatted["value"].'</td>
						</tr>';
					}else{
						$text .= '<tr>
						<td style="'.$style.'"><strong>'.$formatted["title"].'</strong></td>
						<td style="'.$style.'">'.$formatted["value"].'</td>
						</tr>';
					}
					$i++;
				} 
			}
			$text .="</table>";
			$email_content = str_replace( $all_fields_shortcode, $text, $email_content );
		}
		return $email_content;
	}
	public function replace_setting_shortcodes( $setting, $form_data ) {
		// Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
		return preg_replace_callback( "/(\[field[^]]*id='(\w+)'[^]]*\])/", function( $matches ) use ( $form_data ) {
			$value = '';
			if ( isset( $form_data[ "[field id='".$matches[2]."']" ] ) ) {
				$src = $form_data[ "[field id='".$matches[2]."']" ];
				$value = $src;
				preg_match('/(src=["\'](.*?)["\'])/', $src, $match); 
				if( isset($match[0])){
					$split = preg_split('/["\']/', $match[0]);
					$src = $split[1];
					$value = $src;
				}
			}
			return $value;
		}, $setting );
	}
	private function field_formatted( $field ) {
		$formatted = '';
		if ( ! empty( $field['title'] ) ) {
			$formatted = array("title"=>$field['title'],"value"=>$field['value']);
		} elseif ( ! empty( $field['value'] ) ) {
			$formatted = array("title"=>$field['value'],"value"=>$field['value']);
		}
		return $formatted;
	}
	function get_list_forms(){
		global $wpdb;
		$lists_form= $wpdb->get_results( "SELECT $wpdb->postmeta.meta_value, $wpdb->posts.ID, $wpdb->posts.post_title FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->postmeta.post_id  WHERE $wpdb->postmeta.meta_key = '_elementor_data'  AND $wpdb->posts.post_status = 'publish'");
		return $lists_form;
	}
	function add_head_settings($post){
		global $wpdb;
		$lists_form = $this->get_list_forms();
		?>
		<div class="yeepdf-testting-order">
			<select name="pdfcreator_formwidget" class="builder_pdf_woo_testing">
				<option value="-1"><?php esc_attr_e( "---Elementor Form---", "pdf-for-elementor") ?></option>
			<?php
			$datas = get_post_meta( $post->ID,'_pdfcreator_formwidget',true);
			foreach ( $lists_form as $form ) {
				$post_id = $form->ID;
				$document = Plugin::$instance->documents->get( $post_id );
				if ( ! $document ) {
					continue;
				}
				$data = $document->get_elements_data();
				if ( empty( $data ) ) {
					continue;
				}
				$data_form = $data;
				foreach ( $data_form as $section ){
					foreach ( $section["elements"] as $column ){ 
						if( isset($column["elements"]) && count($column["elements"]) > 0 ){
							$datas_elements = $column["elements"];
							foreach ( $datas_elements as $widget ){ 
								if( isset($widget["elements"]) && count($widget["elements"]) > 0 ){
									$datas_elements_inner = $widget["elements"];
									foreach ( $datas_elements_inner as $widget_inner ){ 
										if( isset($widget_inner["elements"]) && count($widget_inner["elements"]) > 0 ){
											$datas_elements_inner_1 = $widget_inner["elements"];
											foreach ( $datas_elements_inner_1 as $widget_inner1 ){
												if( isset($widget_inner1["elements"]) && count($widget_inner1["elements"]) > 0 ){
													$datas_elements_inner_2 = $widget_inner1["elements"];
													if( isset($datas_elements_inner_2["widgetType"]) && isset($datas_elements_inner_2["settings"]["form_name"]) ) {
														$form_id = $datas_elements_inner_2["id"];
														$form_id = $form_id . "-" .$post_id;
														$form_title = $datas_elements_inner_2["settings"]["form_name"];
														?>
															<option <?php selected($datas,$form_id) ?> value="<?php echo esc_attr($form_id) ?>"><?php echo esc_html($form_title  .' - '. $widget["id"] .' ('.$form->post_title.')') ?></option>
														<?php
													}
												}else{
													if( isset($widget_inner1["widgetType"]) && isset($widget_inner1["settings"]["form_name"]) ) {
														$form_id = $widget_inner1["id"];
														$form_id = $form_id . "-" .$post_id;
														$form_title = $widget_inner1["settings"]["form_name"];
														?>
															<option <?php selected($datas,$form_id) ?> value="<?php echo esc_attr($form_id) ?>"><?php echo esc_html($form_title  .' - '. $widget["id"] .' ('.$form->post_title.')') ?></option>
														<?php
													}
												}
											}
										}else{
											if( isset($widget_inner["widgetType"]) && isset($widget_inner["settings"]["form_name"]) ) {
												$form_id = $widget_inner["id"];
												$form_id = $form_id . "-" .$post_id;
												$form_title = $widget_inner["settings"]["form_name"];
												?>
													<option <?php selected($datas,$form_id) ?> value="<?php echo esc_attr($form_id) ?>"><?php echo esc_html($form_title  .' - '. $widget["id"] .' ('.$form->post_title.')') ?></option>
												<?php
											}
										}
									}
								}else{
									if( isset($widget["widgetType"]) && isset($widget["settings"]["form_name"]) ) {
										$form_id = $widget["id"];
										$form_id = $form_id . "-" .$post_id;
										$form_title = $widget["settings"]["form_name"];
										?>
											<option <?php selected($datas,$form_id) ?> value="<?php echo esc_attr($form_id) ?>"><?php echo esc_html($form_title  .' - '. $widget["id"] .' ('.$form->post_title.')') ?></option>
										<?php
									}
								}
							}
						}else{
							if( isset($column["widgetType"]) && isset($column["settings"]["form_name"]) ) {
								$form_id = $column["id"];
								$form_id = $form_id . "-" .$post_id;
								$form_title = $column["settings"]["form_name"];
								?>
									<option <?php selected($datas,$form_id) ?> value="<?php echo esc_attr($form_id) ?>"><?php echo esc_html($form_title  .' - '. $column["id"] .' ('.$form->post_title.')') ?></option>
								<?php
							}
						}
					}
				}
			}
			?>
		</select>
	</div>
	<?php
    }
    function save_metabox($post_id, $post){
        if( isset($_POST['pdfcreator_formwidget'])) {
            $id = sanitize_text_field($_POST['pdfcreator_formwidget']);
            update_post_meta($post_id,'_pdfcreator_formwidget',$id);
        }
    }
	function add_shortcode($shortcode) {
		$inner_shortcode["all-fields"] = "All Submitted Fields";
		if( isset($_GET["post"])){
			$post_id = sanitize_text_field($_GET["post"]);
			$form_id = get_post_meta( $post_id,'_pdfcreator_formwidget',true);
			$post_ids = explode("-",$form_id);
			$fields = array();
			if(isset($post_ids[0]) && isset($post_ids[1]) && $post_ids[0] != ""){
				$datas = get_post_meta( $post_ids[1],'_elementor_data',true);
				$data_form = json_decode($datas,true);
				$tags = array();
				foreach ( $data_form as $section ){
					foreach ( $section["elements"] as $column ){ 
	        			if( isset($column["elements"]) && count($column["elements"]) > 0 ){
							$datas_elements = $column["elements"];
							foreach ( $datas_elements as $widget ){ 
								if( isset($widget["elements"]) && count($widget["elements"]) > 0 ){
									$datas_elements_inner = $widget["elements"];
									foreach ( $datas_elements_inner as $widget_inner ){
										if( isset($widget_inner["elements"]) && count($widget_inner["elements"]) > 0 ){
											$datas_elements_inner_1 = $widget_inner["elements"];
											foreach ( $datas_elements_inner_1 as $widget_inner_1 ){
												if( isset($widget_inner_1["elements"]) && count($widget_inner_1["elements"]) > 0 ){
													$datas_elements_inner_2 = $widget_inner_1["elements"];
													foreach ( $datas_elements_inner_2 as $widget_inner_2 ){
														if( isset($widget_inner_2["widgetType"]) && isset($widget_inner_2["settings"]["form_name"]) ) {
															if( $post_ids[0] == $widget_inner_2["id"] ){
																$tags= $widget_inner_2["settings"]["form_fields"];
																break 5;
															}
														}
													}
												}else{
													if( isset($widget_inner_1["widgetType"]) && isset($widget_inner_1["settings"]["form_name"]) ) {
														if( $post_ids[0] == $widget_inner_1["id"] ){
															$tags= $widget_inner_1["settings"]["form_fields"];
															break 4;
														}
													}
												}
											}
										}else{
											if( isset($widget_inner["widgetType"]) && isset($widget_inner["settings"]["form_name"]) ) {
												if( $post_ids[0] == $widget_inner["id"] ){
													$tags= $widget_inner["settings"]["form_fields"];
													break 4;
												}
											}
										}
									}
								}else{
									if( isset($widget["widgetType"]) && isset($widget["settings"]["form_name"]) ) {
										if( $post_ids[0] == $widget["id"] ){
											$tags= $widget["settings"]["form_fields"];
											break 3;
										}
									}
								}
							}
						}else{
							if( isset($column["widgetType"]) && isset($column["settings"]["form_name"]) ) {
								if( $post_ids[0] == $column["id"] ){
		    						$tags= $column["settings"]["form_fields"];
		    						break 2;
		    					}
							}
						}
	        		}
		    	}
				foreach ($tags as $tag_inner):
					if( isset($tag_inner["_id"])  ) {
				    	if(isset($tag_inner["field_label"]) ){
				    		$label = $tag_inner["field_label"];
				    	}else{
				    		$label = $tag_inner["custom_id"];
				    	}
				    	$inner_shortcode["field id='".$tag_inner["custom_id"]."'"] = $label;
				    }
				endforeach;   
			}else{
				$inner_shortcode["https://pdf.add-ons.org/shortcode-not-showing-in-pdf-template/"] = __("Please select a form","pdf-for-elementor");
			}
		}else{
			$inner_shortcode["https://pdf.add-ons.org/shortcode-not-showing-in-pdf-template/"] = __("Please select a form","pdf-for-elementor");
		}
		$shortcode["Elementor Form"] = $inner_shortcode; 
		return $shortcode;
	}
	function notification_save($notification,$form){
		$notification['pdf_template'] = rgpost( '_gform_setting_pdf_template' );
		return $notification;
    }	
}
new Yeepdf_Creator_form_widget_Backend;