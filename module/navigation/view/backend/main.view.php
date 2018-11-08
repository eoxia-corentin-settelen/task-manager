<?php
/**
 * Vue pour afficher la barre de recherche.
 *
 * @since 1.0.0
 * @version 1.6.0
 *
 * @package Task_Manager
 */

namespace task_manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="form" action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="POST" >
	<header class="wpeo-header-bar wpeo-general-search">
		<label for="general-search">
			<i class="dashicons dashicons-search"></i>
			<input type="text" name="term" value="<?php echo esc_attr( $param['term'] ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'task-manager' ); ?>" />
		</label>
		
		<div class="wpeo-dropdown dropdown-force-display">
			<div class="dropdown-toggle wpeo-button button-main"><span><?php esc_html_e( 'Advanced search', 'task-manager' ); ?></span><i class="button-icon fas fa-caret-down"></i></div>
			<ul class="dropdown-content wpeo-grid grid-2">
				<li class="dropdown-item">
					<div class="form-element">
						<span class="form-label"><?php esc_html_e( 'ID Task', 'task-manager' ); ?></span>
						<label class="form-field-container">
							<input type="text" class="form-field" name="task_id" />
						</label>
					</div>
				</li>
				<li class="dropdown-item">
					<div class="form-element">
						<span class="form-label"><?php esc_html_e( 'ID Point', 'task-manager' ); ?></span>
						<label class="form-field-container">
							<input type="text" class="form-field" name="point_id" />
						</label>
					</div>
				</li>
		
				<li class="dropdown-item">
					<?php $eo_search->display( 'tm_search_admin' ); ?>
				</li>
				
				<li class="dropdown-item">
					<?php $eo_search->display( 'tm_search_customer' ); ?>
				</li>
				
				<li class="dropdown-item">
					<?php $eo_search->display( 'tm_search_order' ); ?>
				</li>
				
				<li class="dropdown-item tag-search">
					<?php
					\eoxia\View_Util::exec( 'task-manager', 'navigation', 'backend/tags', array(
						'categories' => $categories,
					) );
					?>
				</li>
				
				<li class="dropdown-item">
					<a class="wpeo-button button-main wpeo-modal-event"
						data-parent="form"
						data-target="wpeo-modal"><i class="button-icon fal fa-hand-pointer"></i> <span><?php esc_html_e( 'Create shortcut', 'task-manager' ); ?></span></a>
						
				</li>
				
				<li class="dropdown-item">
					<a class="action-input search-button"
					data-loader="form"
					data-namespace="taskManager"
					data-module="navigation"
					data-before-method="checkDataBeforeSearch"
					data-action="search"
					data-parent="form"><?php esc_html_e( 'Search', 'task-manager' ); ?></a>
				</li>
				
			</ul>
		</div>
		
		<?php \eoxia\View_Util::exec( 'task-manager', 'navigation', 'backend/modal-create-shortcut' ); ?>
	</header>
	
</div>

<?php Navigation_Class::g()->display_search_result( 
	$param['term'],
	$param['status'],
	$param['task_id'],
	$param['point_id'],
	$param['post_parent'],
	$param['categories_id'],
	$param['user_id'] );
