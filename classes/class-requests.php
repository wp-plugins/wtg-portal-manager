<?php
/** 
* Class for handling $_POST and $_GET requests
* 
* The class is called in the process_admin_POST_GET() method found in the WTGPORTALMANAGER class. 
* The process_admin_POST_GET() method is hooked at admin_init. It means requests are handled in the admin
* head, globals can be updated and pages will show the most recent data. Nonce security is performed
* within process_admin_POST_GET() then the require method for processing the request is used.
* 
* Methods in this class MUST be named within the form or link itself, basically a unique identifier for the form.
* i.e. the Section Switches settings have a form name of "sectionswitches" and so the method in this class used to
* save submission of the "sectionswitches" form is named "sectionswitches".
* 
* process_admin_POST_GET() uses eval() to call class + method 
* 
* @package WTG Portal Manager
* @author Ryan Bayne   
* @since 0.0.1
*/

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
* Class processes form submissions, the class is only loaded once nonce and other security checked
* 
* @author Ryan R. Bayne
* @package WTG Portal Manager
* @since 0.0.1
* @version 1.0.2
*/
class WTGPORTALMANAGER_Requests {  
    public function __construct() {
        global $wtgportalmanager_settings;
    
        // create class objects
        $this->WTGPORTALMANAGER = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER', 'class-wtgportalmanager.php', 'classes' ); # plugin specific functions
        $this->UI = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_UI', 'class-ui.php', 'classes' ); # interface, mainly notices
        $this->DB = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_DB', 'class-wpdb.php', 'classes' ); # database interaction
        $this->PHP = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_PHP', 'class-phplibrary.php', 'classes' ); # php library by Ryan R. Bayne
        $this->Files = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_Files', 'class-files.php', 'classes' );
        $this->Forms = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_Formbuilder', 'class-forms.php', 'classes' );
        $this->WPCore = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_WPCore', 'class-wpcore.php', 'classes' );
        $this->TabMenu = $this->WTGPORTALMANAGER->load_class( "WTGPORTALMANAGER_TabMenu", "class-pluginmenu.php", 'classes','pluginmenu' );   
        $this->PHPBB = $this->WTGPORTALMANAGER->load_class( "WTGPORTALMANAGER_PHPBB", "class-phpbb.php", 'classes','pluginmenu' );   
          
        // set current active portal
        if(!defined( "WTGPORTALMANAGER_ADMINCURRENT" ) ){define( "WTGPORTALMANAGER_ADMINCURRENT", $this->WTGPORTALMANAGER->get_active_portal_id() );}                
    }
    
