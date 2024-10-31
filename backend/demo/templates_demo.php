<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Superaddons_Yeepdfs_Demo {
	function __construct() { 
		add_action("builder_yeepdfs",array($this,"builder_yeepdfs"),1);
	}
	function builder_yeepdfs(){
        $args = array(
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/new.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/0.png",
            "title" => "Blank",
            "id"=> 107,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-9.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-9.png",
            "title" => "Template 9",
            "id"=> 132,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-10.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-10.png",
            "title" => "Agreement",
            "id"=> 142,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-11.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-11.png",
            "title" => "Inspection Services",
            "id"=> 143,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-12.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-12.png",
            "title" => "Inspection Services",
            "id"=> 144,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-13.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-13.png",
            "title" => "CV Template",
            "id"=> 240,
            ),
            array(
            "json"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/form-14.json",
            "img"=>YEEPDF_CREATOR_BUILDER_URL."backend/demo/images/form-14.png",
            "title" => "Quotation Request",
            "id"=> 243,
            )
        );
        foreach ($args as $value) {
            Yeepdf_Settings_Builder_PDF_Backend::item_demo($value);
        }
	}
}
new Superaddons_Yeepdfs_Demo;