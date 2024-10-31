<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_tab_block_template","yeepdf_builder_block_text_list",60);
function yeepdf_builder_block_text_list(){
	?>
	<li data-type="text_list">
		<div class="momongaDraggable">
            <i class="dashicons dashicons-menu-alt"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Text List","yeepdf") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'yeepdf_builder_block_html', "yeepdf_builder_block_text_list_load" );
function yeepdf_builder_block_text_list_load($type){
    $content_element = '<span style="font-size: 18px;"><strong>This is a title</strong></span>';
    $content_element_2 = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
    $type["block"]["text_list"]["builder"] = '
    <div class="builder-row-container builder__item">
        <div style="background-color: rgb(255, 255, 255); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start; padding: 15px 30px;" background_full="not" responsive="ok" data-type="row2" class="builder-row-container-row builder-row-container-row2">
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element.'</div>
                        <div class="text-content">'.$content_element.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="text-align: left;">
                        <a style="display: inline-block; padding: 10px 30px; border-width: 0px; border-style: solid; border-color: rgb(34, 113, 177); border-radius: 3px; font-size: 14px; background-color: rgb(221, 221, 221); color: rgb(34, 113, 177);" class="yeepdf_button" href="#">Click Here</a>
                    </div>
                </div>
            </div>
            <div class="builder-row">
                <div class="builder-elements">
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element.'</div>
                        <div class="text-content">'.$content_element.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="padding: 0px 0px 10px; background-color: transparent; text-align: start;">
                        <div class="text-content-data hidden">'.$content_element_2.'</div>
                        <div class="text-content">'.$content_element_2.'</div>
                    </div>
                    <div class="builder-elements-content" data-type="text" style="text-align: left;">
                        <a style="display: inline-block; padding: 10px 30px; border-width: 0px; border-style: solid; border-color: rgb(34, 113, 177); border-radius: 3px; font-size: 14px; background-color: rgb(221, 221, 221); color: rgb(34, 113, 177);" class="yeepdf_button" href="#">Click Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    return $type; 
}