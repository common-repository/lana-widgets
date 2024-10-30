<?php

/**
 * Class Lana_Well_Widget
 */
class Lana_Well_Widget extends WP_Widget {

	/**
	 * Lana Well Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Well', 'lana-widgets' );
		$widget_description = __( 'Inset effect.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_well', $widget_name, $widget_options );
	}

	/**
	 * Output Widget HTML
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$before_widget = '<div class="card bg-light">';
		$after_widget  = '</div>';

		$before_card_body = '<div class="card-body">';
		$after_card_body  = '</div>';

		$before_card_text = '<p class="card-text">';
		$after_card_text  = '</p>';

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];
		echo $before_widget;
		echo $before_card_body;

		echo $before_card_text;
		echo $instance['text'];
		echo $after_card_text;

		echo $after_card_body;
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
			'text' => '',
		) );
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'text' ); ?>">
				<?php _e( 'Text:', 'lana-widgets' ); ?>
            </label>
            <textarea name="<?php echo $this->get_field_name( 'text' ); ?>"
                      id="<?php echo $this->get_field_id( 'text' ); ?>" class="widefat"
                      rows="3"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
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

		$instance['text'] = sanitize_textarea_field( $new_instance['text'] );

		return apply_filters( 'lana_well_widget_update', $instance, $this );
	}
} 