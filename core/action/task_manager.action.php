<?php
/**
 * Les actions principales de l'application.
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
}

/**
 * Les actions principales de l'application.
 */
class Task_Manager_Action {

	/**
	 * Le constructeur ajoutes les actions WordPress suivantes:
	 * admin_enqueue_scripts (Pour appeller les scripts JS et CSS dans l'admin)
	 * admin_print_scripts (Pour appeler les scripts JS en bas du footer)
	 * plugins_loaded (Pour appeler le domaine de traduction)
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'callback_admin_enqueue_scripts' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'callback_enqueue_scripts' ), 11 );
		add_action( 'wp_print_scripts', array( $this, 'callback_wp_print_scripts' ) );

		add_action( 'init', array( $this, 'callback_plugins_loaded' ) );
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 12 );

		add_action( 'wp_ajax_close_tm_change_log', array( $this, 'callback_close_change_log' ) );
	}

	/**
	 * Initialise le fichier style.min.css et backend.min.js du plugin Task Manager.
	 *
	 * @since 0.1.0
	 * @version 1.5.0
	 *
	 * @return void nothing
	 */
	public function callback_admin_enqueue_scripts() {
		$screen = get_current_screen();
		wp_register_style( 'task-manager-global-style', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/global.css', array(), \eoxia\config_util::$init['task-manager']->version );
		wp_enqueue_style( 'task-manager-global-style' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_media();
		add_thickbox();

		if ( ! empty( \eoxia\Config_Util::$init['task-manager']->insert_scripts_pages ) ) {
			foreach ( \eoxia\Config_Util::$init['task-manager']->insert_scripts_pages as $insert_script_page ) {
				if ( false !== strpos( $screen->id, $insert_script_page ) ) {
					wp_register_style( 'task-manager-style', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/style.min.css', array(), \eoxia\config_util::$init['task-manager']->version );
					wp_enqueue_style( 'task-manager-style' );

					wp_enqueue_style( 'task-manager-datepicker', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/datepicker.min.css', array(), \eoxia\Config_Util::$init['task-manager']->version );
					wp_enqueue_style( 'task-manager-datetimepicker', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/jquery.datetimepicker.css', array(), \eoxia\Config_Util::$init['task-manager']->version );

					wp_enqueue_script( 'task-manager-masonry', PLUGIN_TASK_MANAGER_URL . 'core/assets/js/masonry.min.js', array(), \eoxia\Config_Util::$init['task-manager']->version );
					wp_enqueue_script( 'task-manager-script', PLUGIN_TASK_MANAGER_URL . 'core/assets/js/backend.min.js', array(), \eoxia\Config_Util::$init['task-manager']->version );
					wp_localize_script( 'task-manager-script', 'taskManager', array(
						'updateManagerUrlPage'      => 'admin_page_' . \eoxia\Config_Util::$init['task-manager']->update_page_url,
						'updateManagerconfirmExit'  => __( 'Your data are being updated. If you confirm that you want to leave this page, your data could be corrupted', 'task-manager' ),
						'updateManagerloader'       => '<img src=' . admin_url( '/images/loading.gif' ) . ' />',
						// Translators: %s is the version number with strong markup.
						'updateManagerInProgress'   => sprintf( __( 'Update %s in progress', 'task-manager' ), '<strong>{{ versionNumber }}</strong>' ),
						// Translators: %s is the version number with strong markup.
						'updateManagerErrorOccured' => sprintf( __( 'An error occured. Please take a look at %s logs', 'task-manager' ), '<strong>{{ versionNumber }}</strong>' ),
					) );
					wp_enqueue_script( 'task-manager-datetimepicker-script', PLUGIN_TASK_MANAGER_URL . 'core/assets/js/jquery.datetimepicker.full.js', array(), \eoxia\Config_Util::$init['task-manager']->version );
					break;
				}
			}
		}

			wp_enqueue_script( 'task-manager-global-script', PLUGIN_TASK_MANAGER_URL . 'core/assets/js/global.min.js', array(), \eoxia\Config_Util::$init['task-manager']->version );
	}

	/**
	 * Enqueue scripts in frontend
	 *
	 * @since 1.0.0
	 * @version 1.5.0
	 */
	public function callback_enqueue_scripts() {
		$pagename = get_query_var( 'pagename' );
		if ( in_array( $pagename, \eoxia\Config_Util::$init['task-manager']->insert_scripts_pages, true ) ) {
			wp_enqueue_style( 'task-manager-datepicker', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/datepicker.min.css', array(), \eoxia\Config_Util::$init['task-manager']->version );
		}

		wp_register_style( 'task-manager-frontend-style', PLUGIN_TASK_MANAGER_URL . 'core/assets/css/frontend.css', array(), \eoxia\Config_Util::$init['task-manager']->version );
		wp_enqueue_style( 'task-manager-frontend-style' );

		wp_enqueue_script( 'task-manager-frontend-script', PLUGIN_TASK_MANAGER_URL . 'core/assets/js/frontend.min.js', array(), \eoxia\Config_Util::$init['task-manager']->version, false );
		wp_localize_script( 'task-manager-frontend-script', 'taskManagerFrontend', array(
			'wpeo_project_delete_comment_time' => __( 'Delete this comment ?', 'task-manager' ),
		) );
	}

	/**
	 * Initialise le fichier MO et les capacités
	 *
	 * @since 1.0.0
	 * @version 1.5.0
	 */
	public function callback_plugins_loaded() {
		$i18n_loaded = load_plugin_textdomain( 'task-manager', false, PLUGIN_TASK_MANAGER_DIR . '/core/assets/language/' );

		/** Set capability to administrator by default */
		$administrator_role = get_role( 'administrator' );
		if ( ! $administrator_role->has_cap( 'manage_task_manager' ) ) {
			$administrator_role->add_cap( 'manage_task_manager' );
		}

		Task_Manager_Class::g()->init_default_data();
	}

	/**
	 * Initialise ajaxurl.
	 *
	 * @since 1.6.0
	 * @version 1.6.0
	 *
	 * @return void
	 */
	public function callback_wp_print_scripts() {
		?>
		<script>var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";</script>
		<?php
	}

	/**
	 * Définition du menu "Task Manager" dans l'administration de WordPress.
	 *
	 * @since 1.0.0
	 * @version 1.5.0
	 */
	public function callback_admin_menu() {
		$title = __( 'Task', 'task-manager' );
		$title = apply_filters( 'tm_task_main_menu_title', $title );

		add_menu_page( $title, $title, 'manage_task_manager', 'wpeomtm-dashboard', array( Task_Manager_Class::g(), 'display' ), PLUGIN_TASK_MANAGER_URL . 'core/assets/icon-16x16.png' );
		add_submenu_page( 'wpeomtm-dashboard', __( 'Task', 'task-manager' ), __( 'Task', 'task-manager' ), 'manage_task_manager', 'wpeomtm-dashboard', array( Task_Manager_Class::g(), 'display' ) );
	}

	/**
	 * Lors de la fermeture de la notification de la popup.
	 * Met la metadonnée '_wptm_user_change_log' avec le numéro de version actuel à true.
	 *
	 * @since 1.5.0
	 * @version 1.5.0
	 *
	 * @return void
	 */
	public function callback_close_change_log() {
		check_ajax_referer( 'close_change_log' );

		$version = ! empty( $_POST['version'] ) ? sanitize_text_field( $_POST['version'] ) : '';

		if ( empty( $version ) ) {
			wp_send_json_error();
		}

		$meta = get_user_meta( get_current_user_id(), '_wptm_user_change_log', true );

		if ( empty( $meta ) ) {
			$meta = array();
		}

		$meta[ $version ] = true;
		update_user_meta( get_current_user_id(), '_wptm_user_change_log', $meta );

		wp_send_json_success( array() );
	}

}

new Task_Manager_Action();
