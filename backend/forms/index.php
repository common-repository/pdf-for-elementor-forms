<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Forms_Backend {
    function __construct() {
        add_action('yeepdf_builder_tab_block_addons', array($this,"add_forms"),10, 2);
        add_action("yeepdf_builder_block_forms",array($this,"add_input_text"),10);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_input_text_settings") );
        add_action('yeepdf_builder_tab__editor_before', array($this,'add_editor'));
    }
    function add_forms(){
        ?>
        <div class="builder__widget--inner">
            <div class="builder__widget_tab builder__widget_genaral">
                <div class="builder__widget_tab_title"><span class="builder__widget_tab_title_t"><?php esc_attr_e( "Forms", "yeepdf") ?></span><span
                        class="builder__widget_tab_title_icon dashicons dashicons-arrow-down-alt2"></span><span
                        class="builder__widget_tab_title_icon dashicons dashicons-arrow-up-alt2"></span>
                </div>
                <ul class="momongaPresets momongaPresets_data">
                    <?php do_action( "yeepdf_builder_block_forms" )?>
                </ul>
            </div>
        </div>
        <?php
    }
    function add_input_text(){
        ?>
        <li>
            <div class="momongaDraggable" data-type="form_text">
                <i class="dashicons dashicons-editor-textcolor"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Text Input","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function add_input_text_settings($type){
        $type["block"]["form_text"]["builder"] = '
        <div class="builder-elements">
            <div class="builder-elements-content" data-type="form_text">
                <label class="before_label">Label:</label> 
                <input type="text" name="yeepdf" />
                <label class="after_label"></label> 
            </div>
        </div>';
        $type["block"]["form_text"]["editor"]["container"]["show"]= ["form_label","form_default_val","text-align","background","width","condition"];
        $padding = Yeepdf_Global_Data::$padding;
        $margin = Yeepdf_Global_Data::$margin;
        $text_align = Yeepdf_Global_Data::$text_align;
        $width = Yeepdf_Global_Data::$width;
        //$border = Yeepdf_Global_Data::$border_color;
        $background = Yeepdf_Global_Data::$background;
        $type["block"]["form_text"]["editor"]["container"]["style"]= array();
        $label = array( ".builder__editor--item-form_label .yeepdf_setting_form_before_label"   =>"text");
        $label_after = array( ".builder__editor--item-form_label .yeepdf_setting_form_after_label"   =>"text");
        $type["block"]["form_text"]["editor"]["inner"]["style"]= array("input"=>array_merge($background,$text_align,$width));
        $type["block"]["form_text"]["editor"]["inner"]["attr"] = array(".before_label"=>$label,".after_label"=>$label_after,"input"=>array(".builder__editor--item-form_default_val .yeepdf_setting_form_default"=>"value"));
        return $type; 
    }
    function add_editor(){
        ?>
        <div class="builder__editor--item builder__editor--item-form_label">
            <div class="builder__editor--html">
                <label><?php esc_html_e("Label","pdf-for-wpforms") ?></label>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Before text","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_form_before_label"  type="text" >
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("After text","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_form_after_label"  type="text" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-form_default_val">
            <div class="builder__editor--html">
                <label><?php esc_html_e("Default Value","pdf-for-wpforms") ?></label>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Value","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_form_default"  type="text" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-form_default_select">
            <div class="builder__editor--html">
                <label><?php esc_html_e("Value","pdf-for-wpforms") ?></label>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Value","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <textarea name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_form_default_value" ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
new Yeepdf_Settings_Builder_PDF_Forms_Backend;