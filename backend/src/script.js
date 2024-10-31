(function($) {
    "use strict";
    $( document ).ready( function () {
      var yeepdf_builder_main = {
            json_to_builder: function(){
                var data_json = $(".data_email").val();
                var datas = {};
                var html="";
                if( data_json =="" || typeof data_json === "undefined"){
                  return;
                }
                try {
                    datas = JSON.parse(data_json);
                } catch (e) {
                    return true;
                }
                $(".builder__list").html("");
                $(".email-builder-main").css(datas["container"]);
                for (let index_row in datas['rows'] ) {
                    var row_style = datas['rows'][index_row].style;
                    var row_columns = datas['rows'][index_row].columns;
                    var row_type = datas['rows'][index_row].type;
                    var row_attr = datas['rows'][index_row].attr;
                    var row_condition = datas['rows'][index_row].condition;
                    var row = $('<div class="builder-row-container builder__item"></div>');
                    var inner_row = $('<div data-type="'+row_type+'" class="builder-row-container-row builder-row-container-'+row_type+'"></div>');
                    inner_row.css(row_style);
                    inner_row.attr(row_attr);
                    inner_row.attr("data-condition",row_condition);
                    inner_row.appendTo(row);
                    var i = 0;
                    for (let index_column in row_columns ) {
                      i++;
                      switch(row_type) {
                          case "row3":
                              if( i == 1){
                                var column = $('<div class="builder-row bd-row-2 builder-row-empty"></div>');  
                              }else{
                                var column = $('<div class="builder-row builder-row-empty"></div>');
                              }
                          break;
                          case "row4":
                              if( i != 1){
                                var column = $('<div class="builder-row bd-row-2 builder-row-empty"></div>');  
                              }else{
                                var column = $('<div class="builder-row builder-row-empty"></div>');
                              }
                              break;
                          default:
                              var column = $('<div class="builder-row builder-row-empty"></div>');
                        }
                      var elements = row_columns[index_column].elements;
                      for (let index_element in elements ) { 
                            column.removeClass('builder-row-empty');
                            var element_type = elements[index_element].type;
                            var element=$.yeepdf_load_type(element_type,elements[index_element]);
                            element.appendTo(column);
                      } 
                      column.appendTo(row.find(".builder-row-container-row"));
                    }
                    row.find(".builder-row").yeepdf_element_sortable();             
                    row.appendTo(".builder__list--js");                
                }
            },
            builder_to_json: function(){ 
                var font_family = $(".email-builder-main").css("font-family").replaceAll('"',"");
                if(font_family == ""){
                    font_family = 'dejavu sans';
                }
                var datas = {}; 
                var container = $(".email-builder-main");
                datas['container'] = {
                    "font-size": $(".email-builder-main").css("font-size"),
                    "color": $.email_builder_cover_color($(".email-builder-main").css("color")),
                    "font-family": font_family,
                    'background-color': $.email_builder_cover_color($(".email-builder-main").css("background-color")),
                    'padding-top': $(".email-builder-main").css("padding-top"),
                    'padding-bottom': $(".email-builder-main").css("padding-bottom"),
                    'padding-left': $(".email-builder-main").css("padding-left"),
                    'padding-right': $(".email-builder-main").css("padding-right"),
                    'background-image': $(".email-builder-main").css("background-image"),
                    "background-position-x": "left",
                    "background-position-y": "top",
                    "background-repeat": "no-repeat",
                    "background-size": "auto",
                };
                container.css(datas["container"]);
                datas["rows"] = {};
                $(".builder-row-container-row").each(function(index,row){
                    var type = $(row).data("type");
                    var style_row = {};
                    //var list_css = $.yeepdf_style(type);
                    var list_css = wp_builder_pdf["block"][type]["editor"]["container"]["style"];
                    $.each( list_css, function( key, value ) {
                        var css = $(row).css(value);
                        if( value.indexOf("color") >= 0 ){
                          style_row[value] = $.email_builder_cover_color(css);    
                        }else{
                          style_row[value] = css; 
                        }
                    });
                    var attr_row = {};
                    attr_row["background_full"] = $(row).attr("background_full");
                    if( attr_row["background_full"] !="not" ) {
                      attr_row["background_full"] = "ok";
                    }
                    var condition = $(row).attr("data-condition");
                    if( condition === undefined){
                        condition = ""; 
                    }
                    datas["rows"][index] = {style:style_row,
                                            attr: attr_row,
                                            type:   type,
                                            columns: {},
                                            condition: condition
                                          };
                    $(row).find(".builder-row").each(function(index1,row1){ 
                        datas["rows"][index]["columns"][index1]={
                            elements: {}
                        };
                        $(row1).find(".builder-elements-content").each(function(index2,row2){
                          var type = $(row2).data("type");
                          var element = $(row2).yeepdf_save_type();
                          datas["rows"][index]["columns"][index1]["elements"][index2]= element;
                        })
                    })                       
                })
                return JSON.stringify(datas);
            }
        }
        yeepdf_builder_main.json_to_builder();
        $( ".builder-row-container-row" ).each(function( index ) {
          if( $(this).attr("background_full") != "not" ){
            $(this).closest(".builder-row-container").css("background-color",$(this).css("background-color"));
          }
        });
        $('body').on("click",".builder-row-container",function(e){
            e.preventDefault();
            $(this).find(".builder-row-container-row").click();
        })
        $('body').on("click",".button-yeepdf-save",function(e){
            e.preventDefault();
            $(this).html("....");
            var email_json = yeepdf_builder_main.builder_to_json();
            $(".data_email").val(email_json);
            $("#publish").click();
        })
        $('body').on("click",".momongaDelete",function(e){
            e.preventDefault();
            e.stopPropagation();
            $(".builder__editor .builder__editor--item").addClass('hidden');
            if(  $(this).closest(".builder-elements").length < 1 ){
              $(this).closest(".builder-row-container").remove();
            }else{
              $(this).closest('.builder-elements').remove();
            }
        })
        $('body').on("click",".momongaDuplicate",function(e){
          e.preventDefault();
          e.stopPropagation();
          if(  $(this).closest(".builder-elements").length > 0 ){
            var main_item = $(this).closest('.builder-elements');
            var newItem = main_item.clone(true);
            newItem.find(".builder__toolbar").remove();
            newItem.find(".builder-elements-content").removeClass("wp_builder_pdf_focus");
            main_item.after(newItem);
          }else{
            var main_item = $(this).closest('.builder-row-container');
            var newItem = main_item.clone(true);
            newItem.find(".builder__toolbar").remove();
            newItem.removeClass('wp_builder_pdf_show').find(".builder-row-container-row").removeClass("wp_builder_pdf_focus");
            newItem.find(".builder-elements-content").removeClass("wp_builder_pdf_focus");
            main_item.after(newItem);
          }   
        })
        $("body").on('mouseenter', '.builder-elements', function() {
            if( $(this).closest(".wp_builder_pdf_show").length < 1  ){
              $(this).closest('.builder-row-container').addClass('wp_builder_pdf_hover');
              $(this).addClass('wp_builder_pdf_hover');     
            }else{
                $(this).addClass('wp_builder_pdf_hover');  
            }
        });
        $("body").on('mouseleave', '.builder-elements', function() {
            $(this).closest('.builder-row-container').removeClass('wp_builder_pdf_hover');
            $(this).removeClass('wp_builder_pdf_hover');
        });
        $("body").on('mouseenter', '.builder-row-container-row', function() {
            if( $(this).closest(".wp_builder_pdf_show").length < 1  ){
              $(this).closest('.builder-row-container').addClass('wp_builder_pdf_hover');
            }
        });
        $("body").on('mouseleave', '.builder-row-container-row', function() {
            $(this).closest('.builder-row-container').removeClass('wp_builder_pdf_hover');
        });
        $('body').on("click",".builder__tab a",function(e){
            e.preventDefault();
            $(".builder__tab a").removeClass("active");
            $(this).addClass("active");
            var tab = $(this).attr('id');
            $('.tab__content').hide();
            $(tab).show();
        })
        $('body').on("click",".yeepdf-email-reset a",function(e){
            e.preventDefault();
            if (confirm("Do you want to reset the template?") == true) {
              var data = {
                    'action': 'pdf_reset_template',
                    'id': $("#post_ID").val()
                };
                jQuery.post(ajaxurl, data, function(response) {
                    location.reload(true);
                });
          }
        })
        $('body').on('click', '.yeepdf-email-import', function(e){
            e.preventDefault();
                var button = $(this),
                    custom_uploader = wp.media({
                title: 'Import template',
                library : {
                    type : [ 'json',"text"]
                },
                button: {
                    text: 'Import template' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function() { // it also has "open" and "close" events 
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $.getJSON(attachment.url, function(data){
                    $(".data_email").val(data);
                    yeepdf_builder_main.json_to_builder();
                    $( ".builder-row-container-row" ).each(function( index ) {
                      if( $(this).attr("background_full") != "not" ){
                        $(this).closest(".builder-row-container").css("background-color",$(this).css("background-color"));
                      }
                    });
                }).fail(function(){
                  alert("Error");
                });
            })
            .open();
        });
        $("body").on("click",".yeepdf-email-export",function(){
            $("<a />", {
                "download": "yeepdf.json",
                "href" : "data:text/plain;charset=utf-8," + encodeURIComponent(JSON.stringify($(".data_email").val()))
              }).appendTo("body")
              .click(function() {
                $(this).remove()
              })[0].click();
        })
        $("body").on("click",".yeepdf-email-export-html",function(){
            var id = $("#post_ID").val();
            var data = {
              'action': 'yeepdf_builder_export_html',
              'id': id
            };
            jQuery.post(ajaxurl, data, function(response) {
              var data = new Blob([response], {type: 'text/html'});
              var textFile = window.URL.createObjectURL(data);
              $("<a />", {
                "download": "yeepdf.html",
                "href" : textFile,
              }).appendTo("body")
              .click(function() {
                $(this).remove()
              })[0].click();
            });
        })
        $("body").on("click",".yeepdf-email-choose-template",function(e){
            e.preventDefault();
            //tb_show("Templates", "#TB_inline?width=930&height=550&inlineId=yeepdf-email-templates");
            $( "#yeepdf-email-templates" ).dialog({
                modal: true,
                width: 900,
                title: "Templates",
                buttons: {
                  Close: function() {
                    $( this ).dialog( "close" );
                  }
                }
              });
            return false;
        })
        $("body").on("click",".pdf-remove-font",function(e){
            e.preventDefault();
            if (confirm("Do you want remove font?") == true) {
              var font_name = $(this).closest(".container-list-fonts").find(".pdf-font-name").html();
              $(this).closest(".container-list-fonts").remove();
              var type = $(this).data("type");
              var formData ={
                        'action': 'pdfceator_remove_font',
                        'font_name': font_name,
                        'type': type
                    };
                jQuery.post(ajaxurl, formData, function(response) {
                });
            }
            return false;
        })
        $("body").on("click",".yeepdf-email-actions-import",function(e){
            e.preventDefault();
            var attachment = $(this).closest(".grid-item").data("file");
                $.getJSON(attachment, function(data){
                    $(".data_email").val(data);
                    $(".builder__list").html("");
                    yeepdf_builder_main.json_to_builder();
                    $( ".builder-row-container-row" ).each(function( index ) {
                      if( $(this).attr("background_full") != "not" ){
                        $(this).closest(".builder-row-container").css("background-color",$(this).css("background-color"));
                      }
                    });
                    tb_remove();
                }).fail(function(){
                  alert("Error");
                });
        })
      $("body").on("click",".woocommerce-yeepdf-expand",function(e){
          e.preventDefault();
          var type = $(this).data("type");
          if( type == "left"){
              $(".builder__widget").effect( "size", {
                                  to: { width: 900, }
                                }, 500 );
              $(this).closest('div').find(".woocommerce-yeepdf-shrink").removeClass("hidden");
              $(this).addClass('hidden');
          }else{
              $("#poststuff #post-body.columns-2").css("margin-right","300px");
              $(".yeepdf-email-slide").removeClass('hidden');
              $(".wpbuideremail-expand-right").addClass('hidden');
          }
      })
      $('body').on("click",".yeepdf-builder-goback",function(e){
          e.preventDefault();
          $(".builder__tab li").first().find("a").click();
      })
      $('body').on("click",".yeepdf-builder-choose-blank",function(e){ 
          e.preventDefault();
          if (confirm("Changes you made will be lost.") == true) {
              $.getJSON(yeepdf_script.yeepdf_url_plugin + "backend/demo/new.json", function(data){
                  $(".data_email").val(data);
                  $(".builder__list").html("");
                  yeepdf_builder_main.json_to_builder();
              }).fail(function(){
                alert("Error");
              });
          }
      })
      $("body").on("click",".yeepdf-builder-choose-shortcodes",function(e){
          e.preventDefault();
          $( "#yeepdf-builder-shortcodes-templates" ).dialog({
            modal: true,
            width: 800,
            title: "All Shortcodes",
            buttons: {
              Close: function() {
                $( this ).dialog( "close" );
              }
            }
          });
          return false;
        })
      $("body").on("click",".woocommerce-yeepdf-shrink",function(e){
          e.preventDefault();
          var type = $(this).data("type");
          if( type == "left"){
              $(".builder__widget").effect( "size", {
                                  to: { width: 420, }
                                }, 500 );
              $(this).closest('div').find(".woocommerce-yeepdf-expand").removeClass("hidden");
              $(this).addClass('hidden');
          }else{
              $("#poststuff #post-body.columns-2").css("margin-right","0");
              $(".yeepdf-email-slide").addClass('hidden');
              $(".wpbuideremail-expand-right").removeClass('hidden');
          }
      })
      $("body").on("click",".yeepdf_button_settings",function(e){
        e.preventDefault();
          $(".email-builder-main-change_backgroud").click();
      })
      $("body").on("click",".pdf-merge-tags",function(e){
            e.preventDefault();
            var list_fields = yeepdf_script.shortcodes;
            var html = '<select class="pdf-marketing-list-merge-tags">';
            html += "<option>----</option>";
            $.each(list_fields, function( index_1, values_1 ) {
                html += '<optgroup label="'+index_1+'">';
                $.each(values_1, function( index_2, value_2 ) {
                  if (typeof value_2 === 'object' && value_2 !== null){
                    $.each(value_2, function( index_3, value_3 ) {
                      if( index_3.search("{") <0) {
                        html += "<option value='["+index_3+"]'>["+index_3+"]</option>";
                      }else{
                          html += "<option value='"+index_3+"'>"+index_3+"</option>";
                      }
                    })
                  }else{
                    if( index_2.search("{") <0) {
                      html += "<option value='["+index_2+"]'>["+index_2+"]</option>";
                    }else{
                      html += "<option value='"+index_2+"'>"+index_2+"</option>";
                    }
                  }
                })
                html += '</optgroup>';
            })
            html +='</select>';
            $(this).closest(".pdf-marketing-merge-tags-container").append(html);
        })
      $(document).mouseup(function(e) {
            var container = $(".pdf-marketing-list-merge-tags");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                container.remove();
            }
        });
        $("body").on("change",".pdf-marketing-list-merge-tags",function(e){ 
            e.preventDefault();
            var value = $(this).val();
            var old_value = $(this).closest(".pdf-marketing-merge-tags-container").find("input").val();
            $(this).closest(".pdf-marketing-merge-tags-container").find(".code-selector").val(old_value + value);
            $(this).closest(".pdf-marketing-merge-tags-container").find(".pdf-marketing-list-merge-tags").remove();
        })
      $("body").on("change",".yeepdf_datas_enable",function(e){
          var tab_id = $(this).data("tab");
          if($(this).is(":checked")){
              $(tab_id).removeClass("hidden");
          }else{
              $(tab_id).addClass("hidden");
          }
      }) 
      //custom JS for add-ons
      $("body").on("change1",".builder__editor--item-detail-template input, .builder__editor--item-detail-template select",function(e){
          var template = $(".builder__editor--item-detail-template .detail-template").val();
          var showimg = $(".builder__editor--item-detail-template .detail-img");
          if( showimg.is(":checked")){
            showimg ="yes";
          }else{
            showimg ="no";
          }
          var totals = $(".builder__editor--item-detail-template .detail-totals");
          if( totals.is(":checked")){
            totals ="yes";
          }else{
            totals ="no";
          }
          var sku = $(".builder__editor--item-detail-template .detail-sku");
          if( sku.is(":checked")){
            sku ="yes";
          }else{
            sku ="no";
          }
          var des = $(".builder__editor--item-detail-template .detail-des");
          if( des.is(":checked")){
            des ="yes";
          }else{
            des ="no";
          }
          var shortcode ="[yeepdf_woo_order_detail type='"+template+"' show_img='"+showimg+"' item_totals='"+totals+"' item_sku='"+sku+"' show_des='"+des+"']";
          var data_ajax = {
            'action': 'yeepdf_builder_text',
            'type': $(".builder-elements-content.wp_builder_pdf_focus").data("type"),
            'order_id': $(".builder_pdf_woo_testing").val(),
            'text': shortcode
          };
          $.post(ajaxurl, data_ajax, function(response) {
              $(".builder-elements-content.wp_builder_pdf_focus .text-content").html(response); 
          });
      })  
    })
})(jQuery);