<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_tab_block_template","yeepdf_builder_block_img_list",50);
function yeepdf_builder_block_img_list(){
	?>
	<li data-type="img_list">
		<div class="momongaDraggable">
            <i class="dashicons dashicons-images-alt2"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Image List","yeepdf") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'yeepdf_builder_block_html', "yeepdf_builder_block_img_list_load" );
function yeepdf_builder_block_img_list_load($type){
    $content_element = '<span style="font-size: 18px;"><strong>This is a title</strong></span>';
    $content_element_2 = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
    $img_default = YEEPDF_CREATOR_BUILDER_URL."images/default-image.png";
    $type["block"]["img_list"]["builder"] = '
    <div class="builder-row-container builder__item">
        <div style="background-color: rgb(255, 255, 255); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start; padding: 15px 30px;" background_full="not" responsive="ok" data-type="row5" class="builder-row-container-row builder-row-container-row5">
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content builder-elements-content-img" data-type="image" style="padding: 0px 15px 0px 0px; text-align: start;">
                        <img style="width: 180px; height: 180px; border-width: 0px; border-style: solid; border-color: rgb(102, 102, 102); border-radius: 0px; background-color: transparent;" src="'.$img_default.'" alt="">
                    </div>
                </div>
            </div>
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content builder-elements-content-img" data-type="image" style="padding: 0px 15px 0px 0px; text-align: start;">
                        <img style="width: 180px; height: 180px; border-width: 0px; border-style: solid; border-color: rgb(102, 102, 102); border-radius: 0px; background-color: transparent;" src="'.$img_default.'" alt="">
                    </div>
                </div>
            </div>
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content builder-elements-content-img" data-type="image" style="padding: 0px 0px 0px 0px; text-align: start;">
                        <img style="width: 190px; height: 180px; border-width: 0px; border-style: solid; border-color: rgb(102, 102, 102); border-radius: 0px; background-color: transparent;" src="'.$img_default.'" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>';
    return $type; 
}