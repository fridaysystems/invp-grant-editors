<?php
/**
 * Plugin Name: Inventory Presser - Grant Editors
 * Plugin URI: https://inventorypresser.com/
 * Description: Grants users with the "editor" role full control of Inventory Presser posts.
 * Version: 1.0.0
 * Author: Corey Salzano
 * Author URI: https://github.com/csalzano
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: invp-grant-editors
 * Domain Path: /languages
 * GitHub Plugin URI: fridaysystems/invp-grant-editors
 * Primary Branch: main
 *
 * @package invp-grant-editors
 * @author Corey Salzano <corey@friday.systems>
 */

defined( 'ABSPATH' ) || exit;

register_activation_hook( __FILE__, 'invp_grant_editors_trigger' );
/**
 * Activation hook callback. Adds a shutdown action.
 *
 * @param bool $network_wide Whether the plugin is being activated network-wide.
 * @return void
 */
function invp_grant_editors_trigger( $network_wide ) {
	if ( ! $network_wide ) {
		return;
	}
	add_action( 'shutdown', 'invp_grant_editors' );
}

/**
 * Grants users with the "editor" role the ability to edit inventory items.
 *
 * @return void
 */
function invp_grant_editors() {
	$sites = get_sites(
		array(
			'number' => 200,
		)
	);
	foreach ( $sites as $site ) {
		$site_name = get_blog_details( $site->blog_id )->blogname;

		// Examine every user.
		$users = get_users(
			array(
				'blog_id'  => $site->blog_id,
				'role__in' => array( 'editor' ),
				'number'   => 200,
			)
		);
		if ( empty( $users ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[invp-grant-editors] No editors on ' . $site->blog_id . ' - ' . $site_name );
			}
			continue;
		}
		// If the user is an editor, grant them the ability to edit inventory items.
		foreach ( $users as $user ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[invp-grant-editors] Granting editor ' . $user->user_login . ' on ' . $site->blog_id . ' - ' . $site_name );
			}
			$user->add_cap( 'delete_inventory_vehicles' );
			$user->add_cap( 'delete_others_inventory_vehicles' );
			$user->add_cap( 'delete_private_inventory_vehicles' );
			$user->add_cap( 'delete_published_inventory_vehicles' );
			$user->add_cap( 'edit_inventory_vehicles' );
			$user->add_cap( 'edit_others_inventory_vehicles' );
			$user->add_cap( 'edit_private_inventory_vehicles' );
			$user->add_cap( 'edit_published_inventory_vehicles' );
			$user->add_cap( 'publish_inventory_vehicles' );
			$user->add_cap( 'read_private_inventory_vehicles' );
		}
	}
}
