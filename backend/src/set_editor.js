(function($) {
    "use strict";
    $( document ).ready( function () { 
        var ajax_change_editor = null;
        $(".pro_disable").draggable({ disabled: true });
        //upload IMG
        $('body').on('click', '.upload-editor--image', function(e){
            e.preventDefault();
            var input = $(this).closest(".builder__editor--item").find(".image_url");
            var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    type : 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function() { // it also has "open" and "close" events 
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                input.val(attachment.url).change();
            })
            .open();
        });
        $('body').on('click', '.upload-editor--image-ok', function(e){
            e.preventDefault();
            var input = $(this).closest(".yeepdf_setting_row").find(".image_url");
                var button = $(this),
                    custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    type : 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function() { // it also has "open" and "close" events 
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                input.val(attachment.url).change();
            })
            .open();
        });
        //Menu
        $('body').on("click",".yeepdf_email_add_menu",function(e){
             e.preventDefault();
             var data =$(".builder__editor--item-menu-hidden").html();
             $(".menu-content-tool>ul").append("<li class='data'>"+data+"</li>"); 
             $('.menu-content-tool .text_background,.menu-content-tool .text_color').wpColorPicker({
                change: function(event, ui){
                    $(".wp_builder_pdf_focus").yeepdf_set_type_editor();
                    if( $(".wp_builder_pdf_focus").attr("background_full") == "ok" ){
                        $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color",$(".wp_builder_pdf_focus").css("background-color"));
                    }else{
                        $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color","transparent");
                    }     
                }
            });  
        })
        //Editor Change
        $('body').on("change",".builder__editor--item input, .builder__editor--item select, .builder__editor--item textarea",function(e){
             e.preventDefault();
             $(".wp_builder_pdf_focus").yeepdf_set_type_editor();
                if( $(".wp_builder_pdf_focus").attr("background_full") == "ok" ){
                    $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color",$(".wp_builder_pdf_focus").css("background-color"));
                }else{
                    $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color","transparent");
                }    
        })
        //align
        $('body').on("click",".builder__editor--align a",function(e){
            e.preventDefault();
            $(".builder__editor--align a").removeClass("active");
            $(this).addClass("active");
            var vl = $(this).data("value");
            $(this).closest(".builder__editor--align").find(".text_align").val(vl).change();
        })
        $("body").on("click",".yeepdf_code_editor",function(e){
            tinymce.execCommand('mceToggleEditor', false, 'content');
        })
        $("body").on("change",".yeepdf-image-type-editor",function(e){
            var value = $(this).val();
            if( value == 0 ){
                $(".yeepdf-image-type-upload").removeClass("hidden");
                $(".yeepdf-image-type-field").addClass("hidden");
            }else{
                $(".yeepdf-image-type-upload").addClass("hidden");
                $(".yeepdf-image-type-field").removeClass("hidden");
            }
        })
         //text
        var font_name = $(".builder__editor--button-text .font_family").val();
        tinymce.init({
            selector: '.builder__editor--js',
            mode: 'exact',
            font_formats: yeepdf_script.google_font_font_formats,
            height: 'auto',
            content_style: "body { background-color: #ededed; }",
            height: 'auto',
            skin: "lightgray",
			theme: "modern",
            menubar: false,
            statusbar: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            forced_root_block : false,
            plugins: ["link textcolor colorpicker image code_toggle"],
            toolbar:
                [
                    'bold italic underline | fontselect styleselect',
                    'fontsizeselect | forecolor | backcolor | link image',
                    'yeepdf_shortcodes | code_toggle'
                ],   
            fontsize_formats: '10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 22px 24px 26px 28px 30px 35px 40px 50px 60px',
            setup:function(ed) {
                ed.on('init', function (e) {
                });
                ed.addButton('shortcodes', {
                        type: 'listbox',
                        text: 'Shortcodes',
                        onselect: function (e) {
                            ed.insertContent(this.value())
                        },
                        values: yeepdf_script.shortcode,
                    });
                var full_shortcodes = [];
                $.each(yeepdf_script.shortcodes, function( index_1, values_1 ) {
                    var menu1 = [];
                    $.each(values_1, function( index_2, value_2 ) {
                        if (typeof value_2 === 'object' && value_2 !== null){
                            var menu2 = [];
                            $.each(value_2, function( index_3, value_3 ) {
                                menu2.push({"text":value_3,onclick: function() {
                                    if( index_3.search("{") <0) {
                                        ed.insertContent("["+index_3+"]");
                                    }else{
                                        ed.insertContent(index_3);
                                    }
                                }});
                            });
                            menu1.push({"text":index_2,"menu":menu2});
                        }else{
                            menu1.push({"text":value_2,onclick: function() {
                                if( index_2.search("{") <0) {
                                    ed.insertContent("["+index_2+"]");
                                }else{
                                    ed.insertContent(index_2);
                                }
                            }});
                        }
                    });
                    full_shortcodes.push({"text": index_1, "menu":menu1 });
                });
                ed.addButton('yeepdf_shortcodes', {
                    text: 'Shortcodes',
                    type: "menubutton",
                    menu: full_shortcodes,      
                });
                ed.addButton('contrast', {
                        onclick: function() {
                            ed.windowManager.open( {
                                title: 'Insert icon',
                                body: [{
                                    type: 'textbox',
                                    name: 'icon',
                                    label: 'Icon'
                                },
                                {
                                    type: 'textbox',
                                    name: 'size',
                                    label: 'Size'
                                },
                                {
                                    type: 'textbox',
                                    name: 'color',
                                    label: 'Color'
                                },
                                ],
                                onsubmit: function( e ) {
                                    ed.insertContent( '<span>' + e.data.name + '</span>');
                                }
                            });
                        }
                    });
                ed.on('keyup paste change', function(e) {
                    $(".builder-elements-content.wp_builder_pdf_focus .text-content-data").html(ed.getContent()); 
                    var forcus_el = $(".builder-elements-content.wp_builder_pdf_focus .text-content");
                    if( forcus_el.find(".qrcode").length > 0 || forcus_el.find(".barcode").length >0 ){
                        //don't change
                    }else{
                        $(".builder-elements-content.wp_builder_pdf_focus .text-content").html($.yeepdf_replace_shorcode(ed.getContent())); 
                    }
                });
                ed.on('change focusout', function(e) {
                    $(".builder-elements-content.wp_builder_pdf_focus .text-content-data").html(ed.getContent()); 
                });
            }
        });
         //color
        $('.builder__editor_color').wpColorPicker({
            change: function(event, ui){
                $(".wp_builder_pdf_focus").yeepdf_set_type_editor();
                if( $(".wp_builder_pdf_focus").attr("background_full") == "ok" ){
                    $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color",$(".wp_builder_pdf_focus").css("background-color"));
                }else{
                    $(".wp_builder_pdf_focus").closest(".builder-row-container").css("background-color","transparent");
                }    
            }
        });
        $(document).click(function (e) {
            if (!$(e.target).is(".builder__editor_color")) {
                $('.builder__editor_color').iris('hide');
            }
        });
        $.selector_element = function(){
            var button_tab = $('.builder__tab li a');
            button_tab.each(function () {
                var button = $(this);
                if(button.attr('id') == '#tab__editor' ) {
                    $('.builder__tab li a').removeClass('active');
                    button.addClass('active');
                    var tab = $(button.attr('id'));
                    $('.tab__content').hide();
                    tab.show();
                }
            });
            $('.builder__toolbar').remove();
            $(".builder__editor--item").addClass("hidden");
            $("div").removeClass("wp_builder_pdf_focus").removeClass("wp_builder_pdf_show");
        }
        ///Get ----------------------------------------
        $('body').on("click",".email-builder-main-change_backgroud",function(e){ 
            e.preventDefault();
            e.stopPropagation();
            $.selector_element();
            $(".email-builder-main").addClass("wp_builder_pdf_focus wp_builder_pdf_show");
            $(".email-builder-main").yeepdf_load_type_editor(true);
        })
        //click out 
        $('body').on("click",".builder__tab,.yeepdf-email-slide,.builder-actions,#builder-header,#titlediv",function(e){
            $("div").removeClass('wp_builder_pdf_show wp_builder_pdf_focus');
            $("div").remove(".builder__toolbar");
            $(".builder__editor--item").addClass('hidden');
        })
        $('body').on("click",".builder-elements-content",function(e){
            e.preventDefault();
            e.stopPropagation();
            $.selector_element();
            var toolbar= $('<div class="builder__toolbar">' +
            '<div class="momongaDragHandle"><i class="pdf-creator-icon icon-menu-1"></i></div>' +
            '<div class="momongaEdit"><i class="pdf-creator-icon icon-pencil"></i></div>' +
            '<div class="momongaDuplicate"><i class="pdf-creator-icon icon-docs"></i></div>' +
            '<div class="momongaDelete"><i class="pdf-creator-icon icon-trash"></i></div>' +
            '</div>');
            $(this).addClass("wp_builder_pdf_focus");
            $(this).closest(".builder-row-container").addClass("wp_builder_pdf_show");
            $(this).append(toolbar.clone());
            $(this).closest(".builder-row-container").append(toolbar);
            $(this).yeepdf_load_type_editor();
        })
        $("body").on("click",".momongaEdit",function(e){
            e.preventDefault();
            $(this).closest('.builder__toolbar').parent( ".builder-row-container").find(".builder-row-container-row").click();
        })
        $( '#doaction, #doaction2' ).on( 'click', function( e ) {
                let action = $('select[name="action"]').val();
                if ( action == "pdf_creator" ||  action == "pdf_packing_slip"  ) {
                    e.preventDefault();
                    let template = action;
                    let checked  = [];
                    $('tbody th.check-column input[type="checkbox"]:checked').each(
                        function() {
                            checked.push($(this).val());
                        }
                    );
                    if (!checked.length) {
                        alert('You have to select order(s)!');
                        return;
                    }
                    let order_ids = checked.join(',');
                    if(action == "pdf_packing_slip" ){
                        var url = yeepdf_script.home_url+"/?pdf_preview=preview&id=-1&packing_slip=1&woo_order="+order_ids;
                    }else{
                        var url = yeepdf_script.home_url+"/?pdf_preview=preview&id=-1&woo_order="+order_ids;  
                    }
                    window.open(url,'_blank');
                }
            } );
        $('body').on("click",".builder-row-container-row",function(e){
            e.preventDefault();
            e.stopPropagation();
            $.selector_element();
            var toolbar= $('<div class="builder__toolbar">' +
            '<div class="momongaDragHandle"><i class="pdf-creator-icon icon-menu-1"></i></div>' +
            '<div class="momongaEdit"><i class="pdf-creator-icon icon-pencil"></i></div>' +
            '<div class="momongaDuplicate"><i class="pdf-creator-icon icon-docs"></i></div>' +
            '<div class="momongaDelete"><i class="pdf-creator-icon icon-trash"></i></div>' +
            '</div>');
            $(this).addClass("wp_builder_pdf_focus");
            $(this).closest(".builder-row-container").addClass('wp_builder_pdf_show');
            $(this).closest(".builder-row-container").removeClass('.builder-row-empty');
            $.check_row_empty();
            $(this).closest(".builder-row-container").append(toolbar);
            $(this).yeepdf_load_type_editor(true);
        })
        $.check_row_empty = function() {
            $( ".builder-row-container" ).each(function( index ) {
                    $(this ).find(".builder-row").each(function( index ) { 
                        var check = $(this).find('.builder-elements');
                        if( check.length > 0 ){
                            $(this).removeClass('builder-row-empty');
                            $(this).closest('.builder-row-container').removeClass('builder-row-empty');
                        }
                    })
            });
        }
        $("body").on("click",".yeepdf-popup-add",function(e){
            e.preventDefault();
            $(".yeepdf_condition_add").click();
            $.yeepdf_change_logic();
        })
        $("body").on("click",".yeepdf-popup-minus",function(e){
            e.preventDefault();
            $(this).closest(".yeepdf-logic-item").remove();
            $.yeepdf_change_logic();
        })  
        $("body").on("click",".manager_condition",function(e){
            e.preventDefault();
            var html ="";
            $("a").removeClass("manager_condition_active");
            $(this).addClass("manager_condition_active");
            var datas = $(".manager_condition_active").closest(".builder__editor--item").find("textarea").val();
            if( datas == ""){
            }else{
                datas= JSON.parse(decodeURIComponent(datas));
                var type = datas.type;
                $("#yeepdf-logic-type").val(datas.type);
                $("#yeepdf-logic-logic").val(datas.logic);
                $.each(datas.conditional, function( index, data ) {
                    html += $.yeepdf_get_logic_html(data);
                });
            }
            $(".yeepdf-popup-layout").html(html);
            $( "#yeepdf-popup-content" ).dialog({
                modal: true,
                width: 600,
                title: "Conditional Logic",
                buttons: {
                    Close: function() {
                    $( this ).dialog( "close" );
                    }
                }
            });
          //tb_show("Condition Logic", "#TB_inline?&width=600&height=550&inlineId=yeepdf-popup-content");
            return false;
        })
        $('body').on("click",".yeepdf_condition_add",function(e){
            e.preventDefault();
            var html = $.yeepdf_get_logic_html( {"name":"","rule":"is","value":""});
            $(".yeepdf-popup-layout").append(html);
            $.yeepdf_change_logic();
        }) 
        $.yeepdf_get_logic_html = function(conditional){
            var names = yeepdf_script.shortcodes;
            var html ="";
            var name_logic_html = "";
            var rand_id = Math.floor((Math.random() * 1000));
            $.each(names, function( key, value ) {
                name_logic_html += '<optgroup label="'+key+'">';
                $.each(value, function( k, v ) {
                    var selected_s = "";
                    if( k == ""){
                        return true;
                    }
                    if( conditional.name == k ){
                        selected_s = 'selected';
                    }
                    if(k.search("{") > -1){
                        name_logic_html += '<option '+selected_s+' value="'+k+'">'+k+'</option>';
                    }else{
                        name_logic_html += '<option '+selected_s+' value="'+k+'">['+k+']</option>';
                    }
                })
                name_logic_html += '</optgroup>';
            });
            var rules ={"is":"is","isnot":"is not","greater_than":"greater than","less_than":"less than","contains":"contains","not_contains":"not contains","starts_with": "starts with","ends_with":"ends with"};
            var html = '<div class="yeepdf-logic-item" >';
                html += '<div class="yeepdf-logic-item-name"><select class="yeepdf-logic-name" name="yeepdf_logic[conditional]['+rand_id+'][name]">';
                        html += name_logic_html;
                    html += '</select></div>';
                    html += '<div class="yeepdf-logic-item-rule" ><select class="yeepdf-logic-rule" name="yeepdf_logic[conditional]['+rand_id+'][rule]">';
                    $.each(rules, function( key, rule ) {
                        var selected_s = "";
                        if( conditional.rule == key ){
                            selected_s = 'selected';
                        }
                        html += '<option '+selected_s+' value="'+key+'">'+rule+'</option>';
                    });
                    html += '</select></div>';
                    html += '<div class="yeepdf-logic-item-value" ><input type="text" class="yeepdf-logic-value" name="yeepdf_logic[conditional]['+rand_id+'][value]" value="'+conditional.value+'"></div>';
                    html += '<div class="yeepdf-popup-layout-settings">';
                        html += '<a class="yeepdf-popup-minus" href="#"><span class="dashicons dashicons-trash"></span></a>';
                    html += '</div>';
                html += '</div>';
                return html;
        }
        $('body').on("change keyup",".yeepdf-popup-content select, .yeepdf-popup-content input",function(e){
            $.yeepdf_change_logic();
        })
        $('body').on("click",".builder__widget_tab_title",function(e){ 
            e.preventDefault();
            $(this).toggleClass("yeepdf_tab_hide");
            $(this).closest(".builder__widget_tab").find("ul").slideToggle();
        })
        $.yeepdf_change_logic = function( ){
            var type = $("#yeepdf-logic-type").val();
            var logic = $("#yeepdf-logic-logic").val();
            var conditional = [];
            $(".yeepdf-logic-item").each(function() {
                var name = $(this).find(".yeepdf-logic-name").val();
                var rule = $(this).find(".yeepdf-logic-rule").val();
                var value = $(this).find(".yeepdf-logic-value").val();
                conditional.push({name: name,rule: rule, value: value});
            });
            if( conditional.length == 0 ){
                var data = "";
            }else{
                var data = {"type":type,"logic":logic,"conditional":conditional};
                var data = encodeURIComponent(JSON.stringify(data));
            }
            $(".manager_condition_active").closest(".builder__editor--item").find("textarea").val(data).change();
        }
        $("body").on("click",".medium-editor",function(e){
            var editor = new MediumEditor($(this), {
                toolbar: true,
                paste:{
                    forcePlainText: true,
                    cleanPastedHTML: true,
                    cleanAttrs: ['class', 'style', 'dir','id'],
                    cleanTags: ['meta','div','table','span']
                }
            })
            editor.subscribe('editableInput', function (event, editable) {
                // Do some work
            });
        })
        
        $("body").on("click",".list-view-short-templates-k",function(e){
            var value = $(this).html().trim();
            var field = $(this);
            navigator.clipboard.writeText(value);
            field.html("<strong>Copied</strong>");
            setTimeout(function (){
                field.html(value);
            }, 1000);
        })
})
})(jQuery);