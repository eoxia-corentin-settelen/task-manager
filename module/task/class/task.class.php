<?php
/**
 * Gestion des tâches.
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
 * Gestion des tâches.
 */
class Task_Class extends \eoxia\Post_Class {

	/**
	 * Toutes les couleurs disponibles pour une t$ache
	 *
	 * @var array
	 */
	public $colors = array(
		'white',
		'red',
		'yellow',
		'green',
		'blue',
		'purple',
	);

	/**
	 * Le nom du modèle
	 *
	 * @var string
	 */
	protected $model_name = '\task_manager\Task_Model';

	/**
	 * Le post type
	 *
	 * @var string
	 */
	protected $type = 'wpeo-task';

	/**
	 * La clé principale du modèle
	 *
	 * @var string
	 */
	protected $meta_key = 'wpeo_task';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @var string
	 */
	protected $base = 'task';

	/**
	 * La version de l'objet
	 *
	 * @var string
	 */
	protected $version = '0.1';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = 'wpeo_tag';

	/**
	 * Permet d'ajouter le post_status 'archive'.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @param array   $args   Les paramètres à appliquer pour la récupération @see https://codex.wordpress.org/Function_Reference/WP_Query.
	 * @param boolean $single Si on veut récupérer un tableau, ou qu'une seule entrée.
	 *
	 * @return Object
	 */
	public function get( $args = array(), $single = false ) {
		$array_posts = array();

		// Définition des arguments par défaut pour la récupération des "posts".
		$default_args = array(
			'post_status'    => array(
				'any',
				'archive',
			),
			'post_type'      => $this->get_type(),
			'posts_per_page' => -1,
		);

		$final_args = wp_parse_args( $args, $default_args );

		return parent::get( $final_args, $single );
	}