    /**
    * Processes security for $_POST and $_GET requests,
    * then calls another function to complete the specific request made.
    * 
    * This function is called by process_admin_POST_GET() which is hooked by admin_init.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function process_admin_request() { 
        $method = 'post';// post or get

        // ensure processing requested
        // if a hacker changes this, no processing happens so no validation required
        if(!isset( $_POST['wtgportalmanager_admin_action'] ) && !isset( $_GET['wtgportalmanageraction'] ) ) {
            return;
        }          
               
        // handle $_POST action - form names are validated
        if( isset( $_POST['wtgportalmanager_admin_action'] ) && $_POST['wtgportalmanager_admin_action'] == true){        
            if( isset( $_POST['wtgportalmanager_admin_referer'] ) ){        
                
                // a few forms have the wtgportalmanager_admin_referer where the default hidden values are not in use
                check_admin_referer( $_POST['wtgportalmanager_admin_referer'] ); 
                $function_name = $_POST['wtgportalmanageraction'];     
                   
            } else {                                       
                
                // 99% of forms will use this method
                check_admin_referer( $_POST['wtgportalmanager_form_name'] );
                $function_name = $_POST['wtgportalmanager_form_name'];
            
            }        
        }
                          
        // $_GET request
        if( isset( $_GET['wtgportalmanageraction'] ) ){      
            check_admin_referer( $_GET['wtgportalmanageraction'] );        
            $function_name = $_GET['wtgportalmanageraction'];
            $method = 'get';
        }     
                   
        // arriving here means check_admin_referer() security is positive       
        global $wtgportalmanager_debug_mode, $cont;

        $this->PHP->var_dump( $_POST, '<h1>$_POST</h1>' );           
        $this->PHP->var_dump( $_GET, '<h1>$_GET</h1>' );    
                              
        // $_POST security
        if( $method == 'post' ) {                      
            // check_admin_referer() wp_die()'s if security fails so if we arrive here WordPress security has been passed
            // now we validate individual values against their pre-registered validation method
            // some generic notices are displayed - this system makes development faster
            $post_result = true;
            $post_result = $this->Forms->apply_form_security();// ensures $_POST['wtgportalmanager_form_formid'] is set, so we can use it after this line
            
            // apply my own level of security per individual input
            if( $post_result ){ $post_result = $this->Forms->apply_input_security(); }// detect hacking of individual inputs i.e. disabled inputs being enabled 
            
            // validate users values
            if( $post_result ){ $post_result = $this->Forms->apply_input_validation( $_POST['wtgportalmanager_form_formid'] ); }// values (string,numeric,mixed) validation

            // cleanup to reduce registered data
            $this->Forms->deregister_form( $_POST['wtgportalmanager_form_formid'] );
                    
            // if $overall_result includes a single failure then there is no need to call the final function
            if( $post_result === false ) {        
                return false;
            }
        }
        
        // handle a situation where the submitted form requests a function that does not exist
        if( !method_exists( $this, $function_name ) ){
            wp_die( sprintf( __( "The method for processing your request was not found. This can usually be resolved quickly. Please report method %s does not exist. <a href='https://www.youtube.com/watch?v=vAImGQJdO_k' target='_blank'>Watch a video</a> explaining this problem.", 'wtgportalmanager' ), 
            $function_name) ); 
            return false;// should not be required with wp_die() but it helps to add clarity when browsing code and is a precaution.   
        }
        
        // all security passed - call the processing function
        if( isset( $function_name) && is_string( $function_name ) ) {
            eval( 'self::' . $function_name .'();' );
        }          
    }  

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */    
    public function request_success( $form_title, $more_info = '' ){  
        $this->UI->create_notice( "Your submission for $form_title was successful. " . $more_info, 'success', 'Small', "$form_title Updated");          
    } 

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */    
    public function request_failed( $form_title, $reason = '' ){
        $this->UI->n_depreciated( $form_title . ' Unchanged', "Your settings for $form_title were not changed. " . $reason, 'error', 'Small' );    
    }

    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */    
    public function logsettings() {
        global $wtgportalmanager_settings;
        $wtgportalmanager_settings['globalsettings']['uselog'] = $_POST['wtgportalmanager_radiogroup_logstatus'];
        $wtgportalmanager_settings['globalsettings']['loglimit'] = $_POST['wtgportalmanager_loglimit'];
                                                   
        ##################################################
        #           LOG SEARCH CRITERIA                  #
        ##################################################
        
        // first unset all criteria
        if( isset( $wtgportalmanager_settings['logsettings']['logscreen'] ) ){
            unset( $wtgportalmanager_settings['logsettings']['logscreen'] );
        }
                                                           
        // if a column is set in the array, it indicates that it is to be displayed, we unset those not to be set, we dont set them to false
        if( isset( $_POST['wtgportalmanager_logfields'] ) ){
            foreach( $_POST['wtgportalmanager_logfields'] as $column){
                $wtgportalmanager_settings['logsettings']['logscreen']['displayedcolumns'][$column] = true;                   
            }
        }
                                                                                 
        // outcome criteria
        if( isset( $_POST['wtgportalmanager_log_outcome'] ) ){    
            foreach( $_POST['wtgportalmanager_log_outcome'] as $outcomecriteria){
                $wtgportalmanager_settings['logsettings']['logscreen']['outcomecriteria'][$outcomecriteria] = true;                   
            }            
        } 
        
        // type criteria
        if( isset( $_POST['wtgportalmanager_log_type'] ) ){
            foreach( $_POST['wtgportalmanager_log_type'] as $typecriteria){
                $wtgportalmanager_settings['logsettings']['logscreen']['typecriteria'][$typecriteria] = true;                   
            }            
        }         

        // category criteria
        if( isset( $_POST['wtgportalmanager_log_category'] ) ){
            foreach( $_POST['wtgportalmanager_log_category'] as $categorycriteria){
                $wtgportalmanager_settings['logsettings']['logscreen']['categorycriteria'][$categorycriteria] = true;                   
            }            
        }         

        // priority criteria
        if( isset( $_POST['wtgportalmanager_log_priority'] ) ){
            foreach( $_POST['wtgportalmanager_log_priority'] as $prioritycriteria){
                $wtgportalmanager_settings['logsettings']['logscreen']['prioritycriteria'][$prioritycriteria] = true;                   
            }            
        }         

        ############################################################
        #         SAVE CUSTOM SEARCH CRITERIA SINGLE VALUES        #
        ############################################################
        // page
        if( isset( $_POST['wtgportalmanager_pluginpages_logsearch'] ) && $_POST['wtgportalmanager_pluginpages_logsearch'] != 'notselected' ){
            $wtgportalmanager_settings['logsettings']['logscreen']['page'] = $_POST['wtgportalmanager_pluginpages_logsearch'];
        }   
        // action
        if( isset( $_POST['csv2pos_logactions_logsearch'] ) && $_POST['csv2pos_logactions_logsearch'] != 'notselected' ){
            $wtgportalmanager_settings['logsettings']['logscreen']['action'] = $_POST['csv2pos_logactions_logsearch'];
        }   
        // screen
        if( isset( $_POST['wtgportalmanager_pluginscreens_logsearch'] ) && $_POST['wtgportalmanager_pluginscreens_logsearch'] != 'notselected' ){
            $wtgportalmanager_settings['logsettings']['logscreen']['screen'] = $_POST['wtgportalmanager_pluginscreens_logsearch'];
        }  
        // line
        if( isset( $_POST['wtgportalmanager_logcriteria_phpline'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['line'] = $_POST['wtgportalmanager_logcriteria_phpline'];
        }  
        // file
        if( isset( $_POST['wtgportalmanager_logcriteria_phpfile'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['file'] = $_POST['wtgportalmanager_logcriteria_phpfile'];
        }          
        // function
        if( isset( $_POST['wtgportalmanager_logcriteria_phpfunction'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['function'] = $_POST['wtgportalmanager_logcriteria_phpfunction'];
        }
        // panel name
        if( isset( $_POST['wtgportalmanager_logcriteria_panelname'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['panelname'] = $_POST['wtgportalmanager_logcriteria_panelname'];
        }
        // IP address
        if( isset( $_POST['wtgportalmanager_logcriteria_ipaddress'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['ipaddress'] = $_POST['wtgportalmanager_logcriteria_ipaddress'];
        }
        // user id
        if( isset( $_POST['wtgportalmanager_logcriteria_userid'] ) ){
            $wtgportalmanager_settings['logsettings']['logscreen']['userid'] = $_POST['wtgportalmanager_logcriteria_userid'];
        }
        
        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings );
        $this->UI->n_postresult_depreciated( 'success', __( 'Log Settings Saved', 'wtgportalmanager' ), __( 'It may take sometime for new log entries to be created depending on your websites activity.', 'wtgportalmanager' ) );  
    }  
    
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */       
    public function beginpluginupdate() {
        $this->Updates = $this->WTGPORTALMANAGER->load_class( 'WTGPORTALMANAGER_Formbuilder', 'class-forms.php', 'classes' );
        
        // check if an update method exists, else the plugin needs to do very little
        eval( '$method_exists = method_exists ( $this->Updates , "patch_' . $_POST['wtgportalmanager_plugin_update_now'] .'" );' );

        if( $method_exists){
            // perform update by calling the request version update procedure
            eval( '$update_result_array = $this->Updates->patch_' . $_POST['wtgportalmanager_plugin_update_now'] .'( "update");' );       
        }else{
            // default result to true
            $update_result_array['failed'] = false;
        } 
      
        if( $update_result_array['failed'] == true){           
            $this->UI->create_notice( __( 'The update procedure failed, the reason should be displayed below. Please try again unless the notice below indicates not to. If a second attempt fails, please seek support.', 'wtgportalmanager' ), 'error', 'Small', __( 'Update Failed', 'wtgportalmanager' ) );    
            $this->UI->create_notice( $update_result_array['failedreason'], 'info', 'Small', 'Update Failed Reason' );
        }else{  
            // storing the current file version will prevent user coming back to the update screen
            global $wtgportalmanager_currentversion;        
            update_option( 'wtgportalmanager_installedversion', $wtgportalmanager_currentversion);

            $this->UI->create_notice( __( 'Good news, the update procedure was complete. If you do not see any errors or any notices indicating a problem was detected it means the procedure worked. Please ensure any new changes suit your needs.', 'wtgportalmanager' ), 'success', 'Small', __( 'Update Complete', 'wtgportalmanager' ) );
            
            // do a redirect so that the plugins menu is reloaded
            wp_redirect( get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=wtgportalmanager' );
            exit;                
        }
    }
    
    /**
    * Save drip feed limits  
    */
    public function schedulerestrictions() {
        $c2p_schedule_array = $this->WTGPORTALMANAGER->get_option_schedule_array();
        
        // if any required values are not in $_POST set them to zero
        if(!isset( $_POST['day'] ) ){
            $c2p_schedule_array['limits']['day'] = 0;        
        }else{
            $c2p_schedule_array['limits']['day'] = $_POST['day'];            
        }
        
        if(!isset( $_POST['hour'] ) ){
            $c2p_schedule_array['limits']['hour'] = 0;
        }else{
            $c2p_schedule_array['limits']['hour'] = $_POST['hour'];            
        }
        
        if(!isset( $_POST['session'] ) ){
            $c2p_schedule_array['limits']['session'] = 0;
        }else{
            $c2p_schedule_array['limits']['session'] = $_POST['session'];            
        }
                                 
        // ensure $c2p_schedule_array is an array, it may be boolean false if schedule has never been set
        if( isset( $c2p_schedule_array ) && is_array( $c2p_schedule_array ) ){
            
            // if times array exists, unset the [times] array
            if( isset( $c2p_schedule_array['days'] ) ){
                unset( $c2p_schedule_array['days'] );    
            }
            
            // if hours array exists, unset the [hours] array
            if( isset( $c2p_schedule_array['hours'] ) ){
                unset( $c2p_schedule_array['hours'] );    
            }
            
        }else{
            // $schedule_array value is not array, this is first time it is being set
            $c2p_schedule_array = array();
        }
        
        // loop through all days and set each one to true or false
        if( isset( $_POST['wtgportalmanager_scheduleday_list'] ) ){
            foreach( $_POST['wtgportalmanager_scheduleday_list'] as $key => $submitted_day ){
                $c2p_schedule_array['days'][$submitted_day] = true;        
            }  
        } 
        
        // loop through all hours and add each one to the array, any not in array will not be permitted                              
        if( isset( $_POST['wtgportalmanager_schedulehour_list'] ) ){
            foreach( $_POST['wtgportalmanager_schedulehour_list'] as $key => $submitted_hour){
                $c2p_schedule_array['hours'][$submitted_hour] = true;        
            }           
        }    

        if( isset( $_POST['deleteuserswaiting'] ) )
        {
            $c2p_schedule_array['eventtypes']['deleteuserswaiting']['switch'] = 'enabled';                
        }
        
        if( isset( $_POST['eventsendemails'] ) )
        {
            $c2p_schedule_array['eventtypes']['sendemails']['switch'] = 'enabled';    
        }        
  
        $this->WTGPORTALMANAGER->update_option_schedule_array( $c2p_schedule_array );
        $this->UI->notice_depreciated( __( 'Schedule settings have been saved.', 'wtgportalmanager' ), 'success', 'Large', __( 'Schedule Times Saved', 'wtgportalmanager' ) );   
    } 
    
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */       
    public function logsearchoptions() {
        $this->UI->n_postresult_depreciated( 'success', __( 'Log Search Settings Saved', 'wtgportalmanager' ), __( 'Your selections have an instant effect. Please browse the Log screen for the results of your new search.', 'wtgportalmanager' ) );                   
    }
 
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */        
    public function defaultcontenttemplate () {        
        $this->UI->create_notice( __( 'Your default content template has been saved. This is a basic template, other advanced options may be available by activating the WTG Portal Manager Templates custom post type (pro edition only) for managing multiple template designs.' ), 'success', 'Small', __( 'Default Content Template Updated' ) );         
    }
        
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */       
    public function reinstalldatabasetables() {
        $installation = new WTGPORTALMANAGER_Install();
        $installation->reinstalldatabasetables();
        $this->UI->create_notice( 'All tables were re-installed. Please double check the database status list to
        ensure this is correct before using the plugin.', 'success', 'Small', 'Tables Re-Installed' );
    }
     
    /**
    * form processing function
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */          
    public function globalswitches() {
        global $wtgportalmanager_settings;
        $wtgportalmanager_settings['noticesettings']['wpcorestyle'] = $_POST['uinoticestyle'];        
        $wtgportalmanager_settings['standardsettings']['textspinrespinning'] = $_POST['textspinrespinning'];
        $wtgportalmanager_settings['standardsettings']['systematicpostupdating'] = $_POST['systematicpostupdating'];
        $wtgportalmanager_settings['posttypes']['wtgflags']['status'] = $_POST['flagsystemstatus'];
        $wtgportalmanager_settings['widgetsettings']['dashboardwidgetsswitch'] = $_POST['dashboardwidgetsswitch'];
        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings ); 
        $this->UI->create_notice( __( 'Global switches have been updated. These switches can initiate the use of 
        advanced systems. Please monitor your blog and ensure the plugin operates as you expected it to. If
        anything does not appear to work in the way you require please let WebTechGlobal know.' ),
        'success', 'Small', __( 'Global Switches Updated' ) );       
    } 
       
    /**
    * save capability settings for plugins pages
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function pagecapabilitysettings() {
        
        // get the capabilities array from WP core
        $capabilities_array = $this->WPCore->capabilities();

        // get stored capability settings 
        $saved_capability_array = get_option( 'wtgportalmanager_capabilities' );
        
        // get the tab menu 
        $pluginmenu = $this->TabMenu->menu_array();
                
        // to ensure no extra values are stored (more menus added to source) loop through page array
        foreach( $pluginmenu as $key => $page_array ) {
            
            // ensure $_POST value is also in the capabilities array to ensure user has not hacked form, adding their own capabilities
            if( isset( $_POST['pagecap' . $page_array['name'] ] ) && in_array( $_POST['pagecap' . $page_array['name'] ], $capabilities_array ) ) {
                $saved_capability_array['pagecaps'][ $page_array['name'] ] = $_POST['pagecap' . $page_array['name'] ];
            }
                
        }
          
        update_option( 'wtgportalmanager_capabilities', $saved_capability_array );
         
        $this->UI->create_notice( __( 'Capabilities for this plugins pages have been stored. Due to this being security related I recommend testing before you logout. Ensure that each role only has access to the plugin pages you intend.' ), 'success', 'Small', __( 'Page Capabilities Updated' ) );        
    }
    
    /**
    * Saves the plugins global dashboard widget settings i.e. which to display, what to display, which roles to allow access
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function dashboardwidgetsettings() {
        global $wtgportalmanager_settings;
        
        // loop through pages
        $WTGPORTALMANAGER_TabMenu = WTGPORTALMANAGER::load_class( 'WTGPORTALMANAGER_TabMenu', 'class-pluginmenu.php', 'classes' );
        $menu_array = $WTGPORTALMANAGER_TabMenu->menu_array();       
        foreach( $menu_array as $key => $section_array ) {

            if( isset( $_POST[ $section_array['name'] . 'dashboardwidgetsswitch' ] ) ) {
                $wtgportalmanager_settings['widgetsettings'][ $section_array['name'] . 'dashboardwidgetsswitch'] = $_POST[ $section_array['name'] . 'dashboardwidgetsswitch' ];    
            }
            
            if( isset( $_POST[ $section_array['name'] . 'widgetscapability' ] ) ) {
                $wtgportalmanager_settings['widgetsettings'][ $section_array['name'] . 'widgetscapability'] = $_POST[ $section_array['name'] . 'widgetscapability' ];    
            }

        }

        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings );    
        $this->UI->create_notice( __( 'Your dashboard widget settings have been saved. Please check your dashboard to ensure it is configured as required per role.', 'wtgportalmanager' ), 'success', 'Small', __( 'Settings Saved', 'wtgportalmanager' ) );         
    }

    /**
    * Insert a new portal to the portals database table. 
    * 
    * A portal name should match project names when possible to make integration
    * with WTG project management plugin.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function createportal() {
   
        // build optional fields array (pages, blog category, forum ID etc)
        $optional_fields = array();
        $optional_fields['newportalmainpageid'] = $_POST['newportalmainpageid'];
        $optional_fields['newportalupdatespageid'] = $_POST['newportalupdatespageid'];
        $optional_fields['newportalblogcategory'] = $_POST['newportalblogcategory'];
        $optional_fields['newportalfaqpage'] = $_POST['newportalfaqpage'];
        $optional_fields['newportalfeaturespage'] = $_POST['newportalfeaturespage'];
        $optional_fields['newportalforumid'] = $_POST['newportalforumid'];
        $optional_fields['newportalsupportpage'] = $_POST['newportalsupportpage'];
        $optional_fields['newportalscreenshotspage'] = $_POST['newportalscreenshotspage'];
        $optional_fields['newportalvideospage'] = $_POST['newportalvideospage'];
        $optional_fields['newportaltestimonialspage'] = $_POST['newportaltestimonialspage'];
        
        // insert the portal
        $portal_id = $this->WTGPORTALMANAGER->insertportal( $_POST['newportalname'], $_POST['newportaldescription'], $_POST['selectedmenu'], $optional_fields );  

        // set the new portal to active (applies to current user only)
        $this->WTGPORTALMANAGER->activate_portal( get_current_user_id(), $portal_id );
        
        $this->UI->create_notice( __( "The new portal ID is $portal_id and you can begin working on the portal now.", 'wtgportalmanager' ), 'success', 'Small', __( 'Portal Created', 'wtgportalmanager' ) );                                              
    }
    
    /**
    * Activates portal on admin side for editing - only one partal can be active for editing at a time
    * for the current user.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function currentportal() {
        $this->WTGPORTALMANAGER->activate_portal( $_POST['portalactivation'], get_current_user_id() ); 
        $this->UI->create_notice( __( "You activated portal with ID " . $_POST['portalactivation'] . " and when using this plugins other views that is the portal you will be viewing/editing.", 'wtgportalmanager' ), 'success', 'Small', __( 'Portal Activated', 'wtgportalmanager' ) );                                              
    }
    
    /**
    * Create relationship between current portal and page (not post)
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function addpagerelationship() {
        $this->WTGPORTALMANAGER->create_page_relationship( $this->WTGPORTALMANAGER->get_active_portal_id(), $_POST['addpageid'] );
        $this->UI->create_notice( __( "A new page has been added to your current active portal.", 'wtgportalmanager' ), 'success', 'Small', __( 'Page Relationship Created', 'wtgportalmanager' ) );                                                       
    }

    /**
    * Handle requests made using the form that lists all
    * related pages i.e. delete the relationship.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function listrelatedpages() {
    
    }

    /**
    * Sets the current portals main category.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function maincategory() {
        $this->WTGPORTALMANAGER->update_main_category( $this->WTGPORTALMANAGER->get_active_portal_id(), $_POST['selectedcategory'] );
        $this->UI->create_notice( __( "The main category allows your portal to have a blog and the selected category will be focused on.", 'wtgportalmanager' ), 'success', 'Small', __( 'Main Category Set', 'wtgportalmanager' ) );
    }
                                                                                                                                                                                                   
    /**
    * Handle request to delete the relationship between
    * portal and categories.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function portalcategories() {

    }

    /**
    * Create relationship between category and portal.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function addcategories() {
        $this->WTGPORTALMANAGER->add_portal_subcategory( $this->WTGPORTALMANAGER->get_active_portal_id(), $_POST['selectedsubcategory'] );
        $this->UI->create_notice( __( "The new category is now available within your portal.", 'wtgportalmanager' ), 'success', 'Small', __( 'Category Added', 'wtgportalmanager' ) );
    }

    /**
    * Create relationship between users WP menu and portal.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function addmenu() {
        //     'selectedmenu' => string '2' (length=1)
        // 'ismainmenu0' => string 'ismain' (length=6)
    }

    /**
    * Handles request to delete relationship between menu and portal.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function menulist() {

    }
    
    /**
    * Adds a sidebar to this plugins options. This plugin
    * needs to register the sidebar for use by dynamic_sidebars()
    * which must be in theme sidebar.php
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function createsidebar() {
        global $wtgportalmanager_settings;
        $wtgportalmanager_settings['sidebars'][] = $_POST['newsidebarname'];
        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings );    
        $this->UI->create_notice( __( "A new sidebar has been stored in this plugins options (WP currently offers no alternative solution). As a result the new sidebar will only be available while this plugin is active.", 'wtgportalmanager' ), 'success', 'Small', __( 'Sidebar Registered', 'wtgportalmanager' ) );
    }
    
    /**
    * Save the main sidebar ID.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function setsidebars() {
        // get the integration data I have setup in array - it is a long term array for all my plugins
        $themes_integration_array = $this->WTGPORTALMANAGER->get_themes_integration_info(); 
             
        // loop current themes sidebars
        foreach( $themes_integration_array['sidebars'] as $themes_dynamic_sidebars ) 
        {                           

            // forms menu names and ID equal the post meta_key used to store sidebar ID's
            $selected_sidebar_id = $_POST[ $themes_dynamic_sidebars['metakey'] ];
            
            // set new portal -> sidebar relationship which adds post meta_key used in sidebar.php to the portal meta_key 
            $this->WTGPORTALMANAGER->set_sidebar_relationship( $this->WTGPORTALMANAGER->get_active_portal_id(), $themes_dynamic_sidebars['metakey'], $selected_sidebar_id );    
            
            // add post meta to all posts that do not have it but do have a relationship with the current portal
            // get post ID's by querying post, page, maincategory, subcategory meta_keys in portal table
                
        }        
   
        $this->UI->create_notice( __( "Your current portals sidebars have been set. This change is applied instantly. Please view your portal as a none registered viewer to test.", 'wtgportalmanager' ), 'success', 'Small', __( 'Main Sidebar Set', 'wtgportalmanager' ) );        
    }
    
    /**
    * Activates or disables API's 
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function setupdefaulttwitter() {
        global $wtgportalmanager_settings;
         
        $wtgportalmanager_settings['api']['twitter']['active'] = $_POST['twitterapiswitch'];
        //$wtgportalmanager_settings['api']['twitter']['apps']['default'] = $_POST['cache_expire'];
        
        $wtgportalmanager_settings['api']['twitter']['apps']['default']['consumer_key'] = $_POST['consumer_key'];
        $wtgportalmanager_settings['api']['twitter']['apps']['default']['consumer_secret'] = $_POST['consumer_secret'];
        $wtgportalmanager_settings['api']['twitter']['apps']['default']['access_token'] = $_POST['access_token'];
        $wtgportalmanager_settings['api']['twitter']['apps']['default']['token_secret'] = $_POST['access_token_secret'];  
        $wtgportalmanager_settings['api']['twitter']['apps']['default']['screenname'] = $_POST['screenname'];
                                        
        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings );    
        $this->UI->create_notice( __( "Please check features related to the API you disabled or activated and ensure they are working as normal.", 'wtgportalmanager' ), 'success', 'Small', __( 'API Updated', 'wtgportalmanager' ) );       
    }
    
    /**
    * Store Twitter API settings for the current portal only.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function setupportaltwitter() {
        $this->WTGPORTALMANAGER->update_portals_twitter_api( WTGPORTALMANAGER_ADMINCURRENT, $_POST['consumer_key'], $_POST['consumer_secret'], $_POST['access_token'], $_POST['access_token_secret'], $_POST['screenname'] );    
        $this->UI->create_notice( __( "You have updated the current portals Twitter App account.", 'wtgportalmanager' ), 'success', 'Small', __( 'Portals Twitter Updated', 'wtgportalmanager' ) );       
    }
    
    /**
    * Saves global forum configuration.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function configureforum() {
        global $wtgportalmanager_settings;
        
        // sanitize path
        $forum_path_modified = sanitize_text_field( $_POST['forumpath'] );
        $forum_path_modified = stripslashes_deep( $forum_path_modified );        
        $forum_path_modified = trailingslashit( $forum_path_modified );
        
        // now determine if phpBB actually exists 
        $does_phpbb_exist = $this->PHPBB->phpbb_exists( $forum_path_modified );
        if( !$does_phpbb_exist ) {
            $this->UI->create_notice( __( "Your forum installation could not be located on the path you gave. Please ensure your forum is supported and remember to visit the forum for advice.", 'wtgportalmanager' ), 'success', 'Small', __( 'Forum Not Found', 'wtgportalmanager' ) );       
            return;    
        }
        
        // include the phpBB config file - we need database prefix for queries
        require( $forum_path_modified . 'config.php' );
        
        // add config to settings
        $wtgportalmanager_settings['forumconfig']['path'] = $forum_path_modified;
        $wtgportalmanager_settings['forumconfig']['status'] = $_POST['globalforumswitch'];
        $wtgportalmanager_settings['forumconfig']['tableprefix'] = $table_prefix;
        $wtgportalmanager_settings['forumconfig']['admrelativepath'] = $phpbb_adm_relative_path;
        $wtgportalmanager_settings['forumconfig']['phpbbversion'] =  $this->PHPBB->version();
         
        // ensure compatible phpBB version installed
        if( $wtgportalmanager_settings['forumconfig']['phpbbversion'] < '3.1' ) { 
            $this->UI->create_notice( __( "This plugin does not support your current phpBB version which is " . $wtgportalmanager_settings['forumconfig']['phpbbversion'], 'wtgportalmanager' ), 'success', 'Small', __( 'Forum Version Not Supported', 'wtgportalmanager' ) );
            return;
        }
        
        $this->WTGPORTALMANAGER->update_settings( $wtgportalmanager_settings );
        
        $this->UI->create_notice( __( "You have saved your forums configuration and can now begin displaying forum data in your portals.", 'wtgportalmanager' ), 'success', 'Small', __( 'Forum Configuration Saved', 'wtgportalmanager' ) );       
    }
    
    /**
    * Handle request to save the main forum settings for the current portal.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function setupportalforum() {
        $got_forum_row = $this->PHPBB->get_forum( $_POST['mainforumid'] );
        
        // ensure forum ID is valid (numeric validation already done before arriving here using my own security approach)
        if( !$got_forum_row || empty( $got_forum_row ) ) {
            $this->UI->create_notice( __( "The forum ID you entered does not match any forums in your phpBB database. Nothing was saved, please try again.", 'wtgportalmanager' ), 'error', 'Small', __( 'Forum Not Found', 'wtgportalmanager' ) );                           
            return;    
        }
        
        $this->WTGPORTALMANAGER->update_portals_forumsettings( WTGPORTALMANAGER_ADMINCURRENT, $_POST['portalforumswitch'], $_POST['mainforumid'] );
        $this->UI->create_notice( __( "You have saved your portals forum settings. If you set the switch to enabled then the next step is to ensure your portal is displaying information using forum data.", 'wtgportalmanager' ), 'success', 'Small', __( 'Forum Settings Saved', 'wtgportalmanager' ) );                       
    }
    
    /**
    * Handles request from form for selecting portals sources of information
    * for display on the Updates page.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function selectupdatesources() {
        global $wtgportalmanager_settings;        
        $this->WTGPORTALMANAGER->update_portal_meta( WTGPORTALMANAGER_ADMINCURRENT, 'updatepagesources', $_POST['informationsources'] );
        $this->UI->create_notice( __( "Sources of information for your Updates page were saved. You should check your current portals Updates page and ensure it is displaying what you expect.", 'wtgportalmanager' ), 'success', 'Small', __( 'Update Sources Saved', 'wtgportalmanager' ) );                               
    }
    
    /**
    * Handles request of selecting portals sources of information
    * for display on the Activity page.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function selectactivitysources() {
        global $wtgportalmanager_settings;        
        $this->WTGPORTALMANAGER->update_portal_meta( WTGPORTALMANAGER_ADMINCURRENT, 'activitypagesources', $_POST['informationsources'] );
        $this->UI->create_notice( __( "Sources of information for your Activity page were saved. You should check your current portals Updates page and ensure it is displaying what you expect.", 'wtgportalmanager' ), 'success', 'Small', __( 'Activity Sources Saved', 'wtgportalmanager' ) );                               
    }
        
}// WTGPORTALMANAGER_Requests       
?>
