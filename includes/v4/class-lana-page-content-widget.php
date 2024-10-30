<?php

/**
 * Class Lana_Page_Content_Widget
 */
class Lana_Page_Content_Widget extends WP_Widget {

	/**
	 * Lana Page Content Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Page Content', 'lana-widgets' );
		$widget_description = __( 'Title or Featured Image.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_page_content', $widget_name, $widget_options );

		add_action( 'wp_enqueue_scripts', array( $this, 'widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'widget_scripts' ) );
	}

	/**
	 * Load widget styles
	 */
	public function widget_styles() {

		/** magnific popup css */
		wp_register_style( 'magnific-popup', LANA_WIDGETS_DIR_URL . '/assets/libs/magnific-popup/css/jquery-magnific-popup.min.css', array(), '1.2.0' );
		wp_enqueue_style( 'magnific-popup' );
	}

	/**
	 * Load widget scripts
	 */
	public function widget_scripts() {

		/** magnific popup js */
		wp_register_script( 'magnific-popup', LANA_WIDGETS_DIR_URL . '/assets/libs/magnific-popup/js/jquery-magnific-popup.min.js', array( 'jquery' ), '1.2.0' );
		wp_enqueue_script( 'magnific-popup' );

		/** lana magnific popup image js */
		wp_register_script( 'lana-magnific-popup-image', LANA_WIDGETS_DIR_URL . '/assets/js/lana-magnific-popup-image.js', array(
			'jquery',
			'magnific-popup',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-magnific-popup-image' );
	}

	/**
	 * Output Widget HTML
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];
		echo $this->post_content( $instance['type'] );
		echo $args['after_widget'];
	}

	/**
	 * Post content
	 *
	 * @param $type
	 *
	 * @return string
	 */
	function post_content( $type ) {
		global $post;

		$output = '';

		if ( empty( $post ) ) {
			return $output;
		}

		/**
		 * Title
		 * post content type
		 */
		if ( 'title' == $type ) {

			$before_title = '<h3>';
			$after_title  = '</h3>';

			$output .= $before_title;
			$output .= $post->post_title;
			$output .= $after_title;
		}

		/**
		 * Featured image
		 * post content type
		 */
		if ( 'featured_image' == $type ) {

			if ( ! has_post_thumbnail() ) {
				return $output;
			}

			$before_image = '<a href="%s" class="pull-left thumbnail magnific" title="%s">';
			$after_image  = '</a>';
			$clearfix     = '<div class="clearfix"></div>';

			$image_url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );

			$output .= sprintf( $before_image, $image_url, $post->post_title );
			$output .= get_the_post_thumbnail( $post->ID, 'large' );
			$output .= $after_image;
			$output .= $clearfix;
		}

		return $output;
	}

	/**
	 * Output Widget Form
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array(
			'type' => 'title',
		) );

		$types = array(
			'title'          => __( 'Title', 'lana-widgets' ),
			'featured_image' => __( 'Featured Image', 'lana-widgets' ),
		);
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>">
				<?php _e( 'Type:', 'lana-widgets' ); ?>
            </label>
            <select name="<?php echo $this->get_field_name( 'type' ); ?>"
                    id="<?php echo $this->get_field_id( 'type' ); ?>">
				<?php foreach ( $types as $type_id => $type ) : ?>
                    <option
                            value="<?php echo esc_attr( $type_id ); ?>" <?php selected( $type_id, $instance['type'] ); ?>>
						<?php esc_html_e( $type ); ?>
                    </option>
				<?php endforeach; ?>
            </select>
        </p>
		<?php
	}

	/**
	 * Update Widget Data
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array|mixed
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['type'] = sanitize_text_field( $new_instance['type'] );

		return apply_filters( 'lana_page_content_widget_update', $instance, $this );
	}
} 