<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Creator_Table_Builder {
    function __construct(){
        add_action("yeepdf_builder_block",array($this,"add_block"),120);
        add_filter( 'yeepdf_builder_block_html', array($this,"add_builder") );
        add_action('yeepdf_builder_tab__editor_before', array($this,'add_editor'));
    }
    function add_block(){
        $pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
        $class ="";
        $title ="";
        if( !$pro){
            $class ="pro_disable";
            $title =" Pro Version";
        }
    ?>
        <li>
            <div class="momongaDraggable <?php echo esc_attr($class) ?>" data-type="table" title="<?php echo esc_html($title) ?>" >
                <i class="dashicons dashicons-editor-table"></i>
                <div class="yeepdf-tool-text"><?php esc_html_e("Table","pdf-for-wpforms") ?></div>
            </div>
        </li>
    <?php
    }
    function add_builder($type){
        $type["block"]["table"]["builder"] = '
        <div class="builder-elements">
                <div class="builder-elements-content" data-type="table" style="padding: 15px 0;">
                    <div class="yeepdf-table-builder-conatiner">
                        <table class="yeepdf-table-builder medium-editor" data-col="3" data-row="3">
                            <thead>
                                <tr>
                                    <th>Header 1</th>
                                    <th>Header 2</th>
                                    <th>Header 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="yeepdf-table-builder-tr-even">
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                </tr>
                                <tr class="yeepdf-table-builder-tr-odd">
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';
        $type["block"]["table"]["builder_table"] = '
            <div class="builder-elements">
                    <div class="builder-elements-content" data-type="table" style="padding: 15px 0;">
                        <div class="yeepdf-table-builder-conatiner">
                        </div>
                    </div>
                </div>';   
        //Show editor
        $type["block"]["table"]["editor"]["container"]["show"]= ["padding","margin","table","condition"];
        $table_style = array(
            //".builder__editor--item-table .yeepdf_setting_input_layout"     =>"table-layout",
        );
        $padding = Yeepdf_Global_Data::$padding;
        $margin = Yeepdf_Global_Data::$margin;
        $pd_mg = array_merge($padding,$margin);
        $table_border = array(
            ".builder__editor--item-table .yeepdf_setting_input_tb_border_color"     =>"border-color",
            ".builder__editor--item-table .yeepdf_setting_input_tb_border_style"     =>"border-style",
            ".builder__editor--item-table .yeepdf_setting_input_tb_border_width"     =>"border-width",
            ".builder__editor--item-table .yeepdf_setting_input_tb_border_collapse"     =>"border-collapse",
        );
        $pd_mg_tb = array_merge($pd_mg,$table_style);
        $pd_mg_tb_boder = array_merge($pd_mg_tb,$table_border);
        
        $table_data = array(
            ".builder__editor--item-table .yeepdf_setting_input_col"     =>"data-col",
            ".builder__editor--item-table .yeepdf_setting_input_row"     =>"data-row",
        );
        $table_header = array(
            ".builder__editor--item-table .yeepdf_setting_input_header_bg"     =>"background-color",
            ".builder__editor--item-table .yeepdf_setting_input_header_color"     =>"color",
        );
        $table_even = array(
            ".builder__editor--item-table .yeepdf_setting_input_even_bg"     =>"background-color",
            ".builder__editor--item-table .yeepdf_setting_input_even_color"     =>"color",
        );
        $table_odd = array(
            ".builder__editor--item-table .yeepdf_setting_input_odd_bg"     =>"background-color",
            ".builder__editor--item-table .yeepdf_setting_input_odd_color"     =>"color",
        );
        $table_cells = array(
            ".builder__editor--item-table .yeepdf_setting_input_cell_ta"     =>"text-align",
            ".builder__editor--item-table .yeepdf_setting_input_cell_padding"     =>"padding",
        );
        $table_cells_full = array_merge($table_border,$table_cells);
        $table_td_full = array_merge($table_cells_full,$table_header);
        $type["block"]["table"]["editor"]["container"]["style"]= array();
        $type["block"]["table"]["editor"]["inner"]["style"]=array(
            ".yeepdf-table-builder"                 =>$pd_mg_tb_boder,
            ".yeepdf-table-builder th"              =>$table_td_full,
            ".yeepdf-table-builder-tr-even td"      =>$table_even,
            ".yeepdf-table-builder-tr-odd td"      =>$table_odd,
            ".yeepdf-table-builder td"              =>$table_cells_full,
        );
        $type["block"]["table"]["editor"]["inner"]["attr"] = array(
                                                                    ".yeepdf-table-builder"=>$table_data,
                                                                    ".yeepdf-table-builder-conatiner"=>array(".builder__editor--html .builder__editor--js"=>"html_hide_table")
                                                                );
        return $type;
    }
    function add_editor(){
        ?>
        <div class="builder__editor--item builder__editor--item-table">
            <div class="builder__editor--html">
                <label><?php esc_html_e("Table","pdf-for-wpforms") ?></label>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_setting_row">
                        <!-- Layout
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Layout","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_layout">
                                    <option value="fixed">fixed</option>
                                    <option value="auto">auto</option>
                                </select>
                            </div>
                        </div>
                        -->
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Columns","pdf-for-wpforms") ?></label>
                            <div title="Columns" class="setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_col" max="100" step="1" type="number">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Rows","pdf-for-wpforms") ?></label>
                            <div title="Rows" class="setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_row" step="1" min="1" type="number" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_check"><?php esc_html_e("Header","pdf-for-wpforms") ?></div>
                    <div class="yeepdf_setting_row">
                        <div class="settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("BG Color","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_header_bg">
                            </div>
                        </div>
                        <div class="settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Text Color","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_header_color">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="yeepdf_setting_group">
                    <div class="yeepdf_div-block-25">
                        <div class="yeepdf_check"><?php esc_html_e("ROWS","pdf-for-wpforms") ?></div>
                    </div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("BG Color (even)","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_even_bg">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Text Color (even)","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_even_color">
                            </div>
                        </div>
                    </div>
                    <div data-element="stripe-styles" class="yeepdf_setting_row is-stripes" style="display: flex;">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("BG Color (odd)","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_odd_bg">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Text Color (odd)","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_odd_color">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="yeepdf_setting_group">
                    <div class="check"><?php esc_html_e("Borders","pdf-for-wpforms") ?></div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Color","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_tb_border_color">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Style","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_tb_border_style">
                                    <option value="solid">solid</option>
                                    <option value="dotted">dotted</option>
                                    <option value="dashed">dashed</option>
                                    <option value="double">double</option>
                                    <option value="groove">groove</option>
                                    <option value="ridge">ridge</option>
                                    <option value="inset">inset</option>
                                    <option value="outset">outset</option>
                                    <option value="none">none</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Width","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <input name="yeepdf_name[]" class="setting_input yeepdf_setting_input_tb_border_width" step="1" type="number" data-after_value="px">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Collapse","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_tb_border_collapse">
                                    <option value="collapse">collapse</option>
                                    <option value="separate">separate</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="yeepdf_setting_group">
                    <div class="check"><?php esc_html_e("Cells","pdf-for-wpforms") ?></div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label for="cell-text-align-dropdown" class="yeepdf_checkbox_label"><?php esc_html_e("Text Align","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_cell_ta">
                                    <option value="left">left</option>
                                    <option value="center">center</option>
                                    <option value="right">right</option>
                                </select>
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label for="cell-padding-input" class="yeepdf_checkbox_label"><?php esc_html_e("Padding","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_cell_padding"step="1" type="number" data-after_value="px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
new Yeepdf_Creator_Table_Builder;