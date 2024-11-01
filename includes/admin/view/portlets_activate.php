<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="note note-info">
                        <h4 class="block"><?php _e('Portlets', Super_Metronic::TEXT_DOMAIN); ?></h4>
                        <?php echo $notice; ?>
                    </div>
                    <?php
                    $url = admin_url( 'admin.php?page=portlets-customize' );
                    if (!empty($arr_portlets)) {
                        $view = '';
                        
                        foreach ($arr_portlets as $key => $portlet) {
                                if( $key%4 == 0 ) $view .= '<div class="row">';
                                $view .= '<div class="col-md-3" id="' . esc_attr($portlet->id) . '">' . htmlspecialchars_decode(wp_unslash($portlet->content));
                                if($portlet->status == 'activate'){
                                    $color = 'grey-cascade';
                                    $text = __('Deactivate', Super_Metronic::TEXT_DOMAIN);
                                }else{
                                    $color = 'green-meadow';
                                    $text = __('Activate', Super_Metronic::TEXT_DOMAIN);
                                }
                                $text_delete = __('Delete', Super_Metronic::TEXT_DOMAIN);
                                $link_portlet = add_query_arg( array('portlet' => $portlet->id), $url );
                                $view .= '<div style="margin-bottom: 10px;"><a href="'.esc_url($link_portlet).'" class="btn blue-madison">Edit</a>';
                                $view .= '<button type="button" class="btn portlet_action '. $color .'" data-id="'. esc_attr($portlet->id) .'" data-action_p="'. esc_attr($portlet->status) .'">'. $text .'</button>';
                                $view .= '<form action="" name="portlet_delete" method="post"><input type="hidden" name="pid" value="'. $portlet->id .'" />';
                                $view .= wp_nonce_field('form_portlet_data', 'action_delete', true, false);
                                $view .= '<button type="submit" class="btn red-sunglo">'. $text_delete .'</button></form></div>';
                                $view .= '</div>';

                                if( $key%4 == 3 ) $view .= '</div>';
                        }
                        
                        echo $view;
                    }else{
                        printf( __('You can create portlets right now and %shere%s.', Super_Metronic::TEXT_DOMAIN), '<a href="'.$url.'">', '</a>');
                    }
                    ?>   
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->