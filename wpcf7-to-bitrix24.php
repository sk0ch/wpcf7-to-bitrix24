<?php
/**
 * Plugin Name: Contact form 7 to Bitrix24
 * Plugin URI: http://skoch.com.ua/
 * Description: Contact form 7 to Bitrix24
 * Author: Webolatory Team
 * Author URI: http://webolatory.com/
 * Text Domain: wpcf7-to-bitrix24
 * Version: 1.0
 * Domain Path: /languages/
 * License: GPL v3
*/

/**
 * Contact form 7 to Bitrix24
 * Copyright (C) 2018, Webolatory - a.skoch@webolatory.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined( 'ABSPATH' ) || die;

/**
 * Init
 * */
class wpcf7_to_bitrix24 {

	const PAGE 			= 'wpcf7_to_bitrix24';
	const SECTION_ID 	= 'wpcf7-to-bitrix24-settings';

	/**
	 * Constructor
	*/
	function __construct() {

		// Add filter
		//add_filter( 'wpcf7_posted_data', array( __CLASS__, 'wpcf7_sent_to_bitrix' ), 10, 1 );
		
		add_filter( 'wpcf7_mail_sent', array( __CLASS__, 'wpcf7_sent_to_bitrix' ), 10, 1 );
		
		

		// Add admin settings page
		add_action( 'admin_menu', 						array( __CLASS__, 'register_settings_page' ) );
		add_action( 'admin_init', 	array( __CLASS__, 'register_options' ) );
		add_action( 'admin_init', 						array( __CLASS__, 'display_custom_options_page_fields' ) );
	}
	
	/**
	 * Register settings page
	 *
	 * @return Void
	 */
	static function register_settings_page() {

		add_submenu_page(
			'tools.php',
			__( 'Bitrix24 Settings' ),
			__( 'Bitrix24 Settings' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'bitrix24_settings_page' )
		);
	}
	
	/**
	 * Show Google settings page
	 *
	 * @return Void
	 */
	static function bitrix24_settings_page() {

		if ( is_super_admin() && ( isset( $_POST['disable_send_data'] ) || isset( $_POST['bitrix_url'] ) || isset( $_POST['bitrix_login'] ) || isset( $_POST['bitrix_password'] ) ) ) {

			// Save options
			self::save_settings( $_POST );
		}

		?>
		<div class="wrap">
			<form method="post">
				<?php

				settings_fields( self::SECTION_ID );
				do_settings_sections( self::PAGE );
				submit_button();

				?>
			</form>
		</div>
		<?php

	}

	/**
	 * Save settings
	 *
	 * @return Void
	 */
	static function save_settings( $data ) {
		
		$settings = array(
			'disable_send_data'	=> isset( $data['disable_send_data'] ) 	? absint( $data['disable_send_data'] ) : null,
			'bitrix_url'		=> isset( $data['bitrix_url'] ) 		? esc_url( $data['bitrix_url'] ) : null,
			'bitrix_login'		=> isset( $data['bitrix_login'] ) 		? sanitize_text_field( $data['bitrix_login'] ) : null,
			'bitrix_password'	=> isset( $data['bitrix_password'] ) 	? sanitize_text_field( $data['bitrix_password'] ) : null,
		);

		update_option( self::PAGE, $settings );
	}

	/**
	 * Register options on Custom options page
	 *
	 * @return Void
	 */
	static function display_custom_options_page_fields() {

		add_settings_section( self::SECTION_ID, __( 'Bitrix24 Settings', 'hubbli' ), null, self::PAGE );
	}

	/**
	 * Register options
	 *
	 * @return Void
	 */
	static function register_options() {

		// Enable send data
		add_settings_field(
			'disable_send_data',
			__( 'Disable send data', 'wpcf7-to-bitrix24' ),
			array( __CLASS__, 'disable_send_data' ),
			self::PAGE,
			self::SECTION_ID
		);
		register_setting( self::SECTION_ID, 'disable_send_data' );

		// Bitrix URL
		add_settings_field(
			'bitrix_url',
			__( 'Bitrix URL', 'wpcf7-to-bitrix24' ),
			array( __CLASS__, 'bitrix_url' ),
			self::PAGE,
			self::SECTION_ID
		);
		register_setting( self::SECTION_ID, 'bitrix_url' );

		// Bitrix Login
		add_settings_field(
			'bitrix_login',
			__( 'Bitrix login', 'wpcf7-to-bitrix24' ),
			array( __CLASS__, 'bitrix_login' ),
			self::PAGE,
			self::SECTION_ID
		);
		register_setting( self::SECTION_ID, 'bitrix_login' );

		// Bitrix Password
		add_settings_field(
			'bitrix_password',
			__( 'Bitrix Password', 'wpcf7-to-bitrix24' ),
			array( __CLASS__, 'bitrix_password' ),
			self::PAGE,
			self::SECTION_ID
		);
		register_setting( self::SECTION_ID, 'bitrix_password' );
	}

