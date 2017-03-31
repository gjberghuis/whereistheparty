<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Discounts_Settings' ) ) :

class Easy_Booking_Discounts_Settings {

	public function __construct() {

		// get plugin options values
		$this->options = get_option( 'easy_booking_discounts_settings' );
		
		// initialize options the first time
		if ( ! $this->options ) {
		
		    $this->options = array(
		    	'easy_booking_discounts_license_key'  => '',
		    	'easy_booking_discounts_mode'         => 'normal',
		    	'easy_booking_discounts_display'      => '',
		    	'easy_booking_discounts_display_mode' => 'reduction',
		    	'easy_booking_global_discounts'       => array(),
		    );

		    add_option( 'easy_booking_discounts_settings', $this->options );

		}

		// Backward compatibility
		if ( ! isset( $this->options['easy_booking_global_discounts'] ) ) {
			$this->options['easy_booking_global_discounts'] = array();
			update_option('easy_booking_discounts_settings', $this->options);
		}

		if ( is_multisite() ) {
			
			$this->global_settings = get_option( 'easy_booking_global_settings' );

			if ( ! isset( $this->global_settings['easy_booking_discounts_license_key'] ) ) {
				$this->global_settings['easy_booking_discounts_license_key'] = '';
			}

			update_option( 'easy_booking_global_settings', $this->global_settings );

		}
		
		if ( is_admin() ) {

			add_action( 'admin_menu', array( $this, 'ebdd_add_setting_page' ), 10 );
			add_action( 'admin_init', array( $this, 'ebdd_admin_init' ) );

		}

		$license_set = ! isset( $this->options['easy_booking_discounts_license_key'] ) || empty( $this->options['easy_booking_discounts_license_key'] ) ? false : true;

		if ( get_option( 'easy_booking_display_notice_ebdd_license' ) !== '1' && ! $license_set ) {
			update_option( 'easy_booking_display_notice_ebdd_license', 0 );
		} else {
			update_option( 'easy_booking_display_notice_ebdd_license', '1' );
		}

	}

	/**
	 * Add settings page
	 *
	 */
	public function ebdd_add_setting_page() {
		$option_page = add_submenu_page(
			'easy-booking',
			'Duration Discounts',
			'Duration Discounts',
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-discounts',
			array( $this, 'easy_booking_discounts_option_page' )
		);

		// load scripts on this page only
		add_action( 'admin_print_scripts-'. $option_page, array( $this, 'ebdd_load_settings_scripts' ) );
	}

	/**
	 * Load scripts and styles
	 *
	 */
	public function ebdd_load_settings_scripts() {
		wp_enqueue_script( 'jquery-tiptip' );

		wp_enqueue_script( 'ebdd-discounts' );

		wp_enqueue_style( 'woocommerce_admin_styles' );

		wp_enqueue_style( 'ebdd-settings' );
	}

	/**
	 * Plugin settings init
	 *
	 */
	public function ebdd_admin_init() {

		$this->ebdd_settings();

		// If multisite, save the license key on the network, not the sites.
		if ( is_multisite() ) {
			$this->ebdd_network_settings();
		}

	}

	/**
	 * Plugin settings
	 *
	 */
	private function ebdd_settings() {
		include_once( 'includes/ebdd-settings.php' );
	}

	/**
	 * Network plugin settings (for multisites)
	 *
	 */
	private function ebdd_network_settings() {
		include_once( 'includes/ebdd-network-settings.php' );
	}

	/**
	 * Settings section (intro - empty for the moment)
	 *
	 */
	public function easy_booking_discounts_section_general() {
		return;
	}

	/**
	 * Settings page content
	 *
	 */
	public function easy_booking_discounts_option_page() {
		include_once( 'views/html-ebdd-settings.php' );
	}

	/**
	 * License key field
	 *
	 */
	public function easy_booking_discounts_license_key() {
		wceb_settings_input( array(
			'type'              => 'text',
			'id'                => 'easy_booking_discounts_license_key',
			'name'              => 'easy_booking_discounts_settings[easy_booking_discounts_license_key]',
			'value'             => isset( $this->options['easy_booking_discounts_license_key'] ) ? $this->options['easy_booking_discounts_license_key'] : '',
			'description'       => __('Enter your license key', 'easy_booking_discounts'),
			'custom_attributes' => array(
				'size' => '40'
			)
		));
	}

