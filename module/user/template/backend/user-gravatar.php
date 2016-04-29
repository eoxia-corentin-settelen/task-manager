<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if ( !empty( $user->id ) ): ?>
	<li title="<?php echo $user->email; ?>" style="background-color: #<?php echo $user->option['user_info']['avatar_color']; ?>" class='user <?php echo !empty( $active ) ? $active : ''; ?>  wpeo-user-<?php echo $user->id; ?>' data-id="<?php echo $user->id; ?>" <?php echo !empty( $nonce ) ? "data-nonce='" . wp_create_nonce( $nonce ) . "'" : ''; ?>>
		<div>
			<img src="<?php echo $user->option['user_info']['avatar']; ?>?s=<?php echo !empty( $size ) ? $size : 50; ?>&d=blank" />
			<?php if (!empty( $display_name ) ): ?><span><?php echo $user->displayname; ?></span><?php endif; ?>
		</div>
		<div class="wpeo-avatar-initial"><span><?php echo strtoupper( $user->option['user_info']['initial'] ); ?></span></div>
	</li>
<?php else: ?>
	<li>
		<img src="http://www.gravatar.com/avatar/00000000000000000000000000000000?s=<?php echo !empty( $size ) ? $size : 50; ?>" />
	</li>
<?php endif; ?>
