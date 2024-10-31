<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_tab_block_template","yeepdf_builder_block_img_box",40);
function yeepdf_builder_block_img_box(){
	?>
	<li data-type="img_box">
		<div class="momongaDraggable">
            <i class="dashicons dashicons-id"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Image Box","yeepdf") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'yeepdf_builder_block_html', "yeepdf_builder_block_img_box_load" );
function yeepdf_builder_block_img_box_load($type){
    $content_element = '<span style="font-size: 18px;"><strong>This is a title</strong></span>';
    $content_element_2 = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
    $img_default = YEEPDF_CREATOR_BUILDER_URL."images/default-image.png";
    $type["block"]["img_box"]["builder"] = '<div class="builder-row-container builder__item">
        <div style="background-color: rgb(255, 255, 255); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start; padding: 15px 30px;" background_full="not" responsive="ok" data-type="row2" class="builder-row-container-row builder-row-container-row2">
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content builder-elements-content-img" data-type="image" style="padding: 0px 15px 0px 0px; text-align: start;">
                        <img style="width: 271px; height: 250px; border-width: 0px; border-style: solid; border-color: rgb(102, 102, 102); border-radius: 0px; background-color: transparent;" src="'.$img_default.'" alt="">
                    </div>
                </div>
            </div>
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 15px; background-color: rgba(0, 0, 0, 0); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element.'</div>
                        <div class="text-content">'.$content_element.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: rgba(0, 0, 0, 0); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 0px; background-color: rgba(0, 0, 0, 0); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    return $type; 
}