	/**
	 * Récupères les tâches.
	 *
	 * @since 1.0.0
	 * @version 1.5.0
	 *
	 * @param array $param {
	 *                      Les propriétés du tableau.
	 *
	 *                      @type integer $id(optional)              L'ID de la tâche.
	 *                      @type integer $offset(optional)          Sautes x tâches.
	 *                      @type integer $posts_per_page(optional)  Le nombre de tâche.
	 *                      @type array   $users_id(optional)        Un tableau contenant l'ID des utilisateurs.
	 *                      @type array   $categories_id(optional)   Un tableau contenant le TERM_ID des categories.
	 *                      @type string  $status(optional)          Le status des tâches.
	 *                      @type integer $post_parent(optional)     L'ID du post parent.
	 *                      @type string  $term(optional)            Le terme pour rechercher une tâche.
	 * }.
	 * @return array        La liste des tâches trouvées.
	 */
	public function get_tasks( $param ) {
		global $wpdb;

		$param['id']             = isset( $param['id'] ) ? (int) $param['id'] : 0;
		$param['offset']         = ! empty( $param['offset'] ) ? (int) $param['offset'] : 0;
		$param['posts_per_page'] = ! empty( $param['posts_per_page'] ) ? (int) $param['posts_per_page'] : -1;
		$param['users_id']       = ! empty( $param['users_id'] ) ? (array) $param['users_id'] : array();
		$param['categories_id']  = ! empty( $param['categories_id'] ) ? (array) $param['categories_id'] : array();
		$param['status']         = ! empty( $param['status'] ) ? sanitize_text_field( $param['status'] ) : 'any';
		$param['post_parent']    = ! empty( $param['post_parent'] ) ? (array) $param['post_parent'] : array( 0 );
		$param['term']           = ! empty( $param['term'] ) ? sanitize_text_field( $param['term'] ) : '';

		$tasks    = array();
		$tasks_id = array();

		if ( ! empty( $param['status'] ) ) {
			if ( 'any' === $param['status'] ) {
				$param['status'] = '"publish","pending","draft","future","private","inherit"';
			} else {
				// Ajout des apostrophes.
				$param['status'] = '"' . $param['status'] . '"';

				// Entre chaque virgule.
				$param['status'] = str_replace( ',', '","', $param['status'] );
			}
		}

		$param = apply_filters( 'task_manager_get_tasks_args', $param );

		if ( ! empty( $param['id'] ) ) {
			$tasks = self::g()->get( array(
				'p' => (int) $param['id'],
			) );
		} else {
			$point_type = Point_Class::g()->get_type();

			$comment_type = Task_Comment_Class::g()->get_type();

			$query = "SELECT DISTINCT TASK.ID FROM {$wpdb->posts} AS TASK
				LEFT JOIN {$wpdb->comments} AS POINT ON POINT.comment_post_ID=TASK.ID AND POINT.comment_approved = 1 AND POINT.comment_type = '{$point_type}'
				LEFT JOIN {$wpdb->comments} AS COMMENT ON COMMENT.comment_parent=POINT.comment_ID AND COMMENT.comment_approved = 1 AND POINT.comment_approved = 1 AND COMMENT.comment_type = '{$comment_type}'
				LEFT JOIN {$wpdb->postmeta} AS TASK_META ON TASK_META.post_id=TASK.ID AND TASK_META.meta_key='wpeo_task'
				LEFT JOIN {$wpdb->term_relationships} AS CAT ON CAT.object_id=TASK.ID
			WHERE TASK.post_type='wpeo-task'

				AND TASK.post_status IN (" . $param['status'] . ") AND
					( (
						TASK.ID LIKE '%" . $param['term'] . "%' OR TASK.post_title LIKE '%" . $param['term'] . "%'
					) OR (
						POINT.comment_ID LIKE '%" . $param['term'] . "%' OR POINT.comment_content LIKE '%" . $param['term'] . "%'
					) OR (
						COMMENT.comment_parent != 0 AND (COMMENT.comment_id LIKE '%" . $param['term'] . "%' OR COMMENT.comment_content LIKE '%" . $param['term'] . "%')
					) )";

			if ( isset( $param['post_parent'] ) ) {
				$query .= 'AND TASK.post_parent IN (' . implode( $param['post_parent'], ',' ) . ')';
			}

			if ( ! empty( $param['users_id'] ) ) {
				$query .= "AND (
					(
						TASK_META.meta_value REGEXP '{\"user_info\":{\"owner_id\":" . implode( $param['users_id'], '|' ) . ",'
					) OR (
						TASK_META.meta_value LIKE '%affected_id\":[" . implode( $param['users_id'], '|' ) . "]%'
					) OR (
						TASK_META.meta_value LIKE '%affected_id\":[" . implode( $param['users_id'], '|' ) . ",%'
					) OR (
						TASK_META.meta_value REGEXP 'affected_id\":\\[[0-9,]+" . implode( $param['users_id'], '|' ) . "\\]'
					) OR (
						TASK_META.meta_value REGEXP 'affected_id\":\\[[0-9,]+" . implode( $param['users_id'], '|' ) . "[0-9,]+\\]'
					)
				)";
			}

			if ( ! empty( $param['categories_id'] ) ) {
				$sub_query = '   ';
				foreach ( $param['categories_id'] as $cat_id ) {
					$sub_query .= '(CAT.term_taxonomy_id=' . $cat_id . ') OR';
				}

				$sub_query = substr( $sub_query, 0, -3 );
				if ( ! empty( $sub_query ) ) {
					$query .= "AND ({$sub_query})";
				}
			}

			$query .= " ORDER BY TASK.post_date DESC ";

			if ( -1 !== $param['posts_per_page'] ) {
				$query .= "LIMIT " . $param['offset'] . "," . $param['posts_per_page'];
			}

			$tasks_id = $wpdb->get_col( $query );

			if ( ! empty( $tasks_id ) ) {
				$tasks = self::g()->get( array(
					'post__in'    => $tasks_id,
					'post_status' => $param['status'],
				) );
			} // End if().
		} // End if().

