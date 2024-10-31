<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Forms_Select {
    function __construct() {
        add_action("yeepdf_builder_block_forms",array($this,"add_input_text"),20);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_input_text_settings") );
    }
    function add_input_text(){
        ?>
        <li>
            <div class="momongaDraggable" data-type="form_select">
                <i class="dashicons dashicons-list-view"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Select","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function add_input_text_settings($type){
        $rand_name = rand(10000,9999999);
        $type["block"]["form_select"]["builder"] = '
        <div class="builder-elements">
            <div class="builder-elements-content" data-type="form_select">
                <label class="before_label">Select:</label> 
                <select name="yepdf_select" size="1">
                </select>
                <label class="after_label"></label> 
            </div>
        </div>';
        $type["block"]["form_select"]["editor"]["container"]["show"]= ["form_label","form_default_select","condition"];
        $label              = array( ".builder__editor--item-form_label .yeepdf_setting_form_before_label"      =>"text");
        $label_after        = array( ".builder__editor--item-form_label .yeepdf_setting_form_after_label"       =>"text");
        $type["block"]["form_select"]["editor"]["container"]["style"]= array();
        $type["block"]["form_select"]["editor"]["inner"]["style"]= array();
        $type["block"]["form_select"]["editor"]["inner"]["attr"] = array(".before_label"=>$label,".after_label"=>$label_after,"select"=>array(".builder__editor--item-form_default_select .yeepdf_setting_form_default_value"=>"value_select"));
        return $type; 
    }
}
new Yeepdf_Settings_Builder_PDF_Forms_Select;