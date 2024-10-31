<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Editor {
    function __construct(){
        add_action("yeepdf_builder_tab__editor",array($this,"yeepdf_builder_tab__editor"),100);  
    }
    public static function get_color_pick($text = "Color Pick"){
        ?>
        <div class="builder__editor--color">
            <label><?php echo esc_html( $text ) ?></label>
            <div class="">
                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color">
            </div>
        </div>
        <?php
    }
    public static function get_padding($text="",$class="") {
        ?>
        <div class="yeepdf_setting_group <?php echo esc_attr( $class ) ?>">
            <?php if($text!= ""){
                ?>
            <div class="yeepdf_setting_title">
                <?php echo esc_html( $text ) ?>
            </div>
            <?php
            } ?>
            <div class="yeepdf_setting_row">
                <div class="yeepdf_settings_group-wrapper">
                    <label class="yeepdf_checkbox_label"><?php esc_html_e("Top","pdf-for-wpforms") ?></label>
                    <div class="yeepdf_setting_input-wrapper">
                        <input name="yeepdf_name[]" class="builder__editor--padding-top setting_input" step="1" type="number" data-after_value="px">
                    </div>
                </div>
                <div class="yeepdf_settings_group-wrapper">
                    <label class="yeepdf_checkbox_label"><?php esc_html_e("Right","pdf-for-wpforms") ?></label>
                    <div class="yeepdf_setting_input-wrapper">
                        <input name="yeepdf_name[]" class="builder__editor--padding-right setting_input" step="1" type="number" data-after_value="px">
                    </div>
                </div>
                <div class="yeepdf_settings_group-wrapper">
                    <label class="yeepdf_checkbox_label"><?php esc_html_e("Bottom","pdf-for-wpforms") ?></label>
                    <div class="yeepdf_setting_input-wrapper">
                        <input name="yeepdf_name[]" class="builder__editor--padding-bottom setting_input" step="1" type="number" data-after_value="px">
                    </div>
                </div>
                <div class="yeepdf_settings_group-wrapper">
                    <label class="yeepdf_checkbox_label"><?php esc_html_e("Left","pdf-for-wpforms") ?></label>
                    <div class="yeepdf_setting_input-wrapper">
                        <input name="yeepdf_name[]" class="builder__editor--padding-left setting_input" step="1" type="number" data-after_value="px">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    function yeepdf_builder_tab__editor($post){  
    ?>
        <div class="yeepdf-builder-goback">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <span class="yeepdf-builder-goback_edit"><?php esc_attr_e( "Edit", "pdf-for-wpforms" ) ?></span>
            <span class="yeepdf-builder-goback_block"></span>
        </div>
        <?php do_action( "yeepdf_builder_tab__editor_before",$post ); ?>
        <div class="builder__editor--item builder__editor--item-html">
            <div class="builder__editor--html">
                <label><?php esc_html_e("Content","pdf-for-wpforms") ?></label>
                <textarea name="yeepdf_name[]" id="builder__editor--js" class="builder__editor--js"></textarea>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-field">
            <label><?php esc_html_e("Map Field","pdf-for-wpforms") ?></label>
            <div class="builder__editor--button-url">
                <div class="yeepdf-filed-type-field">
                    <select name="yeepdf_name[]" class="yeepdf-filed-type-editor-field yeepdf_setting_input">
                        <option value="0"><?php esc_html_e("Choose Field","pdf-for-wpforms") ?></option>
                        <?php 
                        Yeepdf_Settings_Main::get_all_shortcodes_select_option();
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-image">
            <label><?php esc_html_e("Image","pdf-for-wpforms") ?></label>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Type", "pdf-for-wpforms" ) ?></label>
                        <select name="yeepdf_name[]" class="yeepdf-image-type-editor yeepdf_setting_input">
                            <option value="0"><?php esc_html_e("Upload Image","pdf-for-wpforms") ?></option>
                            <option value="1"><?php esc_html_e("Use Field","pdf-for-wpforms") ?></option>
                        </select>
                    </div>
                    <div class="yeepdf_settings_group-wrapper yeepdf-image-type-upload">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Source URL", "pdf-for-wpforms" ) ?></label>
                        <input name="yeepdf_name[]" type="text" class="image_url yeepdf_setting_input" placeholder="Source url">
                    </div>
                    <div class="yeepdf_settings_group-wrapper yeepdf-image-type-upload">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Upload", "pdf-for-wpforms" ) ?></label>
                        <input name="yeepdf_name[]" type="button" class="upload-editor--image button button-primary" value="Upload">
                    </div>
                    <div class="yeepdf_settings_group-wrapper yeepdf-image-type-field">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Upload", "pdf-for-wpforms" ) ?></label>
                        <select name="yeepdf_name[]" class="yeepdf-image-type-editor-field yeepdf_setting_input">
                            <option value="0"><?php esc_html_e("Choose Field","pdf-for-wpforms") ?></option>
                            <?php 
                            Yeepdf_Settings_Main::get_all_shortcodes_select_option();
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-button">
            <label><?php esc_html_e("Button","pdf-for-wpforms") ?></label>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Button text", "pdf-for-wpforms" ) ?></label>
                        <input name="yeepdf_name[]" type="text" value="" class="button_text yeepdf_setting_input">
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Font size", "pdf-for-wpforms" ) ?></label>
                        <input name="yeepdf_name[]" type="number" class="yeepdf_setting_input font_size"data-after_value="px">
                    </div>        
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Button url", "pdf-for-wpforms" ) ?></label>
                        <input name="yeepdf_name[]" type="text" value="" class="button_url yeepdf_setting_input">
                    </div>       
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-background">
            <label><?php esc_html_e("Background","pdf-for-wpforms") ?></label>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row builder__editor--button-url">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label">Color</label>
                        <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input">
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label">Image</label>
                        <input name="yeepdf_name[]" type="text" class="image_url yeepdf_setting_input" placeholder="Source url">
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label">Upload</label>
                        <input name="yeepdf_name[]" type="button" class="upload-editor--image button button-primary" value="Upload">
                    </div>
                </div>
                <div class="yeepdf_setting_row ">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Background-repeat", "pdf-for-wpforms" ) ?></label>
                        <select name="yeepdf_name[]" class="yeepdf_setting_input builder__editor_background_repeat">
                            <option value="no-repeat">no-repeat</option>
                            <option value="repeat">repeat</option>
                            <option value="repeat-x">repeat-x</option>
                            <option value="repeat-y">repeat-y</option>
                        </select>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Background-size", "pdf-for-wpforms" ) ?></label>
                        <select name="yeepdf_name[]" class="yeepdf_setting_input builder__editor_background_size">
                            <option value="cover">cover</option>
                            <option value="auto">auto</option>
                            <option value="contain">contain</option>
                        </select>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_attr_e( "Background-position", "pdf-for-wpforms" ) ?></label>
                        <select name="yeepdf_name[]" class="yeepdf_setting_input builder__editor_background_position">
                            <option value="0% %0">left top</option>
                            <option value="0% 100%">left bottom</option>
                            <option value="0% 50%">left center</option>
                            <option value="100% 0%">right top</option>
                            <option value="100% 100%">right bottom</option>
                            <option value="100% 50%">right center</option>
                            <option value="50% 50%">center center</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-color">
            <?php  Yeepdf_Editor::get_color_pick(esc_html__("Color","pdf-for-wpforms")) ?>
        </div>
        <div class="builder__editor--item builder__editor--item-text-align">
            <label><?php esc_html_e("Text align","pdf-for-wpforms") ?></label>
            <div class="builder__editor--align">
                <a class="button__align builder__editor--align-left" data-value="left"><i
                        class="pdf-creator-icon icon-align-left"></i></a>
                <a class="button__align builder__editor--align-center" data-value="center"><i
                        class="pdf-creator-icon icon-align-justify"></i></a>
                <a class="button__align builder__editor--align-right" data-value="right"><i
                        class="pdf-creator-icon icon-align-right"></i></a>
                <input name="yeepdf_name[]" type="text" value="left" class="text_align hidden">
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-width">
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Width","pdf-for-wpforms") ?></label>
                        <input name="yeepdf_name[]" type="text" class="yeepdf_setting_input text_width" data-after_value="px" />
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-height">
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Height","pdf-for-wpforms") ?></label>
                        <input name="yeepdf_name[]" type="text" class="yeepdf_setting_input text_height" data-after_value="px"  />
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-width_height">
            <label><?php esc_html_e("Size","pdf-for-wpforms") ?></label>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Width","pdf-for-wpforms") ?></label>
                        <input name="yeepdf_name[]" type="text" class="yeepdf_setting_input text_width" data-after_value="px" />
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Height","pdf-for-wpforms") ?></label>
                        <input name="yeepdf_name[]" type="text" class="yeepdf_setting_input text_height" data-after_value="px" />
                    </div>
                </div>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-padding">
            <label><?php esc_html_e("Padding","pdf-for-wpforms") ?></label>
            <?php  Yeepdf_Editor::get_padding() ?>
        </div>
        <div class="builder__editor--item builder__editor--item-margin">
            <label><?php esc_html_e("Margin","pdf-for-wpforms") ?></label>
            <?php Yeepdf_Editor::get_padding() ?>
        </div>
        <div class="builder__editor--item builder__editor--item-border_color">
            <label><?php esc_html_e("Border Color","pdf-for-wpforms") ?></label>
            <?php  Yeepdf_Editor::get_color_pick(esc_html__("Color","pdf-for-wpforms")) ?>
        </div>
        <div class="builder__editor--item builder__editor--item-border">
            <div class="yeepdf_setting_group builder__editor--item-border-main">
                <div class="yeepdf_setting_title"><?php esc_html_e("Borders Style","pdf-for-wpforms") ?></div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Color","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color">
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Width","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <input name="yeepdf_name[]" type="number" class="yeepdf_setting_input border_width" data-after_value="px">
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Style","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <select name="yeepdf_name[]" class="yeepdf_setting_input border_style">
                                <option value="solid">solid</option>
                                <option value="dotted">dotted</option>
                                <option value="dashed">dashed</option>
                                <option value="double">double</option>
                                <option value="groove">groove</option>
                                <option value="ridge">ridge</option>
                                <option value="inset">inset</option>
                                <option value="outset">outset</option>
                                <option value="none">none</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php  //Yeepdf_Editor::get_padding(__("Border Width","pdf-for-wpforms"),"builder__editor--item-border-width")?>
                <?php  //Yeepdf_Editor::get_padding(__("Border radius","pdf-for-wpforms","builder__editor--item-border-radius") )?>
            </div>
        </div>
        <div class="builder__editor--item builder__editor--item-condition">
            <label><?php esc_html_e("Conditional Logic","pdf-for-wpforms") ?></label>
            <textarea name="yeepdf_name[]" class="builder__editor--condition hidden"></textarea>
            <?php
            $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
            if($pro){
            ?>
            <a href="#" class="manager_condition button"><?php esc_html_e("Manager Conditional Logic","pdf-for-wpforms") ?></a>
            <div id="yeepdf-popup-content" style="display:none;">
                <div class="yeepdf-popup-content">
                    <select name="yeepdf_name[]" id="yeepdf-logic-type">
                        <option value="show"><?php esc_html_e("Show","pdf-for-wpforms") ?></option>
                        <option value="hide"><?php esc_html_e("Hide","pdf-for-wpforms") ?></option>
                    </select>
                    <?php esc_html_e(" this field if","pdf-for-wpforms") ?>
                    <select name="yeepdf_name[]" id="yeepdf-logic-logic">
                        <option value="all"><?php esc_html_e("All","pdf-for-wpforms") ?></option>
                        <option value="any"><?php esc_html_e("Any","pdf-for-wpforms") ?></option>
                    </select>
                    <?php esc_html_e("of the following match:","pdf-for-wpforms") ?>
                    <div class="text-center">
                        <a href="#"
                            class="yeepdf_condition_add button"><?php esc_html_e("Add Condition","pdf-for-wpforms") ?></a>
                    </div>
                    <div class="yeepdf-popup-layout">
                    </div>
                </div>
            </div>
            <?php }else{
            ?>
            <p>
            <div class="pro_disable pro_disable_fff"><?php esc_html_e("Upgrade to pro version","pdf-for-wpforms") ?></div>
            </p>
            <?php
            } ?>
        </div>
        <?php
        do_action( "yeepdf_builder_tab__editor_after",$post );
    }
}
new Yeepdf_Editor();