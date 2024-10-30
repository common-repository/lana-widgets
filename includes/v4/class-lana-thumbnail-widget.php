<?php

/**
 * Class Lana_Thumbnail_Widget
 */
class Lana_Thumbnail_Widget extends WP_Widget {

	/**
	 * Lana Thumbnail Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Thumbnail', 'lana-widgets' );
		$widget_description = __( 'Image and text.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_thumbnail', $widget_name, $widget_options );

		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_styles' ) );

		add_action( 'admin_print_scripts-widgets.php', array( $this, 'widget_admin_print_scripts' ) );
	}

	/**
	 * Load widget admin scripts
	 */
	public function widget_admin_scripts() {

		wp_enqueue_media();

		/** lana widgets image admin js */
		wp_register_script( 'lana-widgets-image-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-widgets-image-admin.js', array(
			'jquery',
			'media-upload',
			'media-views',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-widgets-image-admin' );

		/** add l10n to lana widgets image js */
		wp_localize_script( 'lana-widgets-image-admin', 'lana_widgets_image_l10n', array(
			'edit_image' => __( 'Edit Image', 'lana-widgets' ),
		) );

		/** lana image widget admin js */
		wp_register_script( 'lana-thumbnail-widget-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-thumbnail-widget-admin.js', array(
			'jquery',
			'lana-widgets-image-admin',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-thumbnail-widget-admin' );
	}

	/**
	 * Load widget admin styles
	 */
	public function widget_admin_styles() {

		/** lana widgets image admin */
		wp_register_style( 'lana-widgets-image-admin', LANA_WIDGETS_DIR_URL . '/assets/css/lana-widgets-image-admin.css', array(), LANA_WIDGETS_VERSION );
		wp_enqueue_style( 'lana-widgets-image-admin' );
	}

	/**
	 * Load widget admin print scripts
	 */
	public function widget_admin_print_scripts() {
		wp_add_inline_script( 'lana-thumbnail-widget-admin', sprintf( 'new LanaThumbnailWidget("%s");', $this->id_base ), 'after' );
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
		$before_widget = '<div class="card">';
		$after_widget  = '</div>';

		$before_caption = '<div class="card-body">';
		$after_caption  = '</div>';

		$before_text = '<p class="card-text">';
		$after_text  = '</p>';

		$image = '<img src="%s" class="card-img-top" />';

		$before_button_container = '<p>';
		$after_button_container  = '</p>';

		$before_button = '<a href="%s" class="btn btn-primary %s" role="button">';
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

		if ( ! empty( $instance['image_url'] ) ) {
			echo sprintf( $image, esc_attr( $instance['image_url'] ) );
		}

		echo $before_caption;

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo $before_text . $instance['text'] . $after_text;

		if ( ! empty( $instance['button_text'] ) ) {
			echo $before_button_container;
			echo sprintf( $before_button, esc_attr( $instance['button_link'] ), esc_attr( $instance['button_type'] ) );
			echo $instance['button_text'];
			echo $after_button;
			echo $after_button_container;
		}

		echo $after_caption;

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
			'image_url'   => '',
			'title'       => '',
			'text'        => '',
			'button_text' => '',
			'button_link' => '',
			'button_type' => '',
		) );

		/** images */
		$images['image_url'] = $instance['image_url'];

		/** placeholder image */
		$placeholder_image = LANA_WIDGETS_DIR_URL . '/assets/img/lana-image-placeholder.png';

		/** not valid image use placeholder image */
		if ( ! filter_var( $images['image_url'], FILTER_VALIDATE_URL ) ) {
			$images['image_url'] = $placeholder_image;
		}
		?>
        <div class="lana-widgets-thumbnail-widget" data-widget-id="<?php echo esc_attr( $this->id ); ?>">
            <div class="lana-image-editor">
                <div class="lana-widgets-image" data-field-name="image_url"
                     data-widget-id="<?php echo esc_attr( $this->id ); ?>">
                    <label for="<?php echo $this->get_field_id( 'image_url' ); ?>">
						<?php _e( 'Image:', 'lana-widgets' ); ?>
                    </label>
                    <div class="lana-thumbnail">
                        <img src="<?php echo esc_url( $images['image_url'] ); ?>"
                             data-placeholder-src="<?php echo esc_url( $placeholder_image ); ?>"/>
                    </div>
                    <input type="text" name="<?php echo $this->get_field_name( 'image_url' ); ?>"
                           id="<?php echo $this->get_field_id( 'image_url' ); ?>" class="widefat lana-widgets-image-url"
                           value="<?php echo esc_url( $instance['image_url'] ); ?>"/>
                    <input type="button" value="<?php esc_attr_e( 'Edit Image', 'lana-widgets' ); ?>"
                           class="button lana-widgets-edit-image-button"/>
                </div>
            </div>

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
                          rows="5"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
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
                <br/>
                <label for="<?php echo $this->get_field_id( 'button_type' ); ?>">
					<?php _e( 'Button Type:', 'lana-widgets' ); ?>
                </label>
                <select name="<?php echo $this->get_field_name( 'button_type' ); ?>"
                        id="<?php echo $this->get_field_id( 'button_type' ); ?>" class="widefat">
                    <option value="" <?php selected( $instance['button_type'], '' ); ?>>
						<?php esc_html_e( 'Default', 'lana-widgets' ); ?>
                    </option>
                    <option value="btn-block" <?php selected( $instance['button_type'], 'btn-block' ); ?>>
						<?php esc_html_e( 'Block', 'lana-widgets' ); ?>
                    </option>
                </select>
            </p>
        </div>
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
		$instance['image_url']   = esc_url_raw( $new_instance['image_url'] );
		$instance['button_text'] = sanitize_text_field( $new_instance['button_text'] );
		$instance['button_link'] = esc_url_raw( $new_instance['button_link'] );
		$instance['button_type'] = sanitize_text_field( $new_instance['button_type'] );

		return apply_filters( 'lana_thumbnail_widget_update', $instance, $this );
	}
} 