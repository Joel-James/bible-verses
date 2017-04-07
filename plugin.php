<?php
/**
 * Bible Verses
 *
 * The WordPress Widget Boilerplate is an organized, maintainable boilerplate for building widgets using WordPress best practices.
 *
 * @package   BibleVerses
 * @author    Joel James <mail@cjoel.com>
 * @license   GPL-2.0+
 * @link      http://duckdev.com
 * @copyright 2017 Joel James
 *
 * @wordpress-plugin
 * Plugin Name:       Bible Verses
 * Plugin URI:        https://wordpress.org/plugins/bible-verses/
 * Description:       Providing you a beautiful random Bible Verses widget.
 * Version:           2.0.0
 * Author:            Joel James
 * Author URI:        https://duckdev.com/
 * Donate link:       https://paypal.me/JoelCJ
 * Text Domain:       bible-verses
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /lang
 * GitHub Plugin URI: https://github.com/Joel-James/bible-verses
 */
 
 // Prevent direct file access
defined ( 'ABSPATH' ) or exit;

class Bible_Verses extends WP_Widget {

    /**
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $slug = 'bible-verses';


	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		parent::__construct(
			$this->get_widget_slug(),
			__( 'Bible Verses', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Show random bible verses.', $this->get_widget_slug() ),
			)
		);

		// Register site styles and scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'widget_styles' ) );

		// Refreshing the widget's cached output.
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	}


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {

        return $this->widget_slug;
    }

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset ( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset ( $cache[ $args['widget_id'] ] ) ) {
			return print $cache[ $args['widget_id'] ];
		}

		extract( $args, EXTR_SKIP );

		$content = $before_widget;

		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$content .= ob_get_clean();
		$content .= $after_widget;


		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $content;

	}
	
	
	public function flush_widget_cache() {

    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$old_instance['title'] = $new_instance['title'];

		return $old_instance;

	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Bible Verse', 'bible-verses' ) ) );

		// Display the admin form
		include_once( plugin_dir_path( __FILE__ ) . 'views/admin.php' );

	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	}

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

	}

}


add_action( 'widgets_init', create_function( '', 'register_widget("Bible_Verses");' ) );
