<?php

namespace task_manager;
if ( !defined( 'ABSPATH' ) ) exit;

class Task_Help_Filter {
	public function __construct() {
		add_filter( 'mce_buttons', array( $this, 'callback_mce_buttons' ) );
		add_filter( 'mce_external_plugins', array( $this, 'callback_mce_external_plugins' ) );
	}

	public function callback_mce_buttons( $buttons ) {
		array_push( $buttons, 'task' );
		return $buttons;
	}

	public function callback_mce_external_plugins( $plugin_array ) {
		global $task_controller;
		$list_task = $task_controller->index( array( 'post_parent' => 0 ) );

		require_once( wpeo_template_01::get_template_part( WPEO_TASK_HELP_DIR, WPEO_TASK_HELP_TEMPLATES_MAIN_DIR, 'backend', 'list-task' ) );

		$plugin_array['task'] = WPEO_TASKMANAGER_DIR_ASSET . '/js/task-button.js';
		return $plugin_array;
	}

  public function callback_admin_menu() {
    add_submenu_page( 'wpeomtm-dashboard', __( 'Help', 'task-manager' ), __( 'Help', 'task-manager' ), 'manage_options', 'wpeo-project-help', array( &$this, 'callback_submenu_page' ) );
  }

  public function callback_submenu_page() {
    require_once( wpeo_template_01::get_template_part( WPEO_TASK_HELP_DIR, WPEO_TASK_HELP_TEMPLATES_MAIN_DIR, 'backend', 'main' ) );
  }
}

new Task_Help_Filter();
