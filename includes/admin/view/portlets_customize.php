<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Page for Customize
$portlet_color = $style = $title = $body = '';
$btn_main_txt = $btn_sec_txt = '';

if( !empty($portlet) ){
        $portlet_color = $portlet->color;
        $style = 'style="border: 2px solid red;"';
        
        $content = htmlspecialchars_decode(wp_unslash($portlet->content));
        
        require_once (super_metronic()->plugin_path().'/includes/phpQuery/phpQuery.php');
        $document = phpQuery::newDocumentHTML($content);

        $hentries = $document->find('div.portlet');
        
        foreach ($hentries as $hentry) {
                $pq = pq($hentry);
                
                $tag_title = $pq->find('span.view_portlet_title');
                $portlet->title = $tag_title->text();
                
                $tag_body = $pq->find('div.view_portlet_body');
                $portlet->body = $tag_body->html();
                
                $tag_buttons = $pq->find('div.caption_button');                
                $portlet->btns = $tag_buttons->html();

                $button = $tag_buttons->find('button.portlet_button1');
                $portlet->btn_main_txt = $button->text();

                $button = $tag_buttons->find('button.portlet_button2');
                $portlet->btn_sec_txt = $button->text();         
        }
}
?>

<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- END PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <div class="note note-info">
                        <h4 class="block"><?php _e('UI Collor Collection', Super_Metronic::TEXT_DOMAIN); ?></h4>
                    </div>
                        
                    <?php for($i = 0; $i < count($arr_colors); $i++){

                        if( $i%6 == 0 ){ ?>
                            <div class="row">
                        <?php } ?>

                                <div class="col-md-2 col-sm-2 col-xs-6">                            

                                    <!--<input type="checkbox" class="make-switch" checked data-on="success" data-on-color="success" data-off-color="warning" data-size="small">-->

                                    <div class="color-demo tooltips" data-original-title="<?php _e('Click to view demos for this color', Super_Metronic::TEXT_DOMAIN); ?>" data-toggle="modal" data-target="#demo_modal_<?php echo $arr_colors[$i][0]; ?>" <?php if($arr_colors[$i][0] == $portlet_color) echo $style; ?>>
                                        <div class="color-view bg-<?php echo $arr_colors[$i][0]; ?> bg-font-<?php echo $arr_colors[$i][0]; ?> bold uppercase"> #<?php echo $arr_colors[$i][1]; ?> </div>
                                        <div class="color-info bg-white c-font-14 sbold"> <?php echo $arr_colors[$i][0]; ?> </div>
                                    </div>

                                    <?php Super_Metronic_Admin::portlet_modal( $arr_colors[$i][0], $portlet, $images ); ?>

                                </div>

                        <?php if( $i%6 == 5 || $i == 56 ){ ?>
                            </div>
                        <?php } 

                    } ?>                
                   
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->