<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } ?>
<?php

/**
 * K2 is a custom Theme class for the K2 theme.
 *
 * @package Habari
 */

/**
 * A custom theme for K2 output
 */
class K2 extends Theme
{
	protected $defaults = array(
		'login_display_location' => 'sidebar',
		'home_label' => 'Blog',
		'show_author' => false,
	);

	/**
	 * Add the K2 menu block to the nav area upon theme activation if there's nothing already there
	 */
	public function action_theme_activated()
	{
		$opts = Options::get_group( __CLASS__ );
		if ( empty( $opts ) ) {
			Options::set_group( __CLASS__, $this->defaults );
		}

		$blocks = $this->get_blocks( 'nav', 0, $this );
		if ( count( $blocks ) == 0 ) {
			$block = new Block( array(
				'title' => _t( 'K2 Menu', 'k2' ),
				'type' => 'k2_menu',
			) );

			$block->add_to_area( 'nav' );
			Session::notice( _t( 'Added K2 Menu block to Nav area.' ), 'k2' );
		}
	}

	/**
	 * Execute on theme init to apply these filters to output
	 */
	public function action_init_theme()
	{
		// Apply Format::autop() to comment content...
		Format::apply( 'autop', 'comment_content_out' );
		// Apply Format::tag_and_list() to post tags...
		Format::apply( 'tag_and_list', 'post_tags_out' );

		// Remove the comment on the following line to limit post length on the home page to 1 paragraph or 100 characters
		//Format::apply_with_hook_params( 'more', 'post_content_out', _t('more'), 100, 1 );
	}

	/**
	 * Add additional template variables to the template output.
	 *
	 *  You can assign additional output values in the template here, instead of
	 *  having the PHP execute directly in the template.  The advantage is that
	 *  you would easily be able to switch between template types (RawPHP/Smarty)
	 *  without having to port code from one to the other.
	 *
	 *  You could use this area to provide "recent comments" data to the template,
	 *  for instance.
	 *
	 *  Note that the variables added here should possibly *always* be added,
	 *  especially 'user'.
	 *
	 *  Also, this function gets executed *after* regular data is assigned to the
	 *  template.  So the values here, unless checked, will overwrite any existing
	 *  values.
	 */
	public function add_template_vars ( ) {

		parent::add_template_vars();

		$this->assign( 'display_login', Options::get( __CLASS__ . '__login_display_location', 'sidebar' ) );
		$this->assign( 'show_author', Options::get( __CLASS__ . '__show_author', false ) );
		$this->assign( 'home_label' , Options::get( __CLASS__ . '__home_label', _t( 'Blog' ) ) );

		$this->add_template( 'k2_text', dirname( __FILE__ ) . '/formcontrol_text.php' );

		if ( !isset( $this->pages ) ) {
			$this->pages = Posts::get( 'page_list' );
		}

		if ( User::identify()->loggedin ) {
			Stack::add( 'template_header_javascript', Site::get_url( 'scripts' ) . '/jquery.js', 'jquery' );
		}

		if ( ( $this->request->display_entry || $this->request->display_page ) && isset( $this->post ) && $this->post->title != '' ) {
			$this->page_title = $this->post->title . ' - ' . Options::get( 'title' );
		}
		else {
			$this->page_title = Options::get('title');
		}
	}

	/**
	 * function action_theme_ui
	 * Create and display the Theme configuration
	 **/
	public function action_theme_ui()
	{
		$opts = Options::get_group( __CLASS__ );
		if ( empty( $opts ) ) {
			Options::set_group( __CLASS__, $this->defaults );
		}

		$controls = array();
		$controls['home_label'] = array(
			'label' => _t('Home tab label:', 'k2'),
			'type' => 'text'
		);
		$controls['login_display_location'] = array(
			'label' => _t('Login display:', 'k2'),
			'type' => 'select',
			'options' => array(
				'nowhere' => _t( 'Nowhere', 'k2' ),
				'header' => _t( 'As a navigation tab', 'k2' ),
				'sidebar' => _t( 'In the sidebar', 'k2' )
			)
		);
		$controls['show_author'] = array(
			'label' => _t( 'Display author:', 'k2' ),
			'type' => 'checkbox',
		);

		$ui = new FormUI( strtolower( get_class( $this ) ) );
		$wrapper = $ui->append( 'wrapper', 'k2config', 'k2config' );
		$wrapper->class = "settings clear";

		foreach ( $controls as $option_name => $option ) {
			$field = $wrapper->append( $option['type'], $option_name, __CLASS__. '__' . $option_name, $option['label'] );
			$field->template = 'optionscontrol_' . $option['type'];
			$field->class = "item clear";
			if ( $option['type'] === 'select' and isset( $option['options'] ) ) {
				$field->options = $option['options'];
			}
		}
		$ui->append( 'submit', 'save', _t( 'Save', 'k2' ) );
		$ui->on_success( array( $this, 'config_updated') );
		$ui->out();
	}

