<?php

namespace ACF_Gutenberg\Resources;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @since      1.1.0
 *
 * @package    ACF_Gutenberg
 * @subpackage ACF_Gutenberg/admin
 */
class Acfgb_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_scripts() {


		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the JavaScript for Gutenberg blocks.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_frontend_assets() {
        // If in the backend, bail out.
        if (is_admin()) {
            return;
        }

        $asset_path = ACFGB_PATH . '/resources/assets/js/frontend.blocks.js';
		//wp_enqueue_script( 'jsforwp-blocks-frontend', $asset_path, array( 'jquery' ), filemtime($asset_path), false );

	}


	public function render_styles() {

		$styles = false;
		$use_custom_color = get_field( 'use_custom_color', 'option' );
		$theme_colors = get_field( 'theme_colors', 'option' );
//		if ( $use_custom_color && is_array( $theme_colors ) ) {
//		}
			$styles = self::get_custom_styles( $theme_colors );
		echo $styles;

	}


	public static function get_custom_styles( $theme_colors ) {
		ob_start();
		?>
		<!-- Theme colors styles -->
		<style>

			.text-primary {
				color: <?= $theme_colors['primary'] ;?> !important;
			}

			.bg-primary {
				background-color: <?= $theme_colors['primary'] ;?> !important;
			}

			.border-primary,
			.btn-primary {
				border-color: <?= $theme_colors['primary'] ;?> !important;
			}

		</style>
		<?php
		return ob_get_clean();
	}

}
