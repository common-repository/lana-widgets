/**
 * Lana Thumbnail Widget
 */
var LanaThumbnailWidget;

/** Create jQuery anonymous function */
(function () {

    /**
     * Lana Thumbnail Widget
     * @param id
     * @constructor
     */
    LanaThumbnailWidget = function (id) {
        new LanaWidgetsImageWidget(id, '.lana-widgets-thumbnail-widget', ['image_url']);
    };
})(jQuery);
