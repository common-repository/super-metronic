<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Super Metronic
 * 
 * Frontend class
 *
 * @since 1.1
 */
class Super_Metronic_Frontend {
        /**
	 * Setup admin class
	 */
	public function __construct() {
		// load styles/scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
                add_shortcode('smf_portlets', array($this, 'show_portlets') );
	}

	/**
	 * Load admin styles and scripts
	 */
	public function load_styles_scripts() {
            $path_portlet = super_metronic()->plugin_url().'/assets/';
            //styles
            wp_enqueue_style('sm-common', $path_portlet.'css/common.css');
            wp_enqueue_style('sm-font-awesome-css', $path_portlet.'global/plugins/font-awesome/css/font-awesome.min.css' );
            wp_enqueue_style('sm-line-icons-css', $path_portlet.'global/plugins/simple-line-icons/simple-line-icons.min.css' );
            wp_enqueue_style('sm-bootstrap-css', $path_portlet.'global/plugins/bootstrap/css/bootstrap.min.css' );
            
            wp_enqueue_style('sm-components-css', $path_portlet.'global/css/components.min.css');
            
            wp_enqueue_style('smf-front-css', $path_portlet .'css/front.css');
            //scripts
            wp_enqueue_script( 'smf-front-js', $path_portlet . 'js/front.js', array( 'jquery' ) );
            wp_localize_script( 'smf-front-js', 'smfScriptParams', array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' )
            ) );
	}
        
        function show_portlets(){
            global $wpdb;
            
            $sql = "SELECT * FROM {$wpdb->prefix}portlets WHERE status = 'activate'";
            $arr_portlets = $wpdb->get_results( $sql );
            if( empty($arr_portlets) ){       
                return;                
            }
            
            ob_start();        
            ?>                
            <div class="page-container">
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <div class="row">
                            <div class="col-md-12">
                                <?php                                            
                                $view = '<div class="row">';
                                foreach ($arr_portlets as $key => $portlet) {
                                        $view .= '<div class="col-md-6" id="' . esc_attr($portlet->id) . '">' . htmlspecialchars_decode(wp_unslash($portlet->content)) . '</div>';
                                }
                                $view .= '</div>';
                                echo $view;                                           
                                ?>   
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
            </div>
            <?php
            return ob_get_clean();
        }
} // end \Super_Metronic_Frontend class