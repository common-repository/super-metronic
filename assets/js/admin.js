//*** Metronic Backend ***
(function ($) {
    $(function () {   
        // for Portlet's header
        $('input.portlet_title').live('keyup', function(){
            var $ths = $(this);
            $ths.closest('.tab-content').find('.view_portlet_title').text($ths.val());
        }).keyup();
        
        // for Portlet's content: textarea.portlet_body -> .trumbowyg-editor
        $('textarea.portlet_body').trumbowyg();
        
        $('.trumbowyg-editor').live('keyup', function(){
            var $ths = $(this);
            var $thsVal = $ths.html();    
            $ths.closest('.tab-content').find('.view_portlet_body').html($thsVal);
        }).keyup();        
        
        // for Portlet's icon
        $('.portlet_icon').live('click',  function(){
            var $ths = $(this);
            var iconClass = $ths.find('i').data('icon_p');
            $ths.closest('.tab-content').find('.portlet-title').find('i').removeClass().addClass(iconClass);
        });        
        
        // for Portlet's - Main text for button
        $('.portlet_button_text1').live('keyup', function(){
            var $ths = $(this);
            $ths.closest('.tab-content').find('.portlet_button1').text($ths.val());
        }).keyup();
        
        // for Portlet's - Main text for button
        $('.portlet_button_text2').live('keyup', function(){
            var $ths = $(this);
            $ths.closest('.tab-content').find('.portlet_button2').text($ths.val());
        }).keyup();
        
        // Hide Main, Show Secodary text
        $('.caption_button > .portlet_button1').live('click', function(){
            $(this).hide().closest('.caption_button').find('.portlet_button2').show();
        });
        // Hide Secodary, Show Main text
        $('.caption_button > .portlet_button2').live('click', function(){
            $(this).hide().closest('.caption_button').find('.portlet_button1').show();
        });

        // Add Button to Portlet
        $('.portlet_button1').live('click', function(){
            var buttons = $(this).closest('.portlet_button_wp').html();
            $(this).closest('.modal-body').find('.caption_button').html(buttons);
        });
        
        // Saving Portlet's html
        $('.btn.btn-outline.saving').live('click', function(){
                var elParent = $(this).closest('.modal-content');
                 
                var data = {
                    action: 'portlet_saving',
                    portletHtml : elParent.find('.portlet_box').html(),
                    color : elParent.find('.modal-title.bold').text(),
                    number : elParent.find('input[name="portlet_number"]').val(),
                };
        
                $.ajax({
                        url: smfScriptParams.ajaxurl,
                        data: data,
                        dataType: 'json',
                        type: 'POST',
                        success: function(response) {
                                var color = '';
                                color = response.result ? 'green' : 'red';
                                elParent.find('.portlet-notice').empty().text(response.html).css('color', color);
                        }
                });
        });
        
        // Portlet activate/deactivate
        $('.portlet_action').live('click', function(){
          
                var ths = $(this);
                var data = {
                    action: 'portlet_action',
                    portletAction : ths.data('action_p'),
                    id : ths.data('id')
                };
                $.ajax({
                        url: smfScriptParams.ajaxurl,
                        data: data,
                        dataType: 'json',
                        type: 'POST',
                        success: function(response) {
                                if( response.result ){
                                    ths.data('action_p', response.action).removeClass().addClass('btn portlet_action '+response.color).text(response.text);
                                }                                
                        }
                });
        });
        
        //Upload image
        $('.upload_image_button').click(function(){
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		wp.media.editor.send.attachment = function(props, attachment) {
			$(button).parent().prev().attr('src', attachment.url);
			$(button).prev().val(attachment.id);
			wp.media.editor.send.attachment = send_attachment_bkp;
		}
		wp.media.editor.open(button);
		return false;    
	});
        //Remove image
        $('.remove_image_button').click(function(){
		var r = confirm("Are you sure?");
		if (r == true) {
			var src = $(this).parent().prev().attr('data-src');
			$(this).parent().prev().attr('src', src);
			$(this).prev().prev().val('');
		}
		return false;
	});
            
    });
})(jQuery);        