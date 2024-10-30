<?php

/**
 * Class Lana_Panel_Widget
 * with TinyMCE editor
 * with Bootstrap
 */
class Lana_Panel_Widget extends WP_Widget {

	/**
	 * Lana Panel Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Panel', 'lana-widgets' );
		$widget_description = __( 'Put your content in a box.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );
		$control_options    = array( 'width' => 600, 'height' => 400 );

		parent::__construct( 'lana_panel', $widget_name, $widget_options, $control_options );

		add_action( 'admin_enqueue_scripts', array( $this, 'widget_admin_scripts' ) );
	}

	/**
	 * Load widget admin scripts
	 */
	public function widget_admin_scripts() {

		wp_enqueue_media();

		/** lana widgets wp editor admin js */
		wp_register_script( 'lana-widgets-wp-editor-admin', LANA_WIDGETS_DIR_URL . '/assets/js/lana-widgets-wp-editor-admin.js', array(
			'jquery',
			'editor',
			'quicktags',
		), LANA_WIDGETS_VERSION );
		wp_enqueue_script( 'lana-widgets-wp-editor-admin' );
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
		$before_panel = '<div class="panel %s">';
		$after_panel  = '</div>';

		$before_panel_heading = '<div class="panel-heading">';
		$after_panel_heading  = '</div>';

		$before_panel_title = '<h3 class="panel-title">';
		$after_panel_title  = '</h3>';

		$before_panel_body = '<div class="panel-body">';
		$after_panel_body  = '</div>';

		$before_panel_footer = '<div class="panel-footer">';
		$after_panel_footer  = '</div>';

		$instance['panel_body']   = wpautop( $instance['panel_body'] );
		$instance['panel_footer'] = wpautop( $instance['panel_footer'] );

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo sprintf( $before_panel, esc_attr( $instance['type'] ) );

		if ( ! empty( $instance['panel_title'] ) ) {
			echo $before_panel_heading;
			echo $before_panel_title;
			echo $instance['panel_title'];
			echo $after_panel_title;
			echo $after_panel_heading;
		}

		if ( ! empty( $instance['panel_body'] ) ) {
			echo $before_panel_body;
			echo $instance['panel_body'];
			echo $after_panel_body;
		}

		if ( ! empty( $instance['panel_footer'] ) ) {
			echo $before_panel_footer;
			echo $instance['panel_footer'];
			echo $after_panel_footer;
		}

		echo $after_panel;

		echo $args['after_widget'];
	}

	/**
	 * Output Widget Form
	 * with TinyMCE
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		global $lana_editor;

		$instance = wp_parse_args( (array) $instance, array(
			'title'        => '',
			'type'         => 'panel-default',
			'panel_title'  => '',
			'panel_body'   => '',
			'panel_footer' => '',
		) );

		$instance['panel_body']   = wpautop( $instance['panel_body'] );
		$instance['panel_body']   = str_replace( "\n", "", $instance['panel_body'] );
		$instance['panel_footer'] = wpautop( $instance['panel_footer'] );
		$instance['panel_footer'] = str_replace( "\n", "", $instance['panel_footer'] );

		/**
		 * wp editor
		 * settings
		 */
		$panel_body_wp_editor_settings = array(
			'textarea_name' => $this->get_field_name( 'panel_body' ),
			'textarea_rows' => 10,
			'tinymce'       => array(
				'add_unload_trigger' => false,
				'wp_autoresize_on'   => false,
			),
		);

		$panel_footer_wp_editor_settings = array(
			'textarea_name' => $this->get_field_name( 'panel_footer' ),
			'textarea_rows' => 8,
			'tinymce'       => array(
				'add_unload_trigger' => false,
				'wp_autoresize_on'   => false,
			),
		);

		/**
		 * tinymce
		 * settings
		 */
		$tinymce_settings = array();

