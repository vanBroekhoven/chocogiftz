;
var br_saved_timeout;
var br_init_colorpick;
var br_savin_ajax = false;
var br_something_changed = false;
(function ($){
    $(document).ready( function () {
        $(window).on('beforeunload', function() {
            if( br_something_changed ) {
                return 'Something changed and not saved';
            }
        });
        setTimeout(function() {
            $('.br_framework_submit_form *').on('change', function() {
                br_something_changed = true;
            });
        }, 250);
        $('.br_framework_submit_form').submit( function(event) {
            event.preventDefault();
            if( !br_savin_ajax ) {
                br_savin_ajax = true;
                var form_data = $(this).serialize();
                var plugin_name = $(this).data('plugin');
                var url = $(this).attr('action');
                clearTimeout(br_saved_timeout);
                destroy_br_saved();
                $('body').append('<span class="br_saved br_saving"><i class="fa fa-refresh fa-spin"></i></span>');
                $.post(url, form_data, function (data) {
                    if($('.br_saved').length > 0) {
                        $('.br_saved').removeClass('br_saving').find('.fa').removeClass('fa-spin').removeClass('fa-refresh').addClass('fa-check');
                    } else {
                        $('body').append('<span class="br_saved"><i class="fa fa-check"></i></span>');
                    }
                    br_saved_timeout = setTimeout( function(){destroy_br_saved();}, 2000 );
                    br_savin_ajax = false;
                    br_something_changed = false;
                }).fail(function(data) {
                    if($('.br_saved').length > 0) {
                        $('.br_saved').removeClass('br_saving').addClass('br_not_saved').find('.fa').removeClass('fa-spin').removeClass('fa-refresh').addClass('fa-times');
                    } else {
                        $('body').append('<span class="br_saved br_not_saved"><i class="fa fa-times"></i></span>');
                    }
                    br_saved_timeout = setTimeout( function(){destroy_br_saved();}, 2000 );
                    br_savin_ajax = false;
                });
            }
        });

        function destroy_br_saved() {
            $('.br_saved').addClass('br_saved_remove');
            var $get = $('.br_saved');
            setTimeout( function(){$get.remove();}, 200 );
        }

        $('.br_framework_settings ul.side a').click(function(event) {
            var block = $(this).data('block');
            if( block != 'redirect_link' ) {
                event.preventDefault();
                $('.br_framework_settings ul.side a.active').removeClass('active');
                $('.nav-block-active').removeClass('nav-block-active');
                $(this).addClass('active');
                $('.'+$(this).data('block')+'-block').addClass('nav-block-active');
                $('.br_framework_settings .content .title').html( $(this).html() );
            }
        });

        $(window).on('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) {
                switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault();
                    $('.br_framework_submit_form').submit();
                    break;
                }
            }
        });
        br_init_colorpick = function() {
            $('.br_framework_settings .br_colorpicker').each(function (i,o){
                $(o).css('backgroundColor', $(o).data('color'));
                $(o).colpick({
                    layout: 'hex',
                    submit: 0,
                    color: $(o).data('color'),
                    onChange: function(hsb,hex,rgb,el,bySetColor) {
                        if( hex.charAt(0) != '#' ) {
                            hex = '#'+hex;
                        }
                        $(el).css('backgroundColor', hex).parents('.berocket_color').first().find('.br_colorpicker_value').val(hex).trigger('change');
                    }
                })
            });
        }
        br_init_colorpick();
        $(document).on('click', '.br_framework_settings .br_colorpicker_default', function (event) {
            event.preventDefault();
            var $color = $(this).parents('.berocket_color').first();
            var data = $color.find('.br_colorpicker').data('default');
            $color.find('.br_colorpicker').css('backgroundColor', data).colpickSetColor(data);
            $color.find('.br_colorpicker_value').val(data).trigger('change');
        });
        
        $(document).on('click', '.br_framework_settings .berocket_upload_image', function(e) {
            e.preventDefault();
            $p = $(this);
            var custom_uploader = wp.media({
                title: 'Select custom Icon',
                button: {
                    text: 'Set Icon'
                },
                multiple: false 
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $p.prevAll(".berocket_selected_image").html('<image src="'+attachment.url+'" alt="">');
                $p.prevAll(".berocket_image_value").val(attachment.url).trigger('change');
            }).open();
        });
        $(document).on('click', '.br_framework_settings .berocket_remove_image',function(event) {
            event.preventDefault();
            $(this).prevAll(".berocket_selected_image").html("");
            $(this).prevAll(".berocket_image_value").val("").trigger('change');
        });
        var berocket_fa_select_for = $('.berocket_fa_dark');
        $(document).on('click', '.berocket_select_fontawesome .berocket_select_fa',function(event) {
            event.preventDefault();
            berocket_fa_select_for = $(this);
            $('.berocket_fa_dark').not(':first').remove();
            var $html = $('<div class="berocket_select_fontawesome"></div>');
            $html.append($('.berocket_fa_dark'));
            var $html2 = $('<div class="br_framework_settings br_fontawesome_body"></div>');
            $html2.append($html);
            $('body').children('.br_fontawesome_body').remove();
            $('body').append($html2);
            $('.berocket_fa_dark').show();
        });
        $(document).on('hover', '.berocket_select_fontawesome .berocket_fa_hover', function() {
            var window_width = $(window).width();
            window_width = window_width / 2;
            var $this = $(this).parents('.berocket_fa_icon');
            if( $this.offset().left < window_width ) {
                $this.find('.berocket_fa_preview').css({left: '0', right: 'initial'});
                $this.find('.berocket_fa_preview span').appendTo($this.find('.berocket_fa_preview'));
            } else {
                $this.find('.berocket_fa_preview').css({left: 'initial', right: '0'});
                $this.find('.berocket_fa_preview .fa').appendTo($this.find('.berocket_fa_preview'));
            }
        });
        $(document).on('click', '.berocket_select_fontawesome .berocket_fa_hover',function(event) {
            event.preventDefault();
            var value = $(this).parents('.berocket_fa_icon').first().find('.berocket_fa_preview span').text();
            $(berocket_fa_select_for).parents('.berocket_select_fontawesome').find('.berocket_selected_fa').html('<i class="fa '+value+'"></i>');
            $(berocket_fa_select_for).parents('.berocket_select_fontawesome').find('.berocket_fa_value').val(value).trigger('change');
            $('.berocket_fa_dark').hide();
        });
        $(document).on('click', '.berocket_select_fontawesome .berocket_remove_fa',function(event) {
            event.preventDefault();
            $(this).parents('.berocket_select_fontawesome').find('.berocket_selected_fa').html('');
            $(this).parents('.berocket_select_fontawesome').find('.berocket_fa_value').val('').trigger('change');
        });
        $(document).on('keyup', '.berocket_select_fontawesome .berocket_fa_search', function() {
            var $parent = $(this).parents('.berocket_select_fontawesome').first();
            var value = $(this).val();
            value = value.replace(/\s+/g, '');
            value = value.toLowerCase();
            if( value.length >=1 ) {
                $parent.find('.berocket_fa_icon').hide();
                $parent.find('.berocket_fa_preview span:contains("'+value+'")').parents('.berocket_fa_icon').show();
            } else {
                $parent.find('.berocket_fa_icon').show();
            }
        });
        $(document).on('click', '.berocket_select_fontawesome .berocket_fa_dark',function(event) {
            event.preventDefault();
            $(this).hide();
        });
        $(document).on('click', '.berocket_select_fontawesome .berocket_fa_dark .berocket_fa_close',function(event) {
            event.preventDefault();
            $(this).parents('.berocket_fa_dark').hide();
        });
        $(document).on('click', '.berocket_select_fontawesome .berocket_fa_popup',function(event) {
            event.preventDefault();
            event.stopPropagation();
        });
        if( location.hash ) {
            $('.br_framework_settings ul.side a[href="'+location.hash+'"]').trigger('click');
        }
        if( typeof wp.codeEditor != 'undefined' && typeof wp.codeEditor.initialize != 'undefined' ) {
            var css_editor = $('.br_framework_settings .css_editor');
            css_editor.each(function() {
                wp.codeEditor.initialize(this, {"codeEditor":{"codemirror":{"indentUnit":4,"indentWithTabs":true,"inputStyle":"contenteditable","lineNumbers":true,"lineWrapping":true,"styleActiveLine":true,"continueComments":true,"extraKeys":{"Ctrl-Space":"autocomplete","Ctrl-\/":"toggleComment","Cmd-\/":"toggleComment","Alt-F":"findPersistent"},"direction":"ltr","gutters":[],"mode":"css"},"csslint":{"errors":true,"box-model":true,"display-property-grouping":true,"duplicate-properties":true,"known-properties":true,"outline-none":true},"jshint":{"boss":true,"curly":true,"eqeqeq":true,"eqnull":true,"es3":true,"expr":true,"immed":true,"noarg":true,"nonbsp":true,"onevar":true,"quotmark":"single","trailing":true,"undef":true,"unused":true,"browser":true,"globals":{"_":false,"Backbone":false,"jQuery":false,"JSON":false,"wp":false}},"htmlhint":{"tagname-lowercase":true,"attr-lowercase":true,"attr-value-double-quotes":true,"doctype-first":false,"tag-pair":true,"spec-char-escape":true,"id-unique":true,"src-not-empty":true,"attr-no-duplication":true,"alt-require":true,"space-tab-mixed-disabled":"tab","attr-unsafe-chars":true}}});
            })
            var js_editor = $('.br_framework_settings .js_editor');
            js_editor.each(function() {
                wp.codeEditor.initialize(this, {"codeEditor":{"codemirror":{"indentUnit":4,"indentWithTabs":true,"inputStyle":"contenteditable","lineNumbers":true,"lineWrapping":true,"styleActiveLine":true,"continueComments":true,"extraKeys":{"Ctrl-Space":"autocomplete","Ctrl-\/":"toggleComment","Cmd-\/":"toggleComment","Alt-F":"findPersistent"},"direction":"ltr","gutters":[],"mode":"javascript"},"csslint":{"errors":true,"box-model":true,"display-property-grouping":true,"duplicate-properties":true,"known-properties":true,"outline-none":true},"jshint":{"boss":true,"curly":true,"eqeqeq":true,"eqnull":true,"es3":true,"expr":true,"immed":true,"noarg":true,"nonbsp":true,"onevar":true,"quotmark":"single","trailing":true,"undef":true,"unused":true,"browser":true,"globals":{"_":false,"Backbone":false,"jQuery":false,"JSON":false,"wp":false}},"htmlhint":{"tagname-lowercase":true,"attr-lowercase":true,"attr-value-double-quotes":true,"doctype-first":false,"tag-pair":true,"spec-char-escape":true,"id-unique":true,"src-not-empty":true,"attr-no-duplication":true,"alt-require":true,"space-tab-mixed-disabled":"tab","attr-unsafe-chars":true}}});
            });
        }
    });
})(jQuery);
/* PRODUCTS SELECTOR */
(function ($){
    $(document).ready( function () {
        var last_search = '';
        var delay_search = false;
        var ajax_request = false;
        var $current_search = $('');
        $(document).on('click', '.berocket_products_search', function() {
            $(this).find('.berocket_search_input').focus();
        });
        $(document).on('dblclick', '.berocket_products_search', function() {
            $(this).find('.berocket_search_input').select();
        });
        $(document).on('click', '.berocket_products_search .button', function(event) {
            event.stopPropagation();
            var $search_block = $(this);
            var $search_box = $search_block.parents('.berocket_search_box').first();
            $(this).remove();
            if( $search_box.is('.single_product') && $search_box.find('.berocket_product_selected').length == 0 ) {
                $search_box.find('.berocket_product_search').show();
            }
        });
        $(document).on('click', '.berocket_search_box', function(event) {
            event.stopPropagation();
            var $current = $(this).find('.berocket_search_result');
            if( $current.length == 0 ) {
                remove_search_result();
            } 
        });
        $(document).on('keyup focus', '.berocket_search_input', function(event) {
            if( delay_search ) {
                clearTimeout(delay_search);
            }
            var $search_block = $(this);
            var $search_box = $search_block.parents('.berocket_search_box').first();
            if( $search_box.is('.single_product') && $search_box.find('.berocket_product_selected').length > 0 ) {
                return false;
            }
            $current_search = $(this).parents('.berocket_search_box').first();
            delay_search = setTimeout( function () {
                if( $search_block.val().length >= 3 && $search_block.val() != last_search ) {
                    $('.berocket_search_result').remove();
                    last_search = $search_block.val();
                    var exists = [];
                    $search_box.find('.berocket_product_selected input').each(function( i, o ) {
                        exists.push($(o).val());
                    });
                    var data = {
                        action: $search_block.data('action'),
                        term: $search_block.val(),
                        security: berocket_framework_admin.security
                    };
                    stop_search();
                    $search_box.find('.berocket_product_search').append($('<span class="berocket_loads"><i class="fa fa-spinner fa-spin"></i></span>'));
                    ajax_request = $.get(ajaxurl, data, function (data) {
                        $current_search = $search_box;
                            var count = 0;
                            var html = '<ul class="berocket_search_result">';
                            $.each(data, function(index, value) {
                                if( $.inArray(index, exists) == -1 ) {
                                    html += '<li data-id="'+index+'">'+value+'</li>';
                                    count++;
                                }
                            });
                            html += '</ul>';
                            if( count > 0 ) {
                                $result_block = $(html);
                                $result_block = $('body').append($result_block);
                                $('.berocket_search_result').css('position', 'absolute')
                                .css('top', $search_box.offset().top+$search_box.height())
                                .css('left', $search_box.offset().left)
                                .outerWidth($search_box.outerWidth());
                            }
                            $('.berocket_product_search .berocket_loads').remove();
                        }, 'json');
                } else {
                    stop_search();
                }
            }, 500 );
        });
        $(document).on('click', '.berocket_search_result li', function(event) {
            var html = '<li class="berocket_product_selected button"><input data-name="'+$current_search.data('name')+'" name="'+$current_search.data('name')+'" type="hidden" value="'+$(this).data('id')+'"><i class="fa fa-times"></i> '+$(this).text()+'</li>';
            $current_search.find('.berocket_product_search').last().before($(html));
            $current_search.find('.berocket_search_input').val('').trigger('change');
            if( $current_search.is('.single_product') ) {
                $current_search.find('.berocket_product_search').hide();
            }
            remove_search_result();
        });
        $(document).on('click', function(event) {
            remove_search_result();
            stop_search();
        });
        function remove_search_result() {
            $('.berocket_search_result').remove();
            last_search = '';
            $current_search = $('');
        }
        function stop_search() {
            if ( ajax_request !== false ) {
                ajax_request.abort();
            }
            $('.berocket_product_search .berocket_loads').remove();
        }
    });
})(jQuery);
