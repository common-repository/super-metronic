<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Super Metronic
 *
 * Admin class
 *
 * @since 1.1
 */

class Super_Metronic_Admin {

    /**
     * Setup admin class
     */
    public function __construct() {
        // Add menu.
        add_action('admin_menu', array(&$this, 'add_menu_items'));
        // load styles/scripts
        add_action('admin_enqueue_scripts', array($this, 'load_styles_scripts'));

        add_action('wp_ajax_portlet_saving', array($this, 'portlet_saving'));
        add_action('wp_ajax_portlet_action', array($this, 'portlet_action'));
    }

    function add_menu_items() {
        // portlets
        add_menu_page(__('Portlets', Super_Metronic::TEXT_DOMAIN), __('Portlets', Super_Metronic::TEXT_DOMAIN), 'manage_options', 'portlets', array(&$this, 'portlets_activate_page'), super_metronic()->plugin_url().'/assets/img/icon.png', 83);
        add_submenu_page('portlets', 'Portlets - ' . __('Activate', Super_Metronic::TEXT_DOMAIN), __('Activate', Super_Metronic::TEXT_DOMAIN), 'manage_options', 'portlets', array(&$this, 'portlets_activate_page'));
        add_submenu_page('portlets', 'Portlets - ' . __('Customize', Super_Metronic::TEXT_DOMAIN), __('Customize', Super_Metronic::TEXT_DOMAIN), 'manage_options', 'portlets-customize', array(&$this, 'portlets_customize_page'));
    }

    /**
     * Load admin styles and scripts
     */
    public function load_styles_scripts($hook_suffix) {
        if (!in_array($hook_suffix, array('portlets_page_portlets-customize', 'toplevel_page_portlets'))){
            return;
        }
        
        if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}

        $path_portlet = super_metronic()->plugin_url() . '/assets/';
        //styles
        wp_enqueue_style('sm-common', $path_portlet.'css/common.css');
        
        wp_enqueue_style('sm-font-awesome-css', $path_portlet.'global/plugins/font-awesome/css/font-awesome.min.css' );
        wp_enqueue_style('sm-line-icons-css', $path_portlet.'global/plugins/simple-line-icons/simple-line-icons.min.css' );
        wp_enqueue_style('sm-bootstrap-css', $path_portlet.'global/plugins/bootstrap/css/bootstrap.min.css' );
        wp_enqueue_style('sm-uniform-css', $path_portlet.'global/plugins/uniform/css/uniform.default.css' );
        wp_enqueue_style('sm-trumbowyg-css', $path_portlet.'global/plugins/trumbowyg/ui/trumbowyg.min.css' );
        wp_enqueue_style('sm-trumbowyg-colors-css', $path_portlet.'global/plugins/trumbowyg/plugins/colors/ui/trumbowyg.colors.css', array('sm-trumbowyg-css') );
        wp_enqueue_style('sm-trumbowyg-preformatted-css', $path_portlet.'global/plugins/trumbowyg/plugins/preformatted/ui/trumbowyg.preformatted.min.css', array('sm-trumbowyg-css') );
        
        wp_enqueue_style('sm-components-css', $path_portlet.'global/css/components.min.css' );
        wp_enqueue_style('sm-plugins-css', $path_portlet.'global/css/plugins.min.css' );
        wp_enqueue_style('sm-layout-css', $path_portlet.'layouts/layout/css/layout.min.css' );
        wp_register_style('sm-admin-customize-css', $path_portlet.'css/admin-customize.css' );
        wp_register_style('sm-admin-activate-css', $path_portlet.'css/admin-activate.css' );
        //scripts
        wp_enqueue_script('sm-bootstrap-js', $path_portlet .'global/plugins/bootstrap/js/bootstrap.min.js', array('jquery'), false, true);
        wp_enqueue_script('sm-cookie-js', $path_portlet .'global/plugins/js.cookie.min.js', array('jquery'), false, true);
        wp_enqueue_script('sm-slimscroll-js', $path_portlet .'global/plugins/jquery-slimscroll/jquery.slimscroll.min.js', array('jquery'), false, true);
        wp_enqueue_script('sm-blockui-js', $path_portlet .'global/plugins/jquery.blockui.min.js', array('jquery'), false, true);
        wp_enqueue_script('sm-uniform-js', $path_portlet .'global/plugins/uniform/jquery.uniform.min.js', array('jquery'), false, true);

        wp_enqueue_script('sm-trumbowyg-js', $path_portlet .'global/plugins/trumbowyg/trumbowyg.js', array('jquery'), false, false);
        wp_enqueue_script('sm-app-js', $path_portlet .'global/scripts/app.min.js', array('jquery'), false, true);
        wp_enqueue_script('sm-layout-js', $path_portlet .'layouts/layout/scripts/layout.min.js', array('jquery','sm-app-js'), false, true);        
        
