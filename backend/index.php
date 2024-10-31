<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Settings_Builder_PDF_Backend {
    function __construct() {
        add_action('admin_enqueue_scripts', array($this,'style'));
        add_action('admin_head', array($this,'add_font'));
        add_action( 'init', array($this,'create_posttype') );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_filter( 'get_sample_permalink_html', array( $this, 'remove_permalink' ) );
        add_action( 'save_post_yeepdf',array( $this, 'save_metabox' ), 10, 2 );
        add_filter( 'admin_body_class', array($this,'body_class' ));
        add_action( 'admin_footer', array($this,"add_page_templates"));
        add_filter('post_row_actions', array($this,"duplicate_post_link"),10, 2);
        add_action( 'admin_action_rednumber_duplicate', array($this,"rednumber_duplicate") );
        add_action("yeepdf_builder_tab__editor_before",array($this,"yeepdf_builder_tab__editor"),1);  
    }
    public static function check_pro(){
        return apply_filters("superaddons_pdf_check_pro",false);
    }
    function yeepdf_builder_tab__editor($post){
        $post_id= $post->ID;
        $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
        $pro_text ='<div class="pro_disable pro_disable_fff">Upgrade to pro version</div>';
        $sizes = array("A Sizes"=>array(
                "A0"=> "A0 (841 x 1189mm)",
                "A1"=> "A1 (594 x 841mm)",
                "A2"=> "A2 (420 x 594mm)",
                "A3"=> "A3 (297 x 420mm)",
                "A4"=> "A4 (210 x 297mm)",
                "A5"=> "A5 (148 x 210mm)",
                "A6"=> "A6 (105 x 148mm)",
                "A7"=> "A7 (74 x 105mm)",
                "A8"=> "A8 (52 x 74mm)",
                "A9"=> "A9 (37 x 52mm)",
                "A10"=> "A10 (26 x 37mm)",
            ),
            "B sizes" => array(
                "B0"=> "B0 (1414 x 1000mm)",
                "B1"=> "B1 (1000 x 707mm)",
                "B2"=> "B2 (707 x 500mm)",
                "B3"=> "B3 (500 x 353mm)",
                "B4"=> "B4 (353 x 250mm)",
                "B5"=> "B5 (250 x 176mm)",
                "B6"=> "B6 (176 x 125mm)",
                "B7"=> "B7 (125 x 88mm)",
                "B8"=> "B8 (88 x 62mm)",
                "B9"=> "B9 (62 x 44mm)",
                "B10"=> "B10 (44 x 31mm)",
            ),
            "Custom Sizes" => apply_filters("yeepdf_custom_sizes",array()),
        );
        $list_tempates = array();
        $args = array(
            'numberposts' => -1,
            'post_type'   => 'yeepdf',
            'exclude'   => array($post_id)
        );
        $post_list = get_posts( $args );
        foreach ( $post_list as $post ) {
            $list_tempates[$post->ID] = $post->post_title;
        }
        $list_fonts = Yeepdf_Settings_Main::get_list_fonts();
        $pdfs = get_post_meta($post_id,"_builder_pdf_settings",true);
        $font_family = get_post_meta($post_id,"_builder_pdf_settings_font_family",true);
        if(!$font_family){
            $font_family = "dejavu sans";
        }
        if( !is_array($pdfs) ) {
            $pdfs = array("dpi"=>96,"size"=>"A4","orientation"=>"P","show_page"=>"");
        }
        ?>
        <div class="builder__editor--item builder__editor--item-settings">
            <label><?php esc_html_e("Settings","pdf-for-wpforms") ?></label>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("DPI","pdf-for-wpforms") ?></label>
                        <input name="builder_pdf_settings[dpi]" type="text" class="yeepdf_setting_input" value="<?php echo esc_attr($pdfs["dpi"]) ?>">
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Orientation","pdf-for-wpforms") ?></label>
                        <select name="builder_pdf_settings[orientation]" class="yeepdf_setting_input">
                            <option value="P"><?php esc_html_e("Portrait","pdf-for-wpforms") ?></option>
                            <option <?php selected($pdfs["orientation"],"L") ?> value="L"><?php esc_html_e("Landscape","pdf-for-wpforms") ?></option>
                        </select>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Paper Size","pdf-for-wpforms") ?></label>
                        <select name="builder_pdf_settings[size]" class="yeepdf_setting_input">
                        <?php 
                        foreach($sizes as $group=>$options){
                            echo wp_kses_post('<optgroup label="'.$group.'">');
                                foreach($options as $key=>$value){
                                    $check ="";
                                    if( $pdfs["size"] == $key ){
                                        $check ="selected";
                                    }
                                    ?>
                                    <option <?php echo esc_attr($check) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($value) ?></option>
                                    <?php
                                    }
                            echo wp_kses_post('</optgroup>');   
                        }
                        ?>
                        </select>
                    </div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Font family","pdf-for-wpforms") ?></label>
                        <select class="font_family yeepdf_setting_input" name="builder_pdf_settings_font_family">
                        <?php
                        foreach($list_fonts as $font => $vl){
                            ?>
                            <option style="font-family: <?php echo esc_attr($font) ?>" <?php selected($font_family,$font) ?> value="<?php echo esc_attr($font) ?>"><?php echo esc_attr($font) ?></option>
                            <?php
                        }
                        $google_fonts = get_option("pdf_custom_fonts",array());
                        foreach($google_fonts as $font => $vl){
                            ?>
                            <option style="font-family: '<?php echo esc_attr($font) ?>'" <?php selected($font_family,$font) ?> value="<?php echo esc_attr($font) ?>"><?php echo esc_attr($font) ?></option>
                        <?php
                        }
                        ?>
                        </select>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Font size","pdf-for-wpforms") ?></label>
                        <input type="number" class="font-size-main">
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <?php Yeepdf_Editor::get_color_pick(esc_html__("Font color","pdf-for-wpforms")) ?>
                    </div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Header Template","pdf-for-wpforms") ?></label>
                        <?php 
                        $header ="";
                        if( isset($pdfs["header"]) ){
                            $header = $pdfs["header"];
                        }
                        if($pro){
                        ?>
                        <select name="builder_pdf_settings[header]" class="yeepdf_setting_input">
                            <option value=""><?php esc_html_e("No Header","pdf-for-wpforms") ?></option>
                            <?php foreach( $list_tempates as $id => $name ){ ?>
                            <option <?php selected($header,$id) ?> value="<?php echo esc_attr($id) ?>"><?php echo esc_html($name) ?></option>
                            <?php } ?>
                        </select>
                        <?php 
                        }else{
                            echo wp_kses_post($pro_text);
                        } ?>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Footer Template","pdf-for-wpforms") ?></label>
                        <?php
                        $footer ="";
                        if( isset($pdfs["footer"])){
                            $footer =$pdfs["footer"];
                        }
                        if($pro){
                        ?>
                        <select name="builder_pdf_settings[footer]" class="yeepdf_setting_input">
                            <option value=""><?php esc_html_e("No Footer","pdf-for-wpforms") ?></option>
                            <?php foreach( $list_tempates as $id => $name ){ ?>
                            <option <?php selected($footer,$id) ?> value="<?php echo esc_attr($id) ?>"><?php echo esc_html($name) ?></option>
                            <?php } ?>
                        </select>
                        <?php 
                        }else{
                            echo wp_kses_post($pro_text);
                        } ?>
                    </div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Watermark text","pdf-for-wpforms") ?></label>
                        <?php
                        $watermark_text ="";
                        if( isset($pdfs["watermark_text"])){
                            $watermark_text =$pdfs["watermark_text"];
                        }
                        if($pro){
                        ?>
                        <textarea rows="3" name="builder_pdf_settings[watermark_text]" class="setting-custom-css"><?php echo esc_textarea($watermark_text) ?></textarea>
                        <?php 
                        }else{
                            echo wp_kses_post($pro_text);
                        } ?>
                    </div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Watermark image","pdf-for-wpforms") ?></label>
                        <?php
                        $watermark_img ="";
                        if( isset($pdfs["watermark_img"])){
                            $watermark_img =$pdfs["watermark_img"];
                        }
                        if($pro){
                        ?>
                        <input type="text" class="image_url yeepdf_setting_input" placeholder="imag url" name="builder_pdf_settings[watermark_img]" value="<?php echo esc_url($watermark_img) ?>">
                        <?php 
                        }else{
                            echo wp_kses_post($pro_text);
                        } ?>
                    </div>
                    <?php if($pro){ ?>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Upload image","pdf-for-wpforms") ?></label>
                        <input type="button" class="upload-editor--image-ok button button-primary" value="Upload">
                    </div>
                    <?php } ?>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Custom CSS","pdf-for-wpforms") ?></label>
                        <?php
                        $css ="";
                        if( isset($pdfs["css"])){
                            $css =$pdfs["css"];
                        }
                        ?>
                        <textarea rows="4" name="builder_pdf_settings[css]" class="setting-custom-css"><?php echo esc_textarea($css) ?></textarea>
                    </div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Disable Active Form","pdf-for-wpforms") ?></label>
                        <?php
                        $css ="";
                        if( isset($pdfs["disable_active_form"]) && $pdfs["disable_active_form"] == "yes"){
                            $disable_active_form = "yes";
                        }else{
                            $disable_active_form = "no"; 
                        }
                        ?>
                        <div class="yeepdf_setting_input-wrapper">
                            <label class="yeepdf-switch">
                                <input name="builder_pdf_settings[disable_active_form]" type="checkbox" <?php checked($disable_active_form,"yes") ?> value="yes">
                                <span class="yeepdf-slider yeepdf-round"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function email_builder_main($post ) {
        $post_id= $post->ID;
        ?>
        <div id="builder-header">
            <div id="header-right">
                <div class="header-right-l">
                    <?php do_action("yeepdf_head_settings",$post) ?>
                    <div class="button-icon yeepdf-builder-choose-shortcodes" title="Shortcodes">
                        <span class="dashicons dashicons-shortcode"></span>
                    </div>
                    <div class="button-icon yeepdf-builder-choose-blank" title="Blank The Template">
                        <span class="dashicons dashicons-media-default"></span>
                    </div>
                </div>
                <div class="header-right-r">
                    <div class="" title="Templates">
                        <a href="#" class="button yeepdf-email-choose-template"><span
                                class="dashicons dashicons-welcome-add-page"></span>
                            <?php esc_html_e("Templates","yeepdf")  ?></a>
                    </div>
                    <div class="" title="Import Template">
                        <a href="#" class="button yeepdf-email-import"><span class="dashicons dashicons-upload"></span></a>
                    </div>
                    <div class="" title="Export Template">
                        <a href="#" class="button yeepdf-email-export"><span
                                class="dashicons dashicons-download"></span></a>
                    </div>
                    <div class="">
                        <?php
                        $oder_id = 0;
                        if( class_exists("Yeepdf_Woocommerce_Backend") ){
                            $oder_id = get_option( "_yeepdf_woocommerce_demo" );
                            $url = add_query_arg(array("pdf_preview"=>"preview","preview"=>1,"id"=>$post_id,"woo_order"=>$oder_id),get_home_url());
                        }else{
                            $url = add_query_arg(array("pdf_preview"=>"preview","preview"=>1,"id"=>$post_id),get_home_url());
                        } 
                        ?>
                        <a class="button" target="_blank" href="<?php echo esc_url(wp_nonce_url($url,"yeepdf")) ?>"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e("Preview","yeepdf")  ?></a>
                    </div>
                    <div class="">
                        <a href="#" class="button button-yeepdf-save button-primary-ok"><span class="dashicons dashicons-saved"></span> <?php esc_html_e("Save","yeepdf")  ?></a>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="yeepdf-builder-container">
            <div class="email-builder-side">
                <div class="builder__right">
                    <div class="builder__widget">
                        <div class="builder_main_info">
                            <div class="builder_main_info_text">
                                <?php esc_attr_e( "YeePDF - PDF Customizer", "yeepdf") ?>
                            </div>
                            <div class="builder_main_info_icon" title="Go To Dashboard">
                                <a href="<?php echo esc_url( get_admin_url()."edit.php?post_type=yeepdf") ?>"><span class="dashicons dashicons-wordpress"></span></a>
                            </div>
                        </div>
                        <ul class="builder__tab">
                            <li class="tab__block_elements"><a class="active" id="#tab__block"><span><?php esc_html_e("Elements","pdf-for-wpforms")  ?></span> </a></li>
                            <li><a class="" id="#tab__editor"><span><?php esc_html_e("Editor","pdf-for-wpforms")  ?></span></a></li>
                        </ul>
                        <div class="tab__inner">
                            <div class="tab__content active" id="tab__block">
                                <div class="builder__widget--inner">
                                    <div class="builder__widget_tab builder__widget_genaral">
                                        <div class="builder__widget_tab_title"><span
                                                class="builder__widget_tab_title_t"><?php esc_attr_e( "Genaral", "yeepdf") ?></span><span
                                                class="builder__widget_tab_title_icon dashicons dashicons-arrow-down-alt2"></span><span
                                                class="builder__widget_tab_title_icon dashicons dashicons-arrow-up-alt2"></span>
                                        </div>
                                        <ul class="momongaPresets momongaPresets_data">
                                            <?php do_action( "yeepdf_builder_block" ) ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="builder__widget--inner">
                                    <div class="builder__widget_tab builder__widget_columns">
                                        <div class="builder__widget_tab_title"><span
                                                class="builder__widget_tab_title_t"><?php esc_attr_e( "Columns", "yeepdf") ?></span><span
                                                class="builder__widget_tab_title_icon dashicons dashicons-arrow-down-alt2"></span><span
                                                class="builder__widget_tab_title_icon dashicons dashicons-arrow-up-alt2"></span><span
                                                class="builder__widget_tab_title_icon dashicons dashicons-arrow-up-alt2"></span>
                                        </div>
                                        <ul class="builder-row-tool momongaPresets_data">
                                            <?php do_action( "yeepdf_builder_tab_block_row" )?>
                                        </ul>
                                    </div>
                                </div>
                                <?php do_action( "yeepdf_builder_tab_block_addons" ) ?>
                            </div>
                            <div class="tab__content" id="tab__editor">
                                <div class="builder__editor">
                                    <?php do_action( "yeepdf_builder_tab__editor",$post ) ?>
                                </div>
                            </div>
                            <div class="builder_main_footer">
                                <div class="builder_main_footer_text">
                                    <a href="<?php echo esc_url(get_dashboard_url()) ?>"><span
                                            class="dashicons dashicons-arrow-left-alt"></span>
                                        <?php esc_attr_e( "BACK TO DASHBOARD", "yeepdf" ) ?></a>
                                </div>
                                <div class="builder_main_footer_icon">
                                    <a href="#"
                                        class="button button-primary yeepdf_button_settings"><?php esc_attr_e( "SETTINGS", "yeepdf" ) ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="email-builder-main" data-type="main">
                <div class="email-builder-main-change_backgroud" data-type="main"><i class="pdf-creator-icon icon-pencil"></i>
                    <?php esc_html_e("Settings PDF","pdf-for-wpforms") ?></div>
                <div class="builder__list builder__list--js">
                    <div class="builder-row-container builder__item">
                        <div style="background-color: #ffffff" data-background_full="not" data-type="row1"
                            class="builder-row-container-row builder-row-container-row1">
                            <div class="builder-row builder-row-empty">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            $data_js = get_post_meta( $post_id,'data_email',true);
            if( is_array($data_js) ){
                $data_js = json_encode($data_js);
            }
        ?>
        <textarea name="data_email" class="data_email hidden"><?php echo esc_attr($data_js) ?></textarea>
        <script type="text/javascript">
        <?php
            $data =array(); 
            $datas = apply_filters("yeepdf_builder_block_html",$data);
        ?>
        var wp_builder_pdf = <?php echo wp_json_encode($datas) ?>
        </script>
        <style type="text/css">
        <?php 
        do_action("yeepdf_add_css", $post_id);
        ?>
        </style>
        <?php
        wp_enqueue_media();
    }
    function style() {
        global $post;
        $add_libs = false;
        if((isset($post->post_type) && $post->post_type == "yeepdf") || (isset($_GET["post_type"]) && $_GET["post_type"] == "yeepdf")){
            $add_libs = true;
        }
        $add_libs = apply_filters( "yeepdf_add_libs", $add_libs );
        if($add_libs){
            $ver = time();
            wp_enqueue_style('yeepdf-font', YEEPDF_CREATOR_BUILDER_URL ."backend/css/pdfcreator.css",array(),$ver);
            wp_enqueue_style('yeepdf-momonga', YEEPDF_CREATOR_BUILDER_URL."backend/css/momonga.css",array("wp-jquery-ui-dialog","wp-color-picker"),$ver);
            wp_enqueue_style('yeepdf-main', YEEPDF_CREATOR_BUILDER_URL."backend/css/main.css",array(),$ver);
            wp_enqueue_script('medium-editor', YEEPDF_CREATOR_BUILDER_URL."backend/libs/medium-editor/js/medium-editor.min.js");
            wp_register_script('yeepdf_pdf_code_toggle', YEEPDF_CREATOR_BUILDER_URL ."backend/src/tinymce-ace.js",array());
            wp_register_script('yeepdf_main', YEEPDF_CREATOR_BUILDER_URL. "backend/src/main.js",array(), $ver);
            wp_register_script('yeepdf_builder', YEEPDF_CREATOR_BUILDER_URL . "backend/src/builder.js",array("yeepdf_main"), $ver);
            wp_register_script('yeepdf_editor', YEEPDF_CREATOR_BUILDER_URL. "backend/src/set_editor.js",array("yeepdf_main"), $ver);
            wp_enqueue_script('yeepdf_script', YEEPDF_CREATOR_BUILDER_URL."backend/src/script.js",array("jquery","medium-editor","yeepdf_main","jquery-ui-core","jquery-ui-dialog","jquery-ui-sortable","jquery-ui-draggable","jquery-ui-droppable","wp-color-picker","wp-tinymce","yeepdf_editor","yeepdf_builder","jquery-effects-core","jquery-effects-scale","yeepdf_pdf_code_toggle","thickbox"),$ver);
            $list_fonts = Yeepdf_Settings_Main::get_list_fonts();
            $google_fonts = get_option("pdf_custom_fonts",array());
            $font_formats = "";
            foreach($list_fonts as $k =>$vl ){
                $font_formats =  $font_formats . $k."=".$k.";"; 
            }
            foreach($google_fonts as $k =>$vl ){
                $font_formats =  $font_formats . $k."=".$k.";"; 
            }
            $builder_shorcode = apply_filters("yeepdf_builder_shortcode",array());
            $shortcodes = Yeepdf_Builder_PDF_Shortcode::list_shortcodes();
            $builder_shorcode_re ="";
            $i= 0;
            foreach( $builder_shorcode as $k=>$v){
                $k = str_replace(array("[","]"), "", $k);
                if($i == 0){
                    $builder_shorcode_re .="\[".$k."\]";
                }else{
                    $builder_shorcode_re .="|\[".$k."\]";
                }
                $i++;
            }
            wp_localize_script( 'yeepdf_script', 'yeepdf_script',
                array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 
                    'youtube_play_src' => "pdf-for-wpforms"."images/youtube_play.png",
                    'yeepdf_url_plugin' => YEEPDF_CREATOR_BUILDER_URL,
                    'shortcodes' =>  $shortcodes,
                    'google_font_font_formats' => $font_formats,
                    'home_url' => get_home_url(),
                    'builder_shorcode' => $builder_shorcode,
                    'builder_shorcode_re' => $builder_shorcode_re,
                     ) );
        }
    }
    function add_font(){
        global $post_type;
        if( "yeepdf" == $post_type || ( isset($_GET["page"] ) && $_GET["page"] == "yeepdf-settings") ) {
            $upload_dir = wp_upload_dir();
            $path_main = $upload_dir['basedir'] . '/pdfs/fonts/';  
            $defaultConfig     = (new Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $fonts = Yeepdf_Settings_Main::get_list_fonts();
            $google_fonts = get_option("pdf_custom_fonts",array());
            ?>
        <style type="text/css">
        <?php foreach($fonts as $key=> $value) {
            foreach($value as $k=> $vl) {
                if(is_readable($fontDirs[0]."/".$vl)) {
                    $url=YEEPDF_CREATOR_BUILDER_URL."vendor/mpdf/mpdf/ttfonts/".$vl;
                }
                else {
                    $url=false;
                }
                if($url !="") {
                    $font_weight="normal";
                    $font_style="normal";
                    if($k=="B") {
                        $font_weight="bold";
                    }
                    elseif ($k=="I") {
                        $font_style="italic";
                    }
                    elseif($k=="BI") {
                        $font_style="italic";
                        $font_weight="bold";
                    }
                    ?>@font-face {
                        font-family: '<?php echo esc_attr($key) ?>';
                        src: url(<?php echo esc_url($url);
                        ?>);
                        font-style: <?php echo esc_attr($font_style) ?>;
                        font-weight: <?php echo esc_attr($font_weight) ?>;
                    }
                    <?php
                }
            }
        }
        foreach($google_fonts as $key=> $value) {
            foreach($value as $k=> $vl) {
                if(is_readable($path_main.$vl)) {
                    $url=$upload_dir["baseurl"]."/pdfs/fonts/".$vl;
                }
                else {
                    $url=false;
                }
                if($url !="") {
                    $font_weight="normal";
                    $font_style="normal";
                    if($k=="B") {
                        $font_weight="bold";
                    }
                    elseif ($k=="I") {
                        $font_style="italic";
                    }
                    elseif($k=="BI") {
                        $font_style="italic";
                        $font_weight="bold";
                    }
                    ?>@font-face {
                        font-family: '<?php echo esc_attr($key) ?>';
                        src: url(<?php echo esc_url($url);
                        ?>);
                        font-style: <?php echo esc_attr($font_style) ?>;
                        font-weight: <?php echo esc_attr($font_weight) ?>;
                    }
                    <?php
                }
            }
        }
        ?>
        </style>
            <?php 
        }   
    }
    function create_posttype() {
        register_post_type( 'yeepdf',
            array(
                'labels' => array(
                    'name' => esc_html__( 'PDF Templates',"pdf-for-wpforms" ),
                    'add_new' => esc_html__( 'New Template',"pdf-for-wpforms" ),
                    'singular_name' => esc_html__( 'yeepdfs',"pdf-for-wpforms" )
                ),
                'public' => true,
                'has_archive' => true,
                'supports'    => array( 'title' ),
                'show_in_menu' => true,
                'rewrite' => array('slug' => 'yeepdf'),
                'show_in_rest' => true,
                'menu_icon'           => 'dashicons-email',
                'menu_position'=>100,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'query_var'=>false
            )
        );
    }
    function save_metabox($post_id, $post) {
        if( isset($_POST['data_email'])) {
            $data_email = ($_POST['data_email']);
            update_post_meta($post_id,'data_email',$data_email);
        }
        if( isset($_POST['builder_pdf_settings_font_family'])) {
            $builder_pdf_settings_font_family = sanitize_text_field($_POST['builder_pdf_settings_font_family']);
            update_post_meta($post_id,'_builder_pdf_settings_font_family',$builder_pdf_settings_font_family);
        }
        if( isset($_POST['builder_pdf_settings'])) { 
            $datas = array();
            if( array($_POST["builder_pdf_settings"])) {
                foreach( $_POST["builder_pdf_settings"] as $key => $value ){
                   $datas[$key] = sanitize_text_field($value); 
                }
                update_post_meta($post_id,'_builder_pdf_settings',$datas);
            }
        }   
    }
    function remove_view_action(){
        global $post_type;
        if ( 'yeepdf' === $post_type ) {
            unset( $actions['view'] );
        }
        return $actions;
    }
    function remove_permalink($link){
        global $post_type;
        if ( 'yeepdf' === $post_type ) {
            return "";
        }else{
            return $link;
        }
    }
    function add_meta_boxes() {
        add_meta_box(
            'email-builder-main',
            esc_html__( 'Builder PDF', "pdf-for-wpforms" ),
            array( $this, 'email_builder_main' ),
            'yeepdf',
            'normal',
            'default'
        );
    }
    function body_class( $classes ) {
        global $post_type;
        $screen = get_current_screen();
        if ( 'yeepdf' == $post_type && $screen->id == 'yeepdf' ) {
            return  $classes . " post-php";
        }else{
            return  $classes;
        }
    }
    function add_page_templates(){
        ?>
<div id="yeepdf-email-templates" style="display:none">
    <div class="list-view-templates">
        <?php 
              $args = array(
                    "json"=>"",
                    "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/template1/1.png",
                    "title"=>"Email templates",
                    "cat" => array(),
                    "id"=>0,
                );
              do_action( "builder_yeepdfs" );
               ?>
    </div>
</div>
<div id="yeepdf-builder-shortcodes-templates" style="display:none">
    <div class="list-view-short-templates">
        <?php 
                $shortcodes = Yeepdf_Builder_PDF_Shortcode::list_shortcodes();
                foreach( $shortcodes as $shortcode_k =>$shortcode_v){
              ?>
        <h3><?php echo esc_html( $shortcode_k ) ?></h3>
        <?php 
                foreach( $shortcode_v as $k =>$v){
                    if(is_array($v)){
                        ?>
        <h4><?php echo esc_html( $k ) ?></h4>
        <?php
                        foreach( $v as $k_i =>$v_i){
                            ?>
        <div class="list-view-short-templates-r">
            <div class="list-view-short-templates-k" title="Click to copy">
                <?php 
                if (strpos($k_i, "{") === false) { 
                    echo esc_html( "[".$k_i."]" );
                }else{
                    echo esc_html( $k_i);
                }
                ?>
            </div>
            <div class="list-view-short-templates-v">
                <?php echo esc_html( $v_i ) ?>
            </div>
        </div>
        <?php
                        }
                    }else{
                        ?>
        <div class="list-view-short-templates-r">
            <div class="list-view-short-templates-k" title="Click to copy">
                <?php 
                if (strpos($k, "{") === false) { 
                    echo esc_html( "[".$k."]" );
                }else{
                    echo esc_html( $k);
                }
                ?>
            </div>
            <div class="list-view-short-templates-v">
                <?php echo esc_html( $v ) ?>
            </div>
        </div>
        <?php
                    }
                }
                } 
              ?>
    </div>
</div>
<?php
    }
    public static function item_demo($args1){
        $defaults = array(
            "json"=>"",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/template1/1.png",
            "title"=>"PDF templates",
            "url" => "#",
            "id"=>0,
            "cat" => array(),
        );
        $args = wp_parse_args( $args1, $defaults );
        $domain = "https://pdf.add-ons.org/";
        $url_view = $domain."?pdf_preview=preview&id=".$args["id"]."&woo_order=18";
        $url_design = $domain."?templates_id=".$args["id"];
        ?>
<div class="grid-item" data-file="<?php echo esc_url($args["json"]) ?>">
    <img src="<?php echo esc_url($args["img"]) ?>">
    <div class="demo_content">
        <div class="demo-title"><?php echo esc_html($args["title"]) ?></div>
        <div class="demo-tags"><?php echo implode(", ",$args["cat"]) ?></div>
        <div class="yeepdf-email-actions">
            <div class="demo-fl">
                <a class="button yeepdf-email-actions-import"
                    href="#"><?php esc_html_e("Import","pdf-for-wpforms") ?></a>
                <a target="_blank" class="button yeepdf-email-actions-design"
                    href="<?php echo esc_url($url_design) ?>"><?php esc_html_e("Design","pdf-for-wpforms") ?></a>
            </div>
            <div class="demo-fr">
                <a target="_blank" class="button yeepdf-email-actions-view"
                    href="<?php echo esc_url($url_view) ?>"><?php esc_html_e("Preview","pdf-for-wpforms") ?></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php
    }
    function duplicate_post_link($actions, $post){
        if ($post->post_type=='yeepdf' && current_user_can('edit_posts') ){
            $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=rednumber_duplicate&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
            $actions['preview_template'] = '<a target="_blank" href="' . esc_url(get_home_url()."?pdf_preview=preview&id=".$post->ID) . '" title="Preview" rel="Preview">Preview</a>';
        }
        return $actions;
    }
    function rednumber_duplicate(){
      global $wpdb;
      if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rednumber_duplicate' == $_REQUEST['action'] ) ) ) {
        wp_die('No post to duplicate has been supplied!');
      }
      $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
      $post = get_post( $post_id );
      $current_user = wp_get_current_user();
      $new_post_author = $current_user->ID;
      if (isset( $post ) && $post != null) {
        $args = array(
          'comment_status' => $post->comment_status,
          'ping_status'    => $post->ping_status,
          'post_content'   => $post->post_content,
          'post_excerpt'   => $post->post_excerpt,
          'post_name'      => $post->post_name,
          'post_parent'    => $post->post_parent,
          'post_password'  => $post->post_password,
          'post_status'    => 'Publish',
          'post_title'     => $post->post_title ."-Demo",
          'post_type'      => $post->post_type,
          'to_ping'        => $post->to_ping,
          'menu_order'     => $post->menu_order
        );
        $new_post_id = wp_insert_post( $args );
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos)!=0) {
          $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
          foreach ($post_meta_infos as $meta_info) {
            $meta_key = $meta_info->meta_key;
            if( $meta_key == '_wp_old_slug' ) continue;
            $meta_value = addslashes($meta_info->meta_value);
            $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
          }
          $sql_query.= implode(" UNION ALL ", $sql_query_sel);
          $wpdb->query($sql_query);
        }
        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
      } else {
        wp_die('Post creation failed, could not find original post: ' . $post_id);
      }
    }
}
new Yeepdf_Settings_Builder_PDF_Backend;