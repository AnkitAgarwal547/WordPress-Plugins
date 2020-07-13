<?php
/*
Plugin Name: DSurvey
Description: Create Survey and Check Results
Version: 1.0.0
Author: <a href="https://www.deaninfotech.com">Dean Infotech</a> 
Author URI: 'https://www.deaninfotech.com'
* Text Domain: DSurvey
*/

// ---------------------------------------------------------------------------------------

// function to create the DB / Options / Defaults
// run the install scripts upon plugin activation

define('DEAN_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('DEAN_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'd_survey_and_results');

function d_survey_and_results() {
    
    global $wpdb;

	// --------------------------------------
	// Survey Table
    $table_name = $wpdb->prefix . "d_surveys";
	$charset_collate = $wpdb->get_charset_collate();
	
    $sql = "CREATE TABLE $table_name (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `title` text NOT NULL,
			`category_id` text NULL,
			`user_id` text NULL,
			`parent_id` text NULL,
			`survey` longtext NOT NULL,
			`is_active` int(10) NOT NULL default 1,
			`is_stopped` int(10) NOT NULL default 0,
			`temp` int(10) NOT NULL default 1,
			`start_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`end_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
			`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	// ------------------------------------------
	// Category Table
	$table_name1 = $wpdb->prefix . "d_categories_surveys";
	$charset_collate = $wpdb->get_charset_collate();
	
    $sql = "CREATE TABLE $table_name1 (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `category_name` text NOT NULL,
			`parent` text DEFAULT 0 NOT NULL,
			`user_id` int(10) NOT NULL,
			`parent_id` int(10) NULL,
			`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
			`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
	
	// ------------------------------------------
	// Category Default questions
	$table_name = $wpdb->prefix . "d_categories_default_questions";
	$charset_collate = $wpdb->get_charset_collate();
	
    $sql = "CREATE TABLE $table_name (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `cat_id` text NOT NULL,
			`user_id` text NOT NULL,
			`parent_id` text NULL,
			`questions` longtext NOT NULL,
			`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
			`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
	
	// ----------------------------------------------
	// Submitted Surveys by User	
	$table_name = $wpdb->prefix . "d_submitted_surveys";
	$charset_collate = $wpdb->get_charset_collate();
	
    $sql = "CREATE TABLE $table_name (
            `id` int(10) NOT NULL AUTO_INCREMENT,
			`mac_address` TEXT NULL,
			`name` text NOT NULL,
			`email` text NULL,
			`phone` text NULL,
			`gender` text NULL,
			`age` text NULL,
			`submitted_survey` longtext NOT NULL,
			`survey_id` text NULL,
			`ip_address` text NULL,
			`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
			`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

add_action('admin_menu','d_survey_show_results');
function d_survey_show_results() {
	//this is the main item for the menu
	add_menu_page(
		'DSurvey', 				//page title
		'DSurvey', 				//menu title
		'manage_options', 		//capabilities
		'surveys', 				//menu slug
		'survey_lists', 		//function
		'dashicons-analytics' 	// menu icon
	);

		//this is a submenu
	// ---------------------------------------------------
	// Category Add
	add_submenu_page(
		'surveys',       					// parent slug
		'Category',    						// page title
		'Categories',             			// menu title
		'manage_options',           		// capability
		'category',	 						// slug
		'DSview_survey_list' 				// callback
	); 
	// ---------------------------------------------------
	// Default Question to categories
	add_submenu_page(
		'surveys',       					// parent slug
		'Add Default Questions',    			// page title
		'Add Default Questions',             	// menu title
		'manage_options',           		// capability
		'default_questions',				// slug
		'd_default_questions'				// callback
	);
	// ----------------------------------------------------
	// Load Default Questions
	add_submenu_page(
		'surveys',       					// parent slug
		'Load Default Questions',			// page title
		'Load Default Questions',  			// menu title
		'manage_options',           		// capability
		'load_default_questions',	     	// slug
		'dean_load_default_questions' 		// callback
	);
	// ---------------------------------------------------
	// Survey Results
	add_submenu_page(
		'surveys',       					// parent slug
		'Surveys',    						// page title
		'Survey Results',             		// menu title
		'manage_options',           		// capability
		'survey_result',	 				// slug
		'd_survey_all_answers' 				// callback
	); 

	// // ---------------------------------------------------
	// // export data
	add_submenu_page(
		'surveys',       					// parent slug
		'Surveys',    						// page title
		'Export Survey Data',          		// menu title
		'manage_options',           		// capability
		'survey_export',	 				// slug
		'd_survey_csv_pull' 				// callback
	);
	// ---------------------------------------------------
	// Page for Answers Details
	add_submenu_page(
		'surveys',       					// parent slug
		'Surveys Answers',    				// page title
		'',           						// menu title
		'manage_options',           		// capability
		'd-survey-answers-backend',	 		// slug
		'd_survey_answers_backend' 			// callback
	);
}

// --------------------------------------------------------------------------------
//  Load CSS and JS on backEND
add_action('admin_enqueue_scripts', 'load_admin_custom_css_js');
function load_admin_custom_css_js(){
	wp_register_style( 'plugin-admin-dean-css', DEAN_PLUGIN_URL.'css/style-admin.css', false, '1.0' );
	wp_enqueue_style( 'plugin-admin-dean-css' );
	
	wp_register_style( 'font-awesome-dean-css', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0' );
	wp_enqueue_style( 'font-awesome-dean-css' );
	wp_register_style( 'sweet-alert-dean-css', 'https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.css', false, '1.0' );
	wp_enqueue_style( 'sweet-alert-dean-css' );
	// wp_register_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',  false, '1.0');
	// wp_enqueue_style( 'bootstrap' );


	wp_enqueue_script( 'Chart.js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js', array(), '1.0', false );
	wp_enqueue_script( 'chart_script.js',  DEAN_PLUGIN_URL.'js/chart_script.js', array(), '1.0', true );

	wp_enqueue_script( 'sweet-alert-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@9', array(), '1.0', true );
	wp_enqueue_script( 'custom-backend-js',  DEAN_PLUGIN_URL.'js/custom-backend.js', array(), '1.0', true );

	// Data Tables
	wp_register_style( 'datatables-css', 'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css', false, '1.0' );
	wp_enqueue_style( 'datatables-css' );

	wp_enqueue_script( 'datatables-js', 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array(), '1.0', true );
}

// --------------------------------------------------------------------------------
//  Load CSS and JS on FrontEND
add_action( 'wp_enqueue_scripts', 'load_custom_css_js' );
function load_custom_css_js() {
	wp_register_style( 'plugin-dean-css', DEAN_PLUGIN_URL.'css/style-front.css', false, '1.0' );
	wp_enqueue_style( 'plugin-dean-css' );

	wp_register_style( 'slick-css', DEAN_PLUGIN_URL.'css/slick.css', false, '1.0' );
	wp_enqueue_style( 'slick-css' );

	wp_register_style( 'slick-theme-css', DEAN_PLUGIN_URL.'css/slick-theme.css', false, '1.0' );
	wp_enqueue_style( 'slick-theme-css' );

	wp_enqueue_script( 'Canvas.js', 'https://canvasjs.com/assets/script/canvasjs.min.js', array(), '1.0', false );
	wp_enqueue_script( 'Chart.js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js', array(), '1.0', false );
	wp_enqueue_script( 'chart_script.js',  DEAN_PLUGIN_URL.'js/chart_script.js', array(), '1.0', true );

	wp_register_style( 'font-awesome-dean-css', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0' );
	wp_enqueue_style( 'font-awesome-dean-css' );
	wp_register_style( 'sweet-alert-dean-css', 'https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.css', false, '1.0' );
	wp_enqueue_style( 'sweet-alert-dean-css' );

	wp_enqueue_script( 'slick-js',  DEAN_PLUGIN_URL.'js/slick.js', array(), '1.0', true );

	// wp_enqueue_script( 'jquery-3.4.1', 'https://code.jquery.com/jquery-3.4.1.min.js', array(), '1.0', false );
	wp_enqueue_script( 'sweet-alert-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@9', array(), '1.0', true );
	wp_enqueue_script( 'custom-js',  DEAN_PLUGIN_URL.'js/custom.js', array(), '1.0', true );
	wp_localize_script( 'rml-script', 'dean_survey_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
	// Data Tables
	wp_register_script('datatables', 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array('jquery'), true);
	wp_register_script('datatables_bootstrap', 'https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js', array('jquery'), true);
	wp_register_style('datatables_style', 'https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css');
	wp_register_style('bootstrap_style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
  
	wp_enqueue_script('datatables');
	wp_enqueue_script('datatables_bootstrap');
	wp_enqueue_style('datatables_style');
	wp_enqueue_style('bootstrap_style');
}

// --------------------------------------------------------------------------------
//  Require files
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-listings.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-chart.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-add.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-edit.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-add-employee.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-link-user.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-submit-survey-answers.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-school-dashboard.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-single-survey-chart.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-survey-chart-statistics.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-add-users.php');
require_once(DEAN_PLUGIN_PATH . 'frontend/d-questions-comp.php');


require_once(DEAN_PLUGIN_PATH . 'backend/survey-list-backend.php');
require_once(DEAN_PLUGIN_PATH . 'backend/category-add.php');
require_once(DEAN_PLUGIN_PATH . 'backend/default-question.php');
require_once(DEAN_PLUGIN_PATH . 'backend/load-default-questions.php');
require_once(DEAN_PLUGIN_PATH . 'backend/submit-answers-backend.php');
require_once(DEAN_PLUGIN_PATH . 'backend/survey-answers-details-backend.php');


// URL encryption
function base64_url_encode($input)
{
	return '?survey=d-survey-link&url-encode='.strtr(base64_encode($input), '+/=', '-_,');
}
// URL decryption
function base64_url_decode($input)
{
	return base64_decode(strtr($input, '-_,', '+/='));
}
