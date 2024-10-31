<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Superaddons_Pdf_Shortcode_Qrcode {
    function __construct(){ 
        add_filter( 'yeepdf_builder_block_html', array($this,"barcode_qrcode_builder") );
        add_action("yeepdf_builder_block",array($this,"add_barcode_qrcode"),190);   
    }
    function add_barcode_qrcode(){
        $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
        $class ="";
        $title ="";
        if( !$pro){
            $class ="pro_disable";
            $title =" Pro Version";
        }
        ?>
        <li>
            <div class="momongaDraggable <?php echo esc_attr($class) ?>" data-type="barcode" title="<?php echo esc_html($title) ?>">
                <i class="pdf-creator-icon icon-barcode"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Barcode","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <li>
            <div class="momongaDraggable <?php echo esc_attr($class) ?>" data-type="qrcode" title="<?php echo esc_html($title) ?>">
                <i class="pdf-creator-icon icon-qrcode"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Qrcode","pdf-for-wpforms") ?></div>
            </div>
        </li>
        <?php
    }
    function barcode_qrcode_builder($type){
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $img_qr = QRcode::png('text qrcode','*');
        $img = base64_encode($generator->getBarcode("text barcode", $generator::TYPE_CODE_128));
        $type["block"]["barcode"]["builder"] = '
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="barcode" data-detail_type="vertical">
                    <div class="text-content-data hidden">text barcode</div>
                    <div class="text-content"><img class="barcode" src="data:image/png;base64,'.$img.'"></div>
                </div>
            </div>';
            $type["block"]["qrcode"]["builder"] = '
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="qrcode" >
                    <div class="text-content-data hidden">text qrcode</div>
                    <div class="text-content"><img class="qrcode" src="data:image/png;base64,'.$img_qr.'"></div>
                </div>
            </div>';
            $padding = Yeepdf_Global_Data::$padding;
            $margin = Yeepdf_Global_Data::$margin;
            $pd_mg = array_merge($padding,$margin);
            $text_align = Yeepdf_Global_Data::$text_align;
            $pd_mg_al = array_merge($pd_mg,$text_align); 
            $background = Yeepdf_Global_Data::$background;
            $pd_mg_al_bg = array_merge($pd_mg_al,$background);
            $container_show = array("text-align","padding","margin","background","html","condition");
            $inner_style = array(
                    ".builder__editor--item-width .text_width"=>"width",
                );
            $inner_attr = array(".text-content"=>array(".builder__editor--html .builder__editor--js"=>"html_not_change"),".text-content-data"=>array(".builder__editor--html .builder__editor--js"=>"html_hide"));
            $type["block"]["barcode"]["editor"]["container"]["show"]= array_merge(array("width"),$container_show);
            $type["block"]["barcode"]["editor"]["container"]["style"]= array_merge($padding,$pd_mg_al_bg,$text_align);
            $type["block"]["barcode"]["editor"]["inner"]["style"]= ["img" => $inner_style];
            $type["block"]["barcode"]["editor"]["inner"]["attr"] = $inner_attr;
            $type["block"]["qrcode"]["editor"]["container"]["show"]= array_merge(array("width"),$container_show);
            $type["block"]["qrcode"]["editor"]["container"]["style"]= array_merge($padding,$pd_mg_al_bg,$text_align);
            $type["block"]["qrcode"]["editor"]["inner"]["style"]= ["img" => $inner_style];
            $type["block"]["qrcode"]["editor"]["inner"]["attr"] = $inner_attr;
        return $type; 
    }
}
new Superaddons_Pdf_Shortcode_Qrcode;