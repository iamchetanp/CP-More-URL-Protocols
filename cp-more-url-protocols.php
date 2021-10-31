<?php
/**
 * CP More URL Protocols plugin for WordPress
 *
 * @package   cp-more-url-protocols
 * @link      https://wordpress.org/plugins/cp-more-url-protocols/
 * @author    Chetan Prajapati
 * @copyright 2016-2018 Chetan Prajapati
 * @license   GPL v2 or later
 *
 * Plugin Name: CP More URL Protocols
 * Plugin URI: https://wordpress.org/plugins/cp-more-url-protocols/
 * Description: This WordPress plugin will allow you to add more URL protocols in HTML attributes like menu and other URL filters.
 * Version: 1.0.1
 * Author: Chetan Prajapati
 * Author URI: https://chetanprajapati.com
 * Text Domain: cpmup
 * License: GPL2

 * Copyright 2016 Chetan Prajapati

 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <http://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CPMUP_PATH', __FILE__ );

if ( ! defined( 'DOING_AJAX' ) ) {

	/**
	 * Plugin's setting link in plugins page.
	 *
	 * @param array $links Array of link attributes.
	 */
	function cpmup_settings_link( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=cp-more-url-protocols.php' ) . '">' . __( 'Add More URL Protocols', 'cpmup' ) . '</a>',
			),
			$links
		);
	}
	add_filter( 'plugin_action_links_' . plugin_basename( CPMUP_PATH ), 'cpmup_settings_link' );

	/**
	 * Delete option on plugin deactivation.
	 */
	function cpmup_uninstall() {
		delete_option( 'cpmup_settings' );
	}
	register_uninstall_hook( CPMUP_PATH, 'cpmup_uninstall' );

	/**
	 * Adds sub menu under options.
	 */
	function cpmup_register_submenu_page() {
		add_options_page( __( 'CP More URL Protocols', 'cpmup' ), __( 'CP URL Protocols', 'cpmup' ), 'manage_options', basename( CPMUP_PATH ), 'cpmup_render_submenu_page' );
	}
	add_action( 'admin_menu', 'cpmup_register_submenu_page' );

	/**
	 * Register setting for option page.
	 */
	function cpmup_register_settings() {
		register_setting( 'cpmup_settings_group', 'cpmup_settings' );
	}
	add_action( 'admin_init', 'cpmup_register_settings' );


	/**
	 * CP More URL Protocols option page.
	 */
	function cpmup_render_submenu_page() {
		$options       = get_option( 'cpmup_settings' );
		$protocols     = isset( $options['cpmup-protocols'] ) && ! empty( $options['cpmup-protocols'] ) ? $options['cpmup-protocols'] : '';
		$new_protocols = array(
			'Skype'    => 'skype',
			'SMS'      => 'sms',
			'Viber'    => 'viber',
			'Telegram' => 'tg',
			'Whatsapp' => 'whatsapp',
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'CP More URL Protocols', 'cpmup' ); ?></h1>
			<form name="cpmup_form" action="options.php" method="post">
				<?php settings_fields( 'cpmup_settings_group' ); ?>
				<div id="cpmup_wrap">
					<table class="form-table">
						<?php do_action( 'cpmup_form_top' ); ?>
						<?php
						foreach ( $new_protocols as $new_protocial_name => $new_protocol ) {
							$checked = '';
							if ( is_array( $protocols ) ) {
								if ( in_array( $new_protocol, $protocols, true ) ) {
									$checked = "checked='checked'";
								}
							}
						?>
							<tr>
								<th>
									<label for="<?php echo esc_html( $new_protocol ); ?>"><?php echo esc_html( $new_protocial_name ); ?></label>
								</th>
								<td>
									<input name="cpmup_settings[cpmup-protocols][]" <?php echo esc_html( $checked ); ?> value="<?php echo esc_html( $new_protocol ); ?>" id="<?php echo esc_html( $new_protocol ); ?>" type="checkbox" />
								</td>
							</tr>
						<?php
						}
						?>
						</table>
					<?php submit_button( __( 'Save URL Protocols', 'cpmup' ), 'primary', 'submit', true ); ?>
					<?php do_action( 'cpmup_form_bottom' ); ?>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	<?php
	}

	/**
	 * Filters to allo custom protocols in HTML attributes.
	 *
	 * @param array $protocols Array of allowed protocols.
	 */
	function cp_more_url_protocols( $protocols ) {
		$options       = get_option( 'cpmup_settings' );
		$cp_protocols  = array( 'skype', 'sms', 'viber', 'tg', 'whatsapp' );
		$protocols_new = isset( $options['cpmup-protocols'] ) && ! empty( $options['cpmup-protocols'] ) ? $options['cpmup-protocols'] : '';
		if ( is_array( $protocols_new ) ) {
			foreach ( $protocols_new as $protocol ) {
				if ( in_array( $protocol, $cp_protocols, true ) ) {
					$protocols[] = $protocol;
				}
			}
		}
		return $protocols;
	}
	add_filter( 'kses_allowed_protocols', 'cp_more_url_protocols' );
}
