<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//add_action("yeepdf_builder_block","superaddons_pdf_builder_block_button",30);
function superaddons_pdf_builder_block_button(){
    ?>
    <li>
        <div class="momongaDraggable" data-type="button">
            <i class="pdf-creator-icon icon-doc-landscape"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Button","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'yeepdf_builder_block_html', "pdf_builder_block_button_load" );
function pdf_builder_block_button_load($type){
    $type["block"]["button"]["builder"] = '
    <div class="builder-elements">
        <div class="builder-elements-content" data-type="button" style="text-align: center;">
            <div class="yeepdf_button"><a class="yeepdf_button_a" href="#">Button</a></div>
        </div>
    </div>';
    $padding = Yeepdf_Global_Data::$padding;
    $margin = Yeepdf_Global_Data::$margin;
    $pd_mg = array_merge($padding,$margin);
    $text_align = Yeepdf_Global_Data::$text_align;
    $color = Yeepdf_Global_Data::$color;
    $pd_mg_al = $pd_mg;
    $background = Yeepdf_Global_Data::$background;
    $pd_mg_al_bg = array_merge($pd_mg_al,$background);
    $border = Yeepdf_Global_Data::$border;
    $pd_mg_al_bg_bd = array_merge($pd_mg_al_bg,$border);
    $font_size = array(
        ".builder__editor--item-button .font_size"=>"font-size"
    );
    $style_a= array_merge($color,$font_size);
    
    //Show editor
    $type["block"]["button"]["editor"]["container"]["show"]= ["text-align","padding","margin","border","button","background","color","condition"];
    //Style container
    $type["block"]["button"]["editor"]["container"]["style"]= Yeepdf_Global_Data::$text_align;
    //Style inner
    
    $type["block"]["button"]["editor"]["inner"]["style"]=[
                                                        ".yeepdf_button" => $pd_mg_al_bg_bd,
                                                        ".yeepdf_button a" => $style_a,
                                                        ];
    // Data Attr
    $type["block"]["button"]["editor"]["inner"]["attr"]=[".yeepdf_button_a"=>  array(".builder__editor--item-button .button_text"=>"text",
        ".builder__editor--item-button .button_url"=>"href") ];
    return $type;
}
