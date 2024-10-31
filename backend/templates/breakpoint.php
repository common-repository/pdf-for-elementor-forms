<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_block","superaddons_pdf_builder_block_break_point",100);
function superaddons_pdf_builder_block_break_point(){
    $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
    $class ="";
    $title ="";
    if( !$pro){
        $class ="pro_disable";
        $title =" Pro Version";
    }
    ?>
    <li>
        <div class="momongaDraggable <?php echo esc_attr($class) ?>" data-type="break" title="<?php echo esc_html($title) ?>">
            <i class="dashicons dashicons-editor-break"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Page Break","pdf-for-wpforms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'yeepdf_builder_block_html', "pdf_builder_block_break_point_load" );
function pdf_builder_block_break_point_load($type){
    $type["block"]["break"]["builder"] = '
    <div class="builder-elements">
        <div class="builder-elements-content" data-type="break" style="padding: 15px 0;">
            <div class="page_break"></div>
        </div>
    </div>';
    //Show editor
    $type["block"]["break"]["editor"]["container"]["show"]= [];
    $type["block"]["break"]["editor"]["container"]["style"]= [];
    $type["block"]["break"]["editor"]["inner"]["style"]=[];
    return $type;
}
