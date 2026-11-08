<?php
/**
 * Removes the plugin's settings when it is deleted from the Plugins screen.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'canjean_cc_settings' );
delete_option( 'canjean_cc_credit_asked' );
