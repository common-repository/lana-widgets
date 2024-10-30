/**
 * Lana Widgets
 * Image
 */
var LanaWidgetsImageField;
var LanaWidgetsImageWidget;

/** Create jQuery anonymous function */
(function ($) {

    /**
     * Lana Widgets Image Field
     * default options
     * @type {{}}
     */
    var lanaWidgetsImageFieldDefaultOptions = {
        /**
         * wp localize variables
         * @typedef {object} lana_widgets_image_l10n
         */
        wp: {
            l10n: lana_widgets_image_l10n
        }
    };

    /**
     * Lana Widgets Image Field
     * @param selector
     * @param options
     * @constructor
     */
    LanaWidgetsImageField = function (selector, options) {
        this.selector = selector;
        this.options = $.extend({}, lanaWidgetsImageFieldDefaultOptions, options);

        this.init();
    };

    /**
     * Lana Widgets Image Field
     * prototype
     */
    LanaWidgetsImageField.prototype = {
        /**
         * element
         * @returns {*}
         */
        $element: function () {
            return $(this.selector);
        },
        /**
         * input image url
         * @returns {*}
         */
        $input: function () {
            return this.$('input.lana-widgets-image-url');
        },
        /**
         * image
         */
        $image: function () {
            return this.$('.lana-thumbnail img');
        },
        /**
         * get jQuery element
         * @param selector
         */
        $: function (selector) {
            return this.$element().find(selector);
        },
        /**
         * main
         * init
         */
        init: function () {
            this.initEvents();
        },
        /**
         * init events
         */
        initEvents: function () {

            this.on(this.$('.lana-widgets-edit-image-button'), 'click', 'onClickEdit');
            this.on(this.$input(), 'change input', 'onChangeInput');
        },
        /**
         * on click edit
         * @param e
         */
        onClickEdit: function (e) {
            e.preventDefault();

            /** edit atachment */
            this.editAttachment();
        },
        /**
         * select attachment
         */
        editAttachment: function () {
            var wpMediaWidgetsImageEditFrame;

            /** wp media frame */
            wpMediaWidgetsImageEditFrame = wp.media({
                title: this.options.wp.l10n['edit_image'],
                library: {
                    type: 'image'
                },
                multiple: false,
                state: 'library',
                editing: false
            });

            /** when open frame */
            wpMediaWidgetsImageEditFrame.on('open', function () {
                wpMediaWidgetsImageEditFrame.$el.closest('.media-modal').addClass('lana-widgets-image-media-modal');
            });

            /** when close frame */
            wpMediaWidgetsImageEditFrame.on('close', function () {
                wpMediaWidgetsImageEditFrame.detach();
            });

            /**
             * select attachment
             * add attachment to lana image gallery
             */
            wpMediaWidgetsImageEditFrame.on('select', this.proxy(function () {
                var selection = wpMediaWidgetsImageEditFrame.state().get('selection');

                if (selection) {
                    selection.each(this.proxy(function (attachment, i) {
                        this.changeImageUrl(attachment.attributes.url);
                    }));
                }
                wpMediaWidgetsImageEditFrame.close();
            }));

            /** open() */
            wpMediaWidgetsImageEditFrame.open();
        },
        /**
         * on change input
         * @param e
         * @param $element
         */
        onChangeInput: function (e, $element) {
            e.preventDefault();

            /** change image */
            this.changeImage($element);
        },
        /**
         * change image
         * @param $element
         */
        changeImage: function ($element) {
            /** change src */
            this.$image().attr('src', $element.val());

            /** not valid src */
            this.$image().on('error', function () {
                $(this).attr('src', $(this).data('placeholder-src'));
            });
        },
        /**
         * change image url
         * @param imageUrl
         */
        changeImageUrl: function (imageUrl) {
            this.$input().val(imageUrl);
            this.$input().trigger('change');
        },
        /**
         * on action
         * @param a1
         * @param a2
         * @param a3
         * @param a4
         */
        on: function (a1, a2, a3, a4) {
            var $element, event, selector, callback, args;

            /** find args */
            if (a1 instanceof jQuery) {

                /** 1. args( $el, event, selector, callback ) */
                if (a4) {
                    $element = a1;
                    event = a2;
                    selector = a3;
                    callback = a4;

                    /** 2. args( $el, event, callback ) */
                } else {
                    $element = a1;
                    event = a2;
                    callback = a3;
                }
            } else {

                /** 3. args( event, selector, callback ) */
                if (a3) {
                    event = a1;
                    selector = a2;
                    callback = a3;

                    /** 4. args( event, callback ) */
                } else {
                    event = a1;
                    callback = a2;
                }
            }

            /** element */
            $element = this.getEventTarget($element);

            /** modify callback */
            if (typeof callback === 'string') {
                callback = this.proxyEvent(this[callback]);
            }

            /** args */
            if (selector) {
                args = [event, selector, callback];
            } else {
                args = [event, callback];
            }

            /** on() */
            $element.on.apply($element, args);
        },
        /**
         *  get event target
         * @param $element
         * @returns {*|HTMLElement}
         */
        getEventTarget: function ($element) {
            return $element || this.$element() || $(document);
        },
        /**
         * validate event
         * @param e
         * @returns {boolean}
         */
        validateEvent: function (e) {
            if (this.selector) {
                return $(e.target).closest(this.selector).is(this.$element());
            } else {
                return true;
            }
        },
        /**
         * proxy event
         * @param callback
         * @returns {*}
         */
        proxyEvent: function (callback) {
            return this.proxy(function (e) {
                var args, extraArgs, eventArgs;

                if (!this.validateEvent(e)) {
                    return;
                }

                args = Array.prototype.slice.call(arguments);
                extraArgs = args.slice(1);
                eventArgs = [e, $(e.currentTarget)].concat(extraArgs);

                callback.apply(this, eventArgs);
            });
        },
        /**
         * proxy
         * with this model
         * @param callback
         * @returns {*}
         */
        proxy: function (callback) {
            return $.proxy(callback, this);
        }
    };

    /**
     * Lana Widgets Image Widget
     */
    LanaWidgetsImageWidget = function (id, selector, fields) {
        this.id = id;
        this.selector = selector;
        this.fields = fields;

        this.init();
    };

    /**
     * Lana Widgets Image Widget
     * prototype
     */
    LanaWidgetsImageWidget.prototype = {
        /**
         * widgets
         */
        widgets: [],
        /**
         * element
         * @returns {*}
         */
        $element: function () {
            return $(this.selector);
        },
        /**
         * get jQuery element
         * @param selector
         */
        $: function (selector) {
            return this.$element().find(selector);
        },
        /**
         * main
         * init
         */
        init: function () {
            this.initWidgetEvents();

            /** document ready */
            $(this.proxy(function () {
                var $widgets;

                /** check is widgets page */
                if ('widgets' !== window.pagenow) {
                    return;
                }

                /** widgets */
                $widgets = $('.widgets-holder-wrap:not(#available-widgets)').find('div.widget');

                /** get widget */
                $widgets.one('click.toggle-widget-expanded', this.proxyEvent(function (e, $element) {
                    /** widget added */
                    this.handleWidgetAdded(new $.Event('widget-added'), $(document), $element);
                }));
            }));
        },
        /**
         * init widget events
         */
        initWidgetEvents: function () {

            this.on($(document), 'widget-added', 'handleWidgetAdded');
            this.on($(document), 'widget-synced widget-updated', 'handleWidgetUpdated');
        },
        /**
         * handle widget added
         * @param e
         * @param $element
         * @param $widget
         */
        handleWidgetAdded: function (e, $element, $widget) {
            var $widgetForm = $widget.find('> .widget-inside > .form, > .widget-inside > form'), idBase, widgetId;

            /** check widget form */
            if (!$widgetForm.find(this.selector).length) {
                return;
            }

            /** get id base */
            idBase = $widgetForm.find('> .id_base').val();

            /** check id base */
            if (idBase !== this.id) {
                return;
            }

            /** get widget id */
            widgetId = $widgetForm.find('> .widget-id').val();

            /** check widget id */
            if ($.inArray(widgetId, this.widgets) > -1) {
                return;
            }

            this.initLanaWidgetsImage(widgetId);
        },
        /**
         * handle widget updated
         * @param e
         * @param $element
         * @param $widget
         */
        handleWidgetUpdated: function (e, $element, $widget) {
            var $widgetForm = $widget.find('> .widget-inside > .form, > .widget-inside > form'), idBase, widgetId;

            /** check widget form */
            if (!$widgetForm.find(this.selector).length) {
                return;
            }

            /** get id base */
            idBase = $widgetForm.find('> .id_base').val();

            /** check id base */
            if (idBase !== this.id) {
                return;
            }

            /** get widget id */
            widgetId = $widgetForm.find('> .widget-id').val();

            /** check widget id */
            if ($.inArray(widgetId, this.widgets) > -1) {
                return;
            }

            this.initLanaWidgetsImage(widgetId);
        },
        /**
         * init lana widgets image
         */
        initLanaWidgetsImage: function (widgetId) {
            var selector;

            /** init all fields */
            $.each(this.fields, this.proxy(function (i, fieldSelector) {

                /** add widget id and field name to selector */
                selector = this.selector + '[data-widget-id="' + widgetId + '"] > .lana-image-editor > .lana-widgets-image[data-field-name="' + fieldSelector + '"]';

                /** add lana widgets image to widgets */
                this.widgets[widgetId] = new LanaWidgetsImageField(selector);
            }));
        },
        /**
         * on action
         */
        on: function (a1, a2, a3, a4) {
            var $element, event, selector, callback, args;

            /** find args */
            if (a1 instanceof jQuery) {

                /** 1. args( $el, event, selector, callback ) */
                if (a4) {
                    $element = a1;
                    event = a2;
                    selector = a3;
                    callback = a4;

                    /** 2. args( $el, event, callback ) */
                } else {
                    $element = a1;
                    event = a2;
                    callback = a3;
                }
            } else {

                /** 3. args( event, selector, callback ) */
                if (a3) {
                    event = a1;
                    selector = a2;
                    callback = a3;

                    /** 4. args( event, callback ) */
                } else {
                    event = a1;
                    callback = a2;
                }
            }

            /** element */
            $element = this.getEventTarget($element);

            /** modify callback */
            if (typeof callback === 'string') {
                callback = this.proxyEvent(this[callback]);
            }

            /** args */
            if (selector) {
                args = [event, selector, callback];
            } else {
                args = [event, callback];
            }

            /** on() */
            $element.on.apply($element, args);
        },
        /**
         *  get event target
         * @param $element
         * @returns {*|HTMLElement}
         */
        getEventTarget: function ($element) {
            return $element || this.$element() || $(document);
        },
        /**
         * proxy event
         * @param callback
         * @returns {*}
         */
        proxyEvent: function (callback) {
            return this.proxy(function (e) {
                var args, extraArgs, eventArgs;

                args = Array.prototype.slice.call(arguments);
                extraArgs = args.slice(1);
                eventArgs = [e, $(e.currentTarget)].concat(extraArgs);

                callback.apply(this, eventArgs);
            });
        },
        /**
         * proxy
         * with this model
         * @param callback
         * @returns {*}
         */
        proxy: function (callback) {
            return $.proxy(callback, this);
        }
    };
})(jQuery);