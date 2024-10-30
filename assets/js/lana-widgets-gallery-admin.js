/**
 * Lana Widgets
 * Gallery
 */
var LanaWidgetsGalleryField;
var LanaWidgetsGalleryWidget;

/** Create jQuery anonymous function */
(function ($) {

    /**
     * Lana Widgets Gallery Field
     * default options
     * @type {{}}
     */
    var lanaWidgetsGalleryFieldDefaultOptions = {
        /** underscore.js template options */
        underscoreTemplate: {
            evaluate: /{{([\s\S]+?)}}/g,
            interpolate: /{{=([\s\S]+?)}}/g,
            escape: /{{-([\s\S]+?)}}/g,
            variable: 'data'
        },
        /**
         * wp localize variables
         * @typedef {object} lana_widgets_gallery_l10n
         */
        wp: {
            l10n: lana_widgets_gallery_l10n
        }
    };

    /**
     * Lana Widgets Gallery Field
     * @param selector
     * @param options
     * @constructor
     */
    LanaWidgetsGalleryField = function (selector, options) {
        this.selector = selector;
        this.options = $.extend({}, lanaWidgetsGalleryFieldDefaultOptions, options);

        this.init();
    };

    /**
     * Lana Widgets Gallery
     * prototype
     */
    LanaWidgetsGalleryField.prototype = {
        /**
         * element
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
         * input
         * @returns {*}
         */
        $input: function () {
            return this.$('input.lana-widgets-gallery-attachment-id');
        },
        /**
         * attachments
         * @returns {*}
         */
        $attachments: function () {
            return this.$('.lana-widgets-gallery-attachments');
        },
        /**
         * attachment
         * @param id
         * @returns {*}
         */
        $attachment: function (id) {
            if (typeof id !== 'undefined') id = '[data-id=' + id + ']';
            else id = '';

            return this.$('.lana-widgets-gallery-attachment' + id);
        },
        /**
         * Underscore template
         * with Lana Image Gallery options
         *
         * @param id
         * @returns {Function}
         */
        template: function (id) {
            return _.template($('#tmpl-lana-widgets-gallery-' + id).html(), this.options.underscoreTemplate);
        },
        /**
         * main
         * init
         */
        init: function () {
            this.initEvents();

            this.addSortable();
            this.addResizable();

            this.resize();
        },
        /**
         * init events
         */
        initEvents: function () {
            this.on('click', '.lana-widgets-gallery-add', 'onClickAdd');
            this.on('click', '.lana-widgets-gallery-remove', 'onClickRemove');

            this.on('resize', 'onResize');
        },
        /**
         * add sortable
         * with jquery ui
         */
        addSortable: function () {

            this.$attachments().sortable({
                items: '.lana-widgets-gallery-attachment',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                scroll: true,
                start: function (event, ui) {
                    ui.placeholder.html(ui.item.html());
                    ui.placeholder.removeAttr('style');
                },
                stop: this.proxyEvent(function () {
                    this.$input().trigger('change');
                })
            });
        },
        /**
         * add resizable
         * with jquery ui
         */
        addResizable: function () {

            this.$element().resizable({
                handles: 's',
                minHeight: 200
            });
        },
        /**
         * resize
         * change columns
         */
        resize: function () {
            var columns, width = this.$element().width();

            /** set columns */
            columns = Math.min(Math.round(width / 150), 8);

            /** update oclumns */
            this.$element().attr('data-columns', columns);
        },
        /**
         * on resize
         */
        onResize: function () {
            this.resize();
        },
        /**
         * on click add
         * @param e
         */
        onClickAdd: function (e) {
            var selectedAttachmentIds, LanaWidgetsGallery = this,
                wpMediaLanaWidgetsGalleryAttachmentLibrary, wpMediaLanaWidgetsGalleryAddFrame;

            e.preventDefault();

            /** wp media frame */
            wpMediaLanaWidgetsGalleryAddFrame = wp.media({
                title: this.options.wp.l10n['add_image_to_gallery'],
                library: {
                    type: 'image'
                },
                multiple: 'add',
                state: 'library',
                editing: true
            });

            /** add lana widgets gallery to frame */
            wpMediaLanaWidgetsGalleryAddFrame.lanaWidgetsGallery = this;

            /** when open frame */
            wpMediaLanaWidgetsGalleryAddFrame.on('open', function () {
                wpMediaLanaWidgetsGalleryAddFrame.$el.closest('.media-modal').addClass('lana-widgets-gallery-media-modal');
            });

            /** when close frame */
            wpMediaLanaWidgetsGalleryAddFrame.on('close', function () {
                wpMediaLanaWidgetsGalleryAddFrame.detach();
            });

            /**
             * select attachment
             * add attachment to lana widgets gallery
             */
            wpMediaLanaWidgetsGalleryAddFrame.on('select', this.proxy(function () {
                var selection = wpMediaLanaWidgetsGalleryAddFrame.state().get('selection');

                if (selection) {
                    selection.each(this.proxy(function (attachment, i) {
                        this.addAttachment(attachment);
                    }));
                }
            }));

            /**
             * add custom attachment library
             * add selected class to attachment
             */
            selectedAttachmentIds = this.$input().map(function (id, element) {
                return parseInt($(element).val());
            }).get();

            /**
             * extend attachment library
             * add selected class
             */
            wpMediaLanaWidgetsGalleryAttachmentLibrary = wp.media.view.Attachment.Library;

            wp.media.view.Attachment.Library = wpMediaLanaWidgetsGalleryAttachmentLibrary.extend({
                /**
                 * render
                 * @returns {*}
                 */
                render: function () {
                    var controllerLanaWidgetsGallery = LanaWidgetsGallery.getObjectProperty(this, 'controller', 'lanaWidgetsGallery'),
                        attributes = LanaWidgetsGallery.getObjectProperty(this, 'model', 'attributes');

                    if (controllerLanaWidgetsGallery && selectedAttachmentIds && attributes) {
                        if ($.inArray(attributes.id, selectedAttachmentIds) > -1) {
                            this.$el.addClass('lana-widgets-gallery-selected');
                        }
                    }

                    return wpMediaLanaWidgetsGalleryAttachmentLibrary.prototype.render.apply(this, arguments);
                }
            });

            /** open() */
            wpMediaLanaWidgetsGalleryAddFrame.open();
        },
        /**
         * validate attachment
         * @param attachment
         * @returns {*}
         */
        validateAttachment: function (attachment) {
            var url;

            /** default attachment */
            attachment = this.parseObjectArgs(attachment, {
                id: '',
                url: '',
                alt: '',
                title: '',
                type: 'image'
            });

            /** wp attachment */
            if (this.getObjectProperty(attachment, 'attributes')) {
                attachment = attachment.attributes;

                /** image url (medium size) */
                url = this.getObjectProperty(attachment, 'sizes', 'medium', 'url');

                if (url !== null) {
                    attachment.url = url;
                }
            }

            return attachment;
        },
        /**
         * add attachment
         */
        addAttachment: function (attachment) {
            var attachmentTemplate = this.template('attachment-html');

            /** validate attachment */
            attachment = this.validateAttachment(attachment);

            /** check attachment */
            if (this.$attachment(attachment['id']).length) {
                return;
            }

            /** append */
            this.$attachments().append(attachmentTemplate({
                input: {
                    name: this.$element().data('input-field-name')
                },
                attachment: attachment
            }));

            /** resize */
            this.resize();

            /** trigger input change */
            this.$input().trigger('change');
        },
        /**
         * on click remove
         * @param e
         * @param $element
         */
        onClickRemove: function (e, $element) {
            var id = $element.data('id');

            /** prevent event from triggering click on attachment */
            e.preventDefault();
            e.stopPropagation();

            /** check id */
            if (!id) {
                return;
            }

            /** remove atachment */
            this.removeAttachment(id);
        },
        /**
         * remove attachment
         * @param id
         */
        removeAttachment: function (id) {

            /** remove attachment */
            this.$attachment(id).remove();

            /** resize */
            this.resize();

            /** trigger input change */
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
        },
        /**
         * parse args
         * @param args
         * @param defaults
         * @returns {*}
         */
        parseObjectArgs: function (args, defaults) {
            if (typeof args !== 'object') args = {};
            if (typeof defaults !== 'object') defaults = {};
            return $.extend({}, defaults, args);
        },
        /**
         * get object property
         * @param obj
         * @returns {*}
         */
        getObjectProperty: function (obj) {
            for (var i = 1; i < arguments.length; i++) {
                if (!obj.hasOwnProperty(arguments[i])) {
                    return null;
                }
                obj = obj[arguments[i]];
            }
            return obj;
        }
    };

    /**
     * Lana Widgets Gallery Widget
     */
    LanaWidgetsGalleryWidget = function (id, selector, fields) {
        this.id = id;
        this.selector = selector;
        this.fields = fields;

        this.init();
    };

    /**
     * Lana Widgets Gallery Widget
     * prototype
     */
    LanaWidgetsGalleryWidget.prototype = {
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

            this.initLanaWidgetsGallery(widgetId);
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

            this.initLanaWidgetsGallery(widgetId);
        },
        /**
         * init lana widgets gallery
         */
        initLanaWidgetsGallery: function (widgetId) {
            var selector;

            /** init all fields */
            $.each(this.fields, this.proxy(function (i, fieldSelector) {

                /** add widget id and field name to selector */
                selector = this.selector + '[data-widget-id="' + widgetId + '"] > .lana-gallery-editor > .lana-widgets-gallery[data-field-name="' + fieldSelector + '"]';

                /** add lana widgets gallery to widgets */
                this.widgets[widgetId] = new LanaWidgetsGalleryField(selector);
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