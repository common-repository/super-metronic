<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Super_Metronic_Install
 */
class Super_Metronic_Install {

	/**
	 * Install table
	 */
	public static function install() {
		global $wpdb;
                
                // create folder
                $upload = wp_upload_dir();
                $upload_dir = $upload['basedir'];
                $upload_dir = $upload_dir . '/smf_portlets';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777);
                }

                $table_name = $wpdb->prefix . "portlets";
        
                $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
                    id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
                    user_id int(32) unsigned NOT NULL,
                    status enum('activate','deactivate') NOT NULL DEFAULT 'deactivate',
                    content LONGTEXT NOT NULL,
                    color VARCHAR(20) NOT NULL DEFAULT '',
                    PRIMARY KEY  (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

                dbDelta ( $sql );
        }
}
