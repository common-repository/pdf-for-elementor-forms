<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_block","superaddons_pdf_builder_block_divider",40);
function superaddons_pdf_builder_block_divider(){
    ?>
    <li>
        <div class="momongaDraggable" data-type="divider">
            <i class="dashicons dashicons-minus"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Divider","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'yeepdf_builder_block_html', "superaddons_pdf_builder_block_divider_load" );
function superaddons_pdf_builder_block_divider_load($type){
    $type["block"]["divider"]["builder"] = '
    <div class="builder-elements">
            <div class="builder-elements-content" data-type="divider" style="padding: 15px 0;">
                <div class="builder-hr"></div>
            </div>
        </div>';
    //Show editor
        $type["block"]["divider"]["editor"]["container"]["show"]= ["padding","margin","background","height","condition"];
        $padding = Yeepdf_Global_Data::$padding;
        $margin = Yeepdf_Global_Data::$margin;
        $pd_mg = array_merge($padding,$margin);
        $background = Yeepdf_Global_Data::$background;
        $inner_style =array_merge($background,array(".builder__editor--item-height .text_height"=>"height"));
        $type["block"]["divider"]["editor"]["container"]["style"]= array_merge($pd_mg);

        $type["block"]["divider"]["editor"]["inner"]["style"]=[".builder-hr" => $inner_style];
    return $type;
}

