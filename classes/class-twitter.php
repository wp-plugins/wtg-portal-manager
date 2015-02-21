<?php
/** 
* WebTechGlobal Twitter Class for use in WordPress plugins only.
* 
* @package WTG Portal Manager
* @author Ryan Bayne   
* @since 0.0.1
*/

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if (!class_exists('TwitterOAuth')) {
  require_once( WTGPORTALMANAGER_ABSPATH . 'oauth/twitteroauth.php');                  
} else {
  define('WTG_USING_EXISTING_LIBRARY_TWITTEROAUTH',true);
}

/**
* WebTechGlobal Twitter Class for use in WordPress.
* 
* @author Ryan R. Bayne
* @package WTG Portal Manager
* @since 0.0.1
* @version 1.0
*/
class WTGPORTALMANAGER_Twitter {
    
    private $defaults = array(
        'directory' => '',
        'key' => '',
        'secret' => '',
        'token' => '',
        'token_secret' => '',
        'screenname' => '',
        'cache_expire' => 3600      
    ); 
    
    public $st_last_error = false;

    function __construct( $args = array() ) {
        // if WTGPORTALMANAGER_Twitter is loaded without the last parameter it will result in $args == null
        if( $args === null ){ $args = array(); }
        $this->defaults = array_merge($this->defaults, $args);
    }
  
    function __toString() {
        return print_r($this->defaults, true);
    }

    /**
    * Call this in theme to get Tweets.
    * 
    * The first plugin to get this class is WTG Portal Manager. I have
    * created it with consideration that portals could benefit from
    * their own set of keys.
    * 
    * @author Ryan R. Bayne
    * @package WTG Portal Manager
    * @since 0.0.1
    * @version 1.0
    * 
    * @param mixed $username
    * @param mixed $count
    * @param mixed $options
    * @param mixed $application use to call a set of API keys
    */
    public function startTwitter( $username = false, $count = 20, $options = false, $application = 'default' ) {
        global $wtgportalmanager_settings;

        $this->defaults['cache_expire'] = 3600;
        $this->defaults['directory'] = plugin_dir_path(__FILE__);
                      
        /*
            1. The values below could be in-line right here if this class was to be used for a single fee.
            2. Might be able to use $options for a more multi-account approach.
            3. For my needs I want to pass a single value ($application) and dynamically retrieve accounts within this method. 
        */
               
        // consumer key
        $this->defaults['key'] = $wtgportalmanager_settings['api']['twitter']['apps'][ $application ]['consumer_key'];
        if( !$this->defaults['key'] ) { return false; }
        
        // consumer secret
        $this->defaults['secret'] = $wtgportalmanager_settings['api']['twitter']['apps'][$application]['consumer_secret'];
        if( !$this->defaults['secret'] ) { return false; }
        
        // access token
        $this->defaults['token'] = $wtgportalmanager_settings['api']['twitter']['apps'][$application]['access_token'];
        if( !$this->defaults['token'] ) { return false; }
        
        // token secret
        $this->defaults['token_secret'] = $wtgportalmanager_settings['api']['twitter']['apps'][$application]['token_secret'];
        if( !$this->defaults['token_secret'] ) { return false; }
        
        // screen name i.e. WebTechGlobal
        $this->defaults['screenname'] = $wtgportalmanager_settings['api']['twitter']['apps'][$application]['screenname'];
        if( !$this->defaults['screenname'] ) { return false; }                                                                  

        $res = $this->getTweets( $username, $count, $options );
        update_option( 'portal_last_twitteraip_error',$this->st_last_error );// all portals update this
        return $res;
    }
        
