<?php
/*
Plugin Name: WP-Core | WM Maintenance
Description: Maintain and support custom features on this website.
Version: 1.1.4
*/

// Include the plugin-update-checker library.
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://wm-streaming.s3.amazonaws.com/wm-apps/wp-plugins/wp-code-wm-maintenance/update.json',
	__FILE__, //Full path to the main plugin file or functions.php.
	'wp-code-wm-maintenance'
);


// Plugin - Log plugins update with version, date, and initiator.
function pul_log_plugin_updates( $upgrader_object, $options ) {
    if ( 'update' === $options['action'] && 'plugin' === $options['type'] ) {
        $plugins = get_plugins();
        $plugin = $plugins[ $options['plugins'][0] ];
        
        $prev_version = $plugin['Version'];
        $new_version = $upgrader_object->skin->plugin_info['Version'];
        $domain_name = get_site_url();

        $current_user = wp_get_current_user();
        $username = $current_user->user_login;

        $endpoint_url = 'https://script.google.com/macros/s/AKfycbwXtkQ98nCAU7vAnYxDQTAVgk57a8Ca4IbDImpmOf6KzjutKxCERWEgyvvDKch-hl9_/exec';
        $endpoint_url = add_query_arg( array(
            'domain' => urlencode( $domain_name ),
            'pluginName' => urlencode( $plugin['Name'] ),
            'previousVersion' => urlencode( $new_version ),
            'newVersion' => urlencode( $prev_version ),
            'updatedBy' => urlencode( $username )
        ), $endpoint_url );

        wp_remote_get( $endpoint_url );
    }
}
add_action( 'upgrader_process_complete', 'pul_log_plugin_updates', 10, 2 );