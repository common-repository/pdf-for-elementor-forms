<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Forms_Radio {
    function __construct() {
        add_action("yeepdf_builder_block_forms",array($this,"add_input_text"),40);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_input_text_settings") );
    }
    function add_input_text(){
        ?>
        <li>
            <div class="momongaDraggable" data-type="form_radio">
                <i class="dashicons dashicons-marker"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Radio","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function add_input_text_settings($type){
        $type["block"]["form_radio"]["builder"] = '
        <div class="builder-elements">
            <div class="builder-elements-content" data-type="form_radio">
                <label class="before_label">Radio:</label>
                <span class="yeepdf_radio_container">
                </span> 
                <label class="after_label"></label> 
            </div>
        </div>';
        $type["block"]["form_radio"]["editor"]["container"]["show"]= ["form_label","form_default_select","condition"];
        $label              = array( ".builder__editor--item-form_label .yeepdf_setting_form_before_label"      =>"text");
        $label_after        = array( ".builder__editor--item-form_label .yeepdf_setting_form_after_label"       =>"text");
        $type["block"]["form_radio"]["editor"]["container"]["style"]= array();
        $type["block"]["form_radio"]["editor"]["inner"]["style"]= array();
        $type["block"]["form_radio"]["editor"]["inner"]["attr"] = array(
            ".before_label"                 =>$label,
            ".after_label"                  =>$label_after,
            ".yeepdf_radio_container"    =>array(".builder__editor--item-form_default_select .yeepdf_setting_form_default_value"=>"value_radio"));
        return $type; 
        return $type; 
    }
}
new Yeepdf_Settings_Builder_PDF_Forms_Radio;