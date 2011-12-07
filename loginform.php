<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } ?>
<!-- loginform -->
<?php
if ( isset( $error ) && ( $error == 'Bad credentials' ) ) {
?>
		<p><?php _e('That login is incorrect.', 'k2'); ?></p>

<?php
}
if ( $loggedin ) {
?>
		<p><?php _e('You are logged in as', 'k2'); ?> <a href="<?php URL::out( 'admin', 'page=user&user=' . $user->username ) ?>" title="<?php _e('Edit Your Profile', 'k2'); ?>"><?php echo $user->username; ?></a>.</p>
		<p><?php _e('Want to', 'k2'); ?> <a href="<?php Site::out_url( 'habari' ); ?>/auth/logout"><?php _e('log out', 'k2'); ?></a>?</p>
<?php
}
else {
?>
	<?php Plugins::act( 'theme_loginform_before' ); ?>
		<form method="post" action="<?php URL::out( 'auth', array( 'page' => 'login' ) ); ?>" id="loginform">
			<p>
			<label for="habari_username"><?php _e('Name:', 'k2'); ?></label>
			<input type="text" size="25" name="habari_username" id="habari_username">
			</p>
			<p>
			<label for="habari_password"><?php _e('Password:', 'k2'); ?></label>
			<input type="password" size="25" name="habari_password" id="habari_password">
			</p>
			<?php Plugins::act( 'theme_loginform_controls' ); ?>
			<p>
			<input type="submit" value="<?php _e('Sign in', 'k2'); ?>">
			</p>
		</form>
		<?php Plugins::act( 'theme_loginform_after' ); ?>
<?php
}
?>
<!-- /loginform -->
