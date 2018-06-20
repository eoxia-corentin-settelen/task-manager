<?php
/**
 * Les actions relatives aux outils.
 *
 * @author Eoxia <dev@eoxia.com>
 * @since 1.5.0
 * @version 1.8.0
 * @copyright 2018 Eoxia.
 * @package Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Les actions relatives aux outils.
 */
class Tools_Action {

	/**
	 * Initialise les actions liées aux outils.
	 *
	 * @since 1.5.0
	 * @version 1.7.1
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'wp_ajax_task_manager_compile_data', array( $this, 'callback_compile_data' ) );
		add_action( 'wp_ajax_task_manager_fix_points_and_comments', array( $this, 'callback_fix_points_and_comments' ) );
	}

	/**
	 * Initialise le menu dans l'onglet 'Outils' du menu de WordPress.
	 *
	 * @since 1.5.0
	 * @version 1.7.1
	 *
	 * @return void
	 */
	public function callback_admin_menu() {
		add_management_page( 'Task Manager', 'Task Manager', 'manage_options', 'taskmanager-tools', array( Tools_Class::g(), 'display' ) );
	}

	/**
	 * Compiles toutes les données de Task Manager dans un fichier de cache au format JSON.
	 *
	 * @since 1.8.0
	 * @version 1.8.0
	 *
	 * @return void
	 */
	public function callback_compile_data() {
		global $wpdb;

		$data_to_compile = array(
			'last' => array(),
			'list' => array(),
		);

		$tasks    = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='wpeo-task'" );
		$points   = $wpdb->get_results( "SELECT comment_ID, comment_content FROM {$wpdb->comments} WHERE comment_type='wpeo_point'" );
		$comments = $wpdb->get_results( "SELECT comment_ID, comment_content FROM {$wpdb->comments} WHERE comment_type='wpeo_time'" );

		if ( ! empty( $tasks ) ) {
			foreach ( $tasks as $task ) {
				$data_to_compile['list'][ 'T' . $task->ID ] = array(
					'id'      => $task->ID,
					'content' => $task->post_title,
					'type'    => 'task',
				);
			}
		}

		if ( ! empty( $points ) ) {
			foreach ( $points as $point ) {
				$data_to_compile['list'][ 'P' . $point->comment_ID ] = array(
					'id'      => $point->comment_ID,
					'content' => $point->comment_content,
					'type'    => 'point',
				);
			}
		}

		if ( ! empty( $comments ) ) {
			foreach ( $comments as $comment ) {
				$data_to_compile['list'][ 'C' . $comment->comment_ID ] = array(
					'id'      => $comment->comment_ID,
					'content' => $comment->comment_content,
					'type'    => 'comment',
				);
			}
		}

		$data_to_compile = json_encode( $data_to_compile );
		$data_to_compile = preg_replace_callback( '/\\\\u([0-9a-f]{4})/i', function ( $matches ) {
			$sym = mb_convert_encoding( pack( 'H*', $matches[1] ), 'UTF-8', 'UTF-16' );
			return $sym;
		}, $data_to_compile );

		$file = fopen( PLUGIN_TASK_MANAGER_PATH . 'core/assets/json/data.json', 'w+' );
		fwrite( $file, $data_to_compile );
		fclose( $file );

		wp_send_json_success();
	}

	public function callback_fix_points_and_comments() {
		$point_schema = Point_Class::g()->get_schema();
		$task_schema  = Task_Class::g()->get_schema();

		$points = get_comments( array(
			'type' => 'wpeo_point',
		) );

		if ( ! empty( $points ) ) {
			foreach ( $points as $point ) {

				$comment_metas = get_comment_meta( (int) $point->comment_ID );

				// Position du point dans la tâche.
				update_comment_meta( (int) $point->comment_ID, $point_schema['order']['field'], $this->search_position( (int) $point->comment_ID, (int) $point->comment_post_ID ) );

				// Statut du point terminé / en cours.
				if ( ! empty( $comment_metas ) && ! empty( $comment_metas[ Point_Class::g()->get_meta_key() ] ) && ! isset( $comment_metas[ $point_schema['completed']['field'] ] ) ) {
					$wpeo_point_meta = json_decode( $comment_metas[ Point_Class::g()->get_meta_key() ][0] );
					if ( true === $wpeo_point_meta->point_info->completed ) {
						$meta_name = $task_schema['count_uncompleted_points']['field'];
						update_comment_meta( (int) $point->comment_ID, $point_schema['completed']['field'], true );
					} else {
						$meta_name = $task_schema['count_completed_points']['field'];
						update_comment_meta( (int) $point->comment_ID, $point_schema['completed']['field'], false );
					}
					$task_number_point = get_post_meta( $point->comment_post_ID, $meta_name, true );
					if ( empty( $count_completed_point ) ) {
						$task_number_point = 0;
					}
					$task_number_point++;
					update_post_meta( $point->comment_post_ID, $meta_name, $task_number_point );
				}
			}
		}

		wp_send_json_success();
	}

	/**
	 * Recherches la position du point dans le tableau "order_point_id" de la tâche.
	 *
	 * @since 1.7.1
	 * @version 1.7.1
	 *
	 * @param  Point_Model $point Les données du point.
	 * @return integer            La position du point.
	 */
	public function search_position( $point_id, $task_id ) {
		$position = false;

		if ( 0 === $task_id ) {
			return 0;
		}

		$task = Task_Class::g()->get( array(
			'id' => $task_id,
		), true );

		if ( empty( $task ) ) {
			$position = false;
		} else {
			$position = array_search( $point_id, $task->data['task_info']['order_point_id'] );
		}

		if ( false === $position ) {
			// \eoxia\LOG_Util::log( 'No order for the point #' . $point->data['id'] . ' setted to 0 in task #' . $task->data['id'] . '(' . wp_json_encode( $task->data['task_info']['order_point_id'] ) . ')', 'task-manager' );
			$position = 0;
		}

		return $position;
	}
}

new Tools_Action();
