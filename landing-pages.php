<?php
/*
Plugin Name: Landing Pages
Plugin URI: http://www.inboundnow.com/landing-pages/
Description: The first true all-in-one Landing Page solution for WordPress, including ongoing conversion metrics, a/b split testing, unlimited design options and so much more!
Version:	1.6.2
Author: Inbound Now
Author URI: http://www.inboundnow.com/
Text Domain: landing-pages
Domain Path: lang
*/

if (!class_exists('Inbound_Landing_Pages_Plugin')) {

	final class Inbound_Landing_Pages_Plugin {
	
		/**
		* Main Inbound_Landing_Pages_Plugin Instance
		*
		*/
		public function __construct() {	
			
			/* Start a PHP Session if in wp-admin */
			if (is_admin()) {
				if(!isset($_SESSION)){@session_start();}
			}
	
			/* Run Loaders */
			self::load_constants();
			self::load_files();	
			self::load_shared_files();			
			self::load_text_domain();
		}

		/** 
		* Setup plugin constants 
		*
		*/
		private static function load_constants() {		
						
			define('LANDINGPAGES_CURRENT_VERSION', '1.6.2' );
			define('LANDINGPAGES_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
			define('LANDINGPAGES_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
			define('LANDINGPAGES_PLUGIN_SLUG', plugin_basename( dirname(__FILE__) ) );
			define('LANDINGPAGES_FILE', __FILE__ );
			define('LANDINGPAGES_STORE_URL', 'http://www.inboundnow.com/' );
			$uploads = wp_upload_dir();
			define('LANDINGPAGES_UPLOADS_PATH', $uploads['basedir'].'/landing-pages/templates/' );
			define('LANDINGPAGES_UPLOADS_URLPATH', $uploads['baseurl'].'/landing-pages/templates/' );
			
		}
		
		/** 
		* Include required plugin files 
		*
		*/
		private static function load_files() {						
			
			/* load core files */
			switch (is_admin()) :
				case true :
					/* loads admin files */
					include_once('modules/module.language-support.php');
					include_once('modules/module.javascript-admin.php');
					include_once('classes/class.activation.php');
					include_once('classes/class.activation.upgrade-routines.php');
					include_once('modules/module.global-settings.php');
					include_once('modules/module.clone.php');
					include_once('modules/module.extension-updater.php');
					include_once('modules/module.extension-licensing.php');
					include_once('modules/module.admin-menus.php');
					include_once('modules/module.welcome.php');
					include_once('modules/module.install.php');
					include_once('modules/module.alert.php');
					include_once('modules/module.metaboxes.php');
					include_once('modules/module.metaboxes-global.php');
					include_once('modules/module.landing-page.php');
					include_once('modules/module.load-extensions.php');
					include_once('modules/module.post-type.php');
					include_once('modules/module.track.php');
					include_once('modules/module.ajax-setup.php');
					include_once('modules/module.utils.php');
					include_once('modules/module.sidebar.php');
					include_once('modules/module.widgets.php');
					include_once('modules/module.cookies.php');
					include_once('modules/module.ab-testing.php');
					include_once('modules/module.click-tracking.php');
					include_once('modules/module.templates.php');
					include_once('modules/module.store.php');
					include_once('modules/module.customizer.php');
	
				BREAK;

				case false :
					/* load front-end files */
					include_once('modules/module.javascript-frontend.php');
					include_once('modules/module.post-type.php');
					include_once('modules/module.track.php');
					include_once('modules/module.ajax-setup.php');
					include_once('modules/module.utils.php');
					include_once('modules/module.sidebar.php');
					include_once('modules/module.widgets.php');
					include_once('modules/module.cookies.php');
					include_once('modules/module.ab-testing.php');
					include_once('modules/module.click-tracking.php');
					include_once('modules/module.landing-page.php');
					include_once('modules/module.customizer.php');

					BREAK;
			endswitch;
		}
		
		/** Load Shared Files at priority 2 */
		private static function load_shared_files() {
			require_once('shared/classes/class.load-shared.php'); 
			add_action( 'plugins_loaded', array( 'Inbound_Load_Shared' , 'init' ) , 2 );
		}
		
		/**
		*  Loads the correct .mo file for this plugin
		*  
		*/
		private static function load_text_domain() {
			add_action('init' , function() {
				load_plugin_textdomain( 'landing-pages' , false , LANDINGPAGES_PLUGIN_SLUG . '/lang/' );
			});
		}
	
	}
	
	/* Initiate Plugin */
	$GLOBALS['Inbound_Landing_Pages_Plugin'] = new Inbound_Landing_Pages_Plugin;

}




/* lagacy - Conditional check LP active */
function lp_check_active() {
	return 1;
}

/* Function to check This has been loaded for the tests */
function landingpages_is_active() {
	return true;
}

/* Function to check plugin code is running in travis */
function inbound_travis_check() {
	echo '*** Landing Pages Plugin is Running on Travis ***';
	return true;
}
