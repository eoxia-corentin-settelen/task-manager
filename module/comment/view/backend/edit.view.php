<?php
/**
 * Edition d'un commentaire dans le backend.
 *
 * @author Jimmy Latour <jimmy.eoxia@gmail.com>
 * @since 1.0.0
 * @version 1.6.0
 * @copyright 2015-2018 Eoxia
 * @package Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<li class="comment new">
	<ul class="wpeo-comment-container">

		<input type="hidden" name="action" value="edit_comment" />
		<?php wp_nonce_field( 'edit_comment' ); ?>
		<input type="hidden" name="comment_id" value="<?php echo esc_attr( $comment->data['id'] ); ?>" />
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $task_id ); ?>" />
		<input type="hidden" name="parent_id" value="<?php echo esc_attr( $point_id ); ?>" />

		<li class="wpeo-comment-date group-date" data-time="true" data-namespace="taskManager" data-module="comment" data-after-method="afterTriggerChangeDate">
			<input type="hidden" class="mysql-date" name="date" value="<?php echo esc_attr( $comment->data['date']['raw'] ); ?>" />
			<input type="hidden" name="value_changed" value="<?php echo ( ! empty( $comment->data['id'] ) ) ? 1 : 0; ?>" />
			<div class="tooltip hover" aria-label="<?php echo esc_attr( $comment->data['date']['rendered']['date_time'] ); ?>"><span class="date dashicons dashicons-calendar-alt"></span></div>
		</li>

		<li class="wpeo-comment-content">
			<input type="hidden" name="content" value="<?php echo esc_attr( $comment->data['content'] ); ?>" />
			<div class="content" contenteditable="true"><?php echo trim( $comment->data['content'] ); ?></div>
			<?php if ( empty( $comment->data['id'] ) ) : ?>
				<span class="wpeo-point-new-placeholder"><?php esc_html_e( 'Your comment here...', 'task-manager' ); ?></span>
			<?php endif; ?>
		</li>

		<li class="wpeo-comment-time">
			<span class="dashicons dashicons-clock"></span>
			<input type="text" name="time" value="<?php echo esc_attr( empty( $comment->data['id'] ) && isset( $comment->data['time_info']['calculed_elapsed'] ) ) ? $comment->data['time_info']['calculed_elapsed'] : $comment->data['time_info']['elapsed']; ?>" />
		</li>

		<?php if ( ! empty( $comment->data['id'] ) ) : ?>
			<li data-parent="wpeo-comment-container" class="wpeo-save-point action-input "><i class="far fa-save" aria-hidden="true"></i></li>
		<?php else : ?>
			<li data-parent="wpeo-comment-container" style="opacity: 0.4;" class="wpeo-point-new-btn action-input dashicons dashicons-plus-alt"></li>
		<?php endif; ?>
	</ul>
</li>
