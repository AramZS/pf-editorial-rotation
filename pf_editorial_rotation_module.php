<?php


class pf_editorial_rotation extends PF_Module {

	function __construct() {
		parent::start();
	}

	/**
	 * Register the admin menu items
	 *
	 * The parent class will take care of registering them
	 */
	function setup_admin_menus() {
		$admin_menus   = array();

		$admin_menus[] = array(
			'page_title' => __( 'User Control', 'pf' ),
			'menu_title' => __( 'User Control', 'pf' ),
			'cap'        => 'edit_posts',
			'slug'       => 'pf-user-control',
			'callback'   => array( $this, 'admin_menu_callback' ),
		);

		parent::setup_admin_menus( $admin_menus );
	}
	
	function setup_module() {
		$enabled = get_option( 'pf_user_control_enable' );
		if ( ! in_array( $enabled, array( 'yes', 'no' ) ) ) {
			$enabled = 'yes';
		}
		
		$mod_settings = array(
			'name' => 'User Control Module',
			'slug' => 'user_control',
			'options' => ''
		);
		
		//update_option( 'pf_foo_settings', $mod_settings );

		
	}
	
	function module_setup(){
		$mod_settings = array(
			'name' => 'User Control Module',
			'slug' => 'user_control',
			'description' => 'This module provides a way to view current users, manipulate their user levels and set up an editorial rotation.',
			'thumbnail' => '',
			'options' => ''
		);
		
		update_option( PF_SLUG . '_' . $this->id . '_settings', $mod_settings );	

		//return $test;
	}	

}