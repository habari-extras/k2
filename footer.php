<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } ?>

<!-- footer -->
<div class="clear"></div>
</div>

<hr>

<p id="footer">
<small><?php Options::out('title'); _e(' is powered by', 'k2'); ?> <a href="http://www.habariproject.org/" title="Habari">Habari</a> <?php _e('and a huge amount of ', 'k2'); ?>
<a href="http://en.wikipedia.org/wiki/Caffeine" title="<?php _e('Caffeine', 'k2'); ?>" rel="nofollow">C<sub>8</sub>H<sub>10</sub>N<sub>4</sub>O<sub>2</sub></a></small><br>
<small><a href="<?php URL::out( 'atom_feed', array( 'index' => '1' ) ); ?>"><?php _e('Atom Entries', 'k2'); ?></a> <?php _e('and', 'k2'); ?> <a href="<?php URL::out( 'atom_feed_comments' ); ?>"><?php _e('Atom Comments', 'k2'); ?></a></small>
</p>

<?php echo $theme->footer(); ?>

<?php
/* In order to see DB profiling information:
1. Insert this line in your config file: define( 'DEBUG', true );
2.Uncomment the followng line
*/
// include 'db_profiling.php';
?>
</body>
</html>
<!-- /footer -->
