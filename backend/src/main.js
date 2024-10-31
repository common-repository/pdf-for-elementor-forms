(function($) {
    "use strict";
    $( document ).ready( function () {
        // Save bulder to josn
        $.fn.yeepdf_save_type = function (type) { 
            var html_emlement = $(this);
            var type = $(this).data("type");
            var container_style = {};
            var container_attr = {};
            var inner_style = {};
            var inner_attr = {};
            var style_container_element = wp_builder_pdf["block"][type]["editor"]["container"]["style"];
            var style_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["style"];
            var attr_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["attr"];
            //Save style container
            $.each( style_container_element, function( key, value ) {
                var data = html_emlement.css(value);
                container_style[value] = data;
            });
            //Save style inner
            $.each( style_inner_element, function( key, value ) {
                var style_content= {};
                $.each( value , function( index, style ) {
                  var data = html_emlement.find(key).css(style);
                  switch(style) {
                    case "border-style":
                    case "border-color":
                      break;
                    case "width":
                      var data1 = html_emlement.find(key)[0].style.width;
                      style_content[style] = data1;
                      break;
                    case "height":
                      var data1 = html_emlement.find(key)[0].style.height;
                      style_content[style] = data1;
                      break;
                    case "border-width":
                      var border_style = html_emlement.find(key).css("border-style");
                      var border_color = html_emlement.find(key).css("border-color");
                      style_content["border"] = data +" "+ border_style + " " +border_color;
                      break;
                    default:
                      style_content[style] = data
                      break
                  }
                })
                inner_style[key] = style_content;
            });
            //Save attr
            $.each( attr_inner_element, function( key, value ) {
                var attr_content= {};
                $.each( value , function( index, style ) { 
                    switch(style) {
                      case "text":
                        var data = html_emlement.find(key).html();
                        break;
                      case "menu":
                        var menu = {};
                        var i =0;
                        html_emlement.find( ".yeepdf-menu td" ).each(function() {
                              var text_menu = $(this).find("a").html();
                              var href = $(this).find("a").attr("href");
                              var background = $(this).find("a").css("background-color");
                              var color = $(this).find("a").css("color");
                              color = $.email_builder_cover_color(color);
                              background = $.email_builder_cover_color(background);
                              menu[i] = {"text":text_menu,"background":background,"color":color,"href":href} ;
                              i++;
                          });
                        var data = menu;
                        break;
                      case "value_select":
                        let select = {};
                        var i =0;
                        html_emlement.find( "option" ).each(function() {
                            let vl = $(this).val();
                            select[i] = {"value":vl,"text":vl} ;
                            i++;
                        })
                        var data = select;
                        break;
                      case "value_checkbox":
                      case "value_radio":
                        let checkbox = {};
                        var i =0;
                        html_emlement.find( "input" ).each(function() {
                            let vl = $(this).val();
                            checkbox[i] = {"value":vl,"text":vl} ;
                            i++;
                        })
                        var data = checkbox;
                        break;
                      case "html":
                      case "html_hide":
                      case "html_not_change":
                        var data = html_emlement.find(key).html();
                        break;
                      case "html_hide_table":
                        var data = html_emlement.find(key).html();
                        const regex = /font-family[^;]+;/g;
                        data = data.replaceAll(regex, "");
                        break;    
                      default:
                        var data = html_emlement.find(key).attr(style);
                    }
                    attr_content[style] = data
                })
                inner_attr[key] = attr_content;
            });
            var condition = "";
            condition = html_emlement.attr("data-condition");
            if( condition === undefined || condition == "" ){
                condition = ""; 
            }
            return {"type":type,"container_style":container_style,"inner_style":inner_style,"inner_attr":inner_attr,"condition":condition };
        }
         //Click editor -> set element
        var ajax_change_editor = null;
        $.fn.yeepdf_set_type_editor = function ( row ) {
                var builder = $(this);
                var type = $(this).data("type");
                if (typeof type === "undefined") {
                    return
                }
                var style_container_element = wp_builder_pdf["block"][type]["editor"]["container"]["style"];
                var attr_container_element = wp_builder_pdf["block"][type]["editor"]["container"]["attr"];
                //set editor in container element style
                $.each( style_container_element, function( key, value ) {
                    $.yeepdf_set_css_element(key,value,builder);
                });
                $.each( attr_container_element, function( key, value ) {
                    var data = $(key).val();
                    if( $(key).attr("type") == "checkbox" ){
                        if(  $(key).is(':checked') ){
                          data = "ok";
                        }else{
                          data = "not";
                        }
                    }
                    builder.attr(value,data);
                });
                var condition = $(".builder__editor--item-condition .builder__editor--condition").val();
                builder.attr("data-condition",condition);
                //Element
                if( builder.closest(".builder-elements").length > 0 ) {
                    var attr_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["attr"];
                    var style_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["style"];
                    //set editor in element style
                    $.each( style_inner_element, function( key, value ) {
                        $.each( value , function( index, style ) {
                          $.yeepdf_set_css_element(index,style,builder,key,value);
                        })
                    });
                    //set editor in  element attr
                    $.each( attr_inner_element, function( key, value ) {
                        $.each( value , function( index, attr ) {
                            var data = $(index).val();
                            var data_value = $(index).data("after_value");
                            if (data_value !== undefined) {
                                data +=data_value;
                            }
                            switch(attr) {
                              case "data-col":
                                //table col
                                if(data > 0){
                                  var html_table = builder.find(key);
                                  var count_col = html_table.find("th").length;
                                  var table_th_header = html_table.find("th").last().html();
                                  var table_td_header = html_table.find("td").last().html();;
                                  if(data > count_col){
                                    var count_col_plus = count_col;
                                    for (count_col_plus; count_col_plus < data; count_col_plus++) {
                                      html_table.find("thead tr").append('<th>'+table_th_header+'</th>');
                                      html_table.find("tbody tr").append('<td>'+table_td_header+'</td>');
                                    }  
                                  }else if(data < count_col){
                                    var count_col_minus = count_col;
                                    var count_col_new = data;
                                    for (count_col_minus;  count_col_new < count_col_minus; count_col_minus--) {
                                      html_table.find("thead tr th:last").remove();
                                      $(html_table.find("tbody tr")).each(function(){
                                        $(this).find('td:last').remove();
                                      });
                                    }
                                  }
                                  builder.find(key).attr(attr,data);
                                }
                                break;
                              case "data-row":
                                //table col
                                if(data > 1){
                                  var html_table = builder.find(key);
                                  var count_row = html_table.find("tr").length;
                                  var table_tr = html_table.find("tr").last().html();
                                  if(data > count_row){
                                    var count_col_plus = count_row;
                                    for (count_col_plus; count_col_plus < data; count_col_plus++) {
                                      var class_tr = "yeepdf-table-builder-tr-even";
                                      if(count_col_plus % 2 == 0){
                                        class_tr= "yeepdf-table-builder-tr-odd";
                                      }
                                      html_table.find("tbody").append('<tr class="'+class_tr+'">'+table_tr+'</tr>');
                                    }  
                                  }else if(data < count_row){
                                    var count_col_minus = count_row;
                                    var count_col_new = data;
                                    for (count_col_minus;  count_col_new < count_col_minus; count_col_minus--) {
                                      html_table.find("tr:last").remove();
                                    }
                                  }
                                  builder.find(key).attr(attr,data);
                                }
                                break;
                              case "html_not_change":
                                break;
                              case "html":
                                //change shortcode
                                if(data == ""){
                                  data = tinymce.activeEditor.getContent();
                                }
                                if(type == "order_detail"){
                                }else{
                                  builder.find(key).html($.yeepdf_replace_shorcode(data));
                                }
                                break;
                              case "text":
                                builder.find(key).html(data);
                                break;
                              case "menu":
                                var tr = $('<tr class="links"></tr');
                                var i_menu = 0;
                                $( ".builder__editor--item-menu .menu-content-tool li.data" ).each(function() {
                                      var text_menu = $(this).find(".text").val();
                                      var href = $(this).find(".text_url").val();
                                      var background = $(this).find(".text_background").val();
                                      var color = $(this).find(".text_color").val();
                                      var td = $('<td align="center" valign="top"><a target="_blank" href=""></a></td>');
                                      td.find("a").css("background-color",background);
                                      td.find("a").css("color",color);
                                      td.find("a").attr("href",href);
                                      td.find("a").html(text_menu);
                                      td.appendTo(tr);
                                      i_menu++;
                                });
                                builder.find("tr").remove();
                                tr.appendTo(builder.find("table"));
                                var menu_width = 100 / i_menu;
                                builder.find("td").css("width",menu_width);
                                $.each( style_inner_element, function( key, value ) {
                                    $.each( value , function( index, style ) {
                                        $.yeepdf_set_css_element(index,style,builder,key,value);
                                    })
                                  });
                                break;
                              case "value_select":
                                if(data !=""){
                                  let options = data.split("\n");
                                  let options_html = "";
                                  $.each( options, function( key, value ) {
                                      options_html +='<option value="'+value+'">'+value+'</option>';
                                  });
                                  builder.find(key).html(options_html);
                                }
                                break;
                              case "value_checkbox":
                                if(data !=""){
                                  let options = data.split("\n");
                                  let options_html = "";
                                  $.each( options, function( key, value ) {
                                      options_html +=' <input value="'+value+'" type="checkbox" /> '+value;
                                  });
                                  builder.find(key).html(options_html);
                                }
                                break;
                              case "value_radio":
                                if(data !=""){
                                  let options = data.split("\n");
                                  let options_html = "";
                                  $.each( options, function( key, value ) {
                                      options_html +=' <input value="'+value+'" type="radio" /> '+value;
                                  });
                                  builder.find(key).html(options_html);
                                }
                              break;
                              case "data-src": 
                                var youtube_id = $.youtube_parser(data);
                                if( youtube_id ) {
                                    var img_youtube = 'http://cors-anywhere.herokuapp.com/http://img.youtube.com/vi/'+youtube_id+'/0.jpg';
                                    var img = new Image();
                                      img.src = img_youtube;
                                      img.setAttribute('crossorigin', 'anonymous'); 
                                    var img_logo = new Image(64,44);
                                      img_logo.src = yeepdf_script.youtube_play_src;    
                                      img_logo.setAttribute('crossorigin', 'anonymous');
                                    img.onload = function(){
                                      var mycanvas = document.createElement("canvas");
                                      mycanvas.width = 480;
                                      mycanvas.height = 270;
                                      var ctx = mycanvas.getContext('2d');
                                      ctx.drawImage(img,0, 45,480, 270,0,0,480,270 );
                                      ctx.drawImage(img_logo,208, 113, img_logo.width, img_logo.height );
                                      var dataURL = mycanvas.toDataURL();
                                      builder.find(key).attr("src",dataURL);
                                      var data = {
                                        'action': 'yeepdf_builder_save_video',
                                        'img': dataURL,
                                        'id': youtube_id
                                      };
                                      jQuery.post(ajaxurl, data, function(response) {
                                        builder.find(key).attr("src",response);
                                        builder.find(key).attr(attr,response);
                                      });
                                    };
                                }
                                break;
                            case "data-showimg": 
                            case "data-sku":
                            case "data-totals": 
                            case "data-showdes": 
                                var showimg = $(".builder__editor--item-detail-template .detail-img");
                                if( showimg.is(":checked")){
                                  showimg ="yes";
                                }else{
                                  showimg ="hidden";
                                }
                                var totals = $(".builder__editor--item-detail-template .detail-totals");
                                if( totals.is(":checked")){
                                  totals ="yes";
                                }else{
                                  totals ="hidden";
                                }
                                var sku = $(".builder__editor--item-detail-template .detail-sku");
                                if( sku.is(":checked")){
                                  sku ="yes";
                                }else{
                                  sku ="hidden";
                                }
                                var des = $(".builder__editor--item-detail-template .detail-des");
                                if( des.is(":checked")){
                                  des ="yes";
                                }else{
                                  des ="hidden";
                                }
                                var value = "";
                                value = $(index).val();
                                if($(index).attr("type") == "checkbox"){
                                  if( $(index).is(":checked")){
                                    value = "yes";
                                  }else{
                                    value = "hidden";
                                  }
                                }else{
                                  value = data;
                                }
                                var shortcode ="[yeepdf_woo_order_detail show_img='"+showimg+"' item_totals='"+totals+"' item_sku='"+sku+"' show_des='"+des+"']";
                                let count_hidden = $('th[data-sku="yes"],th[data-showimg="yes"],th[data-showdes="yes"]',builder).length;
                                builder.find(".woo_totals th").attr("colspan",2 + parseInt(count_hidden));
                                $(".builder-elements-content.wp_builder_pdf_focus .text-content-data").html(shortcode);
                                builder.find(key).attr(attr,value);
                                break  
                            default:
                              if($(index).attr("type") == "checkbox"){
                                if( $(index).is(":checked")){
                                  data = "yes";
                                }else{
                                  data = "no";
                                }
                              }
                              builder.find(key).attr(attr,data);
                              break;  
                          }
                        })
                    });
                }
          }
         //Click emlement -> get and show editor
          $.fn.yeepdf_load_type_editor = function (row ) {
            var type = $(this).data("type");
            var builder = $(this);
            var type_text = type;
            if( wp_builder_pdf["block"][type]["type_text"] !== undefined){
              type_text = wp_builder_pdf["block"][type]["type_text"];
            }
            $(".yeepdf-builder-goback .yeepdf-builder-goback_block").html(type_text);
            var show_class_container = wp_builder_pdf["block"][type]["editor"]["container"]["show"];
            var style_container_element = wp_builder_pdf["block"][type]["editor"]["container"]["style"];
            var attr_container_element = wp_builder_pdf["block"][type]["editor"]["container"]["attr"];
            //Show eidtor
            $.each( show_class_container, function( key, value ) {
              $(".builder__editor--item-"+value).removeClass("hidden");
            });
            //set editor in container element style
            $.each( style_container_element, function( key, value ) {
                var data = builder.css(value);
                if(value == "font-family"){
                    data = data.replaceAll('"',"");
                    if(data == ""){
                        data = 'dejavu sans';
                    }
                }
                $.yeepdf_set_css_editor(value,key,data,builder);
            });
            $.each( attr_container_element, function( key, value ) {
                var data = builder.attr(value);
                var type = $(key).attr("type");
                if( type == "checkbox"){
                  if( data == "ok" || data == "yes"){
                    $(key).prop('checked', true);
                  }else{
                    $(key).prop('checked', false);
                  }
                }
                $(key).val(data);
            });
            var condition = builder.attr("data-condition");
            if( condition === undefined || condition == "" ){
                condition = ""; 
            }
            $(".builder__editor--item-condition .builder__editor--condition").val(condition);
            if( !row ){
                var attr_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["attr"];
                var style_inner_element = wp_builder_pdf["block"][type]["editor"]["inner"]["style"];
                //set editor in element style
                $.each( style_inner_element, function( key, value ) {
                  $.each( value , function( index, style ) {
                      var data = builder.find(key).css(style); 
                      $.yeepdf_set_css_editor(style,index,data,builder);
                  })
                });
                //set editor in  element attr
                $.each( attr_inner_element, function( key, value ) {
                    $.each( value , function( index, attr ) {
                        switch(attr) {
                              case "data-type":
                                var value =  builder.find(key).attr(attr); 
                                $(index).val(value);
                                if( value == 0 ){
                                    $(".yeepdf-image-type-upload").removeClass("hidden");
                                    $(".yeepdf-image-type-field").addClass("hidden");
                                }else{
                                    $(".yeepdf-image-type-upload").addClass("hidden");
                                    $(".yeepdf-image-type-field").removeClass("hidden");
                                }
                                break;
                              case "html_hide":
                              case "data-src":      
                                break;
                              case "text":
                                var data = builder.find(key).html();
                                $(index).val(data);
                                break;
                              case "html":
                              case "html_not_change":  
                                if( builder.find(key+"-data").length > 0 ){
                                    var data = builder.find(key +"-data").html(); 
                                }else{
                                    var data = builder.find(key).html(); 
                                }
                                tinyMCE.get('builder__editor--js').setContent(data);
                                break;
                              case "html_ajax":
                                var data = builder.find(key+"-data").html();
                                tinyMCE.get('builder__editor--js').setContent(data);
                                break;
                              case "menu":
                                var html_menu=$("<ul></ul>");
                                $( builder.find(".yeepdf-menu td") ).each(function() {
                                    var text_menu = $(this).find("a").html();
                                    var href = $(this).find("a").attr("href");
                                    var background = $(this).find("a").css("background-color");
                                    var color = $(this).find("a").css("color");
                                    color = $.email_builder_cover_color(color);
                                    background = $.email_builder_cover_color(background);
                                    var menu = $(".builder__editor--item-menu-hidden").clone().removeClass('hidden builder__editor--item-menu-hidden');
                                    menu.find(".text").val(text_menu);
                                    menu.find(".text_url").val(href);
                                    menu.find(".text_background").val(background);
                                    menu.find(".text_color").val(color);
                                    var container_li = $("<li class='data'></li>");
                                    menu.appendTo(container_li);
                                    container_li.appendTo(html_menu);
                                });
                                html_menu.removeClass('hidden');
                                $(".builder__editor--item-menu .menu-content-tool ul").remove();
                                html_menu.appendTo($(".menu-content-tool"));
                                $('.menu-content-tool .text_background,.menu-content-tool .text_color').wpColorPicker({
                                    change: function(event, ui){
                                        $(".wp_builder_pdf_focus").yeepdf_set_type_editor();   
                                    }
                                });
                                break;
                              case "value_select":
                                var value_select="";
                                let i = 0;
                                $( builder.find("select option") ).each(function() {
                                    if(i == 0){
                                      value_select += $(this).val();
                                    }else{
                                      value_select += "\n"+$(this).val();
                                    }
                                    i++;
                                })
                                $(index).val(value_select);
                                break;
                              case "value_checkbox":
                              case "value_radio":
                                let value_check="";
                                let i_c = 0;
                                $( builder.find(".yeepdf_checkbox_container input") ).each(function() {
                                    if(i_c == 0){
                                      value_check += $(this).val();
                                    }else{
                                      value_check += "\n"+$(this).val();
                                    }
                                    i_c++;
                                })
                                $(index).val(value_check);
                                break;
                              default:
                                var data = builder.find(key).attr(attr); 
                                var type = $(index).attr("type");
                                if(type == "checkbox"){
                                  if( data == "ok" || data == "yes"){
                                      $(index).prop('checked', true);
                                  }else{
                                    $(index).prop('checked', false);
                                  }
                                }else{
                                  $(index).val(data);
                                }
                                break;
                        }
                    })
                });
            }
        } 
        $.yeepdf_replace_shorcode = function (str) { 
          if( str === undefined){
            return str;
          }
            let re = new RegExp(yeepdf_script.builder_shorcode_re, "gi");
            str = str.replaceAll(re, function (matched) {
                matched = matched.replace(/\[|\]/gi, "");
                if(  yeepdf_script.builder_shorcode[matched] === undefined ){
                  return "["+matched+"]";
                }else{
                  return yeepdf_script.builder_shorcode[matched];
                }
            })
            return str;
        }
         // Drop emlement and load builder -> show data builder  
        $.yeepdf_load_type = function (type,elements,save_html=false) { 
                if( type in wp_builder_pdf["block"] ) {
                    var html = $(wp_builder_pdf["block"][type]["builder"]);
                }else{
                    var html = $(wp_builder_pdf["block"]["text"]["builder"]);
                }     
                if( elements ){
                    var container_style = elements.container_style;
                    var inner_style = elements.inner_style;
                    var inner_attr = elements.inner_attr;
                    html.find(".builder-elements-content").css(container_style);
                    $.each( inner_style, function( key, value ) {
                        if( value != "") {
                          html.find(key).css(value); 
                        }
                    });
                    if( elements.condition != ""){
                        html.find(".builder-elements-content").attr("data-condition",elements.condition);
                    }
                    $.each( inner_attr, function( key, value ) {
                            var check_type_img = 0;
                            $.each( value, function( k, v ) { 
                                switch(k) {
                                      case "html_hide_table":
                                        if(v != ""){
                                          v = v.replace("medium-editor medium-editor-element","");
                                        }
                                        html.find(key).html(v);
                                        break;
                                      case "text": 
                                      case "html_hide":
                                        if( save_html ) {
                                            if( type == "qrcode" ){
                                                var v = '[yeepdf_qrcode]'+v+'[/yeepdf_qrcode]';
                                            }else if( type == "barcode") {
                                                var v = '[yeepdf_barcode]'+v+'[/yeepdf_barcode]';
                                            }
                                        }
                                        html.find(key).html(v);
                                        break;
                                      case "html":;
                                        var v_hide = inner_attr[".text-content-data"]["html_hide"];
                                        if(v_hide != ""){
                                          if(v_hide.search("=")>0){
                                            v_hide = v;
                                          }
                                        }
                                        v_hide = $.yeepdf_replace_shorcode(v_hide);
                                        html.find(key).html(v_hide);
                                        break;
                                      case "data-src":
                                        html.find(key).attr("src",v);
                                        html.find(key).attr(k,v);
                                        break;
                                      case "menu":
                                        var menu_main ="";
                                        var width = 100 / _.size(v);
                                        $.each( v, function(menu_key, menu ) {
                                          menu_main += '<td style="width:'+width+'%" align="center" valign="top"><a style="color:'+menu.color+';background-color:'+menu.background+';" target="_blank" href="'+menu.href+'">'+menu.text+'</a></td>';
                                        })
                                        html.find("tr").html(menu_main);
                                        $.each( inner_style, function( key, value ) {
                                            if( value != "") {
                                              html.find(key).css(value); 
                                            }  
                                        });
                                        break;
                                      case "value_select":
                                        let option_main ="";
                                        $.each( v, function(option_key, options ) {
                                          option_main += '<option value="'+options.value+'">'+options.text+'</option>';
                                        })
                                        html.find("select").html(option_main);
                                        break;
                                      case "value_checkbox":
                                        let input_checkbox ="";
                                        $.each( v, function(option_key, options ) {
                                          input_checkbox += ' <input  type="checkbox" value="'+options.value+'"/> '+options.text;
                                        })
                                        html.find(key).html(input_checkbox);
                                        break;
                                      case "value_radio":
                                        let input_radio ="";
                                        $.each( v, function(option_key, options ) {
                                          input_radio += ' <input  type="radio" value="'+options.value+'"/> '+options.text;
                                        })
                                        html.find(key).html(input_radio);
                                        break;
                                      case "data-type":
                                          check_type_img = v;
                                          html.find(key).attr(k,v);
                                          break;
                                      case "data-field":
                                          if( check_type_img == 1 && save_html ){
                                            html.find(key).attr("src",v); 
                                          }
                                          html.find(key).attr(k,v);
                                          break;
                                      default:
                                        html.find(key).attr(k,v);
                                        break;
                                    }
                            })
                    });
                }
            return html;
          }
          //Cover grb to hex
          $.email_builder_cover_color = function (rgb) {
              if( rgb == "" ){
                  return "";
              } 
              if( "rgba(0,0,0,0)" == rgb.replace(/\s/g, '') ){
                  return "transparent";
              }
              rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
              return (rgb && rgb.length === 4) ? "#" +
                      ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
                      ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
                      ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
          }
        //set css edit
        $.youtube_parser =function( url ) {
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            var match = url.match(regExp);
            return (match&&match[7].length==11)? match[7] : false;
        }
        $.yeepdf_background_img =function( data ) {
            var img = data.replace('url(','').replace(')','').replace(/\"/gi, "");
            return img
        }
        $.yeepdf_set_css_editor =function( style, index,data,builder) {
            switch(style) {
                case "border-color":
                case "background-color":
                case "color":
                    data = $.email_builder_cover_color(data);
                    $(index).val(data);
                    $(index).closest(".wp-picker-container").find(".button").css("background-color",data);
                  break;
                case "background-image":
                    if( data == "none"){
                      data = "";
                    }else{
                      data = $.yeepdf_background_img(data);
                    }
                    $(index).val(data); 
                    break;
                case "width":
                    if(builder.find("img").length > 0) {
                      //image
                      var data1 = builder.find("img")[0].style.width;
                    }else{
                      //text
                      if(builder.find(".text-content")[0].style.width === undefined){
                        var data1 = "100%";
                      }else{
                        var data1 = builder.find(".text-content")[0].style.width;
                        if(data1 == ""){
                          data1 ="100%";
                        }
                      }
                    }
                    if(data1 == ""){
                      data = data.replace("px","");
                      data1 = data; 
                    }
                    $(index).val(data1); 
                    break;
                case "height":
                  if(builder.find("img").length > 0) {
                    //image
                    var data1 = builder.find("img")[0].style.height;
                  }else{
                    //text
                    if(builder.find(".text-content")[0].style.height === undefined){
                      var data1 = "auto";
                    }else{
                      var data1 = builder.find(".text-content")[0].style.height;
                      if(data1 == ""){
                        data1 ="auto";
                      }
                    }
                  }
                    if(data1 == ""){
                      data = data.replace("px","");
                      data1 = data; 
                    }
                    $(index).val(data1); 
                    break;
                case "text-align":
                    $(index).val(data); 
                    $(index).closest(".builder__editor--item").find(".button__align").removeClass("active");
                    $(index).closest(".builder__editor--item").find(".builder__editor--align-"+data).addClass("active");
                    if( data == "stat")
                  break;
                default:
                  data = data.replace("px",""); 
                  if(!isNaN(data)){
                    data = Math.ceil(data);
                  }
                  var type_text = $(index).attr("type");
                  if( type_text == "checkbox"){
                      if( data== "none"){
                        $(index).prop('checked', false);
                      }else{
                        $(index).prop('checked', true);
                      }
                  } 
                  $(index).val(data); 
              }
        }
      $.yeepdf_set_css_element =function( selector, style,builder="",key="",value="") {
        var data = $(selector).val();
        var data_value = $(selector).data("after_value");
        if (data_value !== undefined) {
          if(data.search("px") < 0){
            data +=data_value;
          }
        }
        if(key){
          builder = builder.find(key);
        }
        switch(style) {
          case "display":
            if( $(selector).is(':checked') ) { 
              builder.css(style,"inline-block");  
            }else{
                builder.css(style,"none"); 
            }
            break;
          case "text":
            builder.html(data);
            break;
          case "width":
          case "height":
            if(data.search("%") > -1 || data.search("auto") > -1 ){
              data = data.replace("px", "");
            }
            builder.css(style,data);
            break;
          case "background-image":
            if( data == ""){
              builder.css("background-image","");
            }else{
              builder.css("background-image",'url("' + data + '")');
            }
            break;
          case "font-size":
              data = Number(data);  
              builder.css("font-size",data);
              break;
          case "border-style":
          case "border-color":
            break;
          case "border-width":
              var border_style = "solid";
              var border_color = "#000000";
              $.each( value, function( k_vl, v_vl ) {
                if(v_vl == "border-style"){
                  border_style = $(k_vl).val();
                }
                if(v_vl == "border-color"){
                  border_color = $(k_vl).val();
                }
              })
              builder.css("border",data +" "+ border_style + " " +border_color);
              break;
          default:
                builder.css(style,data);
                break;
        }   
      }
      //
      $("body").on("change",".pdf_creator_settings_template_type",function(e){
        var value = $(this).val();
        if(value == 1){
            $(".gform-kitchen-sink-template_id").removeClass("hidden");
            $(".gform-kitchen-sink-template_upload").addClass("hidden");
        }else{
            $(".gform-kitchen-sink-template_upload").removeClass("hidden");
            $(".gform-kitchen-sink-template_id").addClass("hidden");
        }
      })
      $("body").on("change","#pdf_creator_conditional_logic",function(e){  
        if( $(this).is(":checked")){
            $(".yeepdf-popup-content").removeClass("hidden");
        }else{
            $(".yeepdf-popup-content").addClass("hidden");
        }
      }) 
      $("body").on("change",".yeepdf_my_account_buttons",function(e){
          var value = $(this).val();
          if(value == "custom"){
            $(".yeepdf_my_account_buttons_custom").removeClass("hidden");
          }else{
            $(".yeepdf_my_account_buttons_custom").addClass("hidden");
          }
      })    
    })
})(jQuery);