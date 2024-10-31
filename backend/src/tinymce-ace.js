/*
 * TinyMCE Ace code editor
 * MIT License Copyright (c) 2017 Akshay Raje
 * @link https://github.com/akshayraje/tinymce-ace-code-editor
 * @author https://github.com/akshayraje/ 
 */
 (function($) {
    "use strict";
    tinymce.PluginManager.add('code_toggle', function(editor, url) {
    editor.addButton('code_toggle', {
        icon: 'code',
        text: 'Code',
        tooltip: 'Code',
        onclick: function (e) {
            $(".builder__editor--item-html textarea").val(editor.getContent()); 
            toggleCode(this, editor);
        }
    });
    $('#'+editor.id).on('keyup', function(){
        tinyMCE.activeEditor.setContent($(this).val());
         $(".builder-elements-content.wp_builder_pdf_focus .text-content-data").html($(this).val()); 
         $(".builder-elements-content.wp_builder_pdf_focus .text-content").html($(this).val()); 
    });
    var toggleCode = function(elem, editor){
        elem.active( !elem.active() );
        var state = elem.active();
        var self = elem.$el;
        var tinymce = $(self).closest('.mce-tinymce');
        var aceID = 'ace-editor-'+editor.id;
        if (state){
            // Code mode
            tinymce.find('.mce-widget.mce-btn').each(function(){
                if($(this).attr('id') !== $(elem.$el).attr('id')){
                    $(this).css({
                        'pointer-events': 'none',
                        'opacity': '0.4'
                    });
                }
            });
            tinymce.find('.mce-edit-area').hide();
            $('#'+editor.id)
                .css('height', $(editor.getDoc()).height()+'px')
                .show();
            if(typeof ace !== 'undefined'){
                var textarea = $('#'+editor.id).hide();
                $('#'+editor.id).after('<div id="'+aceID+'"></div>');
                var aceEditor = ace.edit(aceID);
                aceEditor.$blockScrolling = 'Infinity';
                aceEditor.setTheme("ace/theme/chrome");
                aceEditor.session.setMode("ace/mode/html_ruby");
                aceEditor.setValue(textarea.val(), 1);
                aceEditor.setOptions({
                    wrap: true,
                    displayIndentGuides: true,
                    highlightActiveLine: false,
                    showPrintMargin: false,
                    minLines: Math.round(300 / 17),
                    maxLines: Math.round(300 / 17)
                });
                aceEditor.on('change', function () {
                    textarea.val(aceEditor.getValue()).trigger('keyup');
                    editor.setContent(aceEditor.getValue());
                });
            }
        } else {
            // Editor mode
            tinymce.find('.mce-widget.mce-btn').each(function(){
                if($(this).attr('id') !== $(elem.$el).attr('id')) {
                    $(this).css({
                        'pointer-events': 'auto',
                        'opacity': '1'
                    });
                }
            });
            tinymce.find('.mce-edit-area').show();
            $('#'+editor.id)
                .hide();
            if(typeof ace !== 'undefined'){
                var aceEditor = ace.edit(aceID);
                aceEditor.destroy();
                $('#'+aceID).remove();
            }
        }
    };
});
})(jQuery);