<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Forms_Checkbox {
    function __construct() {
        add_action("yeepdf_builder_block_forms",array($this,"add_input_text"),30);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_input_text_settings") );
    }
    function add_input_text(){
        ?>
        <li>
            <div class="momongaDraggable" data-type="form_checkbox">
                <i class="dashicons dashicons-yes-alt"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Checkbox","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function add_input_text_settings($type){
        $type["block"]["form_checkbox"]["builder"] = '
        <div class="builder-elements">
            <div class="builder-elements-content" data-type="form_checkbox">
                <label class="before_label">Checkbox:</label>
                <span class="yeepdf_checkbox_container">
                </span> 
                <label class="after_label"></label> 
            </div>
        </div>';
        $type["block"]["form_checkbox"]["editor"]["container"]["show"]= ["form_label","form_default_select","condition"];
        $label              = array( ".builder__editor--item-form_label .yeepdf_setting_form_before_label"      =>"text");
        $label_after        = array( ".builder__editor--item-form_label .yeepdf_setting_form_after_label"       =>"text");
        $type["block"]["form_checkbox"]["editor"]["container"]["style"]= array();
        $type["block"]["form_checkbox"]["editor"]["inner"]["style"]= array();
        $type["block"]["form_checkbox"]["editor"]["inner"]["attr"] = array(
            ".before_label"                 =>$label,
            ".after_label"                  =>$label_after,
            ".yeepdf_checkbox_container"    =>array(".builder__editor--item-form_default_select .yeepdf_setting_form_default_value"=>"value_checkbox"));
        return $type; 
    }
}
new Yeepdf_Settings_Builder_PDF_Forms_Checkbox;