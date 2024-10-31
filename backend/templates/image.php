<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_block","superaddons_pdf_builder_block_image",20);
function superaddons_pdf_builder_block_image(){
    ?>
    <li>
        <div class="momongaDraggable" data-type="image">
            <i class="pdf-creator-icon icon-picture"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Image","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_action( 'yeepdf_builder_block_html', "superaddons_pdf_builder_block_image_load" );
function superaddons_pdf_builder_block_image_load($type){
    $type["block"]["image"]["builder"] = '
    <div class="builder-elements" >
        <div class="builder-elements-content" data-type="image">
            <img data-type="0" data-field="0" style="width:100%;height:auto;" src="'.YEEPDF_CREATOR_BUILDER_URL.'images/your-image.png" alt="">
        </div>
    </div>';
    //Show editor
    $type["block"]["image"]["editor"]["container"]["show"]= ["padding","margin","image","text-align","width_height","condition"];
    //Style container
    $container_style = array(
            ".builder__editor--item-background .builder__editor_color"=>"background-color",
            ".builder__editor--item-background .image_url"=>"background-image",
        );
    $text_align = Yeepdf_Global_Data::$text_align;
    $border  = Yeepdf_Global_Data::$border;
    $padding = Yeepdf_Global_Data::$padding;
    $margin  = Yeepdf_Global_Data::$margin;
    $size    = Yeepdf_Global_Data::$width_height;
    $pd_mg   = array_merge($padding,$margin);
    $pd_mg_bd=  $pd_mg; 
    $pd_mg_bd_size=  array_merge($pd_mg_bd,$size);  
    $type["block"]["image"]["editor"]["container"]["style"]= $text_align;
    $type["block"]["image"]["editor"]["inner"]["style"]=["img" => $pd_mg_bd_size];
    $type["block"]["image"]["editor"]["inner"]["attr"]= ["img"=>[
        ".builder__editor--item-image .image_url"=>"src",
        ".builder__editor--item-image .yeepdf-image-type-editor"=>"data-type",
        ".builder__editor--item-image .yeepdf-image-type-editor-field"=>"data-field"
        ]];
    return $type;
}