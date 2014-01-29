<?php
/**
 * Plugin class. This class is used to work with the
 * administrative side of the WordPress site.
 *
 * @package Quotery_Qod_Admin
 * @author  Quotery <contact@quotery.com>
 */
class Quotery_Qod_Admin
{
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	protected $plugin_slug;

	protected $inline_js;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct()
	{
		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$this->plugin_slug 	= Quotery_Quote::get_instance()->get_plugin_slug();

		add_action( 'media_buttons', 								array( $this, 'media_buttons' ), 20 );
		add_action( 'media_upload_insert_quotery_qod_shortcode', 	array( $this, 'media_browser' ) );
		add_action( 'admin_footer', 								array( $this, 'output_inline_js' ), 25 );

		//add_action( 'admin_enqueue_scripts', 						array( $this, 'enqueue_styles' ) );
		//add_action( 'admin_enqueue_scripts', 						array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_menu', 									array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', 									array( $this, 'process_options_page') );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu()
	{

		$this->plugin_screen_hook_suffix = add_options_page(
			__('Quotery: Quote of the day', $this->plugin_slug),
			__('Quotery: Quote of the day', $this->plugin_slug),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render options page.
	 */
	public function display_plugin_admin_page()
	{
		include_once( 'views/admin.php' );
	}


	/**
	 * Form handler for options page
	 * Fires when form is submitted.
	 */
	public function process_options_page()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['page']) && $_GET['page'] == $this->plugin_slug) {
			if (!wp_verify_nonce($_POST['_qqod'], 'clear')) {
				wp_die('You have no access to this page');
			}

			// clear all cache
			Quotery_Quote::get_instance()->clear_cache();

			wp_redirect(admin_url('admin.php?page=' . $this->plugin_slug . '&cleared'));
			exit;
		}
	}

	/**
	 * media_buttons function.
	 *
	 * @access public
	 * @return void
	 */
	public function media_buttons( $editor_id = 'content' )
	{
		global $post;

		echo '<a href="#" id="quotery-qod-insert-quote" class="button insert-quote" data-editor="' . esc_attr( $editor_id ) . '" title="' . esc_attr__( 'Insert Quote', $this->plugin_slug ) . '">' . __( 'Insert Quote', $this->plugin_slug ) . '</a>';

		ob_start();
		?>
		jQuery(function($){
			$('#quotery-qod-insert-quote').on('click', function(e){
				tb_show('<?php esc_attr_e( 'Insert Quote', $this->plugin_slug ); ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&amp;type=insert_quotery_qod_shortcode&amp;from=wpdlm01&amp;TB_iframe=true&amp;height=200');
				return false;
			});
		});
		<?php

		$js_code = ob_get_clean();
		$this->add_inline_js( $js_code );
	}

	/**
	 * media_browser function.
	 *
	 * @access public
	 * @return void
	 */
	public function media_browser()
	{
		// Enqueue scripts and styles for panel
		wp_enqueue_script( 'common' );
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );

		$this->enqueue_styles();

		echo '<!DOCTYPE html><html lang="en"><head><title>' . __( 'Insert S3 File', $this->plugin_slug ) . '</title><meta charset="utf-8" />';

		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );
		do_action( 'admin_head' );

		$instance = Quotery_Quote::get_instance()->get_quote_default_settings();

		echo '<body id="ww-s3-file-insert-body" class="wp-core-ui">';
		?>
		<div class="wrap">


		<h2 class="nav-tab-wrapper">
			<span class="nav-tab nav-tab-active"><?php _e( 'Insert Shortcode', $this->plugin_slug ); ?></span>
		</h2>
		<form id="ww-s3-file-insert-shortcode" style="padding: 20px;">
			<p>
				<label for="quotery_qod_title"><?php _e('Title:', $this->plugin_slug) ?></label>
				<input type="text" class="widefat" id="quotery_qod_title" name="title" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="quotery_qod_topics"><?php _e('Topic:', $this->plugin_slug) ?></label>
				<select class="widefat" id="quotery_qod_topics" name="topics">
					<?php foreach (Quotery_Quote::get_instance()->get_topics_options() as $value => $name): ?>
						<option value="<?php echo $value ?>"<?php echo $value == $instance['topics'] ? ' selected="selected"' : ''?>><?php echo $name ?></option>
					<?php endforeach ?>
				</select>
			</p>
			<p>
				<label for="quotery_qod_color"><?php _e('Color:', $this->plugin_slug) ?></label>
				<select class="widefat" id="quotery_qod_color" name="color">
					<?php foreach (Quotery_Quote::get_instance()->get_color_options() as $value => $name): ?>
						<option value="<?php echo $value ?>"<?php echo $value == $instance['color'] ? ' selected="selected"' : ''?>><?php echo $name ?></option>
					<?php endforeach ?>
				</select>
			</p>
			<p>
				<input type="checkbox" id="quotery_qod_author" name="author" value="1" <?php echo $instance['author'] ? 'checked="checked"' : ''; ?> />
				<label for="quotery_qod_author"><?php _e('Display author', $this->plugin_slug) ?></label>
			</p>
			<p>
				<input type="checkbox" id="quotery_qod_social" name="social" value="1" <?php echo $instance['social'] ? 'checked="checked"' : ''; ?> />
				<label for="quotery_qod_social"><?php _e('Display social links', $this->plugin_slug) ?></label>
			</p>
			<p>
				<input type="button" id="quotery-qod-insert-quote-shortcode" class="button button-primary button-large" value="<?php _e( 'Insert Shortcode', $this->plugin_slug ); ?>" />
			</p>
		</form>
		</div>
		<script type="text/javascript">
			jQuery(function($) {
				$('#quotery-qod-insert-quote-shortcode').on('click', function(){
					var win = window.dialogArguments || opener || parent || top;
					var file_id = jQuery('#ww-s3-file-insert-id').val();

					var args = '';

					$('#ww-s3-file-insert-shortcode p input:text, select, input:checkbox').each(function(){
						var field = $(this);
						args += ' ' + field.attr('name') + '="' + field.val() + '"';
					})


					var shortcode   = '[quotery_qod' + args + ']';
					console.log(shortcode);
					win.send_to_editor( shortcode );
					return false;
				});
			});
		</script>
		<?php
		echo '</body></html>';
	}


	/**
	 * Enqueue JS to be added to the footer.
	 *
	 * @access public
	 * @param mixed $code
	 * @return void
	 */
	public function add_inline_js( $code )
	{
		$this->inline_js .= "\n" . $code . "\n";
	}

	/**
	 * Output enqueued JS
	 *
	 * @access public
	 * @return void
	 */
	public function output_inline_js()
	{
		if ( $this->inline_js ) {
			echo "<script type=\"text/javascript\">\njQuery(document).ready(function($) {";
			echo $this->inline_js;
			echo "});\n</script>\n";
			$this->inline_js = '';
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Quotery_Quote::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Quotery_Quote::VERSION );
	}
}
