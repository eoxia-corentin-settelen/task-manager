<?php
/**
 * La liste des commentaires dans le frontend.
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
}

if ( ! empty( $comments ) ) :
	foreach ( $comments as $comment ) :
		if ( 0 !== $comment->id ) :
			\eoxia\View_Util::exec( 'task-manager', 'comment', 'frontend/comment', array(
				'comment' => $comment,
			) );
		endif;
	endforeach;
endif;