	/**
	 * Discount mode field
	 *
	 */
	public function easy_booking_discounts_mode() {
		wceb_settings_select( array(
			'id'          => 'easy_booking_discounts_mode',
			'name'        => 'easy_booking_discounts_settings[easy_booking_discounts_mode]',
			'value'       => isset( $this->options['easy_booking_discounts_mode'] ) ? $this->options['easy_booking_discounts_mode'] : 'normal',
			'description' => __('Choose whether to cumulate discounts when calculating price, or apply only the one corresponding to the selected duration.' , 'easy_booking_discounts'),
			'options'     => array(
				'normal'     => __('Normal', 'easy_booking_discounts'),
				'cumulative' => __('Cumulative', 'easy_booking_discounts')
			)
		));
	}

	/**
	 * Discount display field
	 *
	 */
	public function easy_booking_discounts_display() {
		wceb_settings_checkbox( array(
			'id'          => 'easy_booking_discounts_display',
			'name'        => 'easy_booking_discounts_settings[easy_booking_discounts_display]',
			'description' => __('Display duration discounts on the product page.', 'easy_booking_discounts'),
			'value'       => isset( $this->options['easy_booking_discounts_display'] ) ? $this->options['easy_booking_discounts_display'] : '',
			'cbvalue'     => 'on'
		));
	}

	/**
	 * Discount display mode field
	 *
	 */
	public function easy_booking_discounts_display_mode() {
		wceb_settings_select( array(
			'id'          => 'easy_booking_discounts_display_mode',
			'name'        => 'easy_booking_discounts_settings[easy_booking_discounts_display_mode]',
			'value'       => isset( $this->options['easy_booking_discounts_display_mode'] ) ? $this->options['easy_booking_discounts_display_mode'] : 'reduction',
			'description' => __('Choose to display either the reduction applied, or the discounted price (for single product discounts only). E.g for a 10&euro; product : -10% or 9&euro;.' , 'easy_booking_discounts'),
			'options'     => array(
				'reduction' => __('Reduction', 'easy_booking_discounts'),
				'price'     => __('Price', 'easy_booking_discounts')
			)
		));
	}