		return $tasks;
	}

	/**
	 * Charges les tâches, et fait le rendu.
	 *
	 * @param array $tasks    La liste des tâches qu'il faut afficher.
	 * @param bool  $frontend L'affichage aura t il lieu dans le front ou le back.
	 *
	 * @return void
	 *
	 * @since 1.3.6
	 * @version 1.6.0
	 *
	 * @todo: With_wrapper ?
	 */
	public function display_tasks( $tasks, $frontend = false ) {
		if ( $frontend ) {
			\eoxia\View_Util::exec( 'task-manager', 'task', 'frontend/tasks', array(
				'tasks'        => $tasks,
				'with_wrapper' => false,
			) );
		} else {
			\eoxia\View_Util::exec( 'task-manager', 'task', 'backend/tasks', array(
				'tasks'        => $tasks,
				'with_wrapper' => false,
			) );
		}
	}

	/**
	 * Fait le rendu de la metabox
	 *
	 * @param  WP_Post $post les données du post.
	 * @return void
	 *
	 * @since 1.0.0
	 * @version 1.6.0
	 */
	public function callback_render_metabox( $post ) {
		$parent_id = $post->ID;
		$user_id   = $post->post_author;

		$tasks                = array();
		$task_ids_for_history = array();
		$total_time_elapsed   = 0;
		$total_time_estimated = 0;

		// Affichage des tâches de l'élément sur lequel on se trouve.
		$tasks[ $post->ID ]['title'] = '';
		$tasks[ $post->ID ]['data']  = self::g()->get_tasks( array(
			'post_parent' => $post->ID,
		) );

		if ( ! empty( $tasks[ $post->ID ]['data'] ) ) {
			foreach ( $tasks[ $post->ID ]['data'] as $task ) {
				if ( empty( $tasks[ $post->ID ]['total_time_elapsed'] ) ) {
					$tasks[ $post->ID ]['total_time_elapsed'] = 0;
				}

				$tasks[ $post->ID ]['total_time_elapsed'] += $task->data['time_info']['elapsed'];
				$total_time_elapsed                       += $task->data['time_info']['elapsed'];
				$total_time_estimated                     += $task->data['last_history_time']->data['estimated_time'];

				$task_ids_for_history[] = $task->data['id'];
			}
		}

		// Récupération des enfants de l'élément sur lequel on se trouve.
		$args     = array(
			'post_parent' => $post->ID,
			'post_type'   => \eoxia\Config_Util::$init['task-manager']->associate_post_type,
			'numberposts' => -1,
			'post_status' => 'any',
		);
		$children = get_posts( $args );

		if ( ! empty( $children ) ) {
			foreach ( $children as $child ) {
				/* Translators: Titre du post sur lequel on veut afficher les tâches. */
				$tasks[ $child->ID ]['title'] = sprintf( __( 'Task for %1$s', 'task-manager' ), $child->post_title );
				$tasks[ $child->ID ]['data']  = self::g()->get_tasks( array(
					'post_parent' => $child->ID,
				) );

				if ( empty( $tasks[ $child->ID ]['data'] ) ) {
					unset( $tasks[ $child->ID ] );
				}

				if ( ! empty( $tasks[ $child->ID ]['data'] ) ) {
					foreach ( $tasks[ $child->ID ]['data'] as $task ) {
						if ( empty( $tasks[ $child->ID ]['total_time_elapsed'] ) ) {
							$tasks[ $child->ID ]['total_time_elapsed'] = 0;
						}
						$tasks[ $child->ID ]['total_time_elapsed'] += $task->data['time_info']['elapsed'];
						$total_time_elapsed                        += $task->data['time_info']['elapsed'];
						$total_time_estimated                      += $task->data['last_history_time']->data['estimated_time'];

						$task_ids_for_history[] = $task->data['id'];
					}
				}
			}
		}

		$total_time_elapsed   = \eoxia\Date_Util::g()->convert_to_custom_hours( $total_time_elapsed );
		$total_time_estimated = \eoxia\Date_Util::g()->convert_to_custom_hours( $total_time_estimated );

		\eoxia\View_Util::exec( 'task-manager', 'task', 'backend/metabox-posts', array(
			'post'                 => $post,
			'tasks'                => $tasks,
			'task_ids_for_history' => implode( ',', $task_ids_for_history ),
			'total_time_elapsed'   => $total_time_elapsed,
			'total_time_estimated' => $total_time_estimated,
		) );
	}

}

Task_Class::g();