        wp_enqueue_script('sm-admin-js', $path_portlet . 'js/admin.js', array('jquery','sm-trumbowyg-js'));
        wp_localize_script('sm-admin-js', 'smfScriptParams', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }

    /****** Ajax Admin ******/
    // Save
    function portlet_saving() {
        $data = array('result' => true, 'html' => __('Data was successfully stored', Super_Metronic::TEXT_DOMAIN));
        $portlet_html = isset($_POST['portletHtml']) ? htmlspecialchars($_POST['portletHtml']) : false;
        $color = !empty($_POST['color']) ? sanitize_text_field($_POST['color']) : false;
        //can be a number or maybe a string 'new'
        $id = !empty($_POST['number']) ? sanitize_text_field($_POST['number']) : false;

        if ( $portlet_html !== false && $id && $color ) {
            global $wpdb;

            $user_id = get_current_user_id();
            $table_name = $wpdb->prefix . "portlets";
            
            if( $id == 'new' ){
                    $wpdb->insert($table_name, array(
                            'user_id' => $user_id,
                            'content' => $portlet_html,
                            'color'   => $color,
                        ), array('%d', '%s', '%s')
                    );
            }else{
                    $wpdb->update($table_name, 
                            array(
                                'content' => $portlet_html,
                                'color'   => $color,
                            ), 
                            array('id' => (int)$id),
                            array('%s', '%s'),
                            array('%d')
                    );
            }
            
        } else {
            $data['result'] = false;
            $data['html'] = __('Error', Super_Metronic::TEXT_DOMAIN);
        }

        exit(json_encode($data));
    }

    // Edit
    function portlet_action() {
        $data = array('result' => false);
        $portlet_action = !empty($_POST['portletAction']) ? $_POST['portletAction'] : false;
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : false;

        if ($portlet_action && $id) {
            global $wpdb;

            if ($portlet_action !== 'activate') {
                $portlet_action = 'activate';
                $color = 'grey-cascade';
                $text = __('Deactivate', Super_Metronic::TEXT_DOMAIN);
            } else {
                $portlet_action = 'deactivate';
                $color = 'green-meadow';
                $text = __('Activate', Super_Metronic::TEXT_DOMAIN);
            }

            $table_name = $wpdb->prefix . "portlets";
            $wpdb->update($table_name, array('status' => $portlet_action), array('id' => $id), array('%s'), array('%d') );

            $data = array(
                'result' => true,
                'action' => $portlet_action,
                'color' => $color,
                'text' => $text
            );
        }
        exit(json_encode($data));
    }

    // Activate
    public function portlets_activate_page() {
        wp_enqueue_style('sm-admin-activate-css');
        
        global $wpdb;
        $notice = '';

        $table_name = $wpdb->prefix .'portlets';
        // Delete portlet
        if( !empty($_POST) && wp_verify_nonce($_POST['action_delete'],'form_portlet_data') ){    
                $wpdb->delete( $table_name, array( 'id' => (int)$_POST['pid'] ) );
                $notice = __('Portlet was successfully removed.', Super_Metronic::TEXT_DOMAIN);
        }
        // Get all portlets
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $arr_portlets = $wpdb->get_results( $sql );
        
        require_once( super_metronic()->plugin_path() . '/includes/admin/view/portlets_activate.php' );
    }

    // Customize
    public function portlets_customize_page() {
        $id = !empty($_GET['portlet']) ? (int) $_GET['portlet'] : 0;

        $portlet = $this->get_portlet($id);
        
        $arr_colors = require_once( super_metronic()->plugin_path() . '/includes/admin/colors.php' );

        $images = $this->get_list_files();
        
        wp_enqueue_style('sm-admin-customize-css');
        
        require_once( super_metronic()->plugin_path() . '/includes/admin/view/portlets_customize.php' );
        
    }

