<?php defined( 'ABSPATH' ) or die(); ?>

<script type="text/html" id="tmpl-lana-widgets-gallery-attachment-html">
    <div class="lana-widgets-gallery-attachment" data-id="{{-data.attachment.id}}">
        <div class="lana-widgets-gallery-attachment-margin">
            <input type="hidden" name="{{-data.input.name}}[]" class="lana-widgets-gallery-attachment-id"
                   value="{{-data.attachment.id}}">
            <div class="lana-thumbnail">
                <img src="{{-data.attachment.url}}" alt="{{-data.attachment.alt}}" title="{{-data.attachment.title}}"/>
            </div>
            <div class="lana-actions">
                <a href="#" class="lana-widgets-gallery-remove" data-id="{{-data.attachment.id}}"
                   title="<?php esc_attr_e( 'Remove', 'lana-widgets' ); ?>">
                    <span class="dashicons dashicons-no-alt"></span>
                </a>
            </div>
        </div>
    </div>
</script>