		add_filter( 'tiny_mce_before_init', function ( $mceInit, $editor_id ) use ( &$tinymce_settings ) {
			$tinymce_settings[ $editor_id ] = $mceInit;
		}, 10, 2 );
		?>
        <div class="lana-widgets-panel-widget" data-widget-id="<?php echo esc_attr( $this->id ); ?>">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'lana-widgets' ); ?>
                </label>
                <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"
                       id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'type' ); ?>">
					<?php _e( 'Type:', 'lana-widgets' ); ?>
                </label>
                <select name="<?php echo $this->get_field_name( 'type' ); ?>"
                        id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat">
                    <option value="panel-default" <?php selected( $instance['type'], 'panel-default' ); ?>>
						<?php esc_html_e( 'Default', 'lana-widgets' ); ?>
                    </option>
                    <option value="panel-primary" <?php selected( $instance['type'], 'panel-primary' ); ?>>
						<?php esc_html_e( 'Primary', 'lana-widgets' ); ?>
                    </option>
                    <option value="panel-success" <?php selected( $instance['type'], 'panel-success' ); ?>>
						<?php esc_html_e( 'Success', 'lana-widgets' ); ?>
                    </option>
                    <option value="panel-info" <?php selected( $instance['type'], 'panel-info' ); ?>>
						<?php esc_html_e( 'Info', 'lana-widgets' ); ?>
                    </option>
                    <option value="panel-warning" <?php selected( $instance['type'], 'panel-warning' ); ?>>
						<?php esc_html_e( 'Warning', 'lana-widgets' ); ?>
                    </option>
                    <option value="panel-danger" <?php selected( $instance['type'], 'panel-danger' ); ?>>
						<?php esc_html_e( 'Danger', 'lana-widgets' ); ?>
                    </option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'panel_title' ); ?>">
					<?php _e( 'Panel Title:', 'lana-widgets' ); ?>
                </label>
                <input type="text" name="<?php echo $this->get_field_name( 'panel_title' ); ?>"
                       id="<?php echo $this->get_field_id( 'panel_title' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['panel_title'] ); ?>"/>
            </p>
            <div class="lana-wp-editor">
                <div class="lana-widgets-wp-editor"
                     data-field-name="<?php echo $this->get_field_id( 'panel_body' ); ?>">
                    <label for="<?php echo $this->get_field_id( 'panel_body' ); ?>">
						<?php _e( 'Panel Body:', 'lana-widgets' ); ?>
                    </label>
					<?php if ( defined( 'LANA_EDITOR_VERSION' ) && true == $lana_editor ) : ?>
						<?php wp_editor( $instance['panel_body'], $this->get_field_id( 'panel_body' ), $panel_body_wp_editor_settings ); ?>
					<?php else : ?>
                        <textarea name="<?php echo $this->get_field_name( 'panel_body' ); ?>"
                                  id="<?php echo $this->get_field_id( 'panel_body' ); ?>" class="widefat"
                                  rows="5"><?php echo esc_textarea( $instance['panel_body'] ); ?></textarea>
					<?php endif; ?>
                </div>
            </div>
            <div class="lana-wp-editor">
                <div class="lana-widgets-wp-editor"
                     data-field-name="<?php echo $this->get_field_id( 'panel_footer' ); ?>">
                    <label for="<?php echo $this->get_field_id( 'panel_footer' ); ?>">
						<?php _e( 'Panel Footer:', 'lana-widgets' ); ?>
                    </label>
					<?php if ( defined( 'LANA_EDITOR_VERSION' ) && true == $lana_editor ) : ?>
						<?php wp_editor( $instance['panel_footer'], $this->get_field_id( 'panel_footer' ), $panel_footer_wp_editor_settings ); ?>
					<?php else : ?>
                        <textarea name="<?php echo $this->get_field_name( 'panel_footer' ); ?>"
                                  id="<?php echo $this->get_field_id( 'panel_footer' ); ?>" class="widefat"
                                  rows="5"><?php echo esc_textarea( $instance['panel_footer'] ); ?></textarea>
					<?php endif; ?>
                </div>
            </div>
        </div>

		<?php if ( defined( 'LANA_EDITOR_VERSION' ) && true == $lana_editor ) : ?>
            <script type="text/javascript">
				<?php echo sprintf( 'new LanaWidgetsWpEditorWidget("%s", "%s", %s);', $this->id_base, '.lana-widgets-panel-widget', $this->tinymce_settings_json( $tinymce_settings ) ); ?>
            </script>
		<?php endif; ?>
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
		$instance['type']        = sanitize_text_field( $new_instance['type'] );
		$instance['panel_title'] = sanitize_text_field( $new_instance['panel_title'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['panel_body']   = $new_instance['panel_body'];
			$instance['panel_footer'] = $new_instance['panel_footer'];
		} else {
			$instance['panel_body']   = wp_kses_post( $new_instance['panel_body'] );
			$instance['panel_footer'] = wp_kses_post( $new_instance['panel_footer'] );
		}

		return apply_filters( 'lana_panel_widget_update', $instance, $this );
	}

	/**
	 * tinymce settings json
	 *
	 * @param $tinymce_settings
	 *
	 * @return string
	 */
	public function tinymce_settings_json( $tinymce_settings ) {
		global $lana_editor;

		$tinymce_on = defined( 'LANA_EDITOR_VERSION' ) && true === $lana_editor;

		$tinymce_settings_json = '';

		if ( $tinymce_on ) {
			foreach ( $tinymce_settings as $editor_id => $init ) {
				$options               = $this->tinymce_parse_options( $init );
				$tinymce_settings_json .= "'$editor_id':{$options},";
			}
			$tinymce_settings_json = '{' . trim( $tinymce_settings_json, ',' ) . '}';
		} else {
			$tinymce_settings_json = '{}';
		}

		return $tinymce_settings_json;
	}

	/**
	 * tinymce parse init
	 *
	 * @param $init
	 *
	 * @return string
	 */
	public function tinymce_parse_options( $init ) {
		$options = '';

		foreach ( $init as $key => $value ) {
			if ( is_bool( $value ) ) {
				$val     = $value ? 'true' : 'false';
				$options .= $key . ':' . $val . ',';
				continue;
			} elseif ( ! empty( $value ) && is_string( $value ) && (
					( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] ) ||
					( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] ) ||
					preg_match( '/^\(?function ?\(/', $value )
				) ) {

				$options .= $key . ':' . $value . ',';
				continue;
			}
			$options .= $key . ':"' . $value . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}
}