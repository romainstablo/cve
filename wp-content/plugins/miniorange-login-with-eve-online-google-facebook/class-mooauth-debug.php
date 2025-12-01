<?php
/**
 * Debug
 *
 * @package    debug
 * @author     miniOrange <info@miniorange.com>
 * @license    Expat
 * @link       https://miniorange.com
 */

/**
 * Handle SSO debug logs
 */
class MOOAuth_Debug {

	/**
	 * Get the log file path
	 *
	 * @return string
	 */
	public static function get_log_file_path() {

		return MO_OAUTH_LOG_DIR . DIRECTORY_SEPARATOR . get_option( 'mo_oauth_debug' ) . '.log';
	}

	/**
	 * Handle Debug log.
	 *
	 * @param mixed $mo_message message to be logged.
	 */
	public static function mo_oauth_log( $mo_message ) {
		$mo_pluginlog = self::get_log_file_path();

		$mo_time = time();
		$mo_log  = '[' . gmdate( 'Y-m-d H:i:s', $mo_time ) . ' UTC] : ' . print_r( $mo_message, true ) . PHP_EOL; //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Used for debugging purposes

		if ( get_option( 'mo_debug_enable' ) === 'on' ) {
			// Only write the message if it's not empty or if it's not the initial check.
			if ( ! get_option( 'mo_debug_check' ) && ! empty( $mo_message ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( $mo_log . PHP_EOL, 3, $mo_pluginlog );
			}
		}
	}
}
