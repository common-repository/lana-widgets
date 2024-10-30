<?php

/**
 * Class Lana_Alert_Widget
 */
class Lana_Alert_Widget extends WP_Widget {

	/**
	 * Lana Alert Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Alert', 'lana-widgets' );
		$widget_description = __( 'One-line alert message.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_alert', $widget_name, $widget_options );
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
		$before_widget = '<div class="alert %s alert-dismissible" role="alert">';
		$after_widget  = '</div>';

		$dismissible = '<button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>';

		$before_title = '<strong>';
		$after_title  = '</strong>';

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];
		echo sprintf( $before_widget, esc_attr( $instance['type'] ) );

		echo $dismissible;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		echo $instance['text'];

		echo $after_widget;
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
			'text'  => '',
			'type'  => '',
		) );
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
            <label for="<?php echo $this->get_field_id( 'text' ); ?>">
				<?php _e( 'Text:', 'lana-widgets' ); ?>
            </label>
            <input type="text" name="<?php echo $this->get_field_name( 'text' ); ?>"
                   id="<?php echo $this->get_field_id( 'text' ); ?>" class="widefat"
                   value="<?php echo esc_attr( $instance['text'] ); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>">
				<?php _e( 'Type:', 'lana-widgets' ); ?>
            </label>
            <select name="<?php echo $this->get_field_name( 'type' ); ?>"
                    id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat">
                <option value="alert-success" <?php selected( $instance['type'], 'alert-success' ); ?>>
					<?php esc_html_e( 'Success', 'lana-widgets' ); ?>
                </option>
                <option value="alert-info" <?php selected( $instance['type'], 'alert-info' ); ?>>
					<?php esc_html_e( 'Info', 'lana-widgets' ); ?>
                </option>
                <option value="alert-warning" <?php selected( $instance['type'], 'alert-warning' ); ?>>
					<?php esc_html_e( 'Warning', 'lana-widgets' ); ?>
                </option>
                <option value="alert-danger" <?php selected( $instance['type'], 'alert-danger' ); ?>>
					<?php esc_html_e( 'Danger', 'lana-widgets' ); ?>
                </option>
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
		$instance['text']  = sanitize_text_field( $new_instance['text'] );
		$instance['type']  = sanitize_text_field( $new_instance['type'] );

		return apply_filters( 'lana_alert_widget_update', $instance, $this );
	}
} 