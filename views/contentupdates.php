<?php
/**
 * Configure sources of updates and how they are presented to subscribers.
 * 
 * I want to bring excerpts from various sources together on one page...
 * 
 * Twitter
 * Facebook
 * LinkedIn
 * Blog Categories
 * Hot Forum Topics   
 *
 * @package WTG Portal Manager
 * @subpackage Views
 * @author Ryan Bayne   
 * @since 0.0.1
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Tools to configure updates page.   
 *
 * @package WTG Portal Manager
 * @subpackage Views
 * @author Ryan Bayne
 * @since 0.0.1
 */
class WTGPORTALMANAGER_Contentupdates_View extends WTGPORTALMANAGER_View {

    /**
     * Number of screen columns for post boxes on this screen
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected $screen_columns = 2;
    
    protected $view_name = 'contentupdates';
    
    public $purpose = 'normal';// normal, dashboard, metaarray (return the meta array only)
    
    /**
    * Array of meta boxes, looped through to register them on views and as dashboard widgets
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function meta_box_array() {  
        // array of meta boxes + used to register dashboard widgets (id, title, callback, context, priority, callback arguments (array), dashboard widget (boolean) )   
        return $this->meta_boxes_array = array(
            // array( id, title, callback (usually parent, approach created by Ryan Bayne), context (position), priority, call back arguments array, add to dashboard (boolean), required capability
            array( $this->view_name . '-setupportaltwitter', __( 'Portals Twitter', 'wtgportalmanager' ), array( $this, 'parent' ), 'normal', 'default', array( 'formid' => 'setupportaltwitter' ), true, 'activate_plugins' ),
            array( $this->view_name . '-setupupdates', __( 'Setup Updates Page', 'wtgportalmanager' ), array( $this, 'parent' ), 'normal', 'default', array( 'formid' => 'setupupdates' ), true, 'activate_plugins' ),
        );    
    }
    
    /**
     * Set up the view with data and do things that are specific for this view
     *
     * @since 0.0.1
     *
     * @param string $action Action for this view
     * @param array $data Data for this view
     */
    public function setup( $action, array $data ) {
        global $wtgportalmanager_settings;
        
        // create constant for view name
        if(!defined( "WTGPORTALMANAGER_VIEWNAME") ){define( "WTGPORTALMANAGER_VIEWNAME", $this->view_name );}
        
        // create class objects
        $this->WTGPORTALMANAGER = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER', 'class-wtgportalmanager.php', 'classes' );
        $this->UI = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER_UI', 'class-ui.php', 'classes' );  
        $this->DB = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER_DB', 'class-wpdb.php', 'classes' );
        $this->PHP = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER_PHP', 'class-phplibrary.php', 'classes' );
        $this->Forms = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER_Formbuilder', 'class-forms.php', 'classes' );
        
        // we have the ability to pass arguments to this, it is optional
        $this->TWITTER = $this->WTGPORTALMANAGER->load_class( "WTGPORTALMANAGER_Twitter", "class-twitter.php", 'classes' );
        
        parent::setup( $action, $data );
        
        // using array register many meta boxes
        foreach( self::meta_box_array() as $key => $metabox ) {
            // the $metabox array includes required capability to view the meta box
            if( isset( $metabox[7] ) && current_user_can( $metabox[7] ) ) {
                $this->add_meta_box( $metabox[0], $metabox[1], $metabox[2], $metabox[3], $metabox[4], $metabox[5] );   
            }               
        }             
    }
    
