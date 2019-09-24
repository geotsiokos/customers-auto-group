<?php
/**
 * customers-auto-group.php
 *
 * Copyright (c) 2018 www.netpad.gr
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author gtsiokos
 * @package customers-auto-group
 * @since 1.0.0
 *
 * Plugin Name: Customers Auto Group
 * Plugin URI: http://www.netpad.gr
 * Description: Adds new WC Customers to group
 * Author: gtsiokos
 * Author URI: http://www.netpad.gr
 * Donate-Link: http://www.netpad.gr
 * License: GPLv3
 * Version: 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'user_register', 'cag_user_register', 10, 1 );

/**
 * Check plugin dependencies
 * if Groups and WC are installed and activated
 */
function cag_user_register( $user_id ) {
	if ( cag_check_dependencies() ) {
		$user_data = get_user_data( $user_id );
		$user_roles = $user_data->roles;
		if ( in_array( 'customer', $user_roles, true ) ) {
			cag_add_to_group( 'Customers', $user_id );
		}
	}
}

/**
 * Check if Groups and WooCommerce are active
 *
 * @return boolean
 */
function cag_check_dependencies() {
	$result = false;
	$active_plugins = get_option( 'active_plugins', array() );
	$groups_is_active = in_array( 'groups/groups.php', $active_plugins );
	$wc_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
	if ( $groups_is_active && $wc_is_active ) {
		$result = true;
	}
	
	return $result;
}

/**
 * Adds new user to group
 *
 * @param string $group_name
 * @param int $user_id
 */
function cag_add_to_group( $group_name, $user_id ) {
	if ( class_exists( 'Groups_User_Group' ) ) {
		$group = cag_group_exists( $group_name );
		if ( $group ) {
			Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' =>  $group->group_id ) );	
		}
	}
}

/**
 * Checks if group exists
 * and creates it if not
 *
 * @param string $group_name
 * @return boolean|int|object
 */
function cag_group_exists( $group_name ) {
	$result = false;
	if ( class_exists( 'Groups_Group' ) ) {
		if( !Groups_Group::read_by_name( $group_name ) ) {
			$group_id = Groups_Group::create( array( 'name' => $group_name ) );
			$result = Groups_Group::read( $group_id );
		} else{
			$result = Groups_Group::read_by_name( $group_name );
		}
	}
	return $result;
}
