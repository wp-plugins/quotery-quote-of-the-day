<?php
/**
 * Plugin Name.
 *
 * @package   Quotery_Quote
 * @author    Quotery <contact@quotery.com>
 * @license   GPL-2.0+
 * @link      http://www.quotery.com
 * @copyright 2014 Quotery
 */

/**
 * Plugin class. This class is used to work with the
 * public-facing side of the WordPress site.
 *
 * @package Quotery_Quote
 * @author  Quotery <contact@quotery.com>
 */
class Quotery_Quote {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION 						= '1.0.7';

	/**
	 * Cache prefix
	 */
	const CACHE_GROUP 					= 'quotery_qod_plugin_';

	/**
	 * Unique identifier for your plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug 				= 'quotery-quote-of-the-day';

	protected $quotes_url 				= 'http://www.quotery.com/api/qod/';
	protected $quotes_categories_url 	= 'http://www.quotery.com/api/qod/get/categories/';
	// protected $quotes_url 				= 'http://localhost/wp/quotery/api/qod/';
	// protected $quotes_categories_url 	= 'http://localhost/wp/quotery/api/qod/get/categories/';


	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action('init', array($this, 'add_shortcodes'));
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

		// http://api.theysaidso.com/qod.json
	/**
	 * Get a quote of the day
	 *
	 * @since    1.0.0
	 */
	public function get_quote($options = array(), $force_load = false)
	{

		$category = $options['topics'] ? $options['topics'] : 'all';

		$cache_name = 'quotery_qod_plugin_' . $category;

		$expire 	= strtotime('tomorrow') - time(); // cache until next day

		$url = $this->quotes_url . $category;

		if ($force_load) {
			$quote = false;
		} else {
			$quote = get_transient($cache_name);
		}
		// $quote = false;

		if (false === $quote) {
			$remote = wp_remote_get($url);
			$quote = json_decode($remote['body']);
			if ($quote && $quote->type == 'success') {
				set_transient($cache_name, $quote, $expire);
			} else {
				$quote = false;
			}
		}

		return $quote;
	}

	public function get_categories($force_load = true)
	{
		$cache_name = 'quotery_qod_plugin_categories';
		$expire 	= 60 * 30; // cache quote for 30 minutes

		if ($force_load) {
			$categories = false;
		} else {
			$categories = get_transient($cache_name);
		}
		if (false === $categories) {

			$remote = wp_remote_get($this->quotes_categories_url . '?' . time());
			$categories = json_decode($remote['body']);

			if ($categories && $categories->type == 'success') {
				$categories = (array) $categories->contents->categories;
				set_transient($cache_name, $categories, $expire);
			} else {
				$categories = false;
			}
		}

		return $categories;
	}

	public function add_shortcodes()
	{
		add_shortcode('quotery_qod', array($this, 'shortcode_quotery_qod'));
	}

	public function shortcode_quotery_qod($atts, $content = "")
	{
		$instance = shortcode_atts($this->get_quote_default_settings(), $atts);

		ob_start();
		$this->quote_html($instance);
		$html = ob_get_clean();

		return $html;
	}

	public function quote_html($instance)
	{
		$title = apply_filters('widget_title', $instance['title'] );

		$quote_data = $this->get_quote(array(
			'topics' 	=> $instance['topics'],
			// 'quantity'	=> $instance['quantity'],
		));

		if ($quote_data) {
			$quote = $quote_data->contents->quote->quote;
			$author = $quote_data->contents->quote->author;
			$share_url = $quote_data->contents->quote->share_url;
			$follow = $quote_data->contents->follow;
		} else {
			$quote = __('Cannot fetch quote from source', $this->plugin_slug);
			$instance['social'] = $instance['author'] = false;
		}

		include( plugin_dir_path( __FILE__ ) . 'views/widget-public.php' );
	}

	public function get_quote_default_settings()
	{
		return array(
			'title' 	=> __('Quote of the Day"', $this->plugin_slug),
			'topics'	=> 'all',
			// 'quantity'	=> 1,
			'color'		=> 'light',
			'author'	=> 'yes',
			'social'	=> 'yes',
		);
	}

	public function get_color_options()
	{
		return array(
			'light' 	=> __('Light', $this->plugin_slug),
			'orange' 	=> __('Orange', $this->plugin_slug),
			'dark' 		=> __('Dark', $this->plugin_slug),
		);
	}

	public function get_quantity_options()
	{
		return array(
			1 => __('1', $this->plugin_slug),
			2 => __('2', $this->plugin_slug),
			3 => __('3', $this->plugin_slug),
		);
	}

	public function filter_in_array($key, $array)
	{
		$keys = array_keys($array);

		return in_array($key, $keys) ? $key : $keys[0];
	}

	public function get_topics_options()
	{
		$categories = $this->get_categories();

		if (!$categories) {
			$categories = array(
				'all' 		=> __('All', $this->plugin_slug),
			);
		}

		return $categories;
	}

	public function clear_cache()
	{
		global $wpdb;
		return $wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '%' . self::CACHE_GROUP . '%'
		));
	}
}
