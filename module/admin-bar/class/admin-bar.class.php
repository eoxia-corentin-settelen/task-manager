<?php
/**
 * Classe relatives à l'admin bar.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @since     1.0.0
 * @version   1.7.1
 * @copyright 2018 Eoxia.
 * @package   Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe relatives à l'admin bar.
 */
class Admin_Bar_Class extends \eoxia\Singleton_Util {

	/**
	 * Tableau de bouton à rajouter dans le sous menu "Create" de l'admin bar.
	 *
	 * Cette donnée est remplis par différent plugins/modules grâce aux filtres
	 *
	 * @since 1.7.1
	 * @version 1.7.1
	 *
	 * @var array
	 */
	public $buttons = array();

	/**
	 * Constructeur obligatoire pour Singleton_Util
	 *
	 * @since 1.0.0
	 * @version 1.2.0
	 *
	 * @return void
	 */
	protected function construct() {}

	/**
	 * Ajoutes des boutons provenant de $this->button dans le sous menu "Create" de l'admin bar de WordPress.
	 *
	 * @since 1.0.0
	 * @version 1.7.1
	 *
	 * @param mixed $wp_admin_bar L'objet de WordPress pour gérer les noeuds.
	 *
	 * @return void
	 */
	public function init_create_buttons( $wp_admin_bar ) {
		$this->buttons = apply_filters( 'tm_admin_bar_create_buttons', $this->buttons );

		if ( ! empty( $this->buttons ) ) {
			foreach ( $this->buttons as $button ) {
				$action_output = '';

				if ( ! empty( $button['data'] ) ) {
					foreach ( $button['data'] as $key => $action ) {
						$action_output .= 'data-' . $key . '="' . $action . '"';
					}
				}

				ob_start();
				\eoxia\View_Util::exec( 'task-manager', 'admin-bar', 'backend/create-button', array(
					'button'         => $button,
					'actions_output' => $action_output,
				) );

				$node_button = array(
					'id'     => $button['id'],
					'parent' => 'new-content',
					'title'  => ob_get_clean(),
				);

				$wp_admin_bar->add_node( $node_button );
			}
		}
	}

	/**
	 * Ajoutes le logo de TaskManager et le nombre de demande faites par les clients.
	 * En cliquant dessus, renvoies vers la page "task-manager-indicator".
	 *
	 * @since 1.0.0
	 * @version 1.6.1
	 *
	 * @param mixed $wp_admin_bar L'objet de WordPress pour gérer les noeuds.
	 * @return void
	 */
	public function init_search( $wp_admin_bar ) {
		$use_search_in_admin_bar = get_option( \eoxia\Config_Util::$init['task-manager']->setting->key_use_search_in_admin_bar, true );
		if ( $use_search_in_admin_bar ) {
			ob_start();
			\eoxia\View_Util::exec( 'task-manager', 'admin-bar', 'backend/main' );
			$view = ob_get_clean();

			$button_open_popup = array(
				'id'    => 'button-search-task',
				'title' => $view,
			);

			$wp_admin_bar->add_node( $button_open_popup );
		}
	}

	/**
	 * Ajoutes le logo de TaskManager et le nombre de demande faites par les clients.
	 * En cliquant dessus, renvoies vers la page "task-manager-indicator".
	 *
	 * @since 1.0.0
	 * @version 1.6.0
	 *
	 * @param mixed $wp_admin_bar L'objet de WordPress pour gérer les noeuds.
	 * @return void
	 */
	public function init_customer_link( $wp_admin_bar ) {
		$have_new = false;

		$count = $this->get_number_ask();

		if ( 0 < $count ) {
			$have_new = true;
		}

		$link_to_page = array(
			'id'    => 'button-open-popup-last-ask-customer',
			'href'  => admin_url( 'admin.php?page=task-manager-indicator' ),
			'title' => '<img src="' . PLUGIN_TASK_MANAGER_URL . 'core/assets/icon-16x16.png" alt="TM" />',
		);

		if ( $have_new ) {
			$link_to_page['title'] .= '<span class="wp-core-ui wp-ui-notification"><span>' . $count . '</span></span>';
		}

		$wp_admin_bar->add_node( $link_to_page );
	}

	/**
	 * Renvoies le nombre de demande
	 *
	 * @since 1.2.0
	 * @version 1.6.0
	 *
	 * @return integer Le nombre de demande
	 */
	public function get_number_ask() {
		$count = '';

		if ( isset( \eoxia\Config_Util::$init['task-manager-wpshop'] ) ) {
			$ids   = get_option( \eoxia\Config_Util::$init['task-manager-wpshop']->key_customer_ask, array() );
			$count = 0;
			if ( ! empty( $ids ) ) {
				foreach ( $ids as $task_id => $points ) {
					if ( ! empty( $points ) ) {
						foreach ( $points as $point_id => $id ) {
							$count += count( $id );
						}
					}
				}
			}
		}
		return $count;
	}
}

new Admin_Bar_Class();
