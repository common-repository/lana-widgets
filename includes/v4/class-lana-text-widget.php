<?php

/**
 * Class Lana_Text_Widget
 * with TinyMCE editor
 * with Bootstrap
 */
class Lana_Text_Widget extends WP_Widget {

	/**
	 * Lana Text Widget
	 * constructor
	 */
	public function __construct() {

		$widget_name        = __( 'Lana - Text', 'lana-widgets' );
		$widget_description = __( 'TinyMCE visual editor.', 'lana-widgets' );
		$widget_options     = array( 'description' => $widget_description );
		$control_options    = array( 'width' => 600, 'height' => 400 );

		parent::__construct( 'lana_text', $widget_name, $widget_options, $control_options );

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
		$before_text = '<div class="lana-text">';
		$after_text  = '</div>';

		$instance['text'] = wpautop( $instance['text'] );

		/**
		 * Output
		 * Widget
		 */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo $before_text;
		echo $instance['text'];
		echo $after_text;

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
			'title' => '',
			'text'  => '',
		) );

		$instance['text'] = wpautop( $instance['text'] );
		$instance['text'] = str_replace( "\n", "", $instance['text'] );

		/**
		 * wp editor
		 * settings
		 */
		$wp_editor_settings = array(
			'textarea_name' => $this->get_field_name( 'text' ),
			'textarea_rows' => 10,
			'tinymce'       => array(
				'indent'             => false,
				'add_unload_trigger' => false,
				'wp_autoresize_on'   => false,
				'wpautop'            => true,
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
        <div class="lana-widgets-text-widget" data-widget-id="<?php echo esc_attr( $this->id ); ?>">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'lana-widgets' ); ?>
                </label>
                <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"
                       id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
                       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
            </p>

            <div class="lana-wp-editor">
                <div class="lana-widgets-wp-editor" data-field-name="<?php echo $this->get_field_id( 'text' ); ?>">
                    <label for="<?php echo $this->get_field_id( 'text' ); ?>">
						<?php _e( 'Text:', 'lana-widgets' ); ?>
                    </label>
					<?php if ( defined( 'LANA_EDITOR_VERSION' ) && true === $lana_editor ) : ?>
						<?php wp_editor( $instance['text'], $this->get_field_id( 'text' ), $wp_editor_settings ); ?>
					<?php else : ?>
                        <textarea name="<?php echo $this->get_field_name( 'text' ); ?>"
                                  id="<?php echo $this->get_field_id( 'text' ); ?>" class="widefat"
                                  rows="5"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
					<?php endif; ?>
                </div>
            </div>
        </div>

		<?php if ( defined( 'LANA_EDITOR_VERSION' ) && true == $lana_editor ) : ?>
            <script type="text/javascript">
				<?php echo sprintf( 'new LanaWidgetsWpEditorWidget("%s", "%s", %s);', $this->id_base, '.lana-widgets-text-widget', $this->tinymce_settings_json( $tinymce_settings ) ); ?>
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

		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return apply_filters( 'lana_text_widget_update', $instance, $this );
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