    /**
    * Outputs the meta boxes
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function metaboxes() {
        parent::register_metaboxes( self::meta_box_array() );     
    }

    /**
    * This function is called when on WP core dashboard and it adds widgets to the dashboard using
    * the meta box functions in this class. 
    * 
    * @uses dashboard_widgets() in parent class WTGPORTALMANAGER_View which loops through meta boxes and registeres widgets
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function dashboard() { 
        parent::dashboard_widgets( self::meta_box_array() );  
    }
                                                                                                                             
    /**
    * All add_meta_box() callback to this function to keep the add_meta_box() call simple.
    * 
    * This function also offers a place to apply more security or arguments.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    function parent( $data, $box ) {
        eval( 'self::postbox_' . $this->view_name . '_' . $box['args']['formid'] . '( $data, $box );' );
    }    
    
    /**
    * Enter Twitter application keys for the current portal.
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_contentupdates_setupportaltwitter( $data, $box ) {                                
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'Add your portals own Tweets to the portal update page.', 'wtgportalmanager' ), false );        
        $this->Forms->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );

        global $wtgportalmanager_settings;
        
        if (defined('WTG_USING_EXISTING_LIBRARY_TWITTEROAUTH') && WTG_USING_EXISTING_LIBRARY_TWITTEROAUTH) {
            $reflector = new ReflectionClass('TwitterOAuth');
            $file = $reflector->getFileName();
      
            echo '<div id="message" class="error"><p><strong>oAuth Twitter Feed for Developers</strong> is using an existing version of the TwitterOAuth class library to provide compatibility with existing plugins.<br />This could lead to conflicts if the plugin is using an different version of the class.</p><p>The class is being loaded at <strong>'.$file.'</strong></p></div>';
        }
      
        if (defined('WTG_USING_EXISTING_LIBRARY_OAUTH') && WTG_USING_EXISTING_LIBRARY_OAUTH) {
            $reflector = new ReflectionClass('OAuthConsumer');
            $file = $reflector->getFileName();
            
            echo '<div id="message" class="error"><p><strong>oAuth Twitter Feed for Developers</strong> is using an existing version of the PHP OAuth library to provide compatibility with existing plugins or your PHP installation.<br />This could lead to conflicts if the plugin, or your PHP installed class is using an different version of the class.</p><p>The class is being loaded at <strong>'.$file.'</strong></p></div>';
        }
      
        echo '<p>Most of this configuration can found on the application overview page on the <a href="http://dev.twitter.com/apps">http://dev.twitter.com</a> website.</p>';
        echo '<p>When creating an application for this plugin, you don\'t need to set a callback location and you only need read access.</p>';
        echo '<p>You will need to generate an oAuth token once you\'ve created the application. The button for that is on the bottom of the application overview page.</p>';

        echo '<hr />';
        ?>  

            <table class="form-table">                  
            <?php               
            if( isset( $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['consumer_key'] ) ) { $twitter_consumer_key = $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['consumer_key']; }
            $this->Forms->text_basic( $box['args']['formid'], 'consumer_key', 'consumer_key', 'Consumer Key (API Key)', $twitter_consumer_key, true, array( 'alphanumeric' ) );
            
            $twitter_consumer_secret = '';
            if( isset( $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['consumer_secret'] ) ) { $twitter_consumer_secret = $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['consumer_secret']; }            
            $this->Forms->text_basic( $box['args']['formid'], 'consumer_secret', 'consumer_secret', 'Consumer Secret (API Secret)', $twitter_consumer_secret, true, array( 'alphanumeric' ) );
            
            $twitter_access_token = '';
            if( isset( $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['access_token'] ) ) { $twitter_access_token = $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['access_token']; }            
            $this->Forms->text_basic( $box['args']['formid'], 'access_token', 'access_token', 'Your Access Token', $twitter_access_token, true, array( 'alphanumeric' ) );
            
            $twitter_token_secret = '';
            if( isset( $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['token_secret'] ) ) { $twitter_token_secret = $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['token_secret']; }            
            $this->Forms->text_basic( $box['args']['formid'], 'access_token_secret', 'access_token_secret', 'Access Token Secret', $twitter_token_secret, true, array( 'alphanumeric' ) );
            
            $twitter_screenname = '';
            if( isset( $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['screenname'] ) ) { $twitter_screenname = $wtgportalmanager_settings['api']['twitter']['apps'][WTGPORTALMANAGER_CURRENT]['screenname']; }            
            $this->Forms->text_basic( $box['args']['formid'], 'screenname', 'screenname', 'Twitter Feed Screen Name', $twitter_screenname, false, array( 'alphanumeric' ) );
            ?>
            </table>

        <?php 
                    
        echo '<hr />';
        
        echo '<h3>Debug Information</h3>';
        $last_portal_error = get_option('portal_last_error');
        $last_global_error = get_option('default_portal_last_error');
        if ( empty( $last_error ) ) { $last_error = __( "None", 'wtgportalmanager' ); }
        
        echo '<p>Last Twitter API Error: ' . $last_portal_error.'</p>';        

        $this->UI->postbox_content_footer();                  
    }   
     
    /**
    * Enter Twitter application keys for the current portal.
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_contentupdates_setupupdates( $data, $box ) {                                
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], __( 'Add your portals own Tweets to the portal update page.', 'wtgportalmanager' ), false );        

        echo '<p>Most of this configuration can found on the application overview page on the <a href="http://dev.twitter.com/apps">http://dev.twitter.com</a> website.</p>';
        echo '<p>When creating an application for this plugin, you don\'t need to set a callback location and you only need read access.</p>';
        echo '<p>You will need to generate an oAuth token once you\'ve created the application. The button for that is on the bottom of the application overview page.</p>';
                    
    }   
 
}?>