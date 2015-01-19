<?php
/**
* Beta testing only (check if in use yet) - phasing array files into classes of their own then calling into the main class
*/
class WTGPORTALMANAGER_TabMenu {
    public function menu_array() {
        $menu_array = array();
        
        ######################################################
        #                                                    #
        #                        MAIN                        #
        #                                                    #
        ######################################################
        // can only have one view in main right now until WP allows pages to be hidden from showing in
        // plugin menus. This may provide benefit of bringing user to the latest news and social activity
        // main page
        $menu_array['main']['groupname'] = 'main';        
        $menu_array['main']['slug'] = 'wtgportalmanager';// home page slug set in main file
        $menu_array['main']['menu'] = __( 'Portals Dashboard', 'wtgportalmanager' );// plugin admin menu
        $menu_array['main']['pluginmenu'] = __( 'Portals Dashboard' ,'wtgportalmanager' );// for tabbed menu
        $menu_array['main']['name'] = "main";// name of page (slug) and unique
        $menu_array['main']['title'] = 'Dashboard';// title at the top of the admin page
        $menu_array['main']['parent'] = 'parent';// either "parent" or the name of the parent - used for building tab menu         
        $menu_array['main']['tabmenu'] = false;// boolean - true indicates multiple pages in section, false will hide tab menu and show one page 
        
        ######################################################
        #                                                    #
        #                   PLUGIN UPDATE                    #
        #                                                    #
        ######################################################
        // requests user to initiate plugin update
        $menu_array['pluginupdate']['groupname'] = 'installation';        
        $menu_array['pluginupdate']['slug'] = 'wtgportalmanager_pluginupdate';// home page slug set in main file
        $menu_array['pluginupdate']['menu'] = __( 'WTG Portal Manager Update Ready', 'wtgportalmanager' );// plugin admin menu
        $menu_array['pluginupdate']['pluginmenu'] = __( 'Update Information' ,'wtgportalmanager' );// for tabbed menu
        $menu_array['pluginupdate']['name'] = "pluginupdate";// name of page (slug) and unique
        $menu_array['pluginupdate']['title'] = __( 'Update Ready', 'wtgportalmanager' );// title at the top of the admin page
        $menu_array['pluginupdate']['parent'] = 'parent';// either "parent" or the name of the parent - used for building tab menu 
        $menu_array['pluginupdate']['tabmenu'] = false;
        
        ######################################################
        #                                                    #
        #                  BUILD SECTION                     #
        #                                                    #
        ###################################################### 
  
        // build collection of pages
        $menu_array['buildpages']['groupname'] = 'buildsection';
        $menu_array['buildpages']['slug'] = 'wtgportalmanager_buildpages'; 
        $menu_array['buildpages']['menu'] = __( 'Build', 'wtgportalmanager' );
        $menu_array['buildpages']['pluginmenu'] = __( 'Portal Pages', 'wtgportalmanager' );
        $menu_array['buildpages']['name'] = "buildpages";
        $menu_array['buildpages']['title'] = __( 'Build', 'wtgportalmanager' ); 
        $menu_array['buildpages']['parent'] = 'parent';// this is the parent page 
        $menu_array['buildpages']['tabmenu'] = true;
        
        // build the blog
        $menu_array['buildblog']['groupname'] = 'buildsection';
        $menu_array['buildblog']['slug'] = 'wtgportalmanager_buildblog'; 
        $menu_array['buildblog']['menu'] = __( 'Portal Blog', 'wtgportalmanager' );
        $menu_array['buildblog']['pluginmenu'] = __( 'Portal Blog', 'wtgportalmanager' );
        $menu_array['buildblog']['name'] = "buildblog";
        $menu_array['buildblog']['title'] = __( 'Portal Blog', 'wtgportalmanager' ); 
        $menu_array['buildblog']['parent'] = 'buildpages';// pointing to another page 
        $menu_array['buildblog']['tabmenu'] = true;
        
        // build menus
        $menu_array['buildmenu']['groupname'] = 'buildsection';
        $menu_array['buildmenu']['slug'] = 'wtgportalmanager_buildmenu'; 
        $menu_array['buildmenu']['menu'] = __( 'Portal Menus', 'wtgportalmanager' );
        $menu_array['buildmenu']['pluginmenu'] = __( 'Portal Menus', 'wtgportalmanager' );
        $menu_array['buildmenu']['name'] = "buildmenu";
        $menu_array['buildmenu']['title'] = __( 'Portal Menus', 'wtgportalmanager' ); 
        $menu_array['buildmenu']['parent'] = 'buildpages'; 
        $menu_array['buildmenu']['tabmenu'] = true;

        // build sidebars
        $menu_array['buildsidebar']['groupname'] = 'buildsection';
        $menu_array['buildsidebar']['slug'] = 'wtgportalmanager_buildsidebar'; 
        $menu_array['buildsidebar']['menu'] = __( 'Portal Sidebar', 'wtgportalmanager' );
        $menu_array['buildsidebar']['pluginmenu'] = __( 'Portal Sidebar', 'wtgportalmanager' );
        $menu_array['buildsidebar']['name'] = "buildsidebar";
        $menu_array['buildsidebar']['title'] = __( 'Portal Sidebar', 'wtgportalmanager' ); 
        $menu_array['buildsidebar']['parent'] = 'buildpages'; 
        $menu_array['buildsidebar']['tabmenu'] = true;
                
        /* 
        // cross content - automatically share excerpts, media, links and comments from a page to anywhere else in the portal
        $menu_array['buildads']['groupname'] = 'buildsection';
        $menu_array['buildads']['slug'] = 'wtgportalmanager_buildads'; 
        $menu_array['buildads']['menu'] = __( 'Portals Ads', 'wtgportalmanager' );
        $menu_array['buildads']['pluginmenu'] = __( 'Portals Ads', 'wtgportalmanager' );
        $menu_array['buildads']['name'] = "buildads";
        $menu_array['buildads']['title'] = __( 'Portals Ads', 'wtgportalmanager' ); 
        $menu_array['buildads']['parent'] = 'buildpages'; 
        $menu_array['buildads']['tabmenu'] = true;
        */        
        
        /*    requires integration with WTG Ad Manager 
        // advertising
        $menu_array['buildads']['groupname'] = 'buildsection';
        $menu_array['buildads']['slug'] = 'wtgportalmanager_buildads'; 
        $menu_array['buildads']['menu'] = __( 'Portals Ads', 'wtgportalmanager' );
        $menu_array['buildads']['pluginmenu'] = __( 'Portals Ads', 'wtgportalmanager' );
        $menu_array['buildads']['name'] = "buildads";
        $menu_array['buildads']['title'] = __( 'Portals Ads', 'wtgportalmanager' ); 
        $menu_array['buildads']['parent'] = 'buildpages'; 
        $menu_array['buildads']['tabmenu'] = true;
        */    
            
        /*    requires integration with WTG FAQ Manager - individual FAQ management, WTG FAQ Manager will make use of themes short-code for FAQ output 
        // FAQ
        $menu_array['buildfaq']['groupname'] = 'buildsection';
        $menu_array['buildfaq']['slug'] = 'wtgportalmanager_buildfaq'; 
        $menu_array['buildfaq']['menu'] = __( 'Portal FAQ', 'wtgportalmanager' );
        $menu_array['buildfaq']['pluginmenu'] = __( 'Portal FAQ', 'wtgportalmanager' );
        $menu_array['buildfaq']['name'] = "buildfaq";
        $menu_array['buildfaq']['title'] = __( 'Portal FAQ', 'wtgportalmanager' ); 
        $menu_array['buildfaq']['parent'] = 'buildpages'; 
        $menu_array['buildfaq']['tabmenu'] = true;
        
        */            
        
        /*    optional integration with WTG Tasks Manager which has history functionality for building a changes list
                OR provide setting to manually type changes list
                    OR another setting to use short-code in a selected page and manage changes individally in this plugin
                     
        // Changes
        $menu_array['buildchanges']['groupname'] = 'buildsection';
        $menu_array['buildchanges']['slug'] = 'wtgportalmanager_buildchanges'; 
        $menu_array['buildchanges']['menu'] = __( 'Development Log', 'wtgportalmanager' );
        $menu_array['buildchanges']['pluginmenu'] = __( 'Development Log', 'wtgportalmanager' );
        $menu_array['buildchanges']['name'] = "buildchanges";
        $menu_array['buildchanges']['title'] = __( 'Development Log', 'wtgportalmanager' ); 
        $menu_array['buildchanges']['parent'] = 'buildpages'; 
        $menu_array['buildchanges']['tabmenu'] = true;
        */
                  
        /*    record portal changes in history, use this page to display a list of the changes for public viewing
            this will be an automatic log however this page will allow log adding, deleting and editing
                     
        // Portal Log
        $menu_array['buildlog']['groupname'] = 'buildsection';
        $menu_array['buildlog']['slug'] = 'wtgportalmanager_buildchanges'; 
        $menu_array['buildlog']['menu'] = __( 'Development Log', 'wtgportalmanager' );
        $menu_array['buildlog']['pluginmenu'] = __( 'Development Log', 'wtgportalmanager' );
        $menu_array['buildlog']['name'] = "buildchanges";
        $menu_array['buildlog']['title'] = __( 'Development Log', 'wtgportalmanager' ); 
        $menu_array['buildlog']['parent'] = 'buildpages'; 
        $menu_array['buildlog']['tabmenu'] = true;
        */
          
        ######################################################
        #                                                    #
        #                STATISTICS SECTION                  #
        #                                                    #
        ###################################################### 
        /*  
        // monthly visits graph
        $menu_array['statsmonthly']['groupname'] = 'statisticssection';
        $menu_array['statsmonthly']['slug'] = 'wtgportalmanager_betatest2'; 
        $menu_array['statsmonthly']['menu'] = __( 'Statistics', 'wtgportalmanager' );
        $menu_array['statsmonthly']['pluginmenu'] = __( 'Statistics', 'wtgportalmanager' );
        $menu_array['statsmonthly']['name'] = "betatest2";
        $menu_array['statsmonthly']['title'] = __( 'Statistics', 'wtgportalmanager' ); 
        $menu_array['statsmonthly']['parent'] = 'parent'; 
        $menu_array['statsmonthly']['tabmenu'] = true; 
  
        // monthly activity graph (new pages, new posts, edits, comments and other new content)
        $menu_array['statsactivity']['groupname'] = 'betasection';
        $menu_array['statsactivity']['slug'] = 'wtgportalmanager_statsactivity'; 
        $menu_array['statsactivity']['menu'] = __( 'All Changes', 'wtgportalmanager' );
        $menu_array['statsactivity']['pluginmenu'] = __( 'All Changes', 'wtgportalmanager' );
        $menu_array['statsactivity']['name'] = "statsactivity";
        $menu_array['statsactivity']['title'] = __( 'All Changes', 'wtgportalmanager' ); 
        $menu_array['statsactivity']['parent'] = 'statsmonthly'; 
        $menu_array['statsactivity']['tabmenu'] = true; 
  
        // monthly comments graph
        $menu_array['statscomments']['groupname'] = 'betasection';
        $menu_array['statscomments']['slug'] = 'wtgportalmanager_statscomments'; 
        $menu_array['statscomments']['menu'] = __( 'Comments', 'wtgportalmanager' );
        $menu_array['statscomments']['pluginmenu'] = __( 'Comments', 'wtgportalmanager' );
        $menu_array['statscomments']['name'] = "statscomments";
        $menu_array['statscomments']['title'] = __( 'Comments', 'wtgportalmanager' ); 
        $menu_array['statscomments']['parent'] = 'statsmonthly'; 
        $menu_array['statscomments']['tabmenu'] = true; 
  
        // monthly visits from plugin itself (requires URL to have tracking codes)
        $menu_array['statsvisits']['groupname'] = 'betasection';
        $menu_array['statsvisits']['slug'] = 'wtgportalmanager_statsvisits'; 
        $menu_array['statsvisits']['menu'] = __( 'Visits', 'wtgportalmanager' );
        $menu_array['statsvisits']['pluginmenu'] = __( 'Visits', 'wtgportalmanager' );
        $menu_array['statsvisits']['name'] = "statsvisits";
        $menu_array['statsvisits']['title'] = __( 'Visits', 'wtgportalmanager' ); 
        $menu_array['statsvisits']['parent'] = 'statsmonthly'; 
        $menu_array['statsvisits']['tabmenu'] = true; 
        */
        
        /* requires integration with WTG Digital Downloads
        // monthly download statistics 
        $menu_array['betatest6']['groupname'] = 'betasection';
        $menu_array['betatest6']['slug'] = 'wtgportalmanager_betatest6'; 
        $menu_array['betatest6']['menu'] = __( 'Beta 6', 'wtgportalmanager' );
        $menu_array['betatest6']['pluginmenu'] = __( 'Beta 6', 'wtgportalmanager' );
        $menu_array['betatest6']['name'] = "betatest6";
        $menu_array['betatest6']['title'] = __( 'Beta 6', 'wtgportalmanager' ); 
        $menu_array['betatest6']['parent'] = 'betatest2'; 
        $menu_array['betatest6']['tabmenu'] = true; 
        */
        
        /*  Use of Google and other services
        // analytics
        $menu_array['betatest7']['groupname'] = 'betasection';
        $menu_array['betatest7']['slug'] = 'wtgportalmanager_betatest7'; 
        $menu_array['betatest7']['menu'] = __( 'Beta 7', 'wtgportalmanager' );
        $menu_array['betatest7']['pluginmenu'] = __( 'Beta 7', 'wtgportalmanager' );
        $menu_array['betatest7']['name'] = "betatest7";
        $menu_array['betatest7']['title'] = __( 'Beta 7', 'wtgportalmanager' ); 
        $menu_array['betatest7']['parent'] = 'betatest2'; 
        $menu_array['betatest7']['tabmenu'] = true;      
        */
        
        ######################################################
        #                                                    #
        #                 COMMUNITY CONTROLS                 #
        #                                                    #
        ###################################################### 
        /*  
        // community overview - latest changes by community and excerpts of what they have changed
        $menu_array['communityoverview']['groupname'] = 'communitysection';
        $menu_array['communityoverview']['slug'] = 'wtgportalmanager_betatest2'; 
        $menu_array['communityoverview']['menu'] = __( 'Community', 'wtgportalmanager' );
        $menu_array['communityoverview']['pluginmenu'] = __( 'Community', 'wtgportalmanager' );
        $menu_array['communityoverview']['name'] = "communityoverview";
        $menu_array['communityoverview']['title'] = __( 'Community', 'wtgportalmanager' ); 
        $menu_array['communityoverview']['parent'] = 'parent'; 
        $menu_array['communityoverview']['tabmenu'] = true; 
  
        // community permissions- control who gets to do what on the current portal only
        $menu_array['communitypermissions']['groupname'] = 'betasection';
        $menu_array['communitypermissions']['slug'] = 'wtgportalmanager_communitypermissions'; 
        $menu_array['communitypermissions']['menu'] = __( 'Community Permissions', 'wtgportalmanager' );
        $menu_array['communitypermissions']['pluginmenu'] = __( 'Community Permissions', 'wtgportalmanager' );
        $menu_array['communitypermissions']['name'] = "communitypermissions";
        $menu_array['communitypermissions']['title'] = __( 'Community Permissions', 'wtgportalmanager' ); 
        $menu_array['communitypermissions']['parent'] = 'communityoverview'; 
        $menu_array['communitypermissions']['tabmenu'] = true; 
          
        // options for backers page (like a VIP page offering more information, downloads and other private content)
        $menu_array['communitybackers']['groupname'] = 'betasection';
        $menu_array['communitybackers']['slug'] = 'wtgportalmanager_communitybackers'; 
        $menu_array['communitybackers']['menu'] = __( 'Backers Settings', 'wtgportalmanager' );
        $menu_array['communitybackers']['pluginmenu'] = __( 'Backers Settings', 'wtgportalmanager' );
        $menu_array['communitybackers']['name'] = "communitybackers";
        $menu_array['communitybackers']['title'] = __( 'Backers Settings', 'wtgportalmanager' ); 
        $menu_array['communitybackers']['parent'] = 'communityoverview'; 
        $menu_array['communitybackers']['tabmenu'] = true;
                  
        // options for freelancers page (requires integration with WTG Tasks Manager - page will list tasks and prices)
        $menu_array['communityfreelancers']['groupname'] = 'betasection';
        $menu_array['communityfreelancers']['slug'] = 'wtgportalmanager_communitybackers'; 
        $menu_array['communityfreelancers']['menu'] = __( 'Backers Settings', 'wtgportalmanager' );
        $menu_array['communityfreelancers']['pluginmenu'] = __( 'Backers Settings', 'wtgportalmanager' );
        $menu_array['communityfreelancers']['name'] = "communitybackers";
        $menu_array['communityfreelancers']['title'] = __( 'Backers Settings', 'wtgportalmanager' ); 
        $menu_array['communityfreelancers']['parent'] = 'communityoverview'; 
        $menu_array['communityfreelancers']['tabmenu'] = true; 
 
        */     
           
        ######################################################
        #                                                    #
        #                   WEB SERVICES                     #
        #                                                    #
        ###################################################### 
        /*  
        // twitter - setup authorization, filter options and options regarding where to display tweets in portal
        $menu_array['servicestwitter']['groupname'] = 'webservices';
        $menu_array['servicestwitter']['slug'] = 'wtgportalmanager_servicestwitter'; 
        $menu_array['servicestwitter']['menu'] = __( 'Twitter', 'wtgportalmanager' );
        $menu_array['servicestwitter']['pluginmenu'] = __( 'Twitter', 'wtgportalmanager' );
        $menu_array['servicestwitter']['name'] = "servicestwitter";
        $menu_array['servicestwitter']['title'] = __( 'Twitter', 'wtgportalmanager' ); 
        $menu_array['servicestwitter']['parent'] = 'parent'; 
        $menu_array['servicestwitter']['tabmenu'] = true; 
  
        // facebook - auth, filter and position options
        $menu_array['servicesfacebook']['groupname'] = 'webservices';
        $menu_array['servicesfacebook']['slug'] = 'wtgportalmanager_servicesfacebook'; 
        $menu_array['servicesfacebook']['menu'] = __( 'Facebook', 'wtgportalmanager' );
        $menu_array['servicesfacebook']['pluginmenu'] = __( 'Facebook', 'wtgportalmanager' );
        $menu_array['servicesfacebook']['name'] = "servicesfacebook";
        $menu_array['servicesfacebook']['title'] = __( 'Facebook', 'wtgportalmanager' ); 
        $menu_array['servicesfacebook']['parent'] = 'servicestwitter'; 
        $menu_array['servicesfacebook']['tabmenu'] = true; 
         
        // google - analytics for webmasters and docs to offer more content 
        $menu_array['servicesgoogle']['groupname'] = 'webservices';
        $menu_array['servicesgoogle']['slug'] = 'wtgportalmanager_servicesgoogle'; 
        $menu_array['servicesgoogle']['menu'] = __( 'Google', 'wtgportalmanager' );
        $menu_array['servicesgoogle']['pluginmenu'] = __( 'Google', 'wtgportalmanager' );
        $menu_array['servicesgoogle']['name'] = "servicesgoogle";
        $menu_array['servicesgoogle']['title'] = __( 'Google', 'wtgportalmanager' ); 
        $menu_array['servicesgoogle']['parent'] = 'servicestwitter'; 
        $menu_array['servicesgoogle']['tabmenu'] = true; 
        */
             
        return $menu_array;
    }
} 
?>
