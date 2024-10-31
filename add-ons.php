<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
class Superaddons_List_Addons {
    function __construct(){
        add_action('admin_menu', array($this,"add_menu"),9999);
        add_action('admin_head', array($this,"admin_head"));
        add_filter( "fluentform_global_addons", array($this,"fluentform_global_addons") );
        if(isset($_GET["page"]) && $_GET["page"] == "ninja-forms") {
            add_action('admin_init', array($this,"add_ninja_form"));
        }
    }
    function add_menu(){
        add_submenu_page( "wpcf7","contact-form-7 addons", "<span style='color:#f18500'>Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>36</span></span>", "manage_options", "contact-form-7-addons", array( $this, 'page_addons_cf7' ), 999 );
        add_submenu_page( "elementor","elementor form addons", "<span style='color:#f18500'>Forms Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>15</span></span>", "manage_options", "elementor-forms-addons", array( $this, 'page_addons_elementor' ), 999 );
        add_submenu_page( "fluent_forms", "addons", "<span style='color:#f18500'>Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>36</span></span>", "manage_options", "fluent_forms-addons", array( $this, 'page_addons_fluent_forms' ) );
        add_submenu_page( "formidable", "addons", "<span style='color:#f18500'>Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>36</span></span>", "manage_options", "formidable-addons", array( $this, 'page_addons_formidable' ),999 );
        add_submenu_page( "quform.dashboard", "addons", "<span style='color:#f18500'>Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>36</span></span>", "manage_options", "quform.dashboard-addons", array( $this, 'page_addons_quform' ),999 );
        add_submenu_page( "wpforms-overview", "addons", "<span style='color:#f18500'>Add-ons </span><span class='update-plugins count-1'><span class='plugin-count'>9</span></span>", "manage_options", "wpforms.dashboard-addons", array( $this, 'page_addons_wpforms' ),999 );
        add_filter("http_response",array($this,"http_response_eform"),10,3);
        add_submenu_page( "edit.php?post_type=yeemail_template", "addons", "<span style='color:#f18500'>Add-ons </span>", "manage_options", "yeemail-addons", array( $this, 'page_yeemail' ),999 );
    }
    function page_yeemail(){
        $this->page_addons("yeemail");
    }
    function page_addons_cf7(){
        $this->page_addons("cf7");
    }
    function page_addons_elementor(){
        $this->page_addons("elementor");
    }
    function page_addons_fluent_forms(){
        $this->page_addons("fluent_forms");
    }
    function page_addons_formidable(){
        $this->page_addons("formidable");
    }
    function page_addons_quform(){
        $this->page_addons("quform");
    }
    function page_addons_wpforms(){
        $this->page_addons("wpforms");
    }
    function admin_head(){
        ?>
        <style type="text/css">
            .pro_disable::after {
                content: "Pro";
                position: absolute;
                bottom: 0;
                right: 0;
                background: red;
                padding: 3px;
                font-size: 11px;
                color: #fff;
                border-radius: 5px 0 0 0;
            }
            .pro_disable {
                position: relative;
            }
            .pro_disable_padding{
                padding: 10px !important;
            }
            .pro_text_style{
                color:#9f9e9e;
            }
            body .pro_disable_fff {
                background: transparent;
            }
            .cf7-container-bundle {
                border: 1px solid #e7e7e7;
                padding: 10px;
                text-align: center;
                background: #fff;
                border-radius: 5px;
            }
            .cf7-container-bundle-h {
                font-size: 30px;
                font-weight: bold;
                text-align: center;
            }
            .cf7-container-bundle-h p{
                font-size: 30px;
                font-weight: bold;
                padding: 0;
                margin: 0;
            }
            .list-addons-container {
                display: flex;
                flex-flow: row wrap;
                padding: 10px;
            }
            .add-ons-box {
                margin: 0 0 20px;
                width: 400px;
                position: relative;
                margin-right: 30px;
                border: 1px solid #f7f7f7;
                padding: 14px;
                background: #fff;
                border-radius: 10px;
            }
            .add-ons-box-actions-button-live {
                border: 0;
                border-radius: 4px;
                cursor: pointer;
                display: inline-block;
                font-size: 17px;
                padding: 10px 30px;
                text-align: center;
                text-decoration: none;
                text-transform: uppercase;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background: #fff;
                border: 1px solid #1ea9ea;
                color: #1ea9ea;
                margin-right:50px;
            }
             .add-ons-box-actions-button-download {
                border: 0;
                border-radius: 4px;
                cursor: pointer;
                display: inline-block;
                font-size: 17px;
                padding: 10px 30px;
                text-align: center;
                text-decoration: none;
                text-transform: uppercase;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background: #fff;
                border: 1px solid #1ea9ea;
                color: #1ea9ea;
                background: #1ea9ea;
                border: 1px solid #1ea9ea;
                color: #fff;
            }
            .add-ons-box-actions {
                text-align:center;
            }
            .add-ons-box-actions a:hover {
                opacity: 0.8;
            }
        </style>
        <?php
    }
    function fluentform_global_addons($add_ons_ok ){
        $datas = $this->get_addons();
        $add_ons = array();
        foreach( $datas as $k=> $data ){
           $add_ons[$k] = array(
                        "logo"=>$data["img"],
                        "url"=>$data["download"],
                        "title"=>$data["name"],
                        "description"=>$data["des"],
                        "purchase_url"=>$data["download"],
                        "category"=>"a",  
                    );
        }
        return array_merge($add_ons,$add_ons_ok);
   }
   function add_ninja_form(){
        $saved = get_option( 'ninja_forms_addons_feed', false );
        $datas = $this->get_addons("ninja_forms");
        $add_ons = array();
        foreach( $datas as $k=> $data ){
            $add_ons[] = array(
                        "image"=>$data["img"],
                        "url"=>$data["download"],
                        "title"=>$data["name"],
                        "content"=>$data["des"],
                        "link"=>$data["download"],
                        "plugin"=>"a", 
                        "version"=>"3.0.1",
                        "categories" => array(
                            array(
                                "name"=>"Look &amp; Feel",
                                "slug"=>"form-function-design"
                            )
                        ) 
                    );
        }
        update_option("ninja_forms_addons_feed", json_encode($add_ons));
   }
    function http_response_eform($response,$datas,$url){
        $add_ons = array();
        switch ($url) {
            case "https://wpquark.com/wp-json/ipt-api/v1/fsqm/":
                // eforms
                $datas = $this->get_addons("eforms");
                 foreach(  $datas as $data ){
                    $add_ons[] = array(
                        "image"=>$data["img"],
                        "url"=>$data["download"],
                        "name"=>$data["name"],
                        "description"=>$data["des"],
                        "author"=>"rednumber",
                        "authorurl"=>"https://add-ons.org",
                        "class"=>"",
                        "star"=>5,
                        "starnum"=>rand(10,100),
                        "downloaded"=>rand(100,1000),
                        "version"=>"2.".rand(10,100),
                        "compatible"=>"4.0",
                        "date"=> date("Y-m-d h:i:sa")
                    );
                    $datas_rs = json_decode($response['body'],true);
                    $add_on=$datas_rs["addons"]; 
                    $datas_rs["addons"] = array_merge($add_ons,$add_on);
                    $response["body"] = json_encode($datas_rs);
                }
                break;
            case "https://gravityapi.com/wp-content/plugins/gravitymanager/api.php?op=plugin_browser&page=gf_addons":
                // gravity form
                ob_start();
               ?>
               <h1><?php esc_html_e("Improve your forms with our premium addons.","rednumber") ?></h1>
               <div class="list-addons-container">
                <?php 
                    $datas = $this->get_addons("gravity");
                    foreach ($datas as $data) {
                ?>
                <div class="add-ons-box">
                    <img src="<?php echo esc_attr($data["img"]) ?>">
                    <h3><?php echo esc_attr($data["name"]) ?></h3>
                    <div class="add-ons-box-content">
                        <p><?php echo esc_attr($data["des"]) ?></p>
                        <div class="add-ons-box-actions">
                            <a href="<?php echo esc_attr($data["demo"]) ?>" target="_blank" class="add-ons-box-actions-button-live"><?php esc_html_e("Live Demo") ?></a>
                            <?php 
                                if( wp_http_validate_url($data["download"])){
                                    $dl = $data["download"];
                                }else{
                                    $dl = "https://".$data["download"];
                                }
                            ?>
                            <a href="<?php echo esc_attr($dl) ?>" target="_blank" class="add-ons-box-actions-button-download"><?php esc_html_e("Download") ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
                <?php
                $html= ob_get_clean();
                $response["body"] = $html . $response["body"];
                break;
            case "http://api.ninjaforms.com/feeds/?fetch=addons":
                $datas = $this->get_addons("ninja_forms");
                    $add_ons = array();
                    foreach( $datas as $k=> $data ){
                        $add_ons[] = array(
                                    "image"=>$data["img"],
                                    "url"=>$data["download"],
                                    "title"=>$data["name"],
                                    "content"=>$data["des"],
                                    "link"=>$data["download"],
                                    "plugin"=>"a", 
                                    "version"=>"3.0.1",
                                    "categories" => array(
                                        array(
                                            "name"=>"Look &amp; Feel",
                                            "slug"=>"form-function-design"
                                        )
                                    ) 
                                );
                    }
                    $add_on = json_decode($response['body'],true);
                   $response["body"] = json_encode( array_merge($add_ons,$add_on) ); 
                break;
            default:
                // code...
                break;
        }
        return $response;
    }
    function page_addons($addon=""){
        ?>
        <div class="wrap">
            <h2><?php esc_html_e("Improve your forms with our premium addons.","rednumber") ?></h2>
            <p></p>
            <?php
            switch($addon){
                case "cf7":
                    ?>
                    <div class="cf7-container-bundle">
                        <div class="cf7-container-bundle-h"><p>Having a tough time choosing just a few? </p>
    <p>Bundle and save big with $59</p></div>
    <p>This is a special pack including all add-on for contact form 7 issued by us and every released add-on!
    </p>
                        <h3>Save up to 95%</h3>
                        <p>In fact, purchasing every item singularly you would spend at least $891. Bundle Price – Only $59</p>
                        <a href="https://add-ons.org/plugin/contact-form-7-add-on-bundle-all-in-one/" target="_blank" class="add-ons-box-actions-button-download">Get Now</a>
                    </div>
                    <?php
                    break;
                case "wpforms":
                    ?>
                    <div class="cf7-container-bundle">
                        <div class="cf7-container-bundle-h"><p>Having a tough time choosing just a few? </p>
    <p>Bundle and save big with $49</p></div>
    <p>This is a special pack including all add-on for WPForms issued by us and every released add-on!
    </p>
                        <h3>Save up to 80%</h3>
                        <p>In fact, purchasing every item singularly you would spend at least $250. Bundle Price – Only $49</p>
                        <a href="https://add-ons.org/plugin/wpforms-add-on-bundle-all-in-one/" target="_blank" class="add-ons-box-actions-button-download">Get Now</a>
                    </div>
                    <?php
                    break;
                case "elementor":
                    ?>
                    <div class="cf7-container-bundle">
                        <div class="cf7-container-bundle-h"><p>Having a tough time choosing just a few? </p>
    <p>Bundle and save big with $49</p></div>
    <p>This is a special pack including all add-on for Elementor Forms issued by us and every released add-on!
    </p>
                        <h3>Save up to 85%</h3>
                        <p>In fact, purchasing every item singularly you would spend at least $350. Bundle Price – Only $49</p>
                        <a href="https://add-ons.org/plugin/elementor-forms-add-on-bundle-all-in-one/" target="_blank" class="add-ons-box-actions-button-download">Get Now</a>
                    </div>
                    <?php
                    break;
            }
            ?>
            <div class="list-addons-container">
                <?php 
                $datas = $this->get_addons($addon);
                    foreach ($datas as $data) {
                ?>
                <div class="add-ons-box">
                    <img src="<?php echo esc_attr($data["img"]) ?>">
                    <h3><?php echo esc_attr($data["name"]) ?></h3>
                    <div class="add-ons-box-content">
                        <p><?php echo esc_attr($data["des"]) ?></p>
                        <div class="add-ons-box-actions">
                            <a href="<?php echo esc_attr($data["demo"]) ?>" target="_blank" class="add-ons-box-actions-button-live"><?php esc_html_e("Live Demo") ?></a>
                            <?php 
                                if( wp_http_validate_url($data["download"])){
                                    $dl = $data["download"];
                                }else{
                                    $dl = "https://".$data["download"];
                                }
                            ?>
                            <a href="<?php echo esc_attr($dl) ?>" target="_blank" class="add-ons-box-actions-button-download"><?php esc_html_e("Download") ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
        <?php
    }
    function get_addons($add_on=null){
        if( isset($add_on) ){
            $rs = wp_remote_get("https://cdn.add-ons.org/plugins.php?type=".$add_on);
        }else{
            $rs = wp_remote_get("https://cdn.add-ons.org/plugins.php");
        }
        return json_decode($rs['body'],true);
    }
}
new Superaddons_List_Addons;
