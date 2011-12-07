<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } ?>
<?php $theme->display ( 'header' ); ?>
<!-- error -->
	<div class="content">
	<div id="primary">
		<div id="post" class="error">

		<div class="entry-head">
			<h3 class="entry-title"><?php _e('Error!', 'k2'); ?></h3>
		</div>

		<div class="entry-content">
			<p><?php _e('The requested post was not found.', 'k2'); ?></p>
		</div>

		</div>
	</div>

	<hr>

	<div class="secondary">

<?php $theme->display ( 'sidebar' ); ?>

	</div>

	<div class="clear"></div>
	</div>
<!-- /error -->
<?php $theme->display ('footer'); ?>
