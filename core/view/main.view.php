<?php
/**
 * La vue principale de la page "wpeomtm-dashboard"
 *
 * @author Eoxia <dev@eoxia.com>
 * @since 0.1.0
 * @version 1.6.0
 * @copyright 2015-2018 Eoxia
 * @package Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wrap wpeo-project-wrap">
	<input type="hidden" class="user-id" value="<?php echo esc_attr( get_current_user_id() ); ?>" />

	<div class="wpeo-project-dashboard">
		<h2>
			<?php	esc_html_e( 'Task', 'task-manager' ); ?>
			<a 	href="#"
					class="action-attribute add-new-h2"
					data-action="create_task"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'create_task' ) ); ?>"><?php esc_html_e( 'New task', 'task-manager' ); ?></a>
					
					<!-- Bouton d'ouverture de la modal pour l'import de tâches -->
					<a href="#" class="page-title-action wpeo-modal-event"
						data-target="tm-import-tasks"
						data-parent="wpeo-project-dashboard" ><i class="fas fa-download" ></i>&nbsp;<?php esc_html_e( 'Import', 'task-manager' ); ?></a>
				
					<!-- Structure de la modal pour l'import de tâches -->
					<div class="wpeo-modal tm-import-tasks">
						<div class="modal-container">
							<div class="modal-header">
								<h2 class="modal-title"><?php echo esc_attr( 'Create tasks from text', 'task-manager' ); ?></h2>
								<div class="modal-close"><i class="fal fa-times"></i></div>
							</div>
				
							<div class="modal-content"><p><?php Import_Class::g()->display_textarea(); ?></p></div>
				
							<div class="modal-footer">
								<div class="wpeo-button button-grey button-uppercase modal-close"><span><?php esc_html_e( 'Cancel', 'task-manager' ); ?></span></div>
								<a class="wpeo-button button-main button-uppercase action-input"
									data-parent-id="<?php echo esc_attr( 0 ); ?>"
									data-parent="tm-import-tasks"
									data-action="tm_import_tasks_and_points"
									data-nonce="<?php echo esc_attr( wp_create_nonce( 'tm_import_tasks_and_points' ) ); ?>" ><span><?php esc_html_e( 'Import', 'task-manager' ); ?></span></a>
							</div>
						</div>
					</div>
		</h2>
		

	</div>

	<?php echo do_shortcode( '[task_manager_search_bar term="' . $term . '" categories_id_selected="' . $categories_id_selected . '" follower_id_selected="' . $follower_id_selected . '"]' ); ?>

	<?php
	$waiting_updates = get_option( '_tm_waited_updates', array() );
	if ( ! empty( $waiting_updates ) && strpos( $_SERVER['REQUEST_URI'], 'admin.php' ) && ! strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=' . \eoxia\Config_Util::$init['task-manager']->update_page_url ) ) :
		\eoxia\Update_Manager_Class::g()->display_say_to_update( 'task-manager', __( 'Need to update Task Manager data', 'task-manager' ) );
	else :
		if ( ! empty( $id ) ) :
			echo do_shortcode( '[task id="' . $id . '"]' );
		else :
			echo do_shortcode( '[task term="' . $term . '" categories_id_selected="' . $categories_id_selected . '" follower_id_selected="' . $follower_id_selected . '" status="any" post_parent="0" with_wrapper="0"]' );
		endif;
	endif;

	?>
</div>
