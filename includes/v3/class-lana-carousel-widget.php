<?php

/**
 * Class Lana_Carousel_Widget
 */
class Lana_Carousel_Widget extends WP_Widget {

	/**
	 * Lana Carousel Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Carousel', 'lana-widgets' );
		$widget_description = __( 'Image slider.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );
		$control_options    = array( 'width' => 750, 'height' => 500 );

		parent::__construct( 'lana_carousel', $widget_name, $widget_options, $control_options );

		add_action( 'wp_enqueue_scripts', array( $this, 'widget_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_styles' ) );

		add_action( 'admin_footer-widgets.php', array( $this, 'widget_admin_footer' ) );
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'widget_admin_print_scripts' ) );
	}

	/**
	 * Load widget styles
	 */
	public function widget_styles() {

		wp_register_style( 'lana-carousel', LANA_WIDGETS_DIR_URL . '/assets/css/v3/lana-carousel.css', array(), LANA_WIDGETS_VERSION );
		wp_enqueue_style( 'lana-carousel' );
	}

	/**
	 * Load widget admin scripts
	 */
	public function widget_admin_scripts() {

		wp_enqueue_media();

		/** lana widgets gallery admin js */
		wp_register_script( 'lana-widgets-gallery-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-widgets-gallery-admin.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'jquery-ui-resizable',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-widgets-gallery-admin' );

		/** add l10n to lana widgets gallery js */
		wp_localize_script( 'lana-widgets-gallery-admin', 'lana_widgets_gallery_l10n', array(
			'add_image_to_gallery' => __( 'Add Image to Gallery', 'lana-widgets' ),
		) );

		/** lana carousel widget admin js */
		wp_register_script( 'lana-carousel-widget-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-carousel-widget-admin.js', array(
			'jquery',
			'lana-widgets-gallery-admin',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-carousel-widget-admin' );
	}

	/**
	 * Load widget admin styles
	 */
	public function widget_admin_styles() {

		/** lana widgets gallery admin */
		wp_register_style( 'lana-widgets-gallery-admin', LANA_WIDGETS_DIR_URL . '/assets/css/lana-widgets-gallery-admin.css', array(), LANA_WIDGETS_VERSION );
		wp_enqueue_style( 'lana-widgets-gallery-admin' );
	}

	/**
	 * Load widget admin footer
	 */
	public function widget_admin_footer() {

		/** include templates */
		include_once LANA_WIDGETS_DIR_PATH . '/views/gallery-templates/attachment-html.php';
	}

	/**
	 * Load widget admin print scripts
	 */
	public function widget_admin_print_scripts() {
		wp_add_inline_script( 'lana-carousel-widget-admin', sprintf( 'new LanaCarouselWidget("%s");', $this->id_base ), 'after' );
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
		$before_title = '<h3>';
		$after_title  = '</h3>';

		$before_carousel = '<div id="%s" class="carousel %s slide" data-ride="carousel">';
		$after_carousel  = '</div>';

		$before_indicators = '<ol class="carousel-indicators">';
		$after_indicators  = '</ol>';

		$before_inner = '<div class="carousel-inner" role="listbox">';
		$after_inner  = '</div>';

		$before_image = '<div class="item %s">';
		$after_image  = '</div>';

		$before_carousel_image = '';
		$after_carousel_image  = '';

		$carousel_image = '<div style="%s" class="carousel-image"></div>';

		$before_caption = '<div class="carousel-caption">';
		$after_caption  = '</div>';

		$before_caption_title = '<h3>';
		$after_caption_title  = '</h3>';

		$before_caption_description = '<p>';
		$after_caption_description  = '</p>';

		$before_controller = '';
		$after_controller  = '';

		$control_prev = '<a class="left carousel-control" href="#%s" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                          </a>';

		$control_next = '<a class="right carousel-control" href="#%s" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                          </a>';

		/**
		 * Gallery Images
		 * from gallery ids
		 */
		if ( ! empty( $instance['gallery'] ) && empty( $instance['gallery_ids'] ) ) {
			$gallery_shortcode_atts  = lana_widgets_shortcode_get_atts( 'gallery', $instance['gallery'] );
			$instance['gallery_ids'] = explode( ',', $gallery_shortcode_atts['ids'] );
		}

		/** validate ids */
		if ( empty( $instance['gallery_ids'] ) ) {
			$instance['gallery_ids'] = array( '' );
		}

		/**
		 * get attachments
		 * @var WP_Post $lana_gallery_attachments
		 */
		$images = get_posts( array(
			'post_type'      => 'attachment',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post__in'       => $instance['gallery_ids'],
			'orderby'        => 'post__in',
			'post_mime_type' => 'image',
		) );

		/** check images */
		if ( empty( $images ) ) {
			return;
		}

		/**
		 * Gallery Images
		 * average height
		 */
		if ( empty( $instance['image_height'] ) ) {

			$image_height = array();

			foreach ( $images as $image ) {
				$image_src      = wp_get_attachment_image_src( $image->ID, 'full' );
				$image_height[] = $image_src[2];
			}

			$instance['image_height'] = array_sum( $image_height ) / count( $image_height );
		}

		/**
		 * Side Caption
		 * style
		 */
		if ( 'carousel-side' == $instance['style'] ) {

			$side_style = array(
				'height:' . $instance['image_height'] . 'px;',
			);

			$before_inner = sprintf( '<div class="carousel-inner" role="listbox" style="%s">', implode( '', $side_style ) );

			$before_image         .= '<div class="holder col-sm-8">';
			$after_carousel_image .= '</div>';

			$before_caption = '<div class="col-sm-4">';
			$before_caption .= '<div class="carousel-caption">';
			$after_caption  = '</div>';
			$after_caption  .= '</div>';

			$before_controller = sprintf( '<div class="controllers col-sm-8 col-xs-12" style="%s">', implode( '', $side_style ) );
			$after_controller  = '</div>';
		}

		/**
		 * Output
		 * Widget
		 */
		$carousel_id = $this->id . '-carousel_id';

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		echo sprintf( $before_carousel, $carousel_id, esc_attr( $instance['style'] ) );

		echo $before_inner;

		/** @var WP_Post $image */
		foreach ( $images as $image ) {

			echo sprintf( $before_image, $image === reset( $images ) ? 'active' : '' );

			$image_url = wp_get_attachment_image_url( $image->ID, 'full' );

			$image_style = array(
				'background: url(' . esc_url( $image_url ) . ');',
				'background-repeat: no-repeat;',
				'background-size: cover;',
				'background-position: ' . esc_attr( $instance['image_position'] ) . ';',
				'height: ' . intval( $instance['image_height'] ) . 'px;',
			);

			echo $before_carousel_image;
			echo sprintf( $carousel_image, implode( ' ', $image_style ) );
			echo $after_carousel_image;

			if ( ! empty( $image->post_content ) || ! empty( $image->post_excerpt ) ) {
				echo $before_caption;
				if ( ! empty( $image->post_title ) ) {
					echo $before_caption_title . $image->post_title . $after_caption_title;
				}
				if ( ! empty( $image->post_excerpt ) ) {
					echo $before_caption_description . $image->post_excerpt . $after_caption_description;
				}
				echo $after_caption;
			}

			echo $after_image;
		}

		echo $after_inner;

		/**
		 * CONTROLLER
		 * indicators
		 * control
		 */
		echo $before_controller;

		echo $before_indicators;
		$i = 0;
		foreach ( $images as $image ) {

			echo vsprintf( '<li  class="%s" data-target="#%s" data-slide-to="%s"></li>', array(
				$image === reset( $images ) ? 'active' : '',
				$carousel_id,
				$i ++,
			) );
		}
		echo $after_indicators;

		echo sprintf( $control_prev, $carousel_id );
		echo sprintf( $control_next, $carousel_id );

		echo $after_controller;

		echo $after_carousel;

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
			'title'          => '',
			'gallery'        => '',
			'gallery_ids'    => array(),
			'style'          => 'default',
			'image_height'   => '',
			'image_position' => 'center top',
		) );

		/** convert gallery shortcode to gallery ids */
		if ( ! empty( $instance['gallery'] ) ) {
			$gallery_shortcode_atts  = lana_widgets_shortcode_get_atts( 'gallery', $instance['gallery'] );
			$instance['gallery_ids'] = explode( ',', $gallery_shortcode_atts['ids'] );
		}

		/** validate ids */
		if ( empty( $instance['gallery_ids'] ) ) {
			$instance['gallery_ids'] = array( '' );
		}

		/**
		 * get attachments
		 * @var WP_Post $lana_gallery_attachments
		 */
		$lana_widgets_gallery_attachments = get_posts( array(
			'post_type'      => 'attachment',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post__in'       => $instance['gallery_ids'],
			'orderby'        => 'post__in',
			'post_mime_type' => 'image',
		) );
		?>
        <div class="lana-widgets-carousel-widget" data-widget-id="<?php echo esc_attr( $this->id ); ?>">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'lana-widgets' ); ?>
                </label>
                <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"
                       id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
            </p>