    function getTweets($screenname = false,$count = 20,$options = false) {
        // BC: $count used to be the first argument
        if (is_int($screenname)) {
            list($screenname, $count) = array($count, $screenname);
        }

        if ($count > 20) $count = 20;
        if ($count < 1) $count = 1;

        $default_options = array('trim_user'=>true, 'exclude_replies'=>true, 'include_rts'=>false);

        if ($options === false || !is_array($options)) 
        {
            $options = $default_options;
        } 
        else 
        {
            $options = array_merge($default_options, $options);
        }

        if ($screenname === false || $screenname === 20) $screenname = $this->defaults['screenname'];

        $result = $this->checkValidCache($screenname,$options);

        if ($result !== false) {
            return $this->cropTweets($result,$count);
        }

        //If we're here, we need to load.
        $result = $this->oauthGetTweets($screenname,$options);

        if (is_array($result) && isset($result['errors'])) 
        {
            if (is_array($result) && isset($result['errors'][0]) && isset($result['errors'][0]['message'])) 
            {
                $last_error = $result['errors'][0]['message'];
            } 
            else 
            {
                $last_error = $result['errors'];
            }
            
            return array('error'=>'Twitter said: '.json_encode($last_error));
        } 
        else 
        {
            if (is_array($result)) 
            {
                return $this->cropTweets($result,$count);
            } 
            else 
            {
                $last_error = 'Something went wrong with the twitter request: '.json_encode($result);
                return array('error'=>$last_error);
            }
        }

    }

    private function cropTweets($result,$count) {
        
        // on 20th Feb 2015 $result was not an array and caused error. After refresh the problem was gone.
        if( !is_array( $result ) ) { return false; }
        
        return array_slice($result, 0, $count);
    }

    private function getCacheLocation() {
        return $this->defaults['directory'].'.tweetcache';
    }

    private function getOptionsHash($options) {
        $hash = md5(serialize($options));
        return $hash;
    }

    private function checkValidCache($screenname,$options) {
        $file = $this->getCacheLocation();
        if (is_file($file)) 
        {
            $cache = file_get_contents($file);
            $cache = @json_decode($cache,true);

            if (!isset($cache)) {
                unlink($file);
                return false;
            }

            // Delete the old cache from the first version, before we added support for multiple usernames
            if (isset($cache['time'])) {
                unlink($file);
                return false;
            }

            $cachename = $screenname."-".$this->getOptionsHash($options);

            //Check if we have a cache for the user.
            if (!isset($cache[$cachename]))
            {
                return false;
            }
            
            if (!isset($cache[$cachename]['time']) || !isset($cache[$cachename]['tweets'])) 
            {
                unset($cache[$cachename]);
                file_put_contents($file,json_encode($cache));
                return false;
            }

            if ($cache[$cachename]['time'] < (time() - $this->defaults['cache_expire'])) 
            {                    
                $result = $this->oauthGetTweets($screenname,$options);
                if (!isset($result['errors'])) 
                {
                  return $result;
                }
            }
            
            return $cache[$cachename]['tweets'];
        } 
        else 
        {
            return false;
        }
    }

    private function oauthGetTweets($screenname,$options) {
        $key = $this->defaults['key'];     
        $secret = $this->defaults['secret'];
        $token = $this->defaults['token'];
        $token_secret = $this->defaults['token_secret'];

        $cachename = $screenname."-".$this->getOptionsHash($options);

        $options = array_merge($options, array('screen_name' => $screenname, 'count' => 20));

        if (empty($key)) return array('error'=>'Missing Consumer Key - Check Settings');
        if (empty($secret)) return array('error'=>'Missing Consumer Secret - Check Settings');
        if (empty($token)) return array('error'=>'Missing Access Token - Check Settings');
        if (empty($token_secret)) return array('error'=>'Missing Access Token Secret - Check Settings');
        if (empty($screenname)) return array('error'=>'Missing Twitter Feed Screen Name - Check Settings');

        $connection = new TwitterOAuth($key, $secret, $token, $token_secret);
        $result = $connection->get('statuses/screenname', $options);

        if (is_file($this->getCacheLocation())) {
            $cache = json_decode(file_get_contents($this->getCacheLocation()),true);
        }

        if (!isset($result['errors'])) 
        {
            $cache[$cachename]['time'] = time();
            $cache[$cachename]['tweets'] = $result;
            $file = $this->getCacheLocation();
            file_put_contents($file,json_encode($cache));
        } 
        else
        {
            if (is_array($result) && isset($result['errors'][0]) && isset($result['errors'][0]['message'])) 
            {
                $last_error = '['.date('r').'] Twitter error: '.$result['errors'][0]['message'];
                $this->st_last_error = $last_error;
            } 
            else 
            {
                $last_error = '['.date('r').'] Twitter returned an invalid response. It is probably down.';
                $this->st_last_error = $last_error;
            }
        }

        return $result;
    }      
}
?>