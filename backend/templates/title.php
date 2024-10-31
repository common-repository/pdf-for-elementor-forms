<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action("yeepdf_builder_tab_block_template","yeepdf_builder_block_title",30);
function yeepdf_builder_block_title(){
	?>
	<li data-type="title">
		<div class="momongaDraggable">
            <i class="dashicons dashicons-heading"></i>
            <div class="yeepdf-tool-text"><?php esc_html_e("Title","yeepdf") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'yeepdf_builder_block_html', "yeepdf_builder_block_title_load" );
function yeepdf_builder_block_title_load($type){
    $content_element = '<h1><strong>Enter your title here</strong></h1>';
    $content_element_2 = 'Subtitle';
    $text_show = do_shortcode($content_element);
    $type["block"]["title"]["builder"] = '<div class="builder-row-container builder__item">
        <div style="background-color: rgb(255, 255, 255); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: start; padding: 15px 30px;" background_full="not" data-type="row1" class="builder-row-container-row builder-row-container-row1">
            <div class="builder-row">
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="text" style="padding: 0px; background-color: rgba(0, 0, 0, 0); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: center;">
                    <div class="text-content-data hidden">'.$content_element.'</div>
                    <div class="text-content">'.$text_show.'</div>
                </div>
            </div>
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="text" style="padding: 0px; background-color: rgba(0, 0, 0, 0); background-image: none; background-position: center center; background-repeat: no-repeat; background-size: cover; text-align: center;">
                    <div class="text-content-data hidden">'.$content_element_2.'</div>
                    <div class="text-content">'.$content_element_2.'</div>
                </div>
            </div>
            </div>
        </div>
    </div>';
    return $type; 
}