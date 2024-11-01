//*** Metronic Frontend ***
(function ($) {
    
    $(function () { 
        // Hide Main, Show Secodary text
        $('.caption_button > .portlet_button1').live('click', function(){
            $(this).hide().closest('.caption_button').find('.portlet_button2').show();
        });
        // Hide Secodary, Show Main text
        $('.caption_button > .portlet_button2').live('click', function(){
            $(this).hide().closest('.caption_button').find('.portlet_button1').show();
        });
    });
    
})(jQuery); 