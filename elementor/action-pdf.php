<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Elementor\Controls_Manager;
/**
 * Elementor form ping action.
 *
 * Custom Elementor form action which will ping an external server.
 *
 * @since 1.0.0
 */
class Superaddons_Pdf_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get action name.
	 *
	 * Retrieve ping action name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'pdf_creator';
	}

	/**
	 * Get action label.
	 *
	 * Retrieve ping action label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'PDF', 'elementor-forms-ping-action' );
	}
	protected function get_control_id( $control_id ) {
        return $control_id;
    }
	protected function get_title() {
        return esc_html__( 'PDF Settings', 'pdf-for-elementor-forms' );
    }
	public function register_settings_section( $widget ) {
		$pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		$options_logic = array(
			"==" => esc_html__("is","conditional-logic-for-elementor-forms"),
			"!=" => esc_html__("not is","conditional-logic-for-elementor-forms"),
			"e" => esc_html__("empty","conditional-logic-for-elementor-forms"),
			"!e" => esc_html__("not empty","conditional-logic-for-elementor-forms"),
			"c" => esc_html__("contains","conditional-logic-for-elementor-forms"),
			"!c" => esc_html__("does not contain","conditional-logic-for-elementor-forms"),
			"^" => esc_html__("starts with","conditional-logic-for-elementor-forms"),
			"~" => esc_html__("ends with","conditional-logic-for-elementor-forms"),
			">" => esc_html__("greater than","conditional-logic-for-elementor-forms"),
			"<" => esc_html__("less than","conditional-logic-for-elementor-forms"),
			"array" => esc_html__("list array (a,b,c)","conditional-logic-for-elementor-forms"),
			"!array" => esc_html__("not list array (a,b,c)","conditional-logic-for-elementor-forms"),
			"array_contain" => esc_html__("list array contain (a,b,c)","conditional-logic-for-elementor-forms"),
			"!array_contain" => esc_html__("not list array contain (a,b,c)","conditional-logic-for-elementor-forms"),
		);
		$pdf_templates = get_posts(array( 'post_type' => 'yeepdf','post_status' => 'publish','numberposts'=>-1 ));
	    $templates = array();
		$templates[0] = esc_html__("Choose Template",'crm-marketing');
		foreach ( $pdf_templates as $pdf_template ) {
			$id = $pdf_template->ID;
			$templates[$id] = "(". $id .") ". $pdf_template->post_title;
		}
		$widget->start_controls_section(
			$this->get_control_id('section_sendy'),
			[
				'label' => $this->get_title(),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		$widget->add_control(
			$this->get_control_id('template_pdf'),
			[
				'label' => esc_html__( 'Choose PDF Template', 'pdf-for-elementor-forms' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $templates,
				'description' => esc_html__( 'Enter the pdf template.', 'pdf-for-elementor-forms' ),
			]
		);

		$widget->add_control(
			$this->get_control_id('name_pdf'),
			[
				'label' => esc_html__( 'PDF Name', 'elementor-forms-sendy-action' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'To customize sent fields, copy the shortcode that appears inside each field and paste it above. e.g: [field id="name"]', 'pdf-for-elementor-forms' ),
			]
		);
		$widget->add_control(
			$this->get_control_id('pdf_name_show_id'),
			[
				'label' => esc_html__( 'Show name key number', 'pdf-for-elementor-forms' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'name-{number}.pdf', 'pdf-for-elementor-forms' ),
			]
		);
		if($pro){
			$widget->add_control(
				$this->get_control_id('password_pdf'),
				[
					'label' => esc_html__( 'PDF Password', 'pdf-for-elementor-forms' ),
					'type' => Controls_Manager::TEXT,
					'description' => esc_html__( 'To customize sent fields, copy the shortcode that appears inside each field and paste it above. e.g: [field id="name"]', 'pdf-for-elementor-forms' ),
				]
			);
		}else{
			$widget->add_control(
				$this->get_control_id('pdf_pro'),
				[
					'label' => esc_html__( 'PDF Password', 'pdf-for-elementor-forms' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
					'raw' => esc_html__( 'Upgrade to pro version', 'pdf-for-elementor-forms' ),
				]
			);
		}
		$widget->add_control(
			$this->get_control_id('attach_email_pdf'),
			[
				'label' => esc_html__( 'Attach PDF in email', 'pdf-for-elementor-forms' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		if($pro){
			if (!class_exists("Superaddons_Elementor_Conditional_logic_Init", false)) {
				$widget->add_control(
					$this->get_control_id('pdf_pro'),
					[
						'label' => esc_html__( 'Conditional Logic', 'pdf-for-elementor-forms' ),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
						'raw' => esc_html__( 'Please Install the add-on: ', 'pdf-for-elementor-forms' ).'<a target="_blank" href="https://wordpress.org/plugins/conditional-logic-for-elementor-forms/">https://wordpress.org/plugins/conditional-logic-for-elementor-forms/</a>',
					]
				);
			}else{
				
				$control_id_conditional_logic = $this->get_control_id( 'pdf_conditional_logic' );
				$widget->add_control(
					$control_id_conditional_logic,
					[
						'label' => esc_html__( 'Enable Conditional Logic', 'elementor-pro' ),
						'render_type' => 'none',
						'type' => Controls_Manager::SWITCHER,
					]
				);
				$widget->add_control(
					$this->get_control_id( 'pdf_conditional_logic_display' ),
					[
						'label' => esc_html__( 'Display mode', "conditional-logic-for-elementor-forms" ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'show' => [
								'title' => esc_html__( 'Create if', "conditional-logic-for-elementor-forms" ),
								'icon' => 'fa fa-eye',
							],
							'hide' => [
								'title' => esc_html__( 'Disable if', "conditional-logic-for-elementor-forms" ),
								'icon' => 'fa fa-eye-slash',
							],
						],
						'default' => 'show',
						'condition' => [
							$control_id_conditional_logic => 'yes'
						],
					]
				);
				$widget->add_control(
					$this->get_control_id( 'pdf_conditional_logic_trigger' ),
					[
						'label' => esc_html__( 'When to Trigger', "conditional-logic-for-elementor-forms" ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							"ALL"=>"ALL",
							"ANY"=>"ANY"
						],
						'default' => 'ALL',
						'condition' => [
							$control_id_conditional_logic => 'yes'
						],
					]
				);
				$widget->add_control(
					$this->get_control_id( 'pdf_conditional_logic_datas' ),
					[
						'name'           => 'pdf_conditional_logic_datas',
						'label'          => esc_html__( 'Fields if', "conditional-logic-for-elementor-forms" ),
						'type'           => 'conditional_logic_repeater',
						'fields'         => [
							[
								'name' => 'conditional_logic_id',
								'label' => esc_html__( 'Field ID', "conditional-logic-for-elementor-forms" ),
								'type' => Controls_Manager::TEXT,
								'label_block' => true,
								'default' => '',
							],
							[
								'name' => 'conditional_logic_operator',
								'label' => esc_html__( 'Operator', "conditional-logic-for-elementor-forms" ),
								'type' => Controls_Manager::SELECT,
								'label_block' => true,
								'options' => $options_logic,
							'default' => '==',
							],
							[
								'name' => 'conditional_logic_value',
								'label' => esc_html__( 'Value to compare', "conditional-logic-for-elementor-forms" ),
								'type' => Controls_Manager::TEXT,
								'label_block' => true,
								'default' => '',
							],
						],
						'condition' => [
								$control_id_conditional_logic => 'yes'
							],
						'style_transfer' => false,
						'title_field'    => '{{{ conditional_logic_id  }}} {{{ conditional_logic_operator  }}} {{{ conditional_logic_value  }}}',
						'default'        => array(
							array(
								'conditional_logic_id' => '',
								'conditional_logic_operator' => '==',
								'conditional_logic_value' => '',
							),
						),
					]
				);
			}
		}else{
			$widget->add_control(
				$this->get_control_id('pdf_pro_conditional'),
				[
					'label' => esc_html__( 'Conditional Logic', 'pdf-for-elementor-forms' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
					'raw' => esc_html__( 'Upgrade to pro version', 'pdf-for-elementor-forms' ),
				]
			);
		}
		$widget->end_controls_section();
	}
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}

}
class Superaddons_Pdf2_Action_After_Submit extends Superaddons_Pdf_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator2';
	}
	public function register_settings_section( $widget ) {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			$widget->start_controls_section(
				$this->get_control_id('section_sendy'),
				[
					'label' => $this->get_title(),
					'condition' => [
						'submit_actions' => $this->get_name(),
					],
				]
			);
			$widget->add_control(
				$this->get_control_id('pdf_pro'),
				[
					'label' => esc_html__( 'Pro version', 'pdf-for-elementor-forms' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
					'raw' => esc_html__( 'Upgrade to pro version', 'pdf-for-elementor-forms' ),
				]
			);
			$widget->end_controls_section();
		}else{
			parent::register_settings_section($widget);
		}
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 2  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 2', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 2', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_2";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf3_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator3';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 3  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 3', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 3', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_3";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf4_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator4';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 4  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 4', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 4', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_4";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf5_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator5';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 5  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 5', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 5', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_5";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf6_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator6';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 6  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 6', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 6', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_6";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf7_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator7';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 7  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 7', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 7', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_7";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf8_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator8';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 8  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 8', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 8', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_8";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf9_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator3';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 9  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 9', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 9', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_9";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}
class Superaddons_Pdf10_Action_After_Submit extends Superaddons_Pdf2_Action_After_Submit{
	public function get_name() {
		return 'pdf_creator10';
	}
	public function get_label() {
		$check = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		if(!$check ){
			return esc_html__( 'PDF 10  (Pro version)', 'elementor-forms-ping-action' );
		}else{
			return esc_html__( 'PDF 10', 'elementor-forms-ping-action' );
		}
	}
	protected function get_title() {
        return esc_html__( 'PDF Settings 10', 'pdf-for-elementor-forms' );
    }
	protected function get_control_id( $control_id ) {
        return $control_id."_10";
    }
	public function run( $record, $ajax_handler ) {}
	public function on_export( $element ) {}
}