	/**
	 * Global discounts fields (applied to all products)
	 *
	 */
	public function easy_booking_discounts_global_discounts() {
		$discounts = $this->options['easy_booking_global_discounts'];

        $columns = array(
		    array(
		        'title'   => __( 'Action', 'easy_booking_discounts' ),
		        'name'    => 'action',
		        'content' => array(
		            'function' => 'wceb_settings_select',
		            'data'     => array(
		                'name'    => 'easy_booking_discounts_settings[easy_booking_global_discounts][ebdd_discount_action][]',
		                'options' => array(
		                    'ebdd_reduction' => __('Reduction', 'easy_booking_discounts'),
		                    'ebdd_surcharge' => __('Surcharge', 'easy_booking_discounts')
		                ),
		                'echo'    => false
		            )
		        )
		    ),
		    array(
		        'title'   => __( 'Amount', 'easy_booking_discounts' ),
		        'name'    => 'amount',
		        'content' => array(
		            'function' =>'wceb_settings_input',
		            'data'     => array(
		                'type'              => 'number',
		                'name'              => 'easy_booking_discounts_settings[easy_booking_global_discounts][ebdd_discount_amount][]',
		                'class'             => 'input_text',
		                'placeholder'       => __( 'Discount Amount', 'easy_booking_discounts' ),
		                'custom_attributes' => array(
		                    'min' => 0
		                ),
		                'echo'              => false
		            )
		        )
		    ),
		    array(
		        'title'   => __( 'Type', 'easy_booking_discounts' ),
		        'name'    => 'type',
		        'content' => array(
		            'function' => 'wceb_settings_select',
		            'data'     => array(
		                'name'    => 'easy_booking_discounts_settings[easy_booking_global_discounts][ebdd_discount_type][]',
		                'options' => array(
							'ebdd_single_percent' => __( '1 day &#37; discount', 'easy_booking_discounts'),
							'ebdd_percent'        => __( 'Total &#37; discount', 'easy_booking_discounts'),
							'ebdd_single_fixed'   => __( '1 day fixed discount', 'easy_booking_discounts'),
							'ebdd_fixed'          => __( 'Total fixed discount', 'easy_booking_discounts' )
		                ),
		                'echo'    => false
		            )
		        )
		    ),
		    array(
		        'title'   => __( 'From &hellip; ', 'easy_booking_discounts' ),
		        'tip'     => __( 'Day(s), week(s) or custom booking period(s). It depends on the global and / or product settings.', 'easy_booking_discounts' ),
		        'name'    => 'from',
		        'content' => array(
		            'function' => 'wceb_settings_input',
		            'data'     => array(
		                'type'              => 'number',
		                'name'              => 'easy_booking_discounts_settings[easy_booking_global_discounts][ebdd_discount_from][]',
		                'class'             => 'input_text',
		                'placeholder'       => __( 'From &hellip; day(s)', 'easy_booking_discounts' ),
		                'custom_attributes' => array(
		                    'min' => 0
		                ),
		                'echo'              => false
		            )
		        )
		    ),
		    array(
		        'title'   => __( 'To &hellip; ', 'easy_booking_discounts' ),
		        'tip'     => __( 'Day(s), week(s) or custom booking period(s). It depends on the global and / or product settings.', 'easy_booking_discounts' ),
		        'name'    => 'to',
		        'content' => array(
		            'function' => 'wceb_settings_input',
		            'data'     => array(
		                'type'              => 'number',
		                'name'              => 'easy_booking_discounts_settings[easy_booking_global_discounts][ebdd_discount_to][]',
		                'class'             => 'input_text',
		                'placeholder'       => __( 'To &hellip; day(s)', 'easy_booking_discounts' ),
		                'custom_attributes' => array(
		                    'min' => 0
		                ),
		                'echo'              => false
		            )
		        )
		    )
		);

    // Display html
	include_once( 'views/html-ebdd-global-discounts.php' );
	
	}

	/**
	 * Display multisite settings on a special page (created from Easy Booking)
	 *
	 */
	public function easy_booking_discounts_multisite_settings() {
		do_settings_sections('easy_booking_discounts_multisite_settings');
	}

	/**
	 * License key field for multisites
	 *
	 */
	public function easy_booking_discounts_multisite_license_key() {
		wceb_settings_input( array(
			'type'              => 'text',
			'id'                => 'easy_booking_discounts_license_key',
			'name'              => 'easy_booking_discounts_settings[easy_booking_discounts_license_key]',
			'value'             => isset( $this->global_settings['easy_booking_discounts_license_key'] ) ? $this->global_settings['easy_booking_discounts_license_key'] : '',
			'description'       => __('Enter your license key', 'easy_booking_discounts'),
			'custom_attributes' => array(
				'size' => '40'
			)
		));
	}

	/**
	 * Sanitize settings
	 *
	 */
	public function sanitize_values( $settings ) {

		$global_discounts = array();

		foreach ( $settings as $key => $value ) {

			if ( $key === 'easy_booking_global_discounts' ) {

				$discounts = array(
		            'actions' => $settings['easy_booking_global_discounts']['ebdd_discount_action'],
		            'amounts' => $settings['easy_booking_global_discounts']['ebdd_discount_amount'],
		            'types'   => $settings['easy_booking_global_discounts']['ebdd_discount_type'],
		            'from'    => $settings['easy_booking_global_discounts']['ebdd_discount_from'],
		            'to'      => $settings['easy_booking_global_discounts']['ebdd_discount_to'],
		        );

				// Format and sanitize data
		        $global_discounts = ebdd_format_discounts_for_saving( $discounts );

			} else {
				$settings[$key] = esc_html( $value );
			}
			
		}

		// Save global discounts
		$settings['easy_booking_global_discounts'] = $global_discounts;

		return $settings;
	}

}

return new Easy_Booking_Discounts_Settings();

endif;