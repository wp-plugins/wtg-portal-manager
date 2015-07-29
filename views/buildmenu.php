<?php
/**
 * Build menus important to the current active portal.  
 *
 * @package WTG Portal Manager
 * @subpackage Views
 * @author Ryan Bayne   
 * @since 0.0.1
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Build menus important to the current active portal.  
 *
 * @package WTG Portal Manager
 * @subpackage Views
 * @author Ryan Bayne
 * @since 0.0.1
 */
class WTGPORTALMANAGER_Buildmenu_View extends WTGPORTALMANAGER_View {

    /**
     * Number of screen columns for post boxes on this screen
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected $screen_columns = 2;
    
    protected $view_name = 'buildmenu';

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
            array( $this->view_name . '-menulist', __( 'List Menus', 'wtgportalmanager' ), array( $this, 'parent' ), 'normal', 'default', array( 'formid' => 'menulist' ), true, 'activate_plugins' ),
         );    
    }
        
    /**
    * Set up the view with data and do things that are specific for this view
    *
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.11
    * @version 1.0
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
        
        parent::setup( $action, $data );
        
        // introduction to testing
        $this->add_meta_box( 'buildmenu-addmenu', __( 'Add Menu', 'wtgportalmanager' ), array( $this, 'parent' ), 'normal', 'default', array( 'formid' => 'addmenu' ) );      

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
    * Menu for assigning registered menu to portal. The intention is for the portal
    * manager to present/offer menus in different ways and different places.
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_buildmenu_addmenu( $data, $box ) {    
        $introduction = __( 'Link a registered menu to the current active portal. A relationship with the menu will allow the portal manager to use the menus data.', 'wtgportalmanager' );
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], $introduction, false );        
        $this->Forms->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );                            
            
        echo '<table class="form-table">';
        
        // get users menus
        $menu_terms_array = get_terms( 'nav_menu', array( 'hide_empty' => false ) ); 
        
        // build array of items for menu (key must be the value that is submitted for storage by form)
        $menus = array();
        foreach( $menu_terms_array as $key => $term ) {
            $menus[ $term->term_id ] = $term->name;
        }
          
        $this->Forms->input( $box['args']['formid'], 'menu', 'selectedmenu', 'selectedmenu', 'Select Registered Menu', 'Select Registered Menu', true, '', array( 'itemsarray' => $menus, 'defaultvalue' => 'notselected123', 'defaultitem_name' => __( 'Menu Not Selected', 'wtgportalmanager' ) ) );
        $this->Forms->checkboxes_basic( $box['args']['formid'], 'ismainmenu', 'ismainmenu', __( 'Portals Main Menu', 'wtgportalmanager' ), array( 'ismain' => 'Set As Main' ), array(), false, array(), false );
        
        echo '</table>';
        
        $this->UI->postbox_content_footer();
    }
          
    /**
    * List of menus assigned to the current portal.
    * 
    * @author Ryan Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    */
    public function postbox_buildmenu_menulist( $data, $box ) {    
        $introduction = __( 'A list of registered menus that have been assigned to the current portal. You may remove menus from the portal here but do so with care as always.', 'wtgportalmanager' );
        $this->UI->postbox_content_header( $box['title'], $box['args']['formid'], $introduction, false );        
        $this->Forms->form_start( $box['args']['formid'], $box['args']['formid'], $box['title'] );                            

        echo '<table class="form-table">';
        

        echo '</table>';
        
        $this->UI->postbox_content_footer( __( 'Remove Selected Menus', 'wtgportalmanager' ) );
    }
                           
}?>