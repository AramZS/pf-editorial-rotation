<?php
/*
Plugin Name: PressForward Editorial Rotation
Plugin URI: http://pressforward.org/
Description: This plugin is an editorial user control tool for CHNM's Press Forward project.
Version: 0.0.1
Author: Aram Zucker-Scharff
Author URI: http://aramzs.me
License: GPL2
*/

/*  Developed for the Center for History and New Media

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class editorial_rotation {

	function __construct() {
		global $editorial_uc;
		$editorial_uc = new editorial_user_control();
		add_action('pf_admin_user_settings', array($this, 'generate_user_control_list'));
	}

	function pf_checker(){
		$pfed = false;
		if (defined('PF_SLUG')){
			$pfed = true;
		}
		return $pfed;
	}
	
	function generate_user_control_list() {
		global $editorial_uc;
		echo '<ul>';
		$users = get_users();
		foreach ($users as $user){
			echo '<li>';
				$user_state = $editorial_uc->get_current_user_role($user->ID);
				echo $user->display_name . ' - ' . $user->user_nicename . ' - ' . $user->user_login . ' - ' . $user_state;
			echo '</li>';
		}
		echo '</ul>';
	
	}
	
}

class editorial_user_control {

	# http://wordpress.org/support/topic/how-to-get-the-current-logged-in-users-role
	function get_current_user_role() {
		global $wp_roles;
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift($roles);
		return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
	}

	function pf_get_capabilities($cap = false){
		  # Get the WP_Roles object.
		  global $wp_roles;
		  # Set up array for storage.
		  $role_reversal = array();
		  # Walk through the roles object by role and get capabilities.
		  foreach ($wp_roles->roles as $role_slug=>$role_set){

			foreach ($role_set['capabilities'] as $capability=>$cap_bool){
				# Don't store a capability if it is false for the role (though none are).
				if ($cap_bool){
					$role_reversal[$capability][] = $role_slug;
				}
			}
		  }
		  
		  # Allow users to get specific capabilities.
		  if (!$cap){
			return $role_reversal;
		  } else {
			return $role_reversal[$cap];
		  }
	}

	function pf_get_user_role_select($option, $default){
		global $wp_roles;
		$roles = $wp_roles->get_names();
		$enabled = get_option($option, $default);
#		$roleObj = pf_get_role_by_capability($enabled, true, true);
#		$enabled_role = $roleObj->name;
		foreach ($roles as $slug=>$role){
			$defining_capability = pf_get_defining_capability_by_role($slug);
			?><option value="<?php echo $defining_capability ?>" <?php selected( $enabled, $defining_capability ) ?>><?php _e( $role, PF_SLUG ) ?></option><?php 
		}
	}	
		
	# If we want to allow users to set access by role, we need to give
	# the users the names of the roles, but WordPress needs a capability.
	# This function lets you match the role with the first capability
	# that only it can do, the defining capability.
	function pf_get_defining_capability_by_role($role_slug){
		$caps = pf_get_capabilities();
		foreach ($caps as $slug=>$cap){
			$low_role = pf_get_role_by_capability($slug);
			# Return the first capability only applicable to that role.
			if ($role_slug == ($low_role))
				return $slug;
		}

	}

	function pf_get_role_by_capability($cap, $lowest = true, $obj = false){
		# Get set of roles for capability.
		$roles = pf_get_capabilities($cap);
		# We probobly want to get the lowest role with that capability
		if ($lowest){
			$roles = array_reverse($roles);
		}

	  $the_role = array_shift(array_values($roles));
	  if (!$obj){
		return $the_role;
	  } else {
			return get_role($the_role);
	  }


	}	
	
}

$euc = new editorial_rotation();