    // Get portlet by id
    private function get_portlet($id = 0) {
        $portlet = array();

        if (!$id)   return $portlet;

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}portlets WHERE id=" . $id;
        $obj_portlet = $wpdb->get_row($sql);
        return ( $obj_portlet !== NULL ) ? $obj_portlet : $portlet;
    }

    // View popup
    public static function portlet_modal( $color = 'white', $portlet, $images ){
            $number = 'new';
            $content = $title = $body = '';

            if( !empty($portlet) && $color == $portlet->color ){
                    $number = $portlet->id;
                    $content = htmlspecialchars_decode(wp_unslash($portlet->content));
                    $title = $portlet->title;
                    $body = $portlet->body;
                    $btn_main_txt = $portlet->btn_main_txt;
                    $btn_sec_txt = $portlet->btn_sec_txt;
            }else{
                $btn_main_txt = __('On', Super_Metronic::TEXT_DOMAIN);
                $btn_sec_txt = __('Off', Super_Metronic::TEXT_DOMAIN);
            }

            $color_esc = esc_attr($color);
            $view = '<div class="modal fade" id="demo_modal_' . $color_esc . '">';

            $view .= '<div class="modal-dialog modal-lg">';
            $view .= '<div class="modal-content c-square">';

            $view .= '<div class="modal-header">';
            $view .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            $view .= '<span aria-hidden="true">&times;</span>';
            $view .= '</button>';
            $view .= '<h4 class="modal-title bold uppercase font-' . $color_esc . '">' . esc_html($color) . '</h4>';
            $view .= '</div>';

            $view .= '<div class="modal-body">';
            $view .= '<div class="tabbable-line">';

            $view .= '<ul class="nav nav-tabs uppercase bold">';
            $view .= '<li class="active"><a href="#' . $color_esc . '_tab_1_content" data-toggle="tab">' . __('Typography', Super_Metronic::TEXT_DOMAIN) . '</a></li>';
            $view .= '<li><a href="#' . $color_esc . '_tab_2_content" data-toggle="tab">' . __('Background', Super_Metronic::TEXT_DOMAIN) . '</a></li>';
            $view .= '<li><a href="#' . $color_esc . '_tab_3_content" data-toggle="tab">' . __('Buttons', Super_Metronic::TEXT_DOMAIN) . '</a></li>';
            $view .= '</ul>';

            $view .= '<div class="tab-content">';
            $view .= '<div class="tab-pane active" id="' . $color_esc . '_tab_1_content">';
            $view .= '<h4>Title</h4>';
            $view .= '<div style="margin: 10px 0 30px 0">';

            $view .= '<input type="text" class="input-large portlet_title" name="title" value="'. esc_attr($title) .'">';

            $view .= '</div>';
            $view .= '<h4>Main text</h4>';
            $view .= '<div style="margin: 10px 0 30px 0;"">';
            $view .= '<textarea rows="7" style="width: 100%;" class="portlet_body">'. $body .'</textarea>';
            $view .= '</div>';

            $view .= '<h4>Icon</h4>';
            $view .= '<div style="margin: 10px 0 30px 0">';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-user" data-icon_p="icon-user"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-settings" data-icon_p="icon-settings"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-calendar" data-icon_p="icon-calendar"></i></a><br>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-power" data-icon_p="icon-power"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-paper-plane" data-icon_p="icon-paper-plane"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-paper-clip" data-icon_p="icon-paper-clip"></i></a><br>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-pointer" data-icon_p="icon-pointer"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-lock" data-icon_p="icon-lock"></i>&nbsp;</a>';
            $view .= '<a href="#" class="portlet_icon"><i class="font-' . $color_esc . ' font-lg icon-magnifier" data-icon_p="icon-magnifier"></i></a><br>';
            $view .= '</div>';

            $view .= '</div>';

            // Tab 2
            $view .= '<div class="tab-pane" id="' . $color_esc . '_tab_2_content">';
            $view .= '<div class="row">';
            // Tab 2.1
            $view .= '<div class="col-md-7">';

            if ( !empty($images) ) {
                    $view .= '<div class="row">';
                    $view .= '<div class="col-md-12 bg_priview">';
                    $view .= '<img style="max-height: 170px;height: 100%;" class="bg_priview_img" src="' . esc_url($images[0]['url']) . '" />';
                    $view .= '<button type="button" class="btn btn-sm red" style="float: right;">'. __('Cancel', Super_Metronic::TEXT_DOMAIN) .'</button>';
                    $view .= '</div></div><hr/>';
                    
                    $view .= '<div class="bdImgList">';
//                    $logoID = 10;
                    for($i = 0; $i < count($images); $i++){
//                            if( $i%2 == 0 )     $view .= '<div class="row">';
                            $view .= '<div class="col-md-6" style="margin-top: 10px;">';

                            $view .= '<img height="170" style="width: 100%;" class="bgImgThumb" src="' . esc_url($images[$i]['url']) . '" />';

                            $view .= '</div>';
//                            if( $i%2 == 1 )     $view .= '</div>';                        
                    }
                    $view .= '</div>';
            }
            
            $view .= '</div>';
            
            // Tab 2.2
            $view .= '<div class="col-md-5">';
            
            $view .= '<div class="row">';
            $view .= '<div class="col-md-12">';
            // Upload button on new version
//            $view .= '<div class="bdImageUploadSection"><input type="file" name="file_upload" class="bgpickfile" id="bg_'. $color_esc .'" /></div>';   //old
//            $view .= sm_image_uploader_field('uploader_custom', get_option('uploader_custom')); //new
            $view .= '</div>';            
            
            // View Porlet
            $view .= '<div class="col-md-12 portlet_box" style="margin-top: 20px;">';
            if( $content !== '' ){
                $view .= $content;
            }else{            
                $view .= '<div class="portlet solid ' . $color_esc . '">';
                $view .= '<div class="portlet-title"><div class="caption"><i class="fa"></i><span class="view_portlet_title"></span></div><div class="caption_button"></div></div>';
                $view .= '<div class="portlet-body"><div class="scroller view_portlet_body" style="height:200px" data-rail-visible="1" data-rail-color="yellow" data-handle-color="#a1b2bd"></div></div>';
                $view .= '</div>';            
            }
            $view .= '</div>';
            
            $view .= '</div>';// end row-class           
    
            $view .= '</div>';
            $view .= '</div>';// end row-class 

            $view .= '</div>';

            // Tab 3
            $view .= '<div class="tab-pane" id="' . $color_esc . '_tab_3_content">';
            $view .= '<div class="row">';
            $view .= '<div class="col-md-2">';
            
            $default = __('Button', Super_Metronic::TEXT_DOMAIN);
            
            $view .= '<div class="portlet_button_wp"><button type="button" class="btn uppercase portlet_button1 ' . $color_esc . '">' . $default . '</button>';
            $view .= '<button type="button" class="btn uppercase portlet_button2 ' . $color_esc . '">' . $default . '</button></div>';

            $view .= '<div class="portlet_button_wp"><button type="button" class="btn sbold uppercase portlet_button1 btn-outline ' . $color_esc . '">' . $default . '</button>';
            $view .= '<button type="button" class="btn sbold uppercase portlet_button2 btn-outline ' . $color_esc . '">' . $default . '</button></div>';

            $view .= '<div class="portlet_button_wp"><button type="button" class="btn sbold uppercase portlet_button1 btn-circle ' . $color_esc . '">' . $default . '</button>';
            $view .= '<button type="button" class="btn sbold uppercase portlet_button2 btn-circle ' . $color_esc . '">' . $default . '</button></div>';

            $view .= '<div class="portlet_button_wp"><button type="button" class="btn sbold uppercase portlet_button1 btn-outline btn-circle ' . $color_esc . '">' . $default . '</button>';
            $view .= '<button type="button" class="btn sbold uppercase portlet_button2 btn-outline btn-circle ' . $color_esc . '">' . $default . '</button></div>';

//            $view .= '<div class="portlet_button_wp" id="bt_switch_def"><input type="checkbox" class="make-switch" checked /></div>';
//            $view .= '<div class="portlet_button_wp ' . $color_esc . '"><input type="checkbox" class="make-switch" checked /></div>';

            $view .= '</div>';
            $view .= '<div class="col-md-5">';
            $view .= '<div class="row">';
            $view .= '<div class="col-md-6">';
            $view .= '<label>' . __('Primary text', Super_Metronic::TEXT_DOMAIN) . '</label>';
            $view .= '<input type="text" class="input-small portlet_button_text1" name="button_text1" value="'. $btn_main_txt .'">';
            $view .= '</div>';
            $view .= '<div class="col-md-6">';
            $view .= '<label>' . __('Secondary text', Super_Metronic::TEXT_DOMAIN) . '</label>';
            $view .= '<input type="text" class="input-small portlet_button_text2" name="button_text2" value="'. $btn_sec_txt .'">';
            $view .= '</div>';
            $view .= '</div></div>';
            $view .= '<div class="col-md-4"></div>';
            $view .= '</div>';
            $view .= '</div>';

            $view .= '</div>';
            $view .= '</div>';
            $view .= '</div>';
            // Footer
            $view .= '<input type="hidden" name="portlet_number" value="'. esc_attr($number) .'" />';
            $view .= '<div class="modal-footer"><div class="portlet-notice"></div><button type="button" class="btn btn-outline dark sbold uppercase saving">' . __('Save', Super_Metronic::TEXT_DOMAIN) . '</button><button type="button" class="btn btn-outline dark sbold uppercase" data-dismiss="modal">' . __('Close', Super_Metronic::TEXT_DOMAIN) . '</button></div>';

            $view .= '</div>';

            $view .= '</div>';
            $view .= '</div>';

            echo $view;
    }
    
    // Get list of files
    private function get_list_files() {            
            $dir_upload = wp_upload_dir();
            $path_to_smf_dir = $dir_upload ? $dir_upload['basedir'] . '/smf_portlets/' : '';
        
            $list_files = array();

            if ($handle = opendir($path_to_smf_dir)) {

                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $list_files[] = array(
                                    'url' => $dir_upload['baseurl']. '/smf_portlets/' .$file,
                                    'name' => $file
                        );
                    }
                }
                closedir($handle);
            }
            return $list_files;
    }

}
// end \Super_Metronic_Admin class