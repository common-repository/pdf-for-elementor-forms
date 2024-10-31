<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<style type="text/css" >
    body,.wap {
        width:100%;
    }
    a {
        text-decoration:  none;
    }
    .wap {
        background-position: top left !important;
        background-size: 100% !important;
    }
    p {
        font-size: 13px;
        line-height: 1.1;
        margin: 0;
    }
    .links a {
        display: inline-block;
        width: 100%;
    }
    .yeepdf_button {
        display: block
    }
    .yeepdf_button a {
        width:100%;
        display:block;
    }
    table {
        width: 100%;
    }
    table td{
        vertical-align: top;
    }
    .order-detail-data {
        padding-left: 15px;
        margin-bottom: 5px;
    }
    div {
        display: block !important;
    }
    .page_break { 
        page-break-before: always; 
    }
    .order-detail tr td{
        padding-bottom: 20px;
    }
    .builder-elements table.yeepdf-woocommerce-table td {
        padding: 10px;
    }
    .builder-elements table.yeepdf-woocommerce-table th {
        padding: 10px;
    }
    .builder-elements table.yeepdf-woocommerce-table th:last-child,
    .builder-elements table.yeepdf-woocommerce-table td:last-child {
        text-align: right !important;
        width: 150px;
    }
    .builder-elements table.yeepdf-woocommerce-table th.quantity,
    .builder-elements table.yeepdf-woocommerce-table td.quantity {
        width: 150px;
        text-align: center !important;
    }
    .builder-elements table.yeepdf-woocommerce-table th.thumbnail,
    .builder-elements table.yeepdf-woocommerce-table td.thumbnail {
        width: 32px;
    }
    .builder-elements table.yeepdf-woocommerce-table td.thumbnail img {
        width:  100% !important;
    }
    img.barcode {
        max-width: 500px;
    }
    .col {
        float: left;
        min-height: 1px;
    }
    .row::after {
        content: "";
        display: block;
        clear: both;
    }
    htmlpagefooter {
        text-align: center;
    }
    htmlpagefooter .page-number {
        width: 100px;
        float: right;
        right: 15px;
    }
    img {
        max-width: 100%;
    }
    .woocommerce-Price-currencySymbol, .icon {
        font-family: DejaVu Sans, sans-serif !important;
    }
    .dotab_content {
        border-bottom: 2px dotted;
    }
    .clear {
        clear: both;
    }
    input[type=checkbox]:before { font-family: DejaVu Sans; }
    input[type=checkbox] { display: inline; }
    @page {
        header: page-header;
        footer: page-footer;
    }
    .yeepdf-table,
    .yeepdf-table th,
    .yeepdf-table td{
        border: 1px solid black;
        border-collapse: collapse;
    }
    .yeepdf-table thead tr{
        border-bottom: 2px solid #000;
    }
    .yeepdf-table .tfoot-tr-1 th,.yeepdf-table .tfoot-tr-1 td{
        border-top-width: 4px;
    }
    .yeepdf-order-detail-template-1,
    .yeepdf-order-detail-template-1 th,
    .yeepdf-order-detail-template-1 td
    {
        border: none;
    }
    .yeepdf-order-detail-template-1 tfoot tr.tfoot-tr-1{
        border-top: 1px solid #000;
    }
    .yeepdf-order-detail-template-2 th{
        background-color: #5d6f79;
        color: #fff;
    }
    .yeepdf-order-detail-template-2 tr{
        background-color: #f1f5f6;
        color: #666;
        -top: 1px solid #fff;
    }
    .yeepdf-order-detail-template-2 thead tr{
        border-bottom: none;
    }
    .yeepdf-order-detail-template-2 .tfoot-tr-1 th,.yeepdf-order-detail-template-2 .tfoot-tr-1 td{
        border-top-width: 1px;
    }
    .yeepdf_button {
        display: inline-block;
    }
    .yeepdf-table-builder {
        border: 1px solid #dededf;
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        border-spacing: 100px;
        text-align: center;
    }
    .yeepdf-table-builder th {
        color: rgb(0, 0, 0);
        padding: 10px;
        border-width: 1px;
        border-style: solid;
        border-color: rgb(222, 222, 223);
        min-height: 30px;
    }
    .yeepdf-table-builder td {
        padding: 5px;
        border-width: 1px;
        border-style: solid;
        border-color: rgb(222, 222, 223);
        color: rgb(0, 0, 0);
        min-height: 30px;
    }
    .hidden {
        display: none;
    }
    .text-content-data {
        overflow: hidden;
    }
    <?php
        do_action("yeepdf_css");
    ?>
</style>
<?php if ( is_rtl() ) { 
    echo '<div dir="rtl">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}else{
    echo '<div dir="ltr">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
do_action("yeepdf_header");
?>