<?php
/*
Plugin Name: Airforce SMS to Parent
Description: Send Important Notification, Announcements SMS to Parents
Version: 1.0.0
Author: Dean InfoTech
Author URI: http://www.deaninfotech.com
*/
// function to create the DB / Options / Defaults					
function ss_options_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . "airforce_sms";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `numbers` text NOT NULL,
			`message` text NOT NULL,
			`status` varchar(20) NULL,
			`date` datetime NOT NULL,
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'ss_options_install');

//menu items
add_action('admin_menu','sinetiks_schools_modifymenu');
function sinetiks_schools_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('SMS', //page title
	'SMS', //menu title
	'manage_options', //capabilities
	'sent_sms_airforce', //menu slug
	'sent_sms_airforce',
	'dashicons-email-alt', 40 //function
	);
	
	//this is a submenu
	add_submenu_page('sent_sms_airforce', //parent slug
	'SEND SMS', //page title
	'Send New SMS', //menu title
	'manage_options', //capability
	'send_sms_airforce', //menu slug
	'send_sms_airforce'); //function
}
define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'schools-list.php');
require_once(ROOTDIR . 'schools-create.php');