	/**
	 * Enable send data
	 *
	 * @return Void
	 */
	static function disable_send_data() {

		$options = get_option( self::PAGE );

		$checked = checked( 1, absint( $options['disable_send_data'] ), false );
		?>
			<input type="checkbox" id="disable_send_data" name="disable_send_data" <?php echo $checked; ?> value='1'>
		<?php
	}

	/**
	 * Show setting field
	 *
	 * @return Void
	 */
	static function bitrix_url() {

		$options = get_option( self::PAGE );
		$bitrix_url = isset( $options['bitrix_url'] ) ? $options['bitrix_url'] : '';
		?>
			<input type="text" name="bitrix_url" id="bitrix_url" value="<?php echo $bitrix_url; ?>" size="70"><br>
			<span><b>Example:</b> https://site_name.bitrix24.ru/crm/configs/import/lead.php</span>
		<?php
	}

	/**
	 * Show setting field
	 *
	 * @return Void
	 */
	static function bitrix_login() {

		$options = get_option( self::PAGE );
		$bitrix_login = isset( $options['bitrix_login'] ) ? $options['bitrix_login'] : '';
		?>
			<input type="text" name="bitrix_login" id="bitrix_login" value="<?php echo $bitrix_login; ?>" size="70">
		<?php
	}

	/**
	 * Show setting field
	 *
	 * @return Void
	 */
	static function bitrix_password() {

		$options = get_option( self::PAGE );
		$bitrix_password = isset( $options['bitrix_password'] ) ? $options['bitrix_password'] : '';
		?>
			<input type="password" name="bitrix_password" id="bitrix_password" value="<?php echo $bitrix_password; ?>" size="70">
		<?php
	}

	/**
	 * Sent data to bitrix
	 *
	 * @param Array $data form data
	 *
	 */
	static function wpcf7_sent_to_bitrix( $contact_form ) {

		$title = $contact_form->title;
		$submission = WPCF7_Submission::get_instance();

		if ( $submission ) {
			$data = $submission->get_posted_data();
		} else {
			return;
		}

		$options = get_option( self::PAGE );

		// Maybe was disable sending data?
		if ( isset( $options['disable_send_data'] ) && 1 === $options['disable_send_data'] ) {
			return;
		}

		// Check auth data
		if ( ! isset( $options['bitrix_url'] ) || empty( $options['bitrix_url'] ) || ! isset( $options['bitrix_login'] ) || empty( $options['bitrix_login'] ) || ! isset( $options['bitrix_password'] ) || empty( $options['bitrix_password'] ) ) {
			return;
		}

		global $page;

		// Get page title
		$page = get_post( $data['_wpcf7_container_post'] );

		// Create message
		$message = $data['your-message'] . '<br>';
		$message .= 'All form data:<br>';

		foreach ( $data as $key => $value ) {

			if ( false === strripos( $key, '_wpcf7' ) ) {
				$message .= 'Field: ' . $key . ' Value: ' . $value . '<br>';
			}
		}

		// Create URL
		$url = sprintf(
			//'https://medservice.bitrix24.ru/crm/configs/import/lead.php?LOGIN=%s&PASSWORD=%s&TITLE=%s&NAME=%s&PHONE_WORK=%&EMAIL_WORK=%s&COMMENTS=%s',
			'%s?LOGIN=%s&PASSWORD=%s&TITLE=%s&NAME=%s&PHONE_WORK=%s&EMAIL_WORK=%s&COMMENTS=%s',
			$options['bitrix_url'], 										// Bitrix url
			$options['bitrix_login'],										// Login
			$options['bitrix_password'],									// Password
			$title . ' ( ' . $page->post_title . ' )',			// Title
			isset( $data['your-name'] ) 	? $data['your-name'] 	: ' ',	// your-name
			isset( $data['tel-882'] ) 		? $data['tel-882'] 		: ' ',	// tel-882
			isset( $data['your-email'] ) 	? $data['your-email'] 	: ' ',	// your-email
			$message														// your-message
		);

		// Send data
		$_data = file_get_contents( $url );
	}

}

new wpcf7_to_bitrix24();
