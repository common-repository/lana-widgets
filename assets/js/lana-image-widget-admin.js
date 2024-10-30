/**
 * Lana Image Widget
 */
var LanaImageWidget;

/** Create jQuery anonymous function */
(function () {

    /**
     * Lana Image Widget
     * @param id
     * @constructor
     */
    LanaImageWidget = function (id) {
        new LanaWidgetsImageWidget(id, '.lana-widgets-image-widget', ['image_url']);
    };
})(jQuery);
