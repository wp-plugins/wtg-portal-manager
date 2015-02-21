<?php         
/*
Plugin Name: WTG Portal Manager Beta
Version: 0.0.2
Plugin URI: http://www.webtechglobal.co.uk
Description: Create many portals in one WordPress site that focus on individual products or services.
Author: WebTechGlobal
Author URI: http://www.webtechglobal.co.uk
Last Updated: January 2015
Text Domain: wtgportalmanager
Domain Path: /languages

GPL v3 

This program is free software downloaded from WordPress.org: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. This means
it can be provided for the sole purpose of being developed further
and we do not promise it is ready for any one persons specific needs.
See the GNU General Public License for more details.

See <http://www.gnu.org/licenses/>.
*/           
  
// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'Direct script access is not allowed!' );

// exit early if WTG Portal Manager doesn't have to be loaded
if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) // Login screen
    || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
    || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
    return;
}
              
// package variables
$c2p_currentversion = '0.0.2';# to be removed, version is now in the WTGPORTALMANAGER() class 
$c2p_debug_mode = false;# to be phased out, going to use environment variables (both WP and php.ini instead)

// go into dev mode if on test installation (if directory contains the string you will see errors and other fun stuff for geeks)               
if( strstr( ABSPATH, 'wtgportalmanager' ) ){
    $c2p_debug_mode = true;     
}               

// avoid error output here and there for the sake of performance...              
if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) )
        || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
        || ( defined( 'DOING_CRON' ) && DOING_CRON )
        || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
    $c2p_debug_mode = false;
}                   

// define WebTechGlobal constants applicable to all projects...
if(!defined( "WEBTECHGLOBAL_FULLINTEGRATION") ){define( "WEBTECHGLOBAL_FULLINTEGRATION", false );}// change to true to force tables and files to be shared among WTG plugins automatically
if(!defined( "WEBTECHGLOBAL_FORUM" ) ){define( "WEBTECHGLOBAL_FORUM", 'http://forum.webtechglobal.co.uk/' );}
if(!defined( "WEBTECHGLOBAL_TWITTER" ) ){define( "WEBTECHGLOBAL_TWITTER", 'http://www.twitter.com/WebTechGlobal/' );}
if(!defined( "WEBTECHGLOBAL_FACEBOOK" ) ){define( "WEBTECHGLOBAL_FACEBOOK", 'https://www.facebook.com/WebTechGlobal1/' );}
if(!defined( "WEBTECHGLOBAL_REGISTER" ) ){define( "WEBTECHGLOBAL_REGISTER", 'http://www.webtechglobal.co.uk/login/?action=register' );}
if(!defined( "WEBTECHGLOBAL_LOGIN" ) ){define( "WEBTECHGLOBAL_LOGIN", 'http://www.webtechglobal.co.uk/login/' );}
if(!defined( "WEBTECHGLOBAL_YOUTUBE" ) ){define( "WEBTECHGLOBAL_YOUTUBE", 'https://www.youtube.com/channel/UCVWMSHRCJ2hALZd1CYJrMXA' );}

// define constants, feel free to add some of your own...                              
if(!defined( "WTGPORTALMANAGER_NAME") ){define( "WTGPORTALMANAGER_NAME", 'WTG Portal Manager Beta' );} 
if(!defined( "WTGPORTALMANAGER__FILE__") ){define( "WTGPORTALMANAGER__FILE__", __FILE__);}
if(!defined( "WTGPORTALMANAGER_BASENAME") ){define( "WTGPORTALMANAGER_BASENAME",plugin_basename( WTGPORTALMANAGER__FILE__ ) );}
if(!defined( "WTGPORTALMANAGER_ABSPATH") ){define( "WTGPORTALMANAGER_ABSPATH", plugin_dir_path( __FILE__) );}//C:\AppServ\www\wordpress-testing\wtgplugintemplate\wp-content\plugins\wtgplugintemplate/  
if(!defined( "WTGPORTALMANAGER_PHPVERSIONMINIMUM") ){define( "WTGPORTALMANAGER_PHPVERSIONMINIMUM", '5.3.0' );}// The minimum php version that will allow the plugin to work                                
if(!defined( "WTGPORTALMANAGER_IMAGES_URL") ){define( "WTGPORTALMANAGER_IMAGES_URL",plugins_url( 'images/' , __FILE__ ) );}
if(!defined( "WTGPORTALMANAGER_FREE") ){define( "WTGPORTALMANAGER_FREE", 'paid' );} 
if(!defined( "WTGPORTALMANAGER_PORTAL" ) ){define( "WTGPORTALMANAGER_PORTAL", 'http://www.webtechglobal.co.uk/wtg-portal-manager-wordpress/' );}
if(!defined( "WTGPORTALMANAGER_FORUM" ) ){define( "WTGPORTALMANAGER_FORUM", 'http://forum.webtechglobal.co.uk/viewforum.php?f=42' );}
if(!defined( "WTGPORTALMANAGER_TWITTER" ) ){define( "WTGPORTALMANAGER_TWITTER", 'http://www.twitter.com/WebTechGlobal' );}
if(!defined( "WTGPORTALMANAGER_FACEBOOK" ) ){define( "WTGPORTALMANAGER_FACEBOOK", 'https://www.facebook.com/WebTechGlobal1/' );}
if(!defined( "WTGPORTALMANAGER_YOUTUBEPLAYLIST" ) ){define( "WTGPORTALMANAGER_YOUTUBEPLAYLIST", 'https://www.youtube.com/playlist?list=PLMYhfJnWwPWB_hCPD7hs-5dAa2O22nJFE' );}
        
// require main class...
require_once( WTGPORTALMANAGER_ABSPATH . 'classes/class-wtgportalmanager.php' );

// call the Daddy methods here or remove some lines as a quick configuration approach...
$WTGPORTALMANAGER = new WTGPORTALMANAGER();
$WTGPORTALMANAGER->custom_post_types();

// localization because we all love speaking a little chinese or russian or Klingon!
// Hmm! has anyone ever translated a WP plugin in Klingon?
function wtgportalmanager_textdomain() {
    load_plugin_textdomain( 'wtgportalmanager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action( 'plugins_loaded', 'wtgportalmanager_textdomain' );                                                                                                       
?>