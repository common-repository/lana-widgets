<?php

/**
 * Class Lana_Title_Widget
 */
class Lana_Title_Widget extends WP_Widget {

	/**
	 * Lana Title Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Title', 'lana-widgets' );
		$widget_description = __( 'Title with size adjustment.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_title', $widget_name, $widget_options );
	}

	/**
	 * Output Widget HTML
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		/**
		 * Title
		 * apply filter
		 */
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		/**
		 * Widget
		 * elements
		 */
		$before_title = '<%s>';
		$after_title  = '</%s>';

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {

			echo sprintf( $before_title, $instance['size'] );
			echo $instance['title'];
			echo sprintf( $after_title, $instance['size'] );
		}

		echo $args['after_widget'];
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
			'title' => '',
			'size'  => 'h3',
		) );

		$sizes = array(
			'h1' => __( 'Header 1 (h1)', 'lana-widgets' ),
			'h2' => __( 'Header 2 (h2)', 'lana-widgets' ),
			'h3' => __( 'Header 3 (h3)', 'lana-widgets' ),
			'h4' => __( 'Header 4 (h4)', 'lana-widgets' ),
			'h5' => __( 'Header 5 (h5)', 'lana-widgets' ),
			'h6' => __( 'Header 6 (h6)', 'lana-widgets' ),
		);
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:', 'lana-widgets' ); ?>
            </label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"
                   id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
                   value="<?php echo esc_attr( $instance['title'] ); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'size' ); ?>">
				<?php _e( 'Size:', 'lana-widgets' ); ?>
            </label>
            <select id="<?php echo $this->get_field_id( 'size' ); ?>"
                    name="<?php echo $this->get_field_name( 'size' ); ?>">
				<?php foreach ( $sizes as $size_id => $size ) : ?>
                    <option
                            value="<?php echo esc_attr( $size_id ); ?>" <?php selected( $size_id, $instance['size'] ); ?>>
						<?php esc_html_e( $size ); ?>
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

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['size']  = sanitize_text_field( $new_instance['size'] );

		return apply_filters( 'lana_title_widget_update', $instance, $this );
	}
} 