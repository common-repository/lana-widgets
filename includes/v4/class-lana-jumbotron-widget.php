<?php

/**
 * Class Lana_Jumbotron_Widget
 * with Bootstrap
 */
class Lana_Jumbotron_Widget extends WP_Widget {

	/**
	 * Lana Jumbotron Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Jumbotron', 'lana-widgets' );
		$widget_description = __( 'Showcase your key content.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_jumbotron', $widget_name, $widget_options );
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
		$before_widget = '<div class="jumbotron">';
		$after_widget  = '</div>';

		$before_title = '<h1 class="display-4">';
		$after_title  = '</h1>';

		$before_text = '<p class="lead">';
		$after_text  = '</p>';

		$before_button_container = '<p class="lead">';
		$after_button_container  = '</p>';

		$before_button = '<a href="%s" class="btn btn-primary btn-lg" role="button">';
		$after_button  = '</a>';

		/**
		 * button link
		 * default # value
		 */
		if ( empty( $instance['button_link'] ) ) {
			$instance['button_link'] = '#';
		}

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];
		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		echo $before_text . $instance['text'] . $after_text;

		if ( ! empty( $instance['button_text'] ) ) {

			echo $before_button_container;
			echo sprintf( $before_button, esc_attr( $instance['button_link'] ) );

			echo $instance['button_text'];

			echo $after_button;
			echo $after_button_container;
		}

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
			'title'       => '',
			'text'        => '',
			'button_text' => '',
			'button_link' => '',
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
            <textarea name="<?php echo $this->get_field_name( 'text' ); ?>"
                      id="<?php echo $this->get_field_id( 'text' ); ?>" class="widefat"
                      rows="5"><?php echo $instance['text']; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'button_text' ); ?>">
				<?php _e( 'Button Text:', 'lana-widgets' ); ?>
            </label>
            <input type="text" name="<?php echo $this->get_field_name( 'button_text' ); ?>"
                   id="<?php echo $this->get_field_id( 'button_text' ); ?>" class="widefat"
                   value="<?php echo esc_attr( $instance['button_text'] ); ?>"/>
            <br/>
            <label for="<?php echo $this->get_field_id( 'button_link' ); ?>">
				<?php _e( 'Button Link:', 'lana-widgets' ); ?>
            </label>
            <input type="text" name="<?php echo $this->get_field_name( 'button_link' ); ?>"
                   id="<?php echo $this->get_field_id( 'button_link' ); ?>" class="widefat"
                   value="<?php echo esc_attr( $instance['button_link'] ); ?>"
                   placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>"/>
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

		$instance['title']       = sanitize_text_field( $new_instance['title'] );
		$instance['text']        = sanitize_textarea_field( $new_instance['text'] );
		$instance['button_text'] = sanitize_text_field( $new_instance['button_text'] );
		$instance['button_link'] = esc_url_raw( $new_instance['button_link'] );

		return apply_filters( 'lana_jumbotron_widget_update', $instance, $this );
	}
}
