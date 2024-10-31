<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_block","superaddons_pdf_builder_block_signature",199);
function superaddons_pdf_builder_block_signature(){
    $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
    $class ="";
    $title ="";
    if( !$pro){
        $class ="pro_disable";
        $title =" Pro Version";
    }
    ?>
    <li>
        <div class="momongaDraggable <?php echo esc_attr($class) ?>" title="<?php echo esc_html($title) ?>" data-type="signature">
            <i class="dashicons dashicons-admin-customizer"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Signature","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_action( 'yeepdf_builder_block_html', "superaddons_pdf_builder_block_signature_load" );
function superaddons_pdf_builder_block_signature_load($type){
    $type["block"]["signature"]["builder"] = '
    <div class="builder-elements" >
        <div class="builder-elements-content" data-type="signature">
            <img data-type="0" data-field="0" style="width:150px;height:39px;" src="'.YEEPDF_CREATOR_BUILDER_URL.'images/your-image.png" alt="">
        </div>
    </div>';
    //Show editor
    $type["block"]["signature"]["editor"]["container"]["show"]= ["padding","margin","field","border","text-align","width_height","condition"];
    //Style container
    $container_style = array(
            ".builder__editor--item-background .builder__editor_color"=>"background-color",
            ".builder__editor--item-background .image_url"=>"background-image",
        );
    $text_align = Yeepdf_Global_Data::$text_align;
    $padding = Yeepdf_Global_Data::$padding;
    $margin = Yeepdf_Global_Data::$margin;
    $border = Yeepdf_Global_Data::$border;
    $width_height = Yeepdf_Global_Data::$width_height;
    $type["block"]["signature"]["editor"]["container"]["style"]= array_merge($padding,$text_align,$margin);
    $type["block"]["signature"]["editor"]["inner"]["style"]=["img" => array_merge($border,$width_height)];
    $type["block"]["signature"]["editor"]["inner"]["attr"]= ["img"=>[
        ".builder__editor--item-field .yeepdf-filed-type-editor-field"=>"data-field"]];
    return $type;
}