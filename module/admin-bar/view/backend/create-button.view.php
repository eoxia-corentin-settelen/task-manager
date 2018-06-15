<?php
/**
 * Les boutons de le sous menu "Create" de l'admin bar de WordPress.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @since     1.6.0
 * @version   1.7.1
 * @copyright 2018 Eoxia.
 * @package   Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<span class="<?php echo esc_attr( $button['class'] ); ?>" <?php echo $actions_output; ?>><?php echo esc_html( $button['text'] ); ?></span>
