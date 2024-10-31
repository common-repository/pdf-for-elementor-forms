<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action( 'yeepdf_builder_block_html', "superaddons_pdf_builder_block_main_load" );
function superaddons_pdf_builder_block_main_load($type) {
    $type["block"]["main"]["editor"]["container"]["show"]= ["background","padding","settings"];
    $padding = Yeepdf_Global_Data::$padding;
    $type["block"]["main"]["editor"]["container"]["style"]= array_merge($padding,array(
                ".builder__editor--item-background .builder__editor_color"=>"background-color",
                ".builder__editor--item-background .image_url"=>"background-image",
                ".builder__editor--item-settings .font_family"=>"font-family",
                ".builder__editor--item-settings .font-size-main"=>"font-size",
                ".builder__editor--item-settings .builder__editor_color"=>"color",
            ));
    return $type;
}
class Yeepdf_Global_Data {
    public static $background = array(
        ".builder__editor--item-background .builder__editor_color"=>"background-color",
        ".builder__editor--item-background .image_url"=>"background-image",
        ".builder__editor--item-background .builder__editor_background_repeat"=>"background-repeat",
        ".builder__editor--item-background .builder__editor_background_size"=>"background-size",
        ".builder__editor--item-background .builder__editor_background_position"=>"background-position",
    );
    public static $padding = array(
        ".builder__editor--item-padding .builder__editor--padding-top"=>"padding-top",
        ".builder__editor--item-padding .builder__editor--padding-bottom"=>"padding-bottom",
        ".builder__editor--item-padding .builder__editor--padding-left"=>"padding-left",
        ".builder__editor--item-padding .builder__editor--padding-right"=>"padding-right",
    );
    public static $margin = array(
        ".builder__editor--item-margin .builder__editor--padding-top"=>"margin-top",
        ".builder__editor--item-margin .builder__editor--padding-bottom"=>"margin-bottom",
        ".builder__editor--item-margin .builder__editor--padding-left"=>"margin-left",
        ".builder__editor--item-margin .builder__editor--padding-right"=>"margin-right",
    );
    public static $text_align = array(
        ".builder__editor--item-text-align .text_align"=>"text-align"
    );
    public static $color = array(
        ".builder__editor--item-text-color .builder__editor_color"=>"color"
    );
    public static $border_color = array(
        ".builder__editor--item-border_color .builder__editor_color"=>"color"
    );
    public static $width_height = array(
        ".builder__editor--item-width_height .text_width"=>"width",
        ".builder__editor--item-width_height .text_height"=>"height"
    );
    public static $width = array(
        ".builder__editor--item-width .text_width"=>"width"
    );
    public static $border = array(
        ".builder__editor--item-border-main .border_style"=>"border-style",
        ".builder__editor--item-border-main .builder__editor_color"=>"border-color",
        ".builder__editor--item-border-main .border_width"=>"border-width",
    );
}