<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Forms_Textarea {
    function __construct() {
        add_action("yeepdf_builder_block_forms",array($this,"add_input_text"),50);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_input_text_settings") );
    }
    function add_input_text(){
        ?>
        <li>
            <div class="momongaDraggable" data-type="form_textarea">
                <i class="dashicons dashicons-button"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Textarea","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function add_input_text_settings($type){
        $type["block"]["form_textarea"]["builder"] = '
        <div class="builder-elements">
            <div class="builder-elements-content" data-type="form_textarea">
                <label class="before_label">Label:</label> 
                <textarea name="yeepdf_textarea"></textarea>
                <label class="after_label"></label>  
            </div>
        </div>';
        $type["block"]["form_textarea"]["editor"]["container"]["show"]= ["form_label","form_default_val","text-align","background","width_height","condition"];
        $padding = Yeepdf_Global_Data::$padding;
        $margin = Yeepdf_Global_Data::$margin;
        $text_align = Yeepdf_Global_Data::$text_align;
        $width = Yeepdf_Global_Data::$width_height;
        //$border_color = Yeepdf_Global_Data::$border_color;
        $container_style = array(
                ".builder__editor--item-background .builder__editor_color"=>"background-color",
                ".builder__editor--item-background .image_url"=>"background-image",
            );
        $type["block"]["form_textarea"]["editor"]["container"]["style"]= array();
        $label = array( ".builder__editor--item-form_label .yeepdf_setting_form_before_label"   =>"text");
        $label_after = array( ".builder__editor--item-form_label .yeepdf_setting_form_after_label"   =>"text");
        $type["block"]["form_textarea"]["editor"]["inner"]["style"]= array("textarea"=>array_merge($padding,$container_style,$text_align,$margin,$width));
        $type["block"]["form_textarea"]["editor"]["inner"]["attr"] = array(".before_label"=>$label,".after_label"=>$label_after,"textarea"=>array(".builder__editor--item-form_default_val .yeepdf_setting_form_default"=>"text"));
        return $type; 
    }
}
new Yeepdf_Settings_Builder_PDF_Forms_Textarea;