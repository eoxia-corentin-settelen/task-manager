<?php
/**
 * Initialise les actions liées à la barre de recherche.
 *
 * @author Eoxia <dev@eoxia.com>
 * @since 1.0.0
 * @version 1.6.0
 * @copyright 2015-2018 Eoxia
 * @package Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Les actions relatives aux tâches.
 */
class Navigation_Action {

	/**
	 * Initialise les actions liées à la barre de recherche.
	 *
	 * @since 1.0.0
	 * @version 1.3.6
	 */
	public function __construct() {
		add_action( 'wp_ajax_search', array( $this, 'callback_search' ) );

	}

	/**
	 * Utilises le shorcode "tasks" pour récupérer les tâches correspondant au critères de la recherche.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @version 1.6.0
	 * @todo nonce
	 */
	public function callback_search() {
		$term                   = ! empty( $_POST['term'] ) ? sanitize_text_field( $_POST['term'] ) : '';
		$status                 = ! empty( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		$categories_id_selected = ! empty( $_POST['categories_id_selected'] ) ? sanitize_text_field( $_POST['categories_id_selected'] ) : '';
		$follower_id_selected   = ! empty( $_POST['follower_id_selected' ] ) ? (int) $_POST['follower_id_selected'] : '';
		$post_parent            = ! empty( $_POST['post_parent'] ) ? (int) $_POST['post_parent'] : 0;
		$current_url            = ! empty( $_POST['current_url'] ) ? sanitize_text_field( $_POST['current_url'] ) : '';

		ob_start();
		Navigation_Class::g()->display_search_result( $term, $status, $categories_id_selected, $follower_id_selected, $current_url );
		$search_result_view = ob_get_clean();

		ob_start();
		echo do_shortcode( '[task post_parent="' . $post_parent . '" users_id="' . $follower_id_selected . '" status="' . $status . '" categories_id="' . $categories_id_selected . '" term="' . $term . '" posts_per_page="' . \eoxia\Config_Util::$init['task-manager']->task->posts_per_page . '" with_wrapper="0"]' );
		$tasks_view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'taskManager',
			'module'           => 'navigation',
			'callback_success' => 'searchedSuccess',
			'view'             => array(
				'tasks'         => $tasks_view,
				'search_result' => $search_result_view,
			),
		) );
	}
}

new Navigation_Action();
