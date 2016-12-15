<?php
namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) { exit;
} ?>

<!-- Task header : Pour modifier le titre, le temps estimé et ouvrir le dashboard à droite -->
<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="POST">
	<?php wp_nonce_field( 'wpeo_nonce_edit_task_' . $task->id ); ?>
	<input type="hidden" name="task[option][front_info][display_color]" value="<?php echo htmlspecialchars( ! empty( $task->option['front_info']['display_color'] ) ? $task->option['front_info']['display_color'] : '' ); ?>" />
	<ul class="wpeo-task-header">
		<li class="wpeo-task-author"><?php echo get_avatar( $task->author_id, 32 ); ?></li>

		<li class="wpeo-task-id">#<?php echo $task->id; ?></li>

		<li class="wpeo-task-title">
			<input data-nonce="<?php echo wp_create_nonce( 'edit_title' ); ?>" type="text" name="task[title]" class="wpeo-project-task-title" value="<?php echo $task->title; ?>" />
		</li>

		<li class="wpeo-task-setting">
			<toggle class="wpeo-task-open-action" title="<?php _e( 'Options of task', 'task-manager' ); ?>"
							data-parent="wpeo-task-setting" data-target="task-header-action"><i class="fa fa-ellipsis-v"></i></toggle>
			<div class="task-header-action toggle-content">
				<?php echo apply_filters( 'task_header_action', '', $task ); ?>
			</div>
		</li>
	</ul>
	<?php View_Util::exec( 'task', 'backend/task-header-information', array( 'task' => $task ) ); ?>
</form>