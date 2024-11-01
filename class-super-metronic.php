<?php
/**
 * Plugin Name: Super Metronic
 * Plugin URI: https://wordpress.org/plugins/super-metronic
 * Description: Mega functionality with Metronic.
 * Version: 1.1.2
 * Author: Anton Shulga
 * Author URI: http://bigtonni.in.ua/
 * License: GPLv2 or later
 * Requires at least: 4.8.1
 * Tested up to: 4.8.1
 * 
 * Text Domain: smf
 * Domain Path: /i18n/languages
 */
/*
    Copyright 2017  Anton Shulga  (email: shandm85@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !class_exists('Super_Metronic') ) {

    register_activation_hook(   __FILE__, array( 'Super_Metronic', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Super_Metronic', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'Super_Metronic', 'uninstall' ) );
    
    class Super_Metronic {
        
        /** plugin version number */
        const VERSION = '1.1.2';
        
        /** @var Super_Metronic single instance of this plugin */
        protected static $instance;

        /** plugin text domain */
        const TEXT_DOMAIN = 'smf';

        /** @var \Super_Metronic_Admin instance */
        public $admin;

        /** @var \Super_Metronic_Frontend instance */
        public $frontend;
        
        public function __construct() {            
            // Initialize
            add_action( 'init', array( $this, 'init' ) );
            
            // Internationalize the text strings used.
            add_action('plugins_loaded', array(&$this, 'load_translation'));
        }
        
        /**
         * Do things on plugin activation.
         */
        public static function activate() {
            include_once(  'includes/class-super-metronic-install.php' );
            Super_Metronic_Install::install();
            flush_rewrite_rules();
        }
        
        /**
         * Flush permalinks on plugin deactivation.
         */
        public static function deactivate() {
            flush_rewrite_rules();
        }        
                
        public static function uninstall() {
            if ( ! current_user_can( 'activate_plugins' ) )     return;
            check_admin_referer( 'bulk-plugins' );

            if ( __FILE__ != WP_UNINSTALL_PLUGIN )  	return;
            
            global $wpdb;
            $prefix = $wpdb->prefix;

            $wpdb->query( "DROP TABLE IF EXISTS {$prefix}portlets" );
        }
        
        /**
        * Initialize
        */
        public function init() {
                // Frontend includes
                if ( ! is_admin() ) {
                        $this->frontend_includes();
                }
                // Admin includes
                if ( is_admin() ) {
                        $this->admin_includes();
                }
        }
        
        /**
        * Include required frontend files
        */
        private function frontend_includes() {
                require_once( $this->plugin_path() . '/includes/frontend/class-super-metronic-frontend.php' );
                $this->frontend = new Super_Metronic_Frontend();
        }

        /**
         * Include required admin files
         */
        private function admin_includes() {
                require_once( $this->plugin_path() . '/includes/admin/class-super-metronic-admin.php' );
                require_once( $this->plugin_path() . '/includes/sm-helpers.php' );
                $this->admin = new Super_Metronic_Admin();
        }

        /**
         * Load plugin text domain.
         */
        public function load_translation() {                
                load_plugin_textdomain( 'smf', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
        }

        /**
         * Main Super Metronic Instance, ensures only one instance is/can be loaded
         * @return Super_Metronic
         */
        public static function instance() {
                if ( is_null( self::$instance ) ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }
        
        public function plugin_url() {
                return untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
        
        /**
        * Get the plugin path.
        * @return string
        */
        public function plugin_path() {
                return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * @return __FILE__
         */
        protected function get_file() {
                return __FILE__;
        }        
    }
    
    /**
    * Returns the One True Instance of Super Metronic
    */
    function super_metronic() {
           return Super_Metronic::instance();
    }
    
    super_metronic();
    
}

?>
