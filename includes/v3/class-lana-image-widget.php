<?php

/**
 * Class Lana_Image_Widget
 */
class Lana_Image_Widget extends WP_Widget {

	/**
	 * Lana Image Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Image', 'lana-widgets' );
		$widget_description = __( 'Image with Magnific popup.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );

		parent::__construct( 'lana_image', $widget_name, $widget_options );

		add_action( 'wp_enqueue_scripts', array( $this, 'widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'widget_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_styles' ) );

		add_action( 'admin_print_scripts-widgets.php', array( $this, 'widget_admin_print_scripts' ) );
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
		wp_register_script( 'lana-image-widget-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-image-widget-admin.js', array(
			'jquery',
			'lana-widgets-image-admin',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-image-widget-admin' );
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
		wp_add_inline_script( 'lana-image-widget-admin', sprintf( 'new LanaImageWidget("%s");', $this->id_base ), 'after' );
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
		$before_image = '<a href="%s" class="thumbnail magnific">';
		$after_image  = '</a>';

		$image = '<img src="%s" />';

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		if ( ! empty( $instance['image_url'] ) ) {
			echo sprintf( $before_image, esc_attr( $instance['image_url'] ) );
			echo sprintf( $image, esc_attr( $instance['image_url'] ) );
			echo $after_image;
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
			'title'     => '',
			'image_url' => '',
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
        <div class="lana-widgets-image-widget" data-widget-id="<?php echo esc_attr( $this->id ); ?>">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'lana-widgets' ); ?>
                </label>
                <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"
                       id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
            </p>

            <div class="lana-image-editor">
                <div class="lana-widgets-image" data-field-name="image_url">
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

		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['image_url'] = esc_url_raw( $new_instance['image_url'] );

		return apply_filters( 'lana_image_widget_update', $instance, $this );
	}
}