<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_block","superaddons_pdf_builder_block_spacer",60);
function superaddons_pdf_builder_block_spacer(){
    ?>
    <li>
        <div class="momongaDraggable" data-type="spacer">
            <i class="dashicons dashicons-image-flip-vertical"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Spacer","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'yeepdf_builder_block_html', "superaddons_pdf_builder_block_spacer_load" );
function superaddons_pdf_builder_block_spacer_load($type){
   $type["block"]["spacer"]["builder"] = '
   <div class="builder-elements">
        <div class="builder-elements-content" data-type="spacer">
            <div class="builder-spacer" style="height:50px"></div>
        </div>
    </div>';
   //Show editor
    $type["block"]["spacer"]["editor"]["container"]["show"]= ["padding","background","height","condition"];
    
    $inner_style = array(
            ".builder__editor--item-background .builder__editor_color"=>"background-color",
            ".builder__editor--item-height .text_height"=>"height",
            ".builder__editor--item-background .image_url"=>"background-image",
        );
    $padding = Yeepdf_Global_Data::$padding;
    $type["block"]["spacer"]["editor"]["container"]["style"]= array_merge($padding);

    $type["block"]["spacer"]["editor"]["inner"]["style"]=[".builder-spacer" => $inner_style];
    return $type;
}

