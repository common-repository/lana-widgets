/**
 * Lana Carousel Widget
 */
var LanaCarouselWidget;

/** Create jQuery anonymous function */
(function () {

    /**
     * Lana Carousel Widget
     * @param id
     * @constructor
     */
    LanaCarouselWidget = function (id) {
        new LanaWidgetsGalleryWidget(id, '.lana-widgets-carousel-widget', ['gallery_ids']);
    };
})(jQuery);