	/**
	 * function config_updated
	 * Return a success message
	 **/
	public function config_updated( $ui )
	{
		Session::notice( _t( 'K2 configuration updated', 'k2' ) );
		$ui->save();
	}

	public function k2_comment_class( $comment, $post )
	{
		$class = 'class="comment';
		if ( $comment->status == Comment::STATUS_UNAPPROVED ) {
			$class.= '-unapproved';
		}
		// check to see if the comment is by a registered user
		if ( $u = User::get( $comment->email ) ) {
			$class.= ' byuser comment-author-' . Utils::slugify( $u->displayname );
		}
		if ( $comment->email == $post->author->email ) {
			$class.= ' bypostauthor';
		}

		$class.= '"';
		return $class;
	}

/**
 * If comments are enabled, or there are comments on the post already, output a link to the comments.
 *
 */
	public function comments_link( $post )
	{
		if ( !$post->info->comments_disabled || $post->comments->approved->count > 0 ) {
			$comment_count = $post->comments->approved->count;
			echo "<span class=\"commentslink\"><a href=\"{$post->permalink}#comments\" title=\"" . _t( 'Comments on this post', 'k2' ) . "\">{$comment_count} " . _n( 'Comment', 'Comments', $comment_count, 'k2' ) . "</a></span>";
		}

	}

	/**
	 * Customize comment form layout. Needs thorough commenting.
	 */
	public function action_form_comment( $form ) {
		$form->cf_commenter->caption = '<small><strong>' . _t( 'Name', 'k2' ) . '</strong></small><span class="required">' . ( Options::get( 'comments_require_id' ) == 1 ? ' *' . _t( 'Required', 'k2' ) : '' ) . '</span>';
		$form->cf_commenter->template = 'k2_text';
		$form->cf_email->caption = '<small><strong>' . _t( 'Mail', 'k2' ) . '</strong> ' . _t( '(will not be published)', 'k2' ) .'</small><span class="required">' . ( Options::get( 'comments_require_id' ) == 1 ? ' *' . _t( 'Required', 'k2' ) : '' ) . '</span>';
		$form->cf_email->template = 'k2_text';
		$form->cf_url->caption = '<small><strong>' . _t( 'Website', 'k2' ) . '</strong></small>';
		$form->cf_url->template = 'k2_text';
	        $form->cf_content->caption = '';
		$form->cf_submit->caption = _t( 'Submit', 'k2' );
	}

	/**
	 * Add a k2_menu block to the list of available blocks
	 */
	public function filter_block_list( $block_list )
	{
		$block_list['k2_menu'] = _t( 'K2 Menu', 'k2' );
		return $block_list;
	}

	/**
	 * Produce a menu for the K2 menu block from all of the available pages
	 */
	public function action_block_content_k2_menu( $block, $theme )
	{
		$menus = array( 'home' => array(
			'link' => Site::get_url( 'habari' ),
			'title' => Options::get( 'title' ),
			'caption' => $theme->home_label,
			'cssclass' => $theme->request->display_home ? 'current_page_item' : '',
		) );
		$pages = Posts::get('page_list');
		foreach( $pages as $page ) {
			$menus[] = array(
				'link' => $page->permalink,
				'title' => $page->title,
				'caption' => $page->title,
				'cssclass' => (isset( $theme->post ) && $theme->post->id == $page->id ) ? 'current_page_item' : '',
			);
		}
		if ( User::identify()->loggedin ) {
			$menus['admin'] = array( 'link' => Site::get_url( 'admin', 'k2' ), 'title' => _t( 'Admin area', 'k2' ), 'caption' => _t( 'Admin', 'k2' ), 'cssclass' => 'admintab' );
		}
		else {
			if( $theme->display_login == 'header' ) {
				$menus['admin'] = array( 'link' => Site::get_url( 'login', 'k2' ), 'title' => _t( 'Login', 'k2' ), 'caption' => _t( 'Login', 'k2' ), 'cssclass' => 'admintab' );
			}
		}
		$block->menus = $menus;
	}
}

?>
