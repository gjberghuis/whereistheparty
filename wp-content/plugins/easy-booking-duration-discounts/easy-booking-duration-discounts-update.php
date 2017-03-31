<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Discounts_Update' ) ) :

class Easy_Booking_Discounts_Update {
	var $settings;
	
	/*
	*  Constructor
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function __construct() {
		
		$plugin_settings = ( is_multisite() ) ? get_option('easy_booking_global_settings') : get_option('easy_booking_discounts_settings');

		$license_key = isset( $plugin_settings['easy_booking_discounts_license_key'] ) ? esc_html( $plugin_settings['easy_booking_discounts_license_key'] ) : '';

		// vars
		$this->settings = array(
			'version'	=>	'',
			'remote'	=>	'http://download.herownsweetcode.com/easy-booking-duration-discounts',
			'basename'	=>	plugin_basename( str_replace('-update.php', '.php', __FILE__) ),
			'slug'		=>	dirname( plugin_basename( str_replace('-update.php', '.php', __FILE__) ) ),
			'key'		=>  $license_key
		);

		// actions
		add_action('in_plugin_update_message-' . $this->settings['basename'], array($this, 'in_plugin_update_message'), 10, 2 );
		
		// filters
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
		add_filter('plugins_api', array($this, 'check_info'), 10, 3);
	}

	/*
	*  in_plugin_update_message
	*
	*  Displays an update message for plugin list screens.
	*  Shows only the version updates from the current until the newest version
	*
	*  @type	function
	*  @date	5/06/13
	*
	*  @param	{array}		$plugin_data
	*  @param	{object}	$r
	*/

	function in_plugin_update_message( $plugin_data, $r ) {
		// vars
		$valid_key = $r->valid_key;

        $o = '';

        if ( ! $valid_key ) {

        	// add style	
			$o .= '<style type="text/css">';
			$o .= '.easy-booking-discounts-plugin-update-info { background: #EAF2FA; border: #C7D7E2 solid 1px; margin-top: 5px; padding: 10px; }';
			$o .= '.widefat td .easy-booking-discounts-plugin-update-info p { margin: 0 }';
			$o .= '</style>';

			$o .= '<div class="easy-booking-discounts-plugin-update-info">';
			$o .= '<p>' . __('Your license key is empty or invalid. ', 'easy_booking_discounts');
			$o .= '<a href="admin.php?page=easy-booking-discounts">' . __('Save your license key', 'easy_booking_discounts') . '</a>';
			$o .= __(' and ', 'easy_booking_discounts' );
			$o .= '<a href="update-core.php">' . __('Recheck updates.', 'easy_booking_discounts') . '</a>';
			$o .= '</p>';
			$o .= '</div>';

        }

        $update_message = isset( $r->update_message ) ? $r->update_message : '';

        if ( ! empty( $update_message ) ) {

        	$o .= '<style type="text/css">';
			$o .= '.widefat td p.ebdd_plugin_message { margin-top: 0.5em; padding: 0.5em 1em; background: #d54e21; color: #FFFFFF; }';
			$o .= 'p.ebdd_plugin_message .dashicons { margin-right: 0.5em; }';
			$o .= '</style>';
        	$o .= '<p class="ebdd_plugin_message"><span class="dashicons dashicons-warning"></span>';
        	$o .= esc_html( $update_message );
        	$o .= '</p>';

        }

        echo $o;
        
	}
	
	
	/*
	*  get_remote
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 31/01/13
	*/
	
	function get_remote() {
		// vars
        $info = false;
        
		// Get the remote info
        $request = wp_remote_post( $this->settings['remote'] . '/?key=' . $this->settings['key'] );
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
            $info = @unserialize($request['body']);
            $info->plugin = $this->settings['slug'];
        }
        
        return $info;
	}
	
	
	/*
	*  check_update
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 31/01/13
	*/
	
	function check_update( $transient ) {
	    if ( empty( $transient ) ) {
            return $transient;
        }
        
        // vars
        $info = $this->get_remote();
        
        // validate
        if ( ! $info ) {
	        return $transient;
        }
        
        // compare versions
        if ( version_compare( $info->version, $this->get_version(), '<=' ) ) {
        	return $transient;
        }
        
        // create new object for update
		$obj                 = new stdClass();
		$obj->slug           = $info->plugin;
		$obj->plugin         = $info->plugin;
		$obj->new_version    = $info->version;
		$obj->url            = $info->homepage;
		$obj->valid_key      = $info->valid_key;
		$obj->update_message = $info->update_message;


        if ( $info->valid_key ) {
        	$obj->package = $info->download_link;
        } else {
        	$obj->package = false;
        }
        
        // add to transient
        $transient->response[ $this->settings['basename'] ] = $obj;
        
        return $transient;
	}
	
	
	/*
	*  check_info
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 31/01/13
	*/
	
    function check_info( $false, $action, $arg ) {
    	// validate
    	if ( ! isset( $arg->slug ) || $arg->slug != $this->settings['slug'] ) {
	    	return $false;
    	}
    	
    	if ( $action == 'plugin_information' ) {
	    	$false = $this->get_remote();
    	}
    	        
        return $false;
    }
    
    
    /*
    *  get_version
    *
    *  This function will return the current version of this add-on 
    *
    *  @type	function
    *  @date	27/08/13
    *
    *  @param	N/A
    *  @return	(string)
    */
    
    function get_version() {
    	// populate only once
    	if ( ! $this->settings['version'] ) {
	    	$plugin_data = get_plugin_data( str_replace('-update.php', '.php', __FILE__) );
	    	$this->settings['version'] = $plugin_data['Version'];
    	}
    	
    	// return
    	return $this->settings['version'];
	}

}

// instantiate
if ( is_admin() ) {
	new Easy_Booking_Discounts_Update();
}

endif;

?>