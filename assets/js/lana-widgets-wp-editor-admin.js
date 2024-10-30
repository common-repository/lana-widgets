/**
 * Lana Widgets
 * WordPress Editor (TinyMCE)
 *
 * global tinymce, switchEditors
 */
var LanaWidgetsWpEditorField;
var LanaWidgetsWpEditorWidget;

/** Create jQuery anonymous function */
(function ($) {
    /**
     * Lana Widgets WordPress Editor Field
     * default options
     * @type {{}}
     */
    var lanaWidgetsWpEditorDefaultOptions = {};

    /**
     * Lana Widgets WordPress Editor Field
     * @param selector
     * @param options
     * @constructor
     */
    LanaWidgetsWpEditorField = function (selector, options) {
        this.selector = selector;
        this.options = $.extend({}, lanaWidgetsWpEditorDefaultOptions, options);

        this.init();
    };

    /**
     * Lana Widgets WordPress Editor Field
     * prototype
     */
    LanaWidgetsWpEditorField.prototype = {
        /**
         * element
         * @returns {*}
         */
        $element: function () {
            return $(this.selector);
        },
        /**
         * element id
         */
        id: function () {
            return this.$element().data('field-name');
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
            this.initTinymce();
            this.initQuicktags();

            /** change to text mode */
            switchEditors.go(this.id(), 'tmce');
        },
        /**
         * init
         * TinyMCE
         */
        initTinymce: function () {
            /** add tinymce options to preinit */
            tinyMCEPreInit.mceInit[this.id()] = $.extend({}, this.options.tinymce);

            /** init tinymce */
            tinyMCE.execCommand('mceRemoveEditor', true, this.id());
        },
        /**
         * init
         * Quick Tags
         */
        initQuicktags: function () {
            /** add quicktags options to preinit */
            tinyMCEPreInit.qtInit[this.id()] = $.extend({}, {id: this.id()});

            /** init quicktags */
            QTags(tinyMCEPreInit.qtInit[this.id()]);
            QTags._buttonsInit();
        }
    };

    /**
     * Lana Widgets WordPress Editor Widget
     */
    LanaWidgetsWpEditorWidget = function (id, selector, fields) {
        this.id = id;
        this.selector = selector;
        this.fields = fields;

        this.init();
    };

    /**
     * Lana Widgets WordPress Editor Widget
     * prototype
     */
    LanaWidgetsWpEditorWidget.prototype = {
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
        },
        /**
         * init widget events
         */
        initWidgetEvents: function () {

            this.on($(document), 'widget-added', 'handleWidgetAdded');
        },
        /**
         * handle widget added
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

            this.initLanaWidgetsWpEditor(widgetId);
        },
        /**
         * init lana widgets wp editor
         * @param widgetId
         */
        initLanaWidgetsWpEditor: function (widgetId) {
            var selector;

            /** init all fields */
            $.each(this.fields, this.proxy(function (fieldSelector, options) {

                /** add widget id and field name to selector */
                selector = this.selector + '[data-widget-id="' + widgetId + '"] > .lana-wp-editor > .lana-widgets-wp-editor[data-field-name="' + fieldSelector + '"]';

                /** add lana widgets wp editor to widgets */
                this.widgets[widgetId] = new LanaWidgetsWpEditorField(selector, {tinymce: options});
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