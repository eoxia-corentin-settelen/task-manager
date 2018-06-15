<?php
/**
 * Les actions filtres aux temps rapides.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @since     1.7.1
 * @version   1.7.1
 * @copyright 2018 Eoxia.
 * @package   Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Les actions filtres aux temps rapides.
 */
class Quick_Time_Filter {

	/**
	 * Initialise les filtres liées aux temps rapides.
	 *
	 * @since 1.7.1
	 * @version 1.7.1
	 */
	public function __construct() {
		add_filter( 'tm_admin_bar_create_buttons', array( $this, 'callback_admin_bar_add_button' ) );
	}

	/**
	 * Ajoutes le bouton "Quick Time" dans le sous menu "Create" de l'admin bar de WordPress.
	 *
	 * @since 1.7.1
	 * @version 1.7.1
	 *
	 * @param array $buttons  Tableau contenu tous les boutons a ajouté.
	 *
	 * @return array $buttons Tableau contenu tous les boutons a ajouté + le nouveau bouton ajouté par cette méthode.
	 */
	public function callback_admin_bar_add_button( $buttons ) {
		$buttons[] = array(
			'id'    => 'button-open-popup-quick-time',
			'text'  => __( 'Quick time', 'task-manager' ),
			'class' => 'wpeo-modal-event',
			'data'  => array(
				'action' => 'load_popup_quick_time',
				'class'  => 'popup-quick-time',
				'title'  => __( 'Quick time', 'task-manager' ),
				'nonce'  => wp_create_nonce( 'load_popup_quick_time' ),
			),
		);

		return $buttons;
	}
}

new Quick_Time_Filter();