            <div class="lana-gallery-editor">
                <label for="<?php echo $this->get_field_id( 'gallery' ); ?>">
					<?php _e( 'Gallery:', 'lana-widgets' ); ?>
                </label>

                <div class="lana-widgets-gallery" data-columns="6"
                     data-field-name="gallery_ids"
                     data-input-field-name="<?php echo $this->get_field_name( 'gallery_ids' ); ?>"
                     data-widget-id="<?php echo esc_attr( $this->id ); ?>">
                    <div class="lana-widgets-gallery-attachments">
						<?php if ( ! empty( $lana_widgets_gallery_attachments ) ) : ?>
							<?php foreach ( $lana_widgets_gallery_attachments as $i => $lana_widgets_gallery_attachment ) : ?>
								<?php $attachment = wp_get_attachment_image_src( $lana_widgets_gallery_attachment->ID, 'medium' ); ?>

                                <div class="lana-widgets-gallery-attachment"
                                     data-id="<?php echo esc_attr( $lana_widgets_gallery_attachment->ID ); ?>">
                                    <div class="lana-widgets-gallery-attachment-margin">
                                        <input type="hidden"
                                               name="<?php echo $this->get_field_name( 'gallery_ids' ); ?>[]"
                                               class="lana-widgets-gallery-attachment-id"
                                               value="<?php echo esc_attr( $lana_widgets_gallery_attachment->ID ); ?>">
                                        <div class="lana-thumbnail">
                                            <img src="<?php echo esc_url( $attachment[0] ); ?>"
                                                 alt="<?php echo esc_attr( get_post_meta( $lana_widgets_gallery_attachment->ID, '_wp_attachment_image_alt', true ) ); ?>"
                                                 title="<?php echo esc_attr( get_the_title() ); ?>"/>
                                        </div>
                                        <div class="lana-actions">
                                            <a href="#" class="lana-widgets-gallery-remove"
                                               data-id="<?php echo esc_attr( $lana_widgets_gallery_attachment->ID ); ?>"
                                               title="<?php esc_attr_e( 'Remove', 'lana-widgets-gallery' ); ?>">
                                                <span class="dashicons dashicons-no-alt"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
							<?php endforeach; ?>
						<?php endif; ?>
                    </div>
                    <div class="lana-widgets-gallery-toolbar">
                        <ul class="lana-horizontal-list">
                            <li>
                                <a href="#" class="lana-button button button-primary lana-widgets-gallery-add">
									<?php _e( 'Add images', 'lana-widgets' ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <input type="hidden" name="<?php echo $this->get_field_name( 'gallery' ); ?>"
                       id="<?php echo $this->get_field_id( 'gallery' ); ?>" value=""/>
            </div>

            <p>
                <label for="<?php echo $this->get_field_id( 'style' ); ?>">
					<?php _e( 'Style:', 'lana-widgets' ); ?>
                </label>
                <select name="<?php echo $this->get_field_name( 'style' ); ?>"
                        id="<?php echo $this->get_field_id( 'style' ); ?>" class="widefat">
                    <option value="carousel-default" <?php selected( $instance['style'], 'carousel-default' ); ?>>
						<?php esc_html_e( 'Default', 'lana-widgets' ); ?>
                    </option>
                    <option value="carousel-simple" <?php selected( $instance['style'], 'carousel-simple' ); ?>>
						<?php esc_html_e( 'Simple', 'lana-widgets' ); ?>
                    </option>
                    <option value="carousel-navy" <?php selected( $instance['style'], 'carousel-navy' ); ?>>
						<?php esc_html_e( 'Navy', 'lana-widgets' ); ?>
                    </option>
                    <option value="carousel-side" <?php selected( $instance['style'], 'carousel-side' ); ?>>
						<?php esc_html_e( 'Side Caption', 'lana-widgets' ); ?>
                    </option>
                </select>
            </p>

            <br/>

            <p>
                <label for="<?php echo $this->get_field_id( 'image_height' ); ?>">
					<?php _e( 'Image Height:', 'lana-widgets' ); ?>
                </label>
                <input type="number" name="<?php echo $this->get_field_name( 'image_height' ); ?>"
                       id="<?php echo $this->get_field_id( 'image_height' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['image_height'] ); ?>"/>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'image_position' ); ?>">
					<?php _e( 'Image Position:', 'lana-widgets' ); ?>
                </label>
                <select name="<?php echo $this->get_field_name( 'image_position' ); ?>"
                        id="<?php echo $this->get_field_id( 'image_position' ); ?>" class="widefat">
                    <option value="center top" <?php selected( $instance['image_position'], 'center top' ); ?>>
						<?php esc_html_e( 'center top', 'lana-widgets' ); ?>
                    </option>
                    <option value="center center" <?php selected( $instance['image_position'], 'center center' ); ?>>
						<?php esc_html_e( 'center center', 'lana-widgets' ); ?>
                    </option>
                    <option value="center bottom" <?php selected( $instance['image_position'], 'center bottom' ); ?>>
						<?php esc_html_e( 'center bottom', 'lana-widgets' ); ?>
                    </option>
                    <option value="left top" <?php selected( $instance['image_position'], 'left top' ); ?>>
						<?php esc_html_e( 'left top', 'lana-widgets' ); ?>
                    </option>
                    <option value="left center" <?php selected( $instance['image_position'], 'left center' ); ?>>
						<?php esc_html_e( 'left center', 'lana-widgets' ); ?>
                    </option>
                    <option value="left bottom" <?php selected( $instance['image_position'], 'left bottom' ); ?>>
						<?php esc_html_e( 'left bottom', 'lana-widgets' ); ?>
                    </option>
                    <option value="right top" <?php selected( $instance['image_position'], 'right top' ); ?>>
						<?php esc_html_e( 'right top', 'lana-widgets' ); ?>
                    </option>
                    <option value="right center" <?php selected( $instance['image_position'], 'right center' ); ?>>
						<?php esc_html_e( 'right center', 'lana-widgets' ); ?>
                    </option>
                    <option value="right bottom" <?php selected( $instance['image_position'], 'right bottom' ); ?>>
						<?php esc_html_e( 'right bottom', 'lana-widgets' ); ?>
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

		$instance['title']          = sanitize_text_field( $new_instance['title'] );
		$instance['gallery']        = sanitize_text_field( $new_instance['gallery'] );
		$instance['gallery_ids']    = array_map( 'absint', $new_instance['gallery_ids'] );
		$instance['style']          = sanitize_text_field( $new_instance['style'] );
		$instance['image_height']   = absint( $new_instance['image_height'] );
		$instance['image_position'] = sanitize_text_field( $new_instance['image_position'] );

		return apply_filters( 'lana_carousel_widget_update', $instance, $this );
	}
}

