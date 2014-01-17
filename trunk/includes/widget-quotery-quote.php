<?php
class Quotery_Quote_Widget extends WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	protected $plugin_slug;

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct()
	{

		$this->plugin_slug = Quotery_Quote::get_instance()->get_plugin_slug();

		parent::__construct(
			'quotery-quote-widget',
			__( 'Quotery: Quote of the day', $this->plugin_slug ),
			array(
				'classname'		=>	'quotery-quote-class',
				'description'	=>	__( 'Get famous quote of the day from Quotery.com', $this->plugin_slug )
			)
		);

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance )
	{
		extract( $args, EXTR_SKIP );

		echo $before_widget;

		Quotery_Quote::get_instance()->quote_html($instance);

		echo $after_widget;

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['topics'] 	= Quotery_Quote::get_instance()->filter_in_array($new_instance['topics'], 	Quotery_Quote::get_instance()->get_topics_options());
		// $instance['quantity'] 	= Quotery_Quote::get_instance()->filter_in_array($new_instance['quantity'], Quotery_Quote::get_instance()->get_quantity_options());
		$instance['color'] 		= Quotery_Quote::get_instance()->filter_in_array($new_instance['color'], 	Quotery_Quote::get_instance()->get_color_options());
		$instance['author']		= (int) $new_instance['author'];
		$instance['social']		= (int) $new_instance['social'];

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance )
	{
		$instance = wp_parse_args(
			(array) $instance,
			Quotery_Quote::get_instance()->get_quote_default_settings()
		);

		// Display the admin form
		include( plugin_dir_path( __FILE__ ) . 'views/widget-admin.php' );

	} // end form

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Quotery_Quote_Widget